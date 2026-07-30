<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Patch migration for the legacy digambarfinal database.
 * Adds missing columns and fixes type mismatches so Laravel app works correctly.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            // Disable strict mode for session to avoid MySQL errors when inspecting or altering legacy tables with '0000-00-00' dates
            DB::statement("SET SESSION sql_mode = ''");

            // Clean up invalid legacy date values in 'users' table that break MySQL ALTER TABLE statements
            if (Schema::hasColumn('users', 'birth_date')) {
                DB::statement("UPDATE `users` SET `birth_date` = NULL WHERE `birth_date` = '0000-00-00' OR `birth_date` = '0000-00-00 00:00:00' OR CAST(`birth_date` AS CHAR) LIKE '0000-00-00%'");
            }

            foreach (['created_at', 'updated_at', 'email_verified_at', 'last_login'] as $dateCol) {
                if (Schema::hasColumn('users', $dateCol)) {
                    DB::statement("UPDATE `users` SET `$dateCol` = NULL WHERE `$dateCol` = '0000-00-00' OR `$dateCol` = '0000-00-00 00:00:00' OR CAST(`$dateCol` AS CHAR) LIKE '0000-00-00%'");
                }
            }

            // Ensure password column is nullable (legacy DB has password NOT NULL, app uses password_hash)
            if (Schema::hasColumn('users', 'password')) {
                $colInfo = DB::selectOne("
                    SELECT IS_NULLABLE 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users' 
                    AND COLUMN_NAME = 'password'
                ");
                if ($colInfo && $colInfo->IS_NULLABLE === 'NO') {
                    DB::statement("ALTER TABLE `users` MODIFY COLUMN `password` VARCHAR(255) NULL DEFAULT NULL");
                }
            }
        }

        // 1. Add registration_step column if missing
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'registration_step')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('registration_step')->default(1)->after('is_public');
            });
        }

        // 2. Ensure password_resets table exists (used by ForgotPasswordController)
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 3. Ensure OTP verifications table exists (used by OTPService.php)
        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('email', 255)->index();
                $table->string('otp_code', 10);
                $table->dateTime('expires_at');
                $table->boolean('verified')->default(false);
                $table->timestamps();
            });
        }

        // 4. Add 'Widower' to marital_status enum if missing (wizard uses it)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'marital_status')) {
            $colInfo = DB::selectOne("
                SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = 'marital_status'
            ");
            if ($colInfo && strpos($colInfo->COLUMN_TYPE, 'Widower') === false) {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `marital_status` ENUM('Never Married','Widow','Widower','Divorce') DEFAULT 'Never Married'");
            }
        }

        // 5. Ensure weight is VARCHAR to support text values like "82 kg"
        // The wizard validates as string, so we relax to varchar
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'weight')) {
            $colType = DB::selectOne("
                SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = 'weight'
            ");
            if ($colType && $colType->DATA_TYPE === 'decimal') {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `weight` VARCHAR(50) NULL");
            }
        }
    }

    public function down(): void
    {
        // Only remove what we added, do not drop any legacy columns
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'registration_step')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('registration_step');
            });
        }
    }
};
