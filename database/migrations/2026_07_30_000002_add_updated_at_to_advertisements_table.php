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
        if (Schema::hasTable('advertisements')) {
            if (!Schema::hasColumn('advertisements', 'updated_at')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
                });
            }
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('advertisements')) {
            Schema::table('advertisements', function (Blueprint $table) {
                if (Schema::hasColumn('advertisements', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
            });
        }
    }
};
