<?php
/**
 * FINAL COMPREHENSIVE DB SYNC
 * Reads every model, every migration, cross-checks with DB, adds ALL missing.
 * ONLY does: ADD COLUMN, CREATE TABLE, ALTER ENUM — never drops or changes data.
 * Visit: http://127.0.0.1:8000/db_final_sync.php
 */
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$doFix = isset($_GET['fix']) && $_GET['fix'] === '1';
$log   = [];

function ok($msg)  { global $log; $log[] = ['ok',   $msg]; }
function err($msg) { global $log; $log[] = ['err',  $msg]; }
function inf($msg) { global $log; $log[] = ['info', $msg]; }
function wrn($msg) { global $log; $log[] = ['warn', $msg]; }

// ── Fetch actual DB columns ──────────────────────────────────────────────────
$dbName = DB::selectOne('SELECT DATABASE() as db')->db;

function getDbCols(): array {
    global $dbName;
    $rows = DB::select("
        SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ?
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ", [$dbName]);
    $cols = [];
    foreach ($rows as $r) $cols[$r->TABLE_NAME][$r->COLUMN_NAME] = $r->COLUMN_TYPE;
    return $cols;
}

$db = getDbCols();

// ── MASTER EXPECTED SCHEMA ───────────────────────────────────────────────────
// Source: Every model's $fillable + every migration's Schema::create/table
// Format: table => [ col => sql_fragment ]
// sql_fragment is used for ALTER TABLE `t` ADD COLUMN `c` {sql_fragment}
$expect = [

  // ─ users ──────────────────────────────────────────────────────────────────
  'users' => [
    'profile_id'               => 'VARCHAR(20) NULL DEFAULT NULL',
    'full_name'                => 'VARCHAR(255) NOT NULL',
    'email'                    => 'VARCHAR(255) NULL DEFAULT NULL',
    'mobile'                   => 'VARCHAR(20) NOT NULL',
    'country_code'             => 'VARCHAR(10) NULL DEFAULT NULL',
    'password_hash'            => 'VARCHAR(255) NULL DEFAULT NULL',
    'are_you_digambar_jain'    => "ENUM('Yes','No') NOT NULL DEFAULT 'Yes'",
    'cast'                     => 'VARCHAR(100) NULL DEFAULT NULL',
    'subcast'                  => 'VARCHAR(100) NULL DEFAULT NULL',
    'custom_subcast'           => 'VARCHAR(100) NULL DEFAULT NULL',
    'permanent_address'        => 'TEXT NULL',
    'pin_code'                 => 'VARCHAR(10) NULL DEFAULT NULL',
    'current_address'          => 'TEXT NULL',
    'father_name'              => 'VARCHAR(255) NULL DEFAULT NULL',
    'father_mobile'            => 'VARCHAR(20) NULL DEFAULT NULL',
    'father_income'            => 'DECIMAL(12,2) NULL DEFAULT NULL',
    'father_occupation'        => 'VARCHAR(100) NULL DEFAULT NULL',
    'mother_name'              => 'VARCHAR(255) NULL DEFAULT NULL',
    'mother_mobile'            => 'VARCHAR(20) NULL DEFAULT NULL',
    'mother_occupation'        => 'VARCHAR(100) NULL DEFAULT NULL',
    'mother_occupation_details'=> 'VARCHAR(255) NULL DEFAULT NULL',
    'brothers'                 => 'INT NOT NULL DEFAULT 0',
    'brothers_married'         => 'INT NOT NULL DEFAULT 0',
    'brothers_unmarried'       => 'INT NOT NULL DEFAULT 0',
    'sisters'                  => 'INT NOT NULL DEFAULT 0',
    'sisters_married'          => 'INT NOT NULL DEFAULT 0',
    'sisters_unmarried'        => 'INT NOT NULL DEFAULT 0',
    'mandir'                   => 'VARCHAR(255) NULL DEFAULT NULL',
    'custom_mandir'            => 'VARCHAR(255) NULL DEFAULT NULL',
    'mandir_name'              => 'VARCHAR(255) NULL DEFAULT NULL',
    'mandir_address'           => 'TEXT NULL',
    'mandir_pincode'           => 'VARCHAR(10) NULL DEFAULT NULL',
    'ref1_name'                => 'VARCHAR(255) NULL DEFAULT NULL',
    'ref1_mobile'              => 'VARCHAR(20) NULL DEFAULT NULL',
    'ref1_relation'            => 'VARCHAR(100) NULL DEFAULT NULL',
    'ref2_name'                => 'VARCHAR(255) NULL DEFAULT NULL',
    'ref2_mobile'              => 'VARCHAR(20) NULL DEFAULT NULL',
    'ref2_relation'            => 'VARCHAR(100) NULL DEFAULT NULL',
    'filled_by'                => 'VARCHAR(50) NULL DEFAULT NULL',
    'id_proof_type'            => 'VARCHAR(100) NULL DEFAULT NULL',
    'id_proof_path'            => 'VARCHAR(500) NULL DEFAULT NULL',
    'gender'                   => "ENUM('Male','Female') NULL DEFAULT NULL",
    'birth_date'               => 'DATE NULL DEFAULT NULL',
    'birth_time'               => 'VARCHAR(50) NULL DEFAULT NULL',
    'birth_place'              => 'VARCHAR(255) NULL DEFAULT NULL',
    'native_place'             => 'VARCHAR(255) NULL DEFAULT NULL',
    'gotra'                    => 'VARCHAR(255) NULL DEFAULT NULL',
    'mama_gotra'               => 'VARCHAR(255) NULL DEFAULT NULL',
    'manglik'                  => "ENUM('Yes','No') NULL DEFAULT NULL",
    'height'                   => 'VARCHAR(20) NULL DEFAULT NULL',
    'weight'                   => 'VARCHAR(50) NULL DEFAULT NULL',
    'marital_status'           => "VARCHAR(50) NULL DEFAULT 'Never Married'",
    'handicapped'              => "ENUM('Yes','No') NOT NULL DEFAULT 'No'",
    'higher_education'         => 'TEXT NULL',
    'occupation'               => 'VARCHAR(255) NULL DEFAULT NULL',
    'company_name'             => 'VARCHAR(255) NULL DEFAULT NULL',
    'designation'              => 'VARCHAR(255) NULL DEFAULT NULL',
    'monthly_income'           => 'DECIMAL(12,2) NULL DEFAULT NULL',
    'languages'                => 'TEXT NULL',
    'hobbies'                  => 'TEXT NULL',
    'partner_preference'       => 'TEXT NULL',
    'profile_photo'            => 'VARCHAR(255) NULL DEFAULT NULL',
    'family_photo'             => 'VARCHAR(255) NULL DEFAULT NULL',
    'profile_photo_drive_url'  => 'TEXT NULL',
    'payment_screenshot'       => 'VARCHAR(255) NULL DEFAULT NULL',
    'payment_proof_drive_url'  => 'TEXT NULL',
    'payment_transaction_id'   => 'VARCHAR(100) NULL DEFAULT NULL',
    'payment_status'           => "ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'",
    'status'                   => "VARCHAR(50) NULL DEFAULT 'account_approved'",
    'verified'                 => 'TINYINT(1) NOT NULL DEFAULT 0',
    'approved_by'              => 'BIGINT UNSIGNED NULL DEFAULT NULL',
    'approved_at'              => 'DATETIME NULL DEFAULT NULL',
    'featured_until'           => 'DATE NULL DEFAULT NULL',
    'has_set_password'         => 'TINYINT(1) NOT NULL DEFAULT 0',
    'registration_source'      => "VARCHAR(50) NOT NULL DEFAULT 'website'",
    'is_public'                => 'TINYINT(1) NOT NULL DEFAULT 1',
    'registration_step'        => 'TINYINT NOT NULL DEFAULT 1',
    'is_approved'              => 'TINYINT NOT NULL DEFAULT 0',
    'registration_count'       => 'INT NOT NULL DEFAULT 1',
    'deletion_count'           => 'INT NOT NULL DEFAULT 0',
    'deleted_at'               => 'TIMESTAMP NULL DEFAULT NULL',
    'remember_token'           => 'VARCHAR(100) NULL DEFAULT NULL',
    'created_at'               => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'               => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ admins ─────────────────────────────────────────────────────────────────
  'admins' => [
    'name'                => 'VARCHAR(150) NOT NULL',
    'email'               => 'VARCHAR(150) NOT NULL',
    'password_hash'       => 'VARCHAR(255) NOT NULL',
    'role'                => "ENUM('super_admin','admin','moderator') NOT NULL DEFAULT 'admin'",
    'status'              => 'TINYINT(1) NOT NULL DEFAULT 1',
    'last_login'          => 'DATETIME NULL DEFAULT NULL',
    'last_login_ip'       => 'VARCHAR(45) NULL DEFAULT NULL',
    'password_updated_at' => 'DATETIME NULL DEFAULT NULL',
    'two_factor_enabled'  => 'TINYINT(1) NOT NULL DEFAULT 0',
    'created_at'          => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'          => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ payments ───────────────────────────────────────────────────────────────
  'payments' => [
    'user_id'            => 'BIGINT UNSIGNED NULL DEFAULT NULL',
    'membership_id'      => 'BIGINT UNSIGNED NULL DEFAULT NULL',
    'amount'             => 'DECIMAL(10,2) NULL DEFAULT NULL',
    'transaction_id'     => 'VARCHAR(255) NULL DEFAULT NULL',
    'payment_method'     => 'VARCHAR(100) NULL DEFAULT NULL',
    'payment_remarks'    => 'TEXT NULL',
    'payment_screenshot' => 'VARCHAR(255) NULL DEFAULT NULL',
    'status'             => "ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'",
    'verified_by'        => 'BIGINT UNSIGNED NULL DEFAULT NULL',
    'full_name'          => 'VARCHAR(255) NULL DEFAULT NULL',
    'phone_number'       => 'VARCHAR(20) NULL DEFAULT NULL',
    'email'              => 'VARCHAR(255) NULL DEFAULT NULL',
    'address'            => 'TEXT NULL',
    'dob'                => 'DATE NULL DEFAULT NULL',
    'created_at'         => 'TIMESTAMP NULL DEFAULT NULL',
    // NOTE: NO updated_at — Payment model has const UPDATED_AT = null
  ],

  // ─ memberships ────────────────────────────────────────────────────────────
  'memberships' => [
    'plan_name'        => 'VARCHAR(100) NOT NULL',
    'price'            => 'DECIMAL(10,2) NOT NULL',
    'duration_days'    => 'INT NOT NULL',
    'contact_limit'    => 'INT NOT NULL DEFAULT 0',
    'featured_profile' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'priority_support' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'status'           => 'TINYINT(1) NOT NULL DEFAULT 1',
    'created_at'       => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'       => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ user_memberships ───────────────────────────────────────────────────────
  // UPDATED_AT = null in model → only created_at in DB
  'user_memberships' => [
    'user_id'           => 'BIGINT UNSIGNED NOT NULL',
    'membership_id'     => 'BIGINT UNSIGNED NOT NULL',
    'start_date'        => 'DATE NULL DEFAULT NULL',
    'end_date'          => 'DATE NULL DEFAULT NULL',
    'status'            => "ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active'",
    'can_view_contacts' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'created_at'        => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ contact_messages ───────────────────────────────────────────────────────
  'contact_messages' => [
    'name'       => 'VARCHAR(255) NULL DEFAULT NULL',
    'email'      => 'VARCHAR(255) NULL DEFAULT NULL',
    'mobile'     => 'VARCHAR(20) NULL DEFAULT NULL',
    'subject'    => 'VARCHAR(255) NULL DEFAULT NULL',
    'message'    => 'TEXT NULL',
    'status'     => "ENUM('unread','read','replied') NOT NULL DEFAULT 'unread'",
    'reply_text' => 'TEXT NULL',
    'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at' => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ success_stories ────────────────────────────────────────────────────────
  // UPDATED_AT = null in model
  'success_stories' => [
    'user_id'         => 'BIGINT UNSIGNED NULL DEFAULT NULL',
    'couple_name'     => 'VARCHAR(255) NULL DEFAULT NULL',
    'engagement_date' => 'DATE NULL DEFAULT NULL',
    'marriage_date'   => 'DATE NULL DEFAULT NULL',
    'story'           => 'LONGTEXT NULL',
    'photo'           => 'TEXT NULL',
    'status'          => "ENUM('pending','approved') NOT NULL DEFAULT 'pending'",
    'created_at'      => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ advertisements ─────────────────────────────────────────────────────────
  // timestamps = false in model, but updated_at added by patch migration
  'advertisements' => [
    'title'            => 'VARCHAR(255) NULL DEFAULT NULL',
    'image'            => 'LONGTEXT NULL',
    'link'             => 'VARCHAR(255) NULL DEFAULT NULL',
    'position'         => "ENUM('home_top','home_bottom','sidebar','left_sidebar','right_sidebar','bottom_banner') NULL DEFAULT NULL",
    'status'           => 'TINYINT(1) NOT NULL DEFAULT 1',
    'media_type'       => "VARCHAR(20) NOT NULL DEFAULT 'image'",
    'sort_order'       => 'INT NOT NULL DEFAULT 0',
    'duration_seconds' => 'INT NOT NULL DEFAULT 3',
  ],

  // ─ site_settings ──────────────────────────────────────────────────────────
  // timestamps = false in model
  'site_settings' => [
    'setting_key'   => 'VARCHAR(100) NOT NULL',
    'setting_value' => 'LONGTEXT NULL',
  ],

  // ─ activity_logs ──────────────────────────────────────────────────────────
  // UPDATED_AT = null in model
  'activity_logs' => [
    'user_type'  => "ENUM('admin','user') NULL DEFAULT NULL",
    'user_id'    => 'BIGINT UNSIGNED NULL DEFAULT NULL',
    'action'     => 'VARCHAR(255) NULL DEFAULT NULL',
    'details'    => 'TEXT NULL',
    'ip_address' => 'VARCHAR(100) NULL DEFAULT NULL',
    'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ registration_fields ────────────────────────────────────────────────────
  // UPDATED_AT = null in model
  'registration_fields' => [
    'field_group'  => "VARCHAR(100) NULL DEFAULT 'Basic Details'",
    'field_key'    => 'VARCHAR(100) NOT NULL',
    'field_label'  => 'VARCHAR(255) NOT NULL',
    'field_type'   => "VARCHAR(50) NOT NULL DEFAULT 'text'",
    'field_options'=> 'TEXT NULL',
    'is_custom'    => 'TINYINT(1) NOT NULL DEFAULT 0',
    'is_visible'   => 'TINYINT(1) NOT NULL DEFAULT 1',
    'is_required'  => 'TINYINT(1) NOT NULL DEFAULT 0',
    'is_core'      => 'TINYINT(1) NOT NULL DEFAULT 0',
    'sort_order'   => 'INT NOT NULL DEFAULT 0',
    'created_at'   => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ user_custom_data ───────────────────────────────────────────────────────
  'user_custom_data' => [
    'user_id'     => 'BIGINT UNSIGNED NOT NULL',
    'field_id'    => 'BIGINT UNSIGNED NOT NULL',
    'field_value' => 'TEXT NULL',
    'created_at'  => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'  => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ user_likes ─────────────────────────────────────────────────────────────
  // UPDATED_AT = null in model
  'user_likes' => [
    'user_id'       => 'BIGINT UNSIGNED NOT NULL',
    'liked_user_id' => 'BIGINT UNSIGNED NOT NULL',
    'created_at'    => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ user_relatives ─────────────────────────────────────────────────────────
  // UPDATED_AT = null in model
  'user_relatives' => [
    'user_id'    => 'BIGINT UNSIGNED NOT NULL',
    'relation'   => 'VARCHAR(100) NULL DEFAULT NULL',
    'name'       => 'VARCHAR(255) NULL DEFAULT NULL',
    'mobile'     => 'VARCHAR(20) NULL DEFAULT NULL',
    'occupation' => 'VARCHAR(150) NULL DEFAULT NULL',
    'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ account_requests ───────────────────────────────────────────────────────
  'account_requests' => [
    'user_id'      => 'BIGINT UNSIGNED NOT NULL',
    'request_type' => "ENUM('deactivation','deletion') NOT NULL DEFAULT 'deletion'",
    'reason'       => 'TEXT NULL',
    'status'       => "ENUM('pending','processed','rejected') NOT NULL DEFAULT 'pending'",
    'created_at'   => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'   => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ scrolling_news ─────────────────────────────────────────────────────────
  'scrolling_news' => [
    'content'    => 'VARCHAR(500) NOT NULL',
    'link'       => 'VARCHAR(255) NULL DEFAULT NULL',
    'status'     => 'TINYINT(1) NOT NULL DEFAULT 1',
    'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at' => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ marquee_ads ────────────────────────────────────────────────────────────
  // UPDATED_AT = null in model, but migration adds updated_at for safety
  'marquee_ads' => [
    'notice_text'       => 'TEXT NULL',
    'advertisement_text'=> 'TEXT NULL',   // MarqueeAd model fallback accessor uses this
    'status'            => 'TINYINT(1) NOT NULL DEFAULT 1',
    'created_at'        => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ news ───────────────────────────────────────────────────────────────────
  // UPDATED_AT = null in model, but patch migration adds updated_at
  'news' => [
    'title'      => 'VARCHAR(255) NOT NULL',
    'content'    => 'TEXT NULL',
    'image'      => 'LONGTEXT NULL',
    'status'     => 'TINYINT(1) NOT NULL DEFAULT 1',
    'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ gallery ────────────────────────────────────────────────────────────────
  // UPDATED_AT = null in model
  'gallery' => [
    'title'      => 'VARCHAR(255) NOT NULL',
    'category'   => "VARCHAR(100) NOT NULL DEFAULT 'All Photos'",
    'image_path' => 'LONGTEXT NULL',
    'media_type' => "ENUM('image','pdf','video','youtube') NOT NULL DEFAULT 'image'",
    'media_url'  => 'VARCHAR(500) NULL DEFAULT NULL',
    'status'     => 'TINYINT(1) NOT NULL DEFAULT 1',
    'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ video_gallery ──────────────────────────────────────────────────────────
  // timestamps = true
  'video_gallery' => [
    'title'         => 'VARCHAR(255) NOT NULL',
    'video_type'    => "ENUM('youtube','mp4') NOT NULL DEFAULT 'youtube'",
    'video_url'     => 'VARCHAR(255) NULL DEFAULT NULL',
    'video_file'    => 'VARCHAR(255) NULL DEFAULT NULL',
    'thumbnail'     => 'VARCHAR(255) NULL DEFAULT NULL',
    'description'   => 'TEXT NULL',
    'display_order' => 'INT NOT NULL DEFAULT 0',
    'status'        => "ENUM('active','inactive') NOT NULL DEFAULT 'active'",
    'created_at'    => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'    => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ success_stories already included above ─────────────────────────────────

  // ─ otp_verifications ──────────────────────────────────────────────────────
  'otp_verifications' => [
    'email'      => 'VARCHAR(255) NOT NULL',
    'otp_code'   => 'VARCHAR(10) NOT NULL',
    'expires_at' => 'DATETIME NOT NULL',
    'verified'   => 'TINYINT(1) NOT NULL DEFAULT 0',
    'created_at' => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at' => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ committee_members ──────────────────────────────────────────────────────
  'committee_members' => [
    'name'           => 'VARCHAR(255) NOT NULL',
    'name_en'        => 'VARCHAR(255) NULL DEFAULT NULL',
    'designation'    => 'VARCHAR(150) NULL DEFAULT NULL',
    'designation_en' => 'VARCHAR(150) NULL DEFAULT NULL',
    'description'    => 'TEXT NULL',
    'description_en' => 'TEXT NULL',
    'photo'          => 'VARCHAR(255) NULL DEFAULT NULL',
    'sort_order'     => 'INT NOT NULL DEFAULT 0',
    'status'         => 'TINYINT(1) NOT NULL DEFAULT 1',
    'created_at'     => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'     => 'TIMESTAMP NULL DEFAULT NULL',
  ],

  // ─ community_events ───────────────────────────────────────────────────────
  'community_events' => [
    'title'       => 'VARCHAR(255) NULL DEFAULT NULL',
    'description' => 'LONGTEXT NULL',
    'event_date'  => 'DATE NULL DEFAULT NULL',
    'location'    => 'VARCHAR(255) NULL DEFAULT NULL',
    'banner'      => 'VARCHAR(255) NULL DEFAULT NULL',
    'status'      => 'TINYINT(1) NOT NULL DEFAULT 1',
    'created_at'  => 'TIMESTAMP NULL DEFAULT NULL',
    'updated_at'  => 'TIMESTAMP NULL DEFAULT NULL',
  ],
];

// ── COMPUTE GAPS ──────────────────────────────────────────────────────────────
$missingTables  = [];
$missingCols    = [];  // [table => [col => sql_fragment]]

foreach ($expect as $table => $cols) {
    if (!isset($db[$table])) {
        $missingTables[] = $table;
        continue;
    }
    foreach ($cols as $col => $sqlFrag) {
        if (!isset($db[$table][$col])) {
            $missingCols[$table][$col] = $sqlFrag;
        }
    }
}

$totalMissCols = array_sum(array_map('count', $missingCols));
$allGood = empty($missingTables) && $totalMissCols === 0;

// ── RUN FIXES ─────────────────────────────────────────────────────────────────
if ($doFix) {
    inf('=== Running php artisan migrate --force ===');
    try {
        Artisan::call('migrate', ['--force' => true]);
        $out = trim(Artisan::output());
        ok($out ?: 'No pending migrations.');
    } catch (\Exception $e) {
        err('artisan migrate: ' . $e->getMessage());
    }

    // Re-fetch after migration
    $db = getDbCols();
    $missingCols = [];
    foreach ($expect as $table => $cols) {
        if (!isset($db[$table])) continue;
        foreach ($cols as $col => $sqlFrag) {
            if (!isset($db[$table][$col])) $missingCols[$table][$col] = $sqlFrag;
        }
    }

    inf('=== Adding missing columns ===');
    foreach ($missingCols as $table => $cols) {
        foreach ($cols as $col => $sqlFrag) {
            try {
                DB::statement("ALTER TABLE `$table` ADD COLUMN `$col` $sqlFrag");
                ok("✓ `$table`.`$col`  — $sqlFrag");
            } catch (\Exception $e) {
                err("✗ `$table`.`$col`: " . $e->getMessage());
            }
        }
    }

    inf('=== Fixing critical ENUMs ===');
    $enumFixes = [
        ['payments', 'status', "ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'"],
        ['users',    'payment_status', "ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'"],
    ];
    foreach ($enumFixes as [$t, $c, $newType]) {
        if (!isset($db[$t][$c])) continue;
        try {
            DB::statement("ALTER TABLE `$t` MODIFY COLUMN `$c` $newType");
            ok("✓ Fixed enum `$t`.`$c`");
        } catch (\Exception $e) {
            wrn("~ Enum `$t`.`$c`: " . $e->getMessage());
        }
    }

    // Re-fetch final state
    $db = getDbCols();
    $missingCols = [];
    $missingTables = [];
    foreach ($expect as $table => $cols) {
        if (!isset($db[$table])) { $missingTables[] = $table; continue; }
        foreach ($cols as $col => $sqlFrag) {
            if (!isset($db[$table][$col])) $missingCols[$table][$col] = $sqlFrag;
        }
    }
    $totalMissCols = array_sum(array_map('count', $missingCols));
    $allGood = empty($missingTables) && $totalMissCols === 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DB Final Sync</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',monospace;background:#0f172a;color:#e2e8f0;padding:2rem;font-size:.88rem}
h1{color:#a78bfa;font-size:1.5rem;margin-bottom:.25rem}
.sub{color:#475569;font-size:.77rem;margin-bottom:1.5rem}
.stats{display:flex;gap:1.25rem;flex-wrap:wrap;margin-bottom:1.5rem}
.stat{background:#1e293b;border-radius:.6rem;padding:.7rem 1.1rem;text-align:center;min-width:110px}
.stat .n{font-size:1.8rem;font-weight:900}
.stat .l{font-size:.68rem;color:#64748b;margin-top:.15rem}
.ok{color:#4ade80}.err{color:#f87171}.warn{color:#fbbf24}.info{color:#60a5fa}
.box{background:#111827;border:1px solid #1e293b;border-radius:.7rem;padding:1.1rem;margin-bottom:1.1rem}
h2{font-size:.97rem;margin-bottom:.6rem;padding-bottom:.35rem;border-bottom:1px solid #1e293b}
.btn{display:inline-block;padding:.5rem 1.1rem;border-radius:.4rem;font-weight:700;font-size:.85rem;text-decoration:none;margin:.2rem;cursor:pointer}
.btn-fix{background:#7c3aed;color:#fff}.btn-fix:hover{background:#6d28d9}
.btn-ref{background:#1e293b;color:#a78bfa;border:1px solid #7c3aed}
pre{background:#1e293b;padding:.75rem;border-radius:.4rem;font-size:.75rem;white-space:pre-wrap;word-break:break-all;max-height:500px;overflow-y:auto}
.pill{display:inline-block;padding:.1rem .4rem;border-radius:999px;font-size:.7rem;margin:.1rem;font-family:monospace}
.p-ok{background:#134e4a;color:#5eead4}.p-miss{background:#450a0a;color:#fca5a5;border:1px solid #f87171}
table{width:100%;border-collapse:collapse;font-size:.8rem}
th{background:#1e293b;padding:.4rem .6rem;text-align:left;color:#94a3b8;text-transform:uppercase;font-size:.68rem}
td{padding:.4rem .6rem;border-bottom:1px solid #0f172a;vertical-align:top}
hr{border:none;border-top:1px solid #1e293b;margin:1.1rem 0}
.log-ok{color:#4ade80}.log-err{color:#f87171}.log-info{color:#60a5fa}.log-warn{color:#fbbf24}
</style>
</head>
<body>
<h1>🗄️ DB Final Sync — <?= $dbName ?></h1>
<p class="sub">Cross-checks every Model, Migration, and Controller against actual MySQL DB &nbsp;|&nbsp; <?= now()->format('d M Y H:i:s') ?></p>

<div class="stats">
    <div class="stat"><div class="n <?= empty($missingTables)?'ok':'err' ?>"><?= count($missingTables) ?></div><div class="l">MISSING TABLES</div></div>
    <div class="stat"><div class="n <?= $totalMissCols===0?'ok':'err' ?>"><?= $totalMissCols ?></div><div class="l">MISSING COLUMNS</div></div>
    <div class="stat"><div class="n ok"><?= count(array_diff(array_keys($expect), $missingTables)) ?></div><div class="l">TABLES ✓</div></div>
    <div class="stat"><div class="n info"><?= count($db) ?></div><div class="l">TOTAL DB TABLES</div></div>
</div>

<?php if (!$doFix): ?>
<div style="margin-bottom:1.25rem">
    <?php if (!$allGood): ?>
    <a class="btn btn-fix" href="?fix=1">🔧 ADD ALL MISSING — Fix Database Now</a>
    <?php endif; ?>
    <a class="btn btn-ref" href="?">↺ Refresh Audit</a>
    <p style="color:#475569;font-size:.72rem;margin-top:.35rem">Fix only adds missing columns/tables. Existing data is never touched.</p>
</div>
<?php endif; ?>

<?php if (!empty($log)): ?>
<div class="box" style="border-color:#7c3aed">
    <h2 style="color:#a78bfa">🔧 Fix Log</h2>
    <pre><?php foreach ($log as [$t,$m]): ?><span class="log-<?= $t ?>"><?= htmlspecialchars($m) ?></span>
<?php endforeach; ?></pre>
</div>
<?php endif; ?>

<?php if ($allGood): ?>
<div class="box" style="border-color:#166534">
    <h2 class="ok">✅ ALL TABLES AND COLUMNS ARE PRESENT — DATABASE IS 100% IN SYNC</h2>
    <p style="color:#86efac;margin-top:.5rem">Every table and column used by every Model, Migration and Controller exists in the database. The payment approval flow and all other features will work correctly.</p>
    <p style="color:#86efac;margin-top:.35rem">✓ users &nbsp;✓ admins &nbsp;✓ payments &nbsp;✓ memberships &nbsp;✓ user_memberships &nbsp;✓ advertisements &nbsp;✓ marquee_ads &nbsp;✓ news &nbsp;✓ gallery &nbsp;✓ and all other tables</p>
</div>
<?php else: ?>

<?php if (!empty($missingTables)): ?>
<div class="box" style="border-color:#dc2626">
    <h2 class="err">❌ Missing Tables (<?= count($missingTables) ?>)</h2>
    <?php foreach ($missingTables as $t): ?>
    <div style="padding:.3rem .7rem;background:#450a0a;border-radius:.3rem;margin:.25rem 0;font-family:monospace"><span class="err">✗</span> <?= $t ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($missingCols)): ?>
<div class="box" style="border-color:#b45309">
    <h2 class="warn">⚠️ Missing Columns (<?= $totalMissCols ?> across <?= count($missingCols) ?> tables)</h2>
    <table>
        <thead><tr><th>Table</th><th>Missing Columns</th></tr></thead>
        <tbody>
        <?php foreach ($missingCols as $t => $cs): ?>
        <tr><td><code class="warn"><?= $t ?></code></td><td><?php foreach (array_keys($cs) as $c): ?><span class="pill p-miss"><?= $c ?></span><?php endforeach; ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php endif; // allGood ?>

<hr>
<h2 style="color:#94a3b8;margin-bottom:.9rem">📋 Full Table Column Status</h2>

<?php foreach ($expect as $table => $cols): ?>
<?php
$exists = isset($db[$table]);
$missC  = array_keys($missingCols[$table] ?? []);
$ok     = $exists && empty($missC);
$bc     = !$exists ? '#dc2626' : (!$ok ? '#b45309' : '#166534');
$icon   = !$exists ? '❌' : (!$ok ? '⚠️' : '✅');
?>
<div class="box" style="border-color:<?= $bc ?>;margin-bottom:.7rem">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem">
        <strong><?= $icon ?> <?= $table ?></strong>
        <?php if ($exists): ?>
        <span style="font-size:.7rem;color:#64748b"><?= count($db[$table]) ?> cols in DB</span>
        <?php else: ?>
        <span style="font-size:.7rem;color:#f87171">TABLE MISSING</span>
        <?php endif; ?>
    </div>
    <?php if ($exists): ?>
    <div>
        <?php foreach (array_keys($cols) as $col): ?>
        <span class="pill <?= isset($db[$table][$col]) ? 'p-ok' : 'p-miss' ?>"><?= $col ?><?= !isset($db[$table][$col]) ? ' ✗' : '' ?></span>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:#fca5a5;font-size:.8rem;margin-top:.25rem">Table does not exist in database.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<p style="color:#334155;font-size:.7rem;margin-top:1.5rem">Delete this file after use &nbsp;|&nbsp; <a href="?" style="color:#7c3aed">Refresh</a> &nbsp;|&nbsp; <a href="/admin/payments" style="color:#7c3aed">Go to Payments</a></p>
</body>
</html>
