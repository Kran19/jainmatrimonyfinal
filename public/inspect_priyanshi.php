<?php
/**
 * Interactive Tool: Inspect & Assign Photos for Duplicate Candidates
 * Access at: https://digambarjainparichay.com/inspect_priyanshi.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

header('Content-Type: text/html; charset=utf-8');

// Handle Manual Assignment Action
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_user_id']) && isset($_POST['photo_path'])) {
    $userId = (int)$_POST['assign_user_id'];
    $photoPath = trim($_POST['photo_path']);

    $targetUser = User::find($userId);
    if ($targetUser) {
        $targetUser->profile_photo = $photoPath;
        $targetUser->save();
        $message = "✅ Successfully assigned photo '{$photoPath}' to {$targetUser->full_name} ({$targetUser->profile_id})!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Assignment Tool - Priyanshi Jain</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #e2e8f0; padding: 20px; line-height: 1.6; }
        .container { max-width: 960px; margin: 0 auto; }
        .header-card { background: #1e293b; border-radius: 12px; padding: 24px; margin-bottom: 24px; border: 1px solid #334155; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; }
        .card { background: #1e293b; border-radius: 12px; padding: 20px; border: 1px solid #334155; }
        h2 { color: #38bdf8; margin-top: 0; }
        h3 { color: #facc15; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; margin: 12px 0; }
        th, td { padding: 7px 10px; border-bottom: 1px solid #334155; text-align: left; }
        th { color: #94a3b8; width: 160px; }
        a { color: #60a5fa; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { display: inline-block; background: #0284c7; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; font-size: 13px; }
        .btn:hover { background: #0369a1; }
        .btn-success { background: #059669; }
        .btn-success:hover { background: #047857; }
        .alert { background: #064e3b; color: #6ee7b7; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #059669; }
        .preview-img { width: 140px; height: 160px; object-fit: cover; border-radius: 8px; border: 2px solid #38bdf8; }
    </style>
</head>
<body>
<div class="container">

    <div class="header-card">
        <h2>🔍 Candidate Photo Discovery & Ownership Assignment</h2>
        <p>The imported photo file on your server is: <code>imports/profile_photos/Priyanshi_Jain_profile.jpg</code></p>
        
        <?php if (!empty($message)): ?>
            <div class="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div style="display: flex; gap: 20px; align-items: center; background: #0f172a; padding: 16px; border-radius: 8px; border: 1px solid #334155;">
            <div>
                <img src="/image?file=imports/profile_photos/Priyanshi_Jain_profile.jpg" alt="Priyanshi Jain Photo" class="preview-img" onerror="this.src='https://ui-avatars.com/api/?name=Priyanshi+Jain&size=160';">
            </div>
            <div>
                <h4 style="margin: 0 0 6px 0; color: #e2e8f0;">Photo in File Storage:</h4>
                <p style="font-size: 13px; color: #94a3b8; margin: 0 0 10px 0;">
                    Review the candidate details below (Father Name, City, Age, Education) to confirm which Priyanshi Jain this photo belongs to, then click the button below to assign it.
                </p>
                <a href="/image?file=imports/profile_photos/Priyanshi_Jain_profile.jpg" target="_blank" class="btn" style="background:#475569;">
                    ↗ Open Full Resolution Photo
                </a>
            </div>
        </div>
    </div>

    <div class="grid">
        <?php
        $users = DB::table('users')->where('full_name', 'like', '%priyanshi%')->orderBy('id', 'asc')->get();
        foreach ($users as $u):
        ?>
        <div class="card">
            <h3>Candidate: <?= htmlspecialchars($u->full_name) ?></h3>
            <table>
                <tr><th>Profile ID (MID):</th><td><strong style="color:#38bdf8"><?= htmlspecialchars($u->profile_id ?? 'N/A') ?></strong></td></tr>
                <tr><th>Database ID:</th><td><?= $u->id ?></td></tr>
                <tr><th>Mobile Number:</th><td><strong style="color:#4ade80"><?= htmlspecialchars($u->mobile ?? 'N/A') ?></strong></td></tr>
                <tr><th>Email Address:</th><td><?= htmlspecialchars($u->email ?? 'N/A') ?></td></tr>
                <tr><th>Father's Name:</th><td><?= htmlspecialchars($u->father_name ?? 'N/A') ?></td></tr>
                <tr><th>Mother's Name:</th><td><?= htmlspecialchars($u->mother_name ?? 'N/A') ?></td></tr>
                <tr><th>Birth Date:</th><td><?= htmlspecialchars($u->birth_date ?? 'N/A') ?></td></tr>
                <tr><th>Native Place / City:</th><td><?= htmlspecialchars($u->native_place ?? $u->birth_place ?? 'N/A') ?></td></tr>
                <tr><th>Education:</th><td><?= htmlspecialchars($u->higher_education ?? 'N/A') ?></td></tr>
                <tr><th>Current Photo in DB:</th><td><code><?= htmlspecialchars($u->profile_photo ?? 'NULL (Avatar Badge)') ?></code></td></tr>
                <?php if (!empty($u->profile_photo_drive_url)): ?>
                <tr><th>Original Drive URL:</th><td><a href="<?= htmlspecialchars($u->profile_photo_drive_url) ?>" target="_blank">🔗 Open Google Drive Photo</a></td></tr>
                <?php endif; ?>
            </table>

            <form method="POST" style="margin-top: 16px;">
                <input type="hidden" name="assign_user_id" value="<?= $u->id ?>">
                <input type="hidden" name="photo_path" value="imports/profile_photos/Priyanshi_Jain_profile.jpg">
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    ✅ This is <?= htmlspecialchars($u->full_name) ?>'s Photo (Set Photo)
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top: 24px; text-align: center;">
        <a href="/admin/members" class="btn" style="background: #334155;">&larr; Return to Admin Members Panel</a>
    </div>

</div>
</body>
</html>
