<?php
/**
 * DB Schema diagnostic - REMOVE AFTER USE
 * http://127.0.0.1:8000/dbcheck.php
 */
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
    die('Access denied.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "<pre style='font-family:monospace; font-size:12px; background:#1e1e1e; color:#d4d4d4; padding:20px;'>";

echo "=== USERS TABLE COLUMNS ===\n";
try {
    $cols = Schema::getColumnListing('users');
    echo implode(", ", $cols) . "\n\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

echo "=== KEY COLUMNS CHECK ===\n";
$checkCols = ['registration_step', 'password', 'password_hash', 'monthly_income', 'weight', 'father_income', 'otp_verifications_table'];
foreach (['registration_step', 'password', 'password_hash', 'monthly_income', 'weight', 'father_income'] as $col) {
    $exists = Schema::hasColumn('users', $col);
    echo "$col: " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "\n";
}

echo "\n=== KEY TABLES CHECK ===\n";
foreach (['users', 'otp_verifications', 'password_resets', 'admins', 'memberships', 'payments', 'site_settings', 'registration_fields'] as $tbl) {
    $exists = Schema::hasTable($tbl);
    echo "$tbl: " . ($exists ? "✅ EXISTS" : "❌ MISSING") . "\n";
}

echo "\n=== COLUMN TYPES (weight, father_income, monthly_income) ===\n";
foreach (['weight', 'father_income', 'monthly_income'] as $col) {
    if (Schema::hasColumn('users', $col)) {
        $info = DB::selectOne("SELECT DATA_TYPE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = '$col'");
        echo "$col: " . ($info ? $info->DATA_TYPE . " ({$info->COLUMN_TYPE})" : "N/A") . "\n";
    }
}

echo "\n=== MARITAL_STATUS ENUM ===\n";
$info = DB::selectOne("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'marital_status'");
echo $info ? $info->COLUMN_TYPE : "N/A";

echo "\n\n=== RECENT USER ===\n";
$user = DB::table('users')->latest()->first();
if ($user) {
    echo "ID: {$user->id}, Name: {$user->full_name}, Status: {$user->status}\n";
    echo "Has password_hash: " . (!empty($user->password_hash) ? '✅ YES' : '❌ NO') . "\n";
    echo "Registration step: " . (isset($user->registration_step) ? $user->registration_step : '❌ COLUMN MISSING') . "\n";
} else {
    echo "No users found.\n";
}

echo "\n⚠️  DELETE THIS FILE after use: public/dbcheck.php";
echo "</pre>";
