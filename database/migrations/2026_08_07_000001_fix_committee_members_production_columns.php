<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('committee_members')) {
            // 1. Rename 'image_path' to 'photo' if 'image_path' exists and 'photo' does not
            if (Schema::hasColumn('committee_members', 'image_path') && !Schema::hasColumn('committee_members', 'photo')) {
                Schema::table('committee_members', function (Blueprint $table) {
                    $table->renameColumn('image_path', 'photo');
                });
            }

            // 2. Ensure other columns exist
            Schema::table('committee_members', function (Blueprint $table) {
                if (!Schema::hasColumn('committee_members', 'photo')) {
                    $table->string('photo', 255)->nullable()->after('description');
                }
                if (!Schema::hasColumn('committee_members', 'status')) {
                    $table->boolean('status')->default(true)->after('sort_order');
                }
                if (!Schema::hasColumn('committee_members', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('committee_members')) {
            Schema::table('committee_members', function (Blueprint $table) {
                if (Schema::hasColumn('committee_members', 'photo') && !Schema::hasColumn('committee_members', 'image_path')) {
                    $table->renameColumn('photo', 'image_path');
                }
                if (Schema::hasColumn('committee_members', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('committee_members', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
};
