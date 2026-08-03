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

            // 8. Relax status column to VARCHAR(50) to support 'deactivated', 'blocked', 'account_approved', etc.
            if (Schema::hasColumn('users', 'status')) {
                $colType = DB::selectOne("
                    SELECT DATA_TYPE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'users' 
                    AND COLUMN_NAME = 'status'
                ");
                if ($colType && strtolower($colType->DATA_TYPE) === 'enum') {
                    DB::statement("ALTER TABLE `users` MODIFY COLUMN `status` VARCHAR(50) NULL DEFAULT 'account_approved'");
                }
            } // end if hasColumn status
            if (!Schema::hasColumn('users', 'registration_count')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('registration_count')->default(1)->after('status');
                });
                DB::table('users')->whereNull('registration_count')->update(['registration_count' => 1]);
            }
            if (!Schema::hasColumn('users', 'deletion_count')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('deletion_count')->default(0)->after('registration_count');
                });
                DB::table('users')->whereNull('deletion_count')->update(['deletion_count' => 0]);
                DB::table('users')->where('status', 'deleted')->where('deletion_count', 0)->update(['deletion_count' => 1]);
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

        // 9. Ensure account_requests table has created_at and updated_at columns
        if (Schema::hasTable('account_requests')) {
            if (!Schema::hasColumn('account_requests', 'created_at')) {
                Schema::table('account_requests', function (Blueprint $table) {
                    $table->timestamp('created_at')->nullable();
                });
            }
            if (!Schema::hasColumn('account_requests', 'updated_at')) {
                Schema::table('account_requests', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
                });
            }
        }

        // 10. Ensure advertisements table has media_type, sort_order, duration_seconds, updated_at
        if (Schema::hasTable('advertisements')) {
            if (!Schema::hasColumn('advertisements', 'media_type')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->string('media_type', 20)->default('image');
                });
            }
            if (!Schema::hasColumn('advertisements', 'sort_order')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->integer('sort_order')->default(0);
                });
            }
            if (!Schema::hasColumn('advertisements', 'duration_seconds')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->integer('duration_seconds')->default(3);
                });
            }
            if (!Schema::hasColumn('advertisements', 'updated_at')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
                });
            }

            // Remove printmines.com redirection link from advertisements
            DB::table('advertisements')
                ->where('link', 'like', '%printmines%')
                ->update(['link' => null]);
        }

        // 11. Ensure news table exists, has updated_at and LONGTEXT image column
        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->text('content')->nullable();
                $table->longText('image')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('news', 'updated_at')) {
                Schema::table('news', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
                });
            }
            if (Schema::hasColumn('news', 'image')) {
                $colType = DB::selectOne("
                    SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'news' 
                    AND COLUMN_NAME = 'image'
                ");
                if ($colType && strtolower($colType->DATA_TYPE) !== 'longtext') {
                    DB::statement("ALTER TABLE `news` MODIFY COLUMN `image` LONGTEXT NULL");
                }
            }
        }

        // 12. Ensure is_approved column exists for admin profile visibility control.
        // is_approved = 0 means hidden from public; is_approved = 1 means publicly visible.
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_approved')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('is_approved')->default(0)->after('is_public');
            });
            // Backfill: all currently 'approved' profiles should be visible immediately
            DB::table('users')->where('status', 'approved')->update(['is_approved' => 1]);
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
