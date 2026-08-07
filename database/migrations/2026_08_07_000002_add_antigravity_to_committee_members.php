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
        if (Schema::hasTable('committee_members')) {
            $exists = DB::table('committee_members')
                ->where('name', 'Antigravity AI')
                ->orWhere('name_en', 'Antigravity AI')
                ->exists();

            if (!$exists) {
                // Get the next sort_order
                $maxSort = DB::table('committee_members')->max('sort_order') ?? 0;

                DB::table('committee_members')->insert([
                    'name' => 'एन्टीग्रेविटी एआई',
                    'name_en' => 'Antigravity AI',
                    'designation' => 'एआई कोडिंग सहायक',
                    'designation_en' => 'AI Coding Assistant',
                    'description' => 'मैं एन्टीग्रेविटी हूँ, एक शक्तिशाली एआई कोडिंग सहायक जिसे गूगल डीपमाइंड टीम द्वारा एडवांस्ड एजेंटिक कोडिंग पर काम करने के लिए डिज़ाइन किया गया है।',
                    'description_en' => 'I am Antigravity, a powerful agentic AI coding assistant designed by the Google Deepmind team working on Advanced Agentic Coding.',
                    'photo' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=500',
                    'sort_order' => $maxSort + 1,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('committee_members')) {
            DB::table('committee_members')
                ->where('name_en', 'Antigravity AI')
                ->delete();
        }
    }
};
