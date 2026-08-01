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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'registration_count')) {
                $table->integer('registration_count')->default(1)->after('status');
            }
            if (!Schema::hasColumn('users', 'deletion_count')) {
                $table->integer('deletion_count')->default(0)->after('registration_count');
            }
        });

        // Patch existing records to ensure non-null counts
        DB::table('users')->whereNull('registration_count')->update(['registration_count' => 1]);
        DB::table('users')->whereNull('deletion_count')->update(['deletion_count' => 0]);
        // Set deletion_count = 1 for accounts currently marked as status = 'deleted'
        DB::table('users')->where('status', 'deleted')->where('deletion_count', 0)->update(['deletion_count' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'registration_count')) {
                $table->dropColumn('registration_count');
            }
            if (Schema::hasColumn('users', 'deletion_count')) {
                $table->dropColumn('deletion_count');
            }
        });
    }
};
