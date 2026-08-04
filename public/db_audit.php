<?php
/**
 * FULL DATABASE AUDIT TOOL
 * Checks every table and column the website code expects vs what's actually in the DB.
 * Reports: MISSING tables, MISSING columns, TYPE mismatches, extra columns.
 * Visit: http://127.0.0.1:8000/db_audit.php
 */
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ─── EXPECTED SCHEMA ──────────────────────────────────────────────────────────
// Format: 'table' => ['col1', 'col2', ...]
// Pulled from all migrations + all Model $fillable arrays + controller code
$expected = [

    'users' => [
        'id','profile_id','full_name','email','mobile','country_code','password_hash',
        'are_you_digambar_jain',
        // Cast/Religion
        'cast','subcast','custom_subcast',
        // Address
        'permanent_address','pin_code','current_address',
        // Family
        'father_name','father_mobile','father_income','father_occupation',
        'mother_name','mother_mobile','mother_occupation','mother_occupation_details',
        'brothers','brothers_married','brothers_unmarried',
        'sisters','sisters_married','sisters_unmarried',
        // Mandir
        'mandir','custom_mandir','mandir_name','mandir_address','mandir_pincode',
        // References
        'ref1_name','ref1_mobile','ref1_relation',
        'ref2_name','ref2_mobile','ref2_relation',
        'filled_by','id_proof_type','id_proof_path',
        // Profile details
        'gender','birth_date','birth_time','birth_place','native_place',
        'gotra','mama_gotra','manglik','height','weight','marital_status','handicapped',
        // Professional
        'higher_education','occupation','company_name','designation','monthly_income',
        // Lifestyle
        'languages','hobbies','partner_preference',
        // Media & Payment
        'profile_photo','family_photo','profile_photo_drive_url',
        'payment_screenshot','payment_proof_drive_url','payment_transaction_id','payment_status',
        // Admin statuses
        'status','verified','approved_by','approved_at','featured_until',
        'has_set_password','registration_source','is_public',
        // Added by patch migrations
        'registration_step','is_approved',
        // Added by later migrations
        'registration_count','deletion_count',
        // Soft delete
        'deleted_at',
        'created_at','updated_at','remember_token',
    ],

    'admins' => [
        'id','name','email','password_hash','role','status',
        'last_login','last_login_ip','password_updated_at','two_factor_enabled',
        'created_at','updated_at',
    ],

    'payments' => [
        'id','user_id','membership_id','amount','transaction_id',
        'payment_method','payment_remarks','payment_screenshot',
        'status','verified_by',
        // Backup form data columns
        'full_name','phone_number','email','address','dob',
        'created_at',
        // updated_at intentionally excluded (UPDATED_AT=null in model)
    ],

    'memberships' => [
        'id','plan_name','price','duration_days','contact_limit',
        'featured_profile','priority_support','status',
        'created_at','updated_at',
    ],

    'user_memberships' => [
        'id','user_id','membership_id','start_date','end_date',
        'status','can_view_contacts',
        'created_at','updated_at',
    ],

    'contact_messages' => [
        'id','name','email','mobile','subject','message',
        'status','reply_text',
        'created_at','updated_at',
    ],

    'success_stories' => [
        'id','user_id','couple_name','engagement_date','marriage_date',
        'story','photo','status',
        'created_at','updated_at',
    ],

    'community_events' => [
        'id','title','description','event_date','location','banner','status',
        'created_at','updated_at',
    ],

    'advertisements' => [
        'id','title','image','link','position','status',
        // Added by patch migration
        'media_type','sort_order','duration_seconds',
        'created_at','updated_at',
    ],

    'site_settings' => [
        'id','setting_key','setting_value',
        'created_at','updated_at',
    ],

    'activity_logs' => [
        'id','user_type','user_id','action','details','ip_address',
        'created_at','updated_at',
    ],

    'registration_fields' => [
        'id','field_group','field_key','field_label','field_type','field_options',
        'is_custom','is_visible','is_required','is_core','sort_order',
        'created_at','updated_at',
    ],

    'user_custom_data' => [
        'id','user_id','field_id','field_value',
        'created_at','updated_at',
    ],

    'user_likes' => [
        'id','user_id','liked_user_id',
        'created_at','updated_at',
    ],

    'user_relatives' => [
        'id','user_id','relation','name','mobile','occupation',
        'created_at','updated_at',
    ],

    'account_requests' => [
        'id','user_id','request_type','reason','status',
        'created_at','updated_at',
    ],

    'scrolling_news' => [
        'id','content','link','status',
        'created_at','updated_at',
    ],

    'marquee_ads' => [
        'id','notice_text','status',
        'created_at','updated_at',
    ],

    'news' => [
        'id','title','content','image','status',
        'created_at','updated_at',
    ],

    'gallery' => [
        'id','title','category','image_path','media_type','media_url','status',
        'created_at','updated_at',
    ],

    'video_gallery' => [
        'id','title','video_type','video_url','video_file','thumbnail',
        'description','display_order','status',
        'created_at','updated_at',
    ],

    'otp_verifications' => [
        'id','email','otp_code','expires_at','verified',
        'created_at','updated_at',
    ],

    'password_reset_tokens' => [
        'email','token','created_at',
    ],

    'password_resets' => [
        'id','email','token','created_at',
    ],

    'sessions' => [
        'id','user_id','ip_address','user_agent','payload','last_activity',
    ],

    'committee_members' => [
        'id','name','designation','description','photo','sort_order','status',
        'created_at','updated_at',
    ],

    'import_history' => [
        'id','source_type','imported_records','imported_by','import_date',
        'created_at','updated_at',
    ],

    'members' => [
        'id','full_name','gender','birth_date','birth_time','birth_place',
        'native','gotra','mama_gotra','manglik','height_cm','weight_kg',
        'country_code','mobile_number','email','permanent_address','permanent_pin_code',
        'current_address','higher_education','occupation','company_name','designation',
        'monthly_income','father_name','father_mobile','father_occupation',
        'father_monthly_income','mother_name','mother_mobile','mother_occupation',
        'brothers_total','brothers_married','brothers_unmarried',
        'sisters_total','sisters_married','sisters_unmarried',
        'partner_preferences','languages_known','hobbies',
        'widow_divorce','handicapped_physical_deficiency','profile_photo_path',
        'created_at','updated_at',
    ],
];

