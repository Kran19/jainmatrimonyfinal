<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ensures the payments table has all columns required by PaymentController.
 * Safely adds any missing columns without affecting existing data.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Create payments table if it doesn't exist at all
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('membership_id')->nullable()->index();
                $table->decimal('amount', 10, 2)->default(0);
                $table->string('transaction_id', 100)->nullable()->index();
                $table->string('payment_method', 50)->nullable();
                $table->string('payment_screenshot', 500)->nullable();
                $table->text('payment_remarks')->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->index();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->string('full_name', 200)->nullable();
                $table->string('phone_number', 20)->nullable();
                $table->string('email', 200)->nullable();
                $table->string('address', 500)->nullable();
                $table->date('dob')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                // No updated_at — Payment model has UPDATED_AT = null
            });
            return;
        }

        // If the table already exists, patch missing columns
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index()->after('id');
            }
            if (!Schema::hasColumn('payments', 'membership_id')) {
                $table->unsignedBigInteger('membership_id')->nullable()->index()->after('user_id');
            }
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id', 100)->nullable()->index();
            }
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable();
            }
            if (!Schema::hasColumn('payments', 'payment_screenshot')) {
                $table->string('payment_screenshot', 500)->nullable();
            }
            if (!Schema::hasColumn('payments', 'payment_remarks')) {
                $table->text('payment_remarks')->nullable();
            }
            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status', 20)->default('pending')->index();
            }
            if (!Schema::hasColumn('payments', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable();
            }
            if (!Schema::hasColumn('payments', 'full_name')) {
                $table->string('full_name', 200)->nullable();
            }
            if (!Schema::hasColumn('payments', 'phone_number')) {
                $table->string('phone_number', 20)->nullable();
            }
            if (!Schema::hasColumn('payments', 'email')) {
                $table->string('email', 200)->nullable();
            }
            if (!Schema::hasColumn('payments', 'address')) {
                $table->string('address', 500)->nullable();
            }
            if (!Schema::hasColumn('payments', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (!Schema::hasColumn('payments', 'created_at')) {
                $table->timestamp('created_at')->nullable()->useCurrent();
            }
        });

        // Ensure status column accepts 'verified' — fix enum if needed
        try {
            $colInfo = DB::selectOne("
                SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'payments' 
                AND COLUMN_NAME = 'status'
            ");
            if ($colInfo) {
                $colType = strtolower($colInfo->COLUMN_TYPE);
                if (strpos($colType, 'enum') !== false && strpos($colType, 'verified') === false) {
                    DB::statement("ALTER TABLE `payments` MODIFY COLUMN `status` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'");
                }
            }
        } catch (\Exception $e) {
            // Non-critical: ignore if status column type check fails
        }

        // Ensure payment_status column exists on users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'payment_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('payment_status', 20)->default('pending')->after('id');
            });
        }
    }

    public function down(): void
    {
        // This is a patch migration — don't drop anything on rollback
    }
};
