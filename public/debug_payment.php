<?php
// Payment debug script - outputs everything as plain text
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== PAYMENTS TABLE COLUMNS ===\n";
try {
    $columns = Schema::getColumnListing('payments');
    echo implode(', ', $columns) . "\n\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

echo "=== USERS TABLE (payment_status column check) ===\n";
try {
    $userCols = Schema::getColumnListing('users');
    echo "has payment_status: " . (in_array('payment_status', $userCols) ? 'YES' : 'NO') . "\n";
    echo "All user cols: " . implode(', ', $userCols) . "\n\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

echo "=== PENDING PAYMENTS ===\n";
try {
    $pending = Payment::where('status', 'pending')->get();
    foreach ($pending as $p) {
        $hasUser = $p->user ? "YES (uid={$p->user->id})" : "NO";
        echo "PaymentID:{$p->id} | UserID:{$p->user_id} | HasUser:{$hasUser} | Name:{$p->full_name} | Screenshot:".($p->payment_screenshot ? 'YES' : 'NO')."\n";
    }
    echo "Total pending: " . $pending->count() . "\n\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

echo "=== VERIFIED PAYMENTS ===\n";
try {
    $verified = Payment::where('status', 'verified')->get();
    foreach ($verified as $p) {
        echo "PaymentID:{$p->id} | UserID:{$p->user_id} | Name:{$p->full_name} | VerifiedBy:{$p->verified_by}\n";
    }
    echo "Total verified: " . $verified->count() . "\n\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

echo "=== FK CONSTRAINTS ON PAYMENTS ===\n";
try {
    $fks = DB::select("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'payments' 
        AND TABLE_SCHEMA = DATABASE() 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    if (empty($fks)) {
        echo "No FK constraints found.\n";
    }
    foreach ($fks as $fk) {
        echo "{$fk->CONSTRAINT_NAME}: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== TEST APPROVE (first pending payment, ROLLED BACK) ===\n";
try {
    $pending = Payment::where('status', 'pending')->get();
    if ($pending->count() === 0) {
        echo "No pending payments to test.\n";
    } else {
        $testPayment = $pending->first();
        echo "Testing payment ID: {$testPayment->id}\n";
        
        DB::beginTransaction();
        
        // Step 1: Update payment status
        $testPayment->status = 'verified';
        $testPayment->verified_by = 1;
        $testPayment->payment_remarks = 'Debug test';
        $testPayment->save();
        echo "Step 1 PASSED: payment status set to verified\n";
        
        // Step 2: Update user
        if ($testPayment->user) {
            $testPayment->user->payment_status = 'approved';
            $testPayment->user->save();
            echo "Step 2 PASSED: user payment_status set to approved\n";
        } else {
            echo "Step 2 SKIP: no user linked\n";
        }
        
        DB::rollBack();
        echo "ROLLED BACK (test only)\n";
        echo "CONCLUSION: Approval logic works!\n";
    }
} catch (\Exception $e) {
    try { DB::rollBack(); } catch (\Exception $re) {}
    echo "FAILED at step: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

// 7. Test actual approve if ?do_approve=1&id=X is passed
if (isset($_GET['do_approve']) && isset($_GET['id'])) {
    echo "\n=== ACTUAL APPROVE PAYMENT ID=" . (int)$_GET['id'] . " ===\n";
    try {
        $p = Payment::findOrFail((int)$_GET['id']);
        DB::transaction(function() use ($p) {
            $p->update(['status' => 'verified', 'verified_by' => 1, 'payment_remarks' => 'Manual debug approve']);
            if ($p->user) {
                $p->user->update(['payment_status' => 'approved']);
            }
        });
        echo "SUCCESS! Payment {$p->id} approved.\n";
    } catch (\Exception $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
}