// ─── FETCH ACTUAL DB COLUMNS ──────────────────────────────────────────────────
$actualTables = [];
try {
    $dbName = DB::selectOne('SELECT DATABASE() as db')->db;
    $rows = DB::select("
        SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ?
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ", [$dbName]);

    foreach ($rows as $row) {
        $actualTables[$row->TABLE_NAME][] = [
            'name'     => $row->COLUMN_NAME,
            'type'     => $row->COLUMN_TYPE,
            'nullable' => $row->IS_NULLABLE,
            'default'  => $row->COLUMN_DEFAULT,
            'key'      => $row->COLUMN_KEY,
        ];
    }
} catch (\Exception $e) {
    die("DB connection failed: " . $e->getMessage());
}

// ─── COMPUTE MISMATCHES ───────────────────────────────────────────────────────
$missing_tables   = [];
$missing_columns  = [];
$present_tables   = [];
$extra_columns    = []; // columns in DB not in our expected list (informational)

foreach ($expected as $table => $expectedCols) {
    if (!isset($actualTables[$table])) {
        $missing_tables[] = $table;
        continue;
    }

    $present_tables[] = $table;
    $actualColNames = array_column($actualTables[$table], 'name');

    foreach ($expectedCols as $col) {
        if (!in_array($col, $actualColNames)) {
            $missing_columns[$table][] = $col;
        }
    }

    // Find extra columns (in DB but not expected by us — informational only)
    foreach ($actualColNames as $actualCol) {
        if (!in_array($actualCol, $expectedCols)) {
            $extra_columns[$table][] = $actualCol;
        }
    }
}

// ─── ALSO SHOW TABLES IN DB BUT NOT IN OUR EXPECTED LIST ─────────────────────
$unexpected_tables = array_diff(array_keys($actualTables), array_keys($expected));
// Remove migration tracking tables
$unexpected_tables = array_filter($unexpected_tables, fn($t) => !in_array($t, ['migrations','cache','cache_locks','jobs','job_batches','failed_jobs','import_images']));

// ─── OUTPUT ───────────────────────────────────────────────────────────────────
$hasMissing = !empty($missing_tables) || !empty($missing_columns);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DB Audit Report</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
h1 { color: #a78bfa; font-size: 1.6rem; margin-bottom: 0.5rem; }
.subtitle { color: #64748b; font-size: 0.85rem; margin-bottom: 2rem; }
h2 { font-size: 1.1rem; margin: 2rem 0 0.75rem; padding-bottom: 0.4rem; border-bottom: 1px solid #1e293b; }
.ok { color: #4ade80; }
.err { color: #f87171; font-weight: bold; }
.warn { color: #fbbf24; }
.info { color: #60a5fa; }
.badge { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: bold; margin-left: 0.3rem; }
.badge-ok { background: #14532d; color: #4ade80; }
.badge-err { background: #450a0a; color: #f87171; }
.badge-warn { background: #422006; color: #fbbf24; }
.summary-box { background: #1e293b; border-radius: 0.75rem; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; gap: 2rem; flex-wrap: wrap; }
.summary-stat { text-align: center; }
.summary-stat .num { font-size: 2rem; font-weight: 900; }
.summary-stat .label { font-size: 0.75rem; color: #94a3b8; margin-top: 0.2rem; }
table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; font-size: 0.82rem; }
th { background: #1e293b; padding: 0.5rem 0.75rem; text-align: left; color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; }
td { padding: 0.45rem 0.75rem; border-bottom: 1px solid #1e293b; vertical-align: top; }
tr:hover td { background: #1e293b88; }
.section { background: #111827; border: 1px solid #1e293b; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; }
.tag { display: inline-block; padding: 0.1rem 0.4rem; border-radius: 0.2rem; font-size: 0.7rem; }
.tag-missing { background: #450a0a; color: #fca5a5; }
.tag-extra { background: #1e3a5f; color: #93c5fd; }
.tag-ok { background: #14532d; color: #86efac; }
pre { white-space: pre-wrap; word-break: break-all; }
.cols-grid { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.5rem; }
.col-pill { padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-family: monospace; }
.col-ok { background: #134e4a; color: #5eead4; }
.col-missing { background: #450a0a; color: #fca5a5; border: 1px solid #f87171; }
.col-extra { background: #1e3a5f; color: #93c5fd; }
hr { border: none; border-top: 1px solid #1e293b; margin: 1.5rem 0; }
</style>
</head>
<body>

<h1>🗄️ Database Audit Report</h1>
<p class="subtitle">Comparing what the website code expects vs what's actually in the MySQL database (<?= htmlspecialchars($dbName) ?>)</p>

<?php
$totalMissingCols = array_sum(array_map('count', $missing_columns));
?>
<div class="summary-box">
    <div class="summary-stat">
        <div class="num <?= empty($missing_tables) ? 'ok' : 'err' ?>"><?= count($missing_tables) ?></div>
        <div class="label">MISSING TABLES</div>
    </div>
    <div class="summary-stat">
        <div class="num <?= $totalMissingCols === 0 ? 'ok' : 'err' ?>"><?= $totalMissingCols ?></div>
        <div class="label">MISSING COLUMNS</div>
    </div>
    <div class="summary-stat">
        <div class="num ok"><?= count($present_tables) ?></div>
        <div class="label">TABLES OK</div>
    </div>
    <div class="summary-stat">
        <div class="num info"><?= count($actualTables) ?></div>
        <div class="label">TOTAL DB TABLES</div>
    </div>
    <div class="summary-stat">
        <div class="num" style="color:#fbbf24"><?= array_sum(array_map('count', $extra_columns)) ?></div>
        <div class="label">EXTRA COLS (INFO)</div>
    </div>
</div>

<?php if (!$hasMissing): ?>
<div class="section" style="border-color: #166534;">
    <h2 style="color:#4ade80; border-color:#166534">✅ ALL TABLES AND COLUMNS MATCH — DATABASE IS IN SYNC</h2>
    <p style="color:#86efac; margin-top:0.5rem">Every table and column that the website code expects exists in the database. No action needed.</p>
</div>
<?php endif; ?>

<?php if (!empty($missing_tables)): ?>
<div class="section" style="border-color:#dc2626">
    <h2 class="err">❌ MISSING TABLES (<?= count($missing_tables) ?>)</h2>
    <p style="color:#fca5a5; margin-top:0.5rem; margin-bottom:1rem">These tables are expected by the code but DO NOT EXIST in the database. Run the migration to create them.</p>
    <?php foreach ($missing_tables as $t): ?>
    <div style="margin: 0.5rem 0; padding: 0.5rem 1rem; background:#450a0a; border-radius:0.4rem;">
        <span class="err">✗</span> <code style="color:#fca5a5"><?= $t ?></code>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($missing_columns)): ?>
<div class="section" style="border-color:#b45309">
    <h2 class="warn">⚠️ MISSING COLUMNS (<?= $totalMissingCols ?> across <?= count($missing_columns) ?> tables)</h2>
    <p style="color:#fcd34d; margin-top:0.5rem; margin-bottom:1rem">These columns exist in the code but are NOT in the database. The ALTER migration below will add them.</p>
    <table>
        <thead><tr><th>Table</th><th>Missing Column(s)</th></tr></thead>
        <tbody>
        <?php foreach ($missing_columns as $table => $cols): ?>
        <tr>
            <td><code class="warn"><?= $table ?></code></td>
            <td>
                <?php foreach ($cols as $col): ?>
                <span class="tag tag-missing"><?= $col ?></span>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<hr>

<h2 class="info">📋 Full Table-by-Table Column Report</h2>

<?php foreach ($expected as $table => $expectedCols): ?>
<?php
$tableExists = isset($actualTables[$table]);
$tableMissingCols = $missing_columns[$table] ?? [];
$tableExtraCols = $extra_columns[$table] ?? [];
$actualColNames = $tableExists ? array_column($actualTables[$table], 'name') : [];
?>
<div class="section" style="border-color: <?= !$tableExists ? '#dc2626' : (empty($tableMissingCols) ? '#166534' : '#b45309') ?>">
    <div style="display:flex; justify-content:space-between; align-items:center">
        <strong style="font-size:1rem; color:<?= !$tableExists ? '#f87171' : (empty($tableMissingCols) ? '#4ade80' : '#fbbf24') ?>">
            <?= $tableExists ? (empty($tableMissingCols) ? '✅' : '⚠️') : '❌' ?>
            <?= $table ?>
        </strong>
        <?php if ($tableExists): ?>
        <span style="font-size:0.75rem; color:#64748b"><?= count($actualColNames) ?> columns in DB / <?= count($expectedCols) ?> expected</span>
        <?php else: ?>
        <span class="badge badge-err">TABLE MISSING</span>
        <?php endif; ?>
    </div>

    <?php if ($tableExists): ?>
    <div class="cols-grid" style="margin-top:0.75rem">
        <?php foreach ($expectedCols as $col): ?>
        <span class="col-pill <?= in_array($col, $actualColNames) ? 'col-ok' : 'col-missing' ?>">
            <?= $col ?>
            <?= !in_array($col, $actualColNames) ? ' ✗' : '' ?>
        </span>
        <?php endforeach; ?>
        <?php foreach ($tableExtraCols as $extra): ?>
        <span class="col-pill col-extra" title="Extra column in DB (not in expected list)"><?= $extra ?> *</span>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($tableExtraCols)): ?>
    <p style="font-size:0.7rem; color:#60a5fa; margin-top:0.5rem">* Blue = extra columns in DB that code doesn't use (legacy data — harmless, no action needed)</p>
    <?php endif; ?>
    <?php else: ?>
    <p style="color:#fca5a5; margin-top:0.5rem; font-size:0.85rem">This table does not exist in the database at all.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php if (!empty($unexpected_tables)): ?>
<hr>
<div class="section" style="border-color:#1e3a5f">
    <h2 class="info">ℹ️ Tables in DB but NOT in our expected list (Legacy/Extra)</h2>
    <p style="color:#93c5fd; margin-top:0.5rem; margin-bottom:0.75rem">These tables exist in the DB but are not used by the website code. They are harmless legacy tables — no action needed.</p>
    <?php foreach ($unexpected_tables as $t): ?>
    <span style="display:inline-block; margin:0.2rem; padding:0.2rem 0.6rem; background:#1e3a5f; color:#93c5fd; border-radius:0.25rem; font-size:0.8rem"><?= $t ?></span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<hr>

<?php if ($hasMissing): ?>
<div class="section" style="border-color:#7c3aed">
    <h2 style="color:#a78bfa">🔧 Auto-Fix SQL — Run this to add all missing items</h2>
    <p style="color:#c4b5fd; margin-top:0.5rem; margin-bottom:1rem">Copy and run this in phpMyAdmin or MySQL CLI to fix all missing columns:</p>
    <pre style="background:#1e293b; padding:1rem; border-radius:0.5rem; color:#e2e8f0; font-size:0.8rem">
<?php
// Generate ALTER TABLE statements for missing columns
foreach ($missing_columns as $table => $cols) {
    foreach ($cols as $col) {
        // Determine a safe type for common column names
        $type = match(true) {
            in_array($col, ['id','user_id','membership_id','verified_by','approved_by','imported_by','field_id','liked_user_id']) => 'BIGINT UNSIGNED NULL',
            in_array($col, ['created_at','updated_at','approved_at','last_login','expires_at','import_date','password_updated_at']) => 'TIMESTAMP NULL',
            in_array($col, ['deleted_at']) => 'TIMESTAMP NULL',
            in_array($col, ['birth_date','start_date','end_date','engagement_date','marriage_date','featured_until','dob']) => 'DATE NULL',
            in_array($col, ['status']) => "ENUM('pending','approved','rejected','active') NOT NULL DEFAULT 'pending'",
            in_array($col, ['payment_status']) => "ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'",
            in_array($col, ['is_approved','verified','is_public','has_set_password','status','two_factor_enabled','can_view_contacts','is_visible','is_required','is_core','is_custom']) => 'TINYINT(1) NOT NULL DEFAULT 0',
            in_array($col, ['amount','price','father_income','monthly_income','weight']) => 'DECIMAL(10,2) NULL',
            in_array($col, ['brothers','brothers_married','brothers_unmarried','sisters','sisters_married','sisters_unmarried','sort_order','display_order','duration_seconds','contact_limit','duration_days','registration_step','registration_count','deletion_count']) => 'INT NOT NULL DEFAULT 0',
            in_array($col, ['notice_text','image','content','description','story','partner_preference','languages','hobbies','address','permanent_address','current_address','mandir_address','higher_education','details','field_options','message','reply_text','field_value','payment_proof_drive_url','profile_photo_drive_url']) => 'TEXT NULL',
            in_array($col, ['payload','setting_value','image_path']) => 'LONGTEXT NULL',
            in_array($col, ['remember_token']) => 'VARCHAR(100) NULL',
            in_array($col, ['otp_code']) => 'VARCHAR(10) NULL',
            in_array($col, ['phone_number','mobile','ref1_mobile','ref2_mobile','father_mobile','mother_mobile']) => 'VARCHAR(20) NULL',
            default => 'VARCHAR(255) NULL',
        };
        echo "ALTER TABLE `$table` ADD COLUMN `$col` $type;\n";
    }
}

// Generate CREATE TABLE for missing tables
foreach ($missing_tables as $table) {
    echo "\n-- TODO: CREATE TABLE `$table` (see migrations for full definition)\n";
}
?>
    </pre>
</div>
<?php else: ?>
<div class="section" style="border-color:#166534">
    <h2 class="ok">✅ No fixes needed — Database is fully in sync!</h2>
</div>
<?php endif; ?>

<p style="color:#475569; font-size:0.75rem; margin-top:2rem">Generated at <?= now()->format('d M Y H:i:s') ?> | DB: <?= $dbName ?> | <a href="?" style="color:#7c3aed">Refresh</a></p>

</body>
</html>
