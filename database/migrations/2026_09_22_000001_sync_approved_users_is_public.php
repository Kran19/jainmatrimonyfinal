<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safely sync is_public = 1 for all approved candidate profiles.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_public')) {
            DB::table('users')
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->where('is_public', 0)
                      ->orWhereNull('is_public');
                })
                ->update(['is_public' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive down migration to preserve live candidate visibility
    }
};
