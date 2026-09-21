<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

            if (DB::getDriverName() === 'mysql') {
                $col = DB::selectOne("
                    SELECT COLUMN_TYPE, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'account_requests'
                    AND COLUMN_NAME = 'status'
                ");
                if ($col && strtolower($col->DATA_TYPE) === 'enum') {
                    if (stripos($col->COLUMN_TYPE, 'rejected') === false) {
                        DB::statement("ALTER TABLE `account_requests` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive rollback to avoid losing data
    }
};
