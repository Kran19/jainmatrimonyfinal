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

        // 6. Ensure has_set_password and registration_source columns exist if missing
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'has_set_password')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->boolean('has_set_password')->default(true);
                });
            }
            if (!Schema::hasColumn('users', 'registration_source')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('registration_source', 50)->default('website');
                });
            }

            // 7. Ensure birth_time column is relaxed to VARCHAR(50) to support both 12-hour AM/PM and 24-hour formats
            if (Schema::hasColumn('users', 'birth_time')) {
                $colType = DB::selectOne("
                    SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users' 
                    AND COLUMN_NAME = 'birth_time'
                ");
                if ($colType && strtolower($colType->DATA_TYPE) === 'time') {
                    DB::statement("ALTER TABLE `users` MODIFY COLUMN `birth_time` VARCHAR(50) NULL DEFAULT NULL");
                }
            }
        }

        // 8. Ensure cast and subcast entries exist in registration_fields so Admin can manage sub-cast options
        if (Schema::hasTable('registration_fields')) {
            if (!DB::table('registration_fields')->where('field_key', 'cast')->exists()) {
                DB::table('registration_fields')->insert([
                    'field_group' => 'Personal Details',
                    'field_key' => 'cast',
                    'field_label' => 'Cast (जाति)',
                    'field_type' => 'dropdown',
                    'field_options' => 'Digambar Jain,Other',
                    'is_custom' => false,
                    'is_visible' => true,
                    'is_required' => true,
                    'is_core' => false,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if (!DB::table('registration_fields')->where('field_key', 'subcast')->exists()) {
                DB::table('registration_fields')->insert([
                    'field_group' => 'Personal Details',
                    'field_key' => 'subcast',
                    'field_label' => 'Sub-Cast (उपजाति)',
                    'field_type' => 'dropdown',
                    'field_options' => 'Khandelwal,Agrawal,Oswal,Porwal,Golalare,Humad,Bagherwal,Chaturth,Pancham,Other (अन्य)',
                    'is_custom' => false,
                    'is_visible' => true,
                    'is_required' => false,
                    'is_core' => false,
                    'sort_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
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
