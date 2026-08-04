<?php
/**
 * PAYMENT APPROVAL DIRECT-ACTION DEBUG PAGE
 * Visit: http://127.0.0.1:8000/fix_payment.php
 * 
 * ACTIONS:
 *   ?action=info   → shows DB status (default)
 *   ?action=approve&id=X → directly approves payment ID X
 */
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$action = $_GET['action'] ?? 'info';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Payment Debug Tool</title>
<style>
body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
h2 { color: #a78bfa; }
h3 { color: #60a5fa; margin-top: 1.5rem; }
pre { background: #1e293b; padding: 1rem; border-radius: 0.5rem; overflow: auto; }
.ok { color: #4ade80; }
.err { color: #f87171; }
.warn { color: #fbbf24; }
table { border-collapse: collapse; width: 100%; }
th { background: #1e293b; padding: 0.5rem; text-align: left; color: #94a3b8; font-size: 0.8rem; }
td { padding: 0.5rem; border-bottom: 1px solid #1e293b; font-size: 0.85rem; }
tr:hover { background: #1e293b; }
.btn { display: inline-block; padding: 0.4rem 0.8rem; background: #7c3aed; color: white; border-radius: 0.25rem; text-decoration: none; margin: 0.2rem; }
.btn-green { background: #16a34a; }
.btn-red { background: #dc2626; }
</style>
</head>
<body>
<h2>💳 Payment Approval Debug Tool</h2>
<p><a class="btn" href="?action=info">Refresh Info</a></p>

<?php
// ── DIRECT APPROVE ACTION ──────────────────────────────────────────────────
if ($action === 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    echo "<h3>Approving Payment ID: $id</h3><pre>";
    try {
        $p = Payment::findOrFail($id);
        echo "Found payment: ID={$p->id}, Status={$p->status}, UserID={$p->user_id}\n";
        
        DB::transaction(function() use ($p) {
            // Update payment status
            $result = DB::table('payments')->where('id', $p->id)->update([
                'status'          => 'verified',
                'verified_by'     => 1,
                'payment_remarks' => 'Manually approved via debug tool',
            ]);
            echo "DB update result: $result rows affected\n";
            
            // Update user payment_status
            if ($p->user_id) {
                $userResult = DB::table('users')->where('id', $p->user_id)->update([
                    'payment_status' => 'approved',
                ]);
                echo "User update result: $userResult rows affected\n";
            }
        });
        
        // Verify it was saved
        $p->refresh();
        echo "After save - Status: {$p->status}, VerifiedBy: {$p->verified_by}\n";
        echo "<span class='ok'>✓ SUCCESS! Payment approved!</span>\n";
    } catch (\Exception $e) {
        echo "<span class='err'>✗ FAILED: " . htmlspecialchars($e->getMessage()) . "</span>\n";
        echo "\nFile: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
        echo "\nTrace:\n" . htmlspecialchars($e->getTraceAsString()) . "\n";
    }
    echo "</pre>";
}

// ── DIRECT REJECT ACTION ──────────────────────────────────────────────────
if ($action === 'reject' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    echo "<h3>Rejecting Payment ID: $id</h3><pre>";
    try {
        DB::table('payments')->where('id', $id)->update([
            'status'          => 'rejected',
            'verified_by'     => 1,
            'payment_remarks' => 'Manually rejected via debug tool',
        ]);
        if ($pid = Payment::find($id)?->user_id) {
            DB::table('users')->where('id', $pid)->update(['payment_status' => 'rejected']);
        }
        echo "<span class='ok'>✓ Payment rejected successfully.</span>\n";
    } catch (\Exception $e) {
        echo "<span class='err'>✗ FAILED: " . htmlspecialchars($e->getMessage()) . "</span>\n";
    }
    echo "</pre>";
}
?>

<?php
// ── INFO SECTION ─────────────────────────────────────────────────────────
echo "<h3>📋 Payments Table Columns</h3><pre>";
try {
    $cols = Schema::getColumnListing('payments');
    echo implode(', ', $cols);
    echo "\n\nhas 'updated_at': " . (in_array('updated_at', $cols) ? '<span class="err">YES (could cause issues if Payment::UPDATED_AT=null)</span>' : '<span class="ok">NO (good - model has UPDATED_AT=null)</span>');
    echo "\nhas 'verified_by': ' . (in_array('verified_by', $cols) ? '<span class=\"ok\">YES</span>' : '<span class=\"err\">NO - MISSING COLUMN!</span>');";
} catch (\Exception $e) {
    echo "<span class='err'>ERROR: " . htmlspecialchars($e->getMessage()) . "</span>";
}
echo "</pre>";

echo "<h3>👤 Users Table - payment_status column</h3><pre>";
try {
    $uCols = Schema::getColumnListing('users');
    echo "has 'payment_status': " . (in_array('payment_status', $uCols) ? '<span class="ok">YES</span>' : '<span class="err">NO - MISSING COLUMN!</span>');
} catch (\Exception $e) {
    echo "<span class='err'>ERROR: " . htmlspecialchars($e->getMessage()) . "</span>";
}
echo "</pre>";

echo "<h3>🔗 Foreign Key Constraints on payments</h3><pre>";
try {
    $fks = DB::select("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'payments' AND TABLE_SCHEMA = DATABASE() 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    if (empty($fks)) {
        echo '<span class="ok">No FK constraints - good.</span>';
    }
    foreach ($fks as $fk) {
        echo "{$fk->CONSTRAINT_NAME}: {$fk->COLUMN_NAME} → {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
    }
} catch (\Exception $e) {
    echo "<span class='err'>ERROR: " . htmlspecialchars($e->getMessage()) . "</span>";
}
echo "</pre>";

echo "<h3>⏳ Pending Payments</h3>";
try {
    $pending = Payment::where('status', 'pending')->get();
    if ($pending->isEmpty()) {
        echo '<p class="warn">No pending payments found.</p>';
    } else {
        echo '<table><tr><th>ID</th><th>User ID</th><th>Name</th><th>Phone</th><th>Has User?</th><th>Screenshot?</th><th>Created</th><th>Actions</th></tr>';
        foreach ($pending as $p) {
            $hasUser = $p->user ? '<span class="ok">YES</span>' : '<span class="err">NO</span>';
            $hasSS = $p->payment_screenshot ? '<span class="ok">YES</span>' : '<span class="warn">NO</span>';
            echo "<tr>
                <td>{$p->id}</td>
                <td>{$p->user_id}</td>
                <td>" . htmlspecialchars($p->full_name ?? $p->user?->full_name ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($p->phone_number ?? '') . "</td>
                <td>{$hasUser}</td>
                <td>{$hasSS}</td>
                <td>" . ($p->created_at?->format('d M Y H:i') ?? 'N/A') . "</td>
                <td>
                    <a class='btn btn-green' href='?action=approve&id={$p->id}' onclick='return confirm(\"Approve payment {$p->id}?\")'>✓ Approve</a>
                    <a class='btn btn-red' href='?action=reject&id={$p->id}' onclick='return confirm(\"Reject payment {$p->id}?\")'>✕ Reject</a>
                </td>
            </tr>";
        }
        echo '</table>';
    }
} catch (\Exception $e) {
    echo "<p class='err'>ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>✅ Verified Payments</h3>";
try {
    $verified = Payment::where('status', 'verified')->get();
    if ($verified->isEmpty()) {
        echo '<p class="warn">No verified payments yet.</p>';
    } else {
        echo '<table><tr><th>ID</th><th>User ID</th><th>Name</th><th>Verified By</th><th>Created</th></tr>';
        foreach ($verified as $p) {
            echo "<tr>
                <td>{$p->id}</td>
                <td>{$p->user_id}</td>
                <td>" . htmlspecialchars($p->full_name ?? $p->user?->full_name ?? 'N/A') . "</td>
                <td>{$p->verified_by}</td>
                <td>" . ($p->created_at?->format('d M Y') ?? 'N/A') . "</td>
            </tr>";
        }
        echo '</table>';
    }
} catch (\Exception $e) {
    echo "<p class='err'>ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// ── SIMULATE THE EXACT CONTROLLER LOGIC ─────────────────────────────────
echo "<h3>🔬 Test Controller Logic (first pending payment, ROLLED BACK)</h3><pre>";
try {
    $pending = Payment::where('status', 'pending')->first();
    if (!$pending) {
        echo '<span class="warn">No pending payment to test.</span>';
    } else {
        echo "Testing with Payment ID: {$pending->id}\n";
        DB::beginTransaction();
        try {
            // Exact same code as controller
            $adminId = 1; // Simulated admin ID
            
            $pending->update([
                'status'          => 'verified',
                'verified_by'     => $adminId,
                'payment_remarks' => 'Test remarks',
            ]);
            echo "<span class='ok'>✓ Step 1 OK: payment->update() succeeded</span>\n";
            
            if ($pending->user) {
                $pending->user->update(['payment_status' => 'approved']);
                echo "<span class='ok'>✓ Step 2 OK: user payment_status updated</span>\n";
            } else {
                echo "<span class='warn'>⚠ Step 2 SKIP: payment->user is null (user_id={$pending->user_id})</span>\n";
            }
            
            DB::rollBack();
            echo "<span class='ok'>✓ All steps passed! ROLLED BACK safely.</span>\n";
            echo "\nCONCLUSION: The controller logic itself works fine.\n";
            echo "The problem is likely with SESSION/CSRF handling in the form submission.";
        } catch (\Exception $inner) {
            DB::rollBack();
            throw $inner;
        }
    }
} catch (\Exception $e) {
    echo "<span class='err'>✗ FAILED: " . htmlspecialchars($e->getMessage()) . "</span>\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
echo "</pre>";
?>

<h3>🔧 Diagnosis Notes</h3>
<ul>
<li>If <b>Test Controller Logic</b> passes → the issue is CSRF/session/route middleware</li>
<li>If it fails → there's a DB column or constraint problem</li>
<li>You can use the Approve/Reject buttons above to directly approve payments (bypasses CSRF)</li>
<li>After approving here, check <a href="http://127.0.0.1:8000/admin/payments" style="color:#a78bfa">admin/payments</a> and <a href="http://127.0.0.1:8000/admin/members?status=paid" style="color:#a78bfa">paid members</a></li>
</ul>

</body>
</html>
