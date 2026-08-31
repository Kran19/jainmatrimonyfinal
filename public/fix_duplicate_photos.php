<?php
/**
 * Standalone Script: Disassociate Duplicate Legacy Profile Photos
 * Access at: https://digambarjainparichay.com/fix_duplicate_photos.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Duplicate Profile Photos - Maintenance Tool</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #e2e8f0; padding: 24px; line-height: 1.6; }
        .card { max-width: 800px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h2 { color: #38bdf8; margin-top: 0; }
        .log-box { background: #090d16; padding: 16px; border-radius: 8px; font-family: Consolas, monospace; font-size: 13px; max-height: 450px; overflow-y: auto; color: #a5f3fc; }
        .success { color: #4ade80; font-weight: bold; }
        .warn { color: #facc15; }
        .error { color: #f87171; }
        .btn { display: inline-block; background: #2563eb; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 16px; }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
<div class="card">
    <h2>🧹 Disassociate Duplicate Legacy Candidate Photos</h2>
    <p>Scanning all candidates sharing duplicate <code>profile_photo</code> legacy file paths...</p>

    <div class="log-box">
    <?php
    $duplicates = DB::table('users')
        ->select('profile_photo', DB::raw('COUNT(*) as count'))
        ->whereNotNull('profile_photo')
        ->where('profile_photo', '!=', '')
        ->groupBy('profile_photo')
        ->having('count', '>', 1)
        ->get();

    if ($duplicates->isEmpty()) {
        echo "<span class='success'>✅ No duplicate profile photos found! Every candidate photo is already unique.</span>\n";
    } else {
        echo "<span class='warn'>Found " . $duplicates->count() . " shared photo paths across multiple candidates:</span>\n\n";

        $clearedCount = 0;

        foreach ($duplicates as $dup) {
            $photoPath = $dup->profile_photo;
            $users = User::where('profile_photo', $photoPath)->orderBy('id', 'asc')->get();

            $primaryUser = $users->first();
            echo "• Photo Path: <strong style='color:#fff'>" . htmlspecialchars($photoPath) . "</strong>\n";
            echo "  [KEPT] Primary Member: " . htmlspecialchars($primaryUser->full_name) . " (ID: {$primaryUser->id}, MID: {$primaryUser->profile_id})\n";

            foreach ($users->slice(1) as $secondaryUser) {
                $secondaryUser->profile_photo = null;
                $secondaryUser->save();
                $clearedCount++;

                echo "  <span class='warn'>[CLEARED]</span> Duplicate Member: " . htmlspecialchars($secondaryUser->full_name) . " (ID: {$secondaryUser->id}, MID: {$secondaryUser->profile_id})\n";
            }
            echo "\n";
        }

        echo "<hr style='border-color:#334155'>\n";
        echo "<span class='success'>✅ SUCCESS: Disassociated {$clearedCount} duplicate photo assignments!</span>\n";
        echo "Those duplicate profiles will now cleanly display their unique letter badge avatar until a unique photo is uploaded for them.\n";
    }
    ?>
    </div>

    <div style="margin-top: 20px;">
        <a href="/admin/members" class="btn">Return to Admin Members Panel</a>
    </div>
</div>
</body>
</html>
