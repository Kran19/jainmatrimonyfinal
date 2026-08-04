<?php
/**
 * DB AUTO-FIX VIA HTTP
 * Runs pending migrations + adds any known-missing columns directly.
 * Visit: http://127.0.0.1:8000/db_fix.php
 * DELETE THIS FILE after use!
 */
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$log = [];
function addLog($msg, $type = 'ok') {
    global $log;
    $log[] = ['msg' => $msg, 'type' => $type];
}

// ─── Step 1: Run pending migrations ───────────────────────────────────────────
addLog('Running php artisan migrate --force...', 'info');
try {
    Artisan::call('migrate', ['--force' => true]);
    $migrateOutput = Artisan::output();
    addLog("Migration output:\n" . $migrateOutput, 'ok');
} catch (\Exception $e) {
    addLog('Migration error: ' . $e->getMessage(), 'err');
}

// ─── Step 2: Patch known missing columns ─────────────────────────────────────
addLog('--- Patching individual missing columns ---', 'info');

$patches = [
    // TABLE => [ [column, type, nullable, default, after], ... ]
    'users' => [
        ['registration_step',  'tinyInteger', true, 1, 'is_public'],
        ['is_approved',        'tinyInteger', true, 0, 'registration_step'],
        ['registration_count', 'integer',     true, 0, 'is_approved'],
        ['deletion_count',     'integer',     true, 0, 'registration_count'],
    ],
    'payments' => [
        ['verified_by',     'bigInteger', true, null, 'status'],
        ['full_name',       'string',     true, null, 'verified_by'],
        ['phone_number',    'string',     true, null, 'full_name'],
        ['email',           'string',     true, null, 'phone_number'],
        ['address',         'text',       true, null, 'email'],
        ['dob',             'date',       true, null, 'address'],
    ],
    'advertisements' => [
        ['media_type',       'string',  true, 'image', 'status'],
        ['sort_order',       'integer', true, 0,       'media_type'],
        ['duration_seconds', 'integer', true, 3,       'sort_order'],
    ],
    'marquee_ads' => [
        // ensure notice_text is text not varchar
    ],
];

foreach ($patches as $table => $columns) {
    if (!Schema::hasTable($table)) {
        addLog("Table '$table' doesn't exist — skipping patches.", 'warn');
        continue;
    }
    foreach ($columns as [$col, $type, $nullable, $default, $after]) {
        if (Schema::hasColumn($table, $col)) {
            addLog("  $table.$col already exists — skip.", 'info');
            continue;
        }
        try {
            Schema::table($table, function (Blueprint $table_bp) use ($col, $type, $nullable, $default, $after) {
                $colDef = match($type) {
                    'tinyInteger' => $table_bp->tinyInteger($col),
                    'integer'     => $table_bp->integer($col),
                    'bigInteger'  => $table_bp->unsignedBigInteger($col),
                    'string'      => $table_bp->string($col, 255),
                    'text'        => $table_bp->text($col),
                    'date'        => $table_bp->date($col),
                    default       => $table_bp->string($col),
                };
                if ($nullable) $colDef->nullable();
                if ($default !== null) $colDef->default($default);
                if ($after) $colDef->after($after);
            });
            addLog("  ✓ Added $table.$col ($type)", 'ok');
        } catch (\Exception $e) {
            addLog("  ✗ Failed $table.$col: " . $e->getMessage(), 'err');
        }
    }
}

// ─── Step 3: Fix payments.status enum ─────────────────────────────────────────
addLog('--- Checking payments.status enum includes verified ---', 'info');
try {
    if (Schema::hasTable('payments')) {
        $col = DB::selectOne("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'status'");
        if ($col && strpos(strtolower($col->COLUMN_TYPE), 'verified') === false) {
            DB::statement("ALTER TABLE `payments` MODIFY COLUMN `status` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'");
            addLog("  ✓ Fixed payments.status enum to include 'verified'", 'ok');
        } else {
            addLog("  payments.status enum OK: {$col->COLUMN_TYPE}", 'ok');
        }
    }
} catch (\Exception $e) {
    addLog("  ✗ payments.status fix failed: " . $e->getMessage(), 'err');
}

// ─── Step 4: Fix users.payment_status enum ────────────────────────────────────
addLog('--- Checking users.payment_status ---', 'info');
try {
    if (Schema::hasTable('users') && !Schema::hasColumn('users', 'payment_status')) {
        Schema::table('users', function (Blueprint $t) {
            $t->enum('payment_status', ['pending','approved','rejected'])->default('pending')->after('payment_transaction_id');
        });
        addLog("  ✓ Added users.payment_status", 'ok');
    } else {
        addLog("  users.payment_status already exists", 'ok');
    }
} catch (\Exception $e) {
    addLog("  ✗ users.payment_status: " . $e->getMessage(), 'err');
}

// ─── Step 5: Verify final state ───────────────────────────────────────────────
addLog('--- Done! Run db_audit.php to verify ---', 'info');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>DB Fix Results</title>
<style>
body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
h1 { color: #a78bfa; margin-bottom: 1.5rem; }
.ok   { color: #4ade80; }
.err  { color: #f87171; }
.warn { color: #fbbf24; }
.info { color: #60a5fa; }
pre { background: #1e293b; padding: 1rem; border-radius: 0.5rem; overflow: auto; white-space: pre-wrap; }
a { color: #a78bfa; }
</style>
</head>
<body>
<h1>🔧 DB Fix Results</h1>
<pre>
<?php foreach ($log as $entry): ?>
<span class="<?= $entry['type'] ?>"><?= htmlspecialchars($entry['msg']) ?></span>
<?php endforeach; ?>
</pre>
<p style="margin-top:1.5rem">
    <a href="/db_audit.php">→ Go to DB Audit to verify results</a> &nbsp;|&nbsp;
    <a href="/admin/payments">→ Go to Admin Payments</a>
</p>
<p style="color:#475569; font-size:0.75rem; margin-top:1rem">⚠️ Delete db_fix.php and db_audit.php and run_migrate.php after use for security.</p>
</body>
</html>
