<?php
/**
 * URGENT DB Fix Runner - REMOVE THIS FILE AFTER USE
 * Access at: http://127.0.0.1:8000/run-patch.php
 */

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
    die('Access denied.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

echo "<pre style='font-family:monospace; background:#1e1e1e; color:#d4d4d4; padding:20px; font-size:13px; line-height:1.6;'>";
echo "=== URGENT DB FIX RUNNER ===\n\n";

$fixes = [];

// FIX 1 (CRITICAL): Make 'password' column nullable so user INSERT doesn't fail
// The DB has password NOT NULL but we don't write to it (we use password_hash)
echo "Fix 1: Making 'password' column nullable...\n";
try {
    if (Schema::hasColumn('users', 'password')) {
        $colInfo = DB::selectOne("
            SELECT IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = 'password'
        ");
        if ($colInfo && $colInfo->IS_NULLABLE === 'NO') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `password` VARCHAR(255) NULL DEFAULT NULL");
            echo "  ✅ password column made nullable\n";
            $fixes[] = "password column fixed";
        } else {
            echo "  ✅ password column is already nullable (OK)\n";
        }
    } else {
        echo "  ℹ️  password column does not exist (OK - not needed)\n";
    }
} catch (Exception $e) {
    echo "  ❌ ERROR: " . htmlspecialchars($e->getMessage()) . "\n";
}

// FIX 2: Add registration_step column if missing
echo "\nFix 2: Adding 'registration_step' column...\n";
try {
    if (!Schema::hasColumn('users', 'registration_step')) {
        DB::statement("ALTER TABLE `users` ADD COLUMN `registration_step` TINYINT NOT NULL DEFAULT 1 AFTER `is_public`");
        echo "  ✅ registration_step column added\n";
        $fixes[] = "registration_step added";
    } else {
        echo "  ✅ registration_step already exists (OK)\n";
    }
} catch (Exception $e) {
    echo "  ❌ ERROR: " . htmlspecialchars($e->getMessage()) . "\n";
}

// FIX 3: Ensure password_resets table exists
echo "\nFix 3: Ensuring 'password_resets' table exists...\n";
try {
    if (!Schema::hasTable('password_resets')) {
        DB::statement("CREATE TABLE `password_resets` (
            `email` varchar(255) NOT NULL,
            `token` varchar(255) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            KEY `password_resets_email_index` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "  ✅ password_resets table created\n";
        $fixes[] = "password_resets created";
    } else {
        echo "  ✅ password_resets already exists (OK)\n";
    }
} catch (Exception $e) {
    echo "  ❌ ERROR: " . htmlspecialchars($e->getMessage()) . "\n";
}

// FIX 4: Ensure otp_verifications table exists  
echo "\nFix 4: Ensuring 'otp_verifications' table exists...\n";
try {
    if (!Schema::hasTable('otp_verifications')) {
        DB::statement("CREATE TABLE `otp_verifications` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `otp_code` varchar(10) NOT NULL,
            `expires_at` datetime NOT NULL,
            `verified` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `otp_verifications_email_index` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "  ✅ otp_verifications table created\n";
        $fixes[] = "otp_verifications created";
    } else {
        echo "  ✅ otp_verifications already exists (OK)\n";
    }
} catch (Exception $e) {
    echo "  ❌ ERROR: " . htmlspecialchars($e->getMessage()) . "\n";
}

// FIX 5: Fix marital_status enum to include 'Widower'
echo "\nFix 5: Fixing 'marital_status' enum to include Widower...\n";
try {
    if (Schema::hasColumn('users', 'marital_status')) {
        $colInfo = DB::selectOne("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'marital_status'");
        if ($colInfo && strpos($colInfo->COLUMN_TYPE, 'Widower') === false) {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `marital_status` ENUM('Never Married','Widow','Widower','Divorce') DEFAULT 'Never Married'");
            echo "  ✅ marital_status enum updated to include Widower\n";
            $fixes[] = "marital_status enum fixed";
        } else {
            echo "  ✅ marital_status already has Widower (OK)\n";
        }
    }
} catch (Exception $e) {
    echo "  ❌ ERROR: " . htmlspecialchars($e->getMessage()) . "\n";
}

// FIX 6: Fix weight column to be varchar (wizard validates as string)
echo "\nFix 6: Checking 'weight' column type...\n";
try {
    if (Schema::hasColumn('users', 'weight')) {
        $colType = DB::selectOne("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'weight'");
        if ($colType && $colType->DATA_TYPE === 'decimal') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `weight` VARCHAR(50) NULL DEFAULT NULL");
            echo "  ✅ weight column changed from decimal to varchar\n";
            $fixes[] = "weight column changed to varchar";
        } else {
            echo "  ✅ weight is already varchar (OK) - type: " . ($colType->DATA_TYPE ?? 'unknown') . "\n";
        }
    }
} catch (Exception $e) {
    echo "  ❌ ERROR: " . htmlspecialchars($e->getMessage()) . "\n";
}

// Mark migration as run in migrations table to avoid running it again
echo "\nMarking patch migration as completed...\n";
try {
    $migrationName = '2026_07_30_000001_patch_legacy_users_table';
    $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
    if (!$exists) {
        $batch = DB::table('migrations')->max('batch') ?? 0;
        DB::table('migrations')->insert(['migration' => $migrationName, 'batch' => $batch + 1]);
        echo "  ✅ Migration marked as completed in migrations table\n";
    } else {
        echo "  ✅ Migration already marked (OK)\n";
    }
} catch (Exception $e) {
    echo "  ⚠️  Could not mark migration: " . htmlspecialchars($e->getMessage()) . "\n";
}

echo "\n\n=== FINAL STATUS ===\n";

// Verify current state
$checkCols = ['password', 'password_hash', 'registration_step', 'weight', 'marital_status'];
foreach ($checkCols as $col) {
    if (Schema::hasColumn('users', $col)) {
        $info = DB::selectOne("SELECT IS_NULLABLE, DATA_TYPE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = '$col'");
        $nullable = ($info && $info->IS_NULLABLE === 'YES') ? '(nullable)' : '(NOT NULL)';
        echo "$col: ✅ EXISTS as {$info->DATA_TYPE} $nullable\n";
    } else {
        echo "$col: ℹ️  Does not exist\n";
    }
}

echo "\nTables:\n";
foreach (['otp_verifications', 'password_resets'] as $tbl) {
    echo "$tbl: " . (Schema::hasTable($tbl) ? '✅ EXISTS' : '❌ MISSING') . "\n";
}

if (count($fixes) > 0) {
    echo "\n✅ Applied " . count($fixes) . " fix(es): " . implode(', ', $fixes) . "\n";
} else {
    echo "\n✅ All checks passed - no fixes needed.\n";
}

echo "\n⚠️  IMPORTANT: DELETE THIS FILE after verifying: public/run-patch.php\n";
echo "</pre>";
