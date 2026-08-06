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
        Schema::table('committee_members', function (Blueprint $table) {
            if (!Schema::hasColumn('committee_members', 'name_en')) {
                $table->string('name_en', 255)->nullable()->after('name');
            }
            if (!Schema::hasColumn('committee_members', 'designation_en')) {
                $table->string('designation_en', 150)->nullable()->after('designation');
            }
            if (!Schema::hasColumn('committee_members', 'description_en')) {
                $table->text('description_en')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('committee_members', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'designation_en', 'description_en']);
        });
    }
};
