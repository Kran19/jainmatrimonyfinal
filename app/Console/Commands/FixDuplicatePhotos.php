<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FixDuplicatePhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:fix-duplicate-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disassociate shared legacy profile photo paths among candidates with the same name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Scanning for candidates sharing identical profile photo paths...");

        $duplicates = DB::table('users')
            ->select('profile_photo', DB::raw('COUNT(*) as count'))
            ->whereNotNull('profile_photo')
            ->where('profile_photo', '!=', '')
            ->groupBy('profile_photo')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info("✅ No duplicate profile photos found. All candidate photos are unique.");
            return 0;
        }

        $this->warn("Found " . $duplicates->count() . " shared photo paths across multiple candidates.");

        $clearedCount = 0;

        foreach ($duplicates as $dup) {
            $photoPath = $dup->profile_photo;
            $users = User::where('profile_photo', $photoPath)->orderBy('id', 'asc')->get();

            $primaryUser = $users->first();
            $this->line("• Photo: <comment>{$photoPath}</comment>");
            $this->line("  - Kept for Primary Member: <info>{$primaryUser->full_name}</info> (ID: {$primaryUser->id}, MID: {$primaryUser->profile_id})");

            foreach ($users->slice(1) as $secondaryUser) {
                $secondaryUser->profile_photo = null;
                $secondaryUser->save();
                $clearedCount++;

                $this->line("  - Disassociated from Duplicate Member: <fg=red>{$secondaryUser->full_name}</fg=red> (ID: {$secondaryUser->id}, MID: {$secondaryUser->profile_id})");
            }
        }

        $this->info("\n✅ SUCCESS: Disassociated {$clearedCount} duplicate photo assignments. Those profiles will now show their unique avatar badge until their unique photo is uploaded.");

        return 0;
    }
}
