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
        if (Schema::hasTable('marquee_ads')) {
            Schema::table('marquee_ads', function (Blueprint $table) {
                if (!Schema::hasColumn('marquee_ads', 'notice_text')) {
                    $table->text('notice_text')->nullable()->after('id');
                }
                if (!Schema::hasColumn('marquee_ads', 'advertisement_text')) {
                    $table->text('advertisement_text')->nullable();
                }
                if (!Schema::hasColumn('marquee_ads', 'status')) {
                    $table->boolean('status')->default(true);
                }
                if (!Schema::hasColumn('marquee_ads', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });

            // Copy advertisement_text to notice_text if notice_text is empty
            try {
                if (Schema::hasColumn('marquee_ads', 'notice_text') && Schema::hasColumn('marquee_ads', 'advertisement_text')) {
                    DB::table('marquee_ads')
                        ->whereNull('notice_text')
                        ->whereNotNull('advertisement_text')
                        ->update(['notice_text' => DB::raw('advertisement_text')]);
                }
            } catch (\Exception $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destruct drops needed to preserve user data
    }
};
