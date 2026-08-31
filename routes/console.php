<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('storage:link-safe', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (!file_exists($target)) {
        @mkdir($target, 0755, true);
    }

    if (file_exists($link) || is_link($link)) {
        if (is_link($link)) {
            @unlink($link);
        } elseif (is_dir($link)) {
            $this->info("Storage link directory already exists at [$link].");
            return 0;
        }
    }

    if (function_exists('symlink') && @symlink($target, $link)) {
        $this->info("The [$link] link has been connected to [$target].");
    } else {
        if (!file_exists($link)) {
            @mkdir($link, 0755, true);
        }
        $this->info("symlink() is disabled in php.ini by host. Fallback directory created at [$link]. Use deploy.sh (Linux ln -sfn) to create native symlinks.");
    }
    return 0;
})->purpose('Create storage symlink safely without requiring exec() on shared hosting');

Schedule::call(function () {
    $updateCols = ['status' => 'deactivated'];
    if (Schema::hasColumn('users', 'is_approved')) $updateCols['is_approved'] = 0;
    if (Schema::hasColumn('users', 'verified')) $updateCols['verified'] = 0;
    if (Schema::hasColumn('users', 'is_public')) $updateCols['is_public'] = 0;

    $expiredUsersCount = DB::table('users')
        ->where('status', 'approved')
        ->whereNotNull('expiry_date')
        ->where('expiry_date', '<', now()->toDateString())
        ->update($updateCols);
    
    if ($expiredUsersCount > 0) {
        Log::info("Auto-deactivated {$expiredUsersCount} expired member profiles.");
    }
})->daily();

Artisan::command('members:deactivate-expired', function () {
    $updateCols = ['status' => 'deactivated'];
    if (Schema::hasColumn('users', 'is_approved')) $updateCols['is_approved'] = 0;
    if (Schema::hasColumn('users', 'verified')) $updateCols['verified'] = 0;
    if (Schema::hasColumn('users', 'is_public')) $updateCols['is_public'] = 0;

    $expiredUsersCount = DB::table('users')
        ->where('status', 'approved')
        ->whereNotNull('expiry_date')
        ->where('expiry_date', '<', now()->toDateString())
        ->update($updateCols);
    
    $this->info("Successfully deactivated {$expiredUsersCount} expired member profiles.");
})->purpose('Deactivate member profiles whose validity has expired');

Artisan::command('members:fix-duplicate-photos', function () {
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
})->purpose('Disassociate duplicate legacy profile photo paths among candidates with the same name');
