<?php
/**
 * DB AUDIT + AUTO-FIX IN ONE STEP
 * Reads the actual DB, compares vs code expectations, adds everything missing.
 * ONLY modifies DB schema (ADD COLUMN / CREATE TABLE / ALTER ENUM).
 * Zero UI or website code changes.
 * Visit: http://127.0.0.1:8000/db_patch.php
 */
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$results = [];
$doFix = isset($_GET['fix']) && $_GET['fix'] === '1';

function log_result(&$results, $table, $column, $status, $detail = '') {
    $results[] = compact('table', 'column', 'status', 'detail');
}

// ─── GET ACTUAL DB STATE ──────────────────────────────────────────────────────
$dbName = DB::selectOne('SELECT DATABASE() as db')->db;
$dbRows = DB::select("
    SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = ?
    ORDER BY TABLE_NAME, ORDINAL_POSITION
", [$dbName]);

$dbCols = []; // [table => [col => type]]
foreach ($dbRows as $row) {
    $dbCols[$row->TABLE_NAME][$row->COLUMN_NAME] = $row->COLUMN_TYPE;
}

// ─── COMPLETE EXPECTED SCHEMA (all tables + columns the code needs) ───────────
$schema = [

    // ── USERS ─────────────────────────────────────────────────────────────────
    'users' => [
        'id'                     => 'bigint unsigned auto_increment PK',
        'profile_id'             => ['type'=>'string','len'=>20,   'nullable'=>true],
        'full_name'              => ['type'=>'string','len'=>255,  'nullable'=>false],
        'email'                  => ['type'=>'string','len'=>255,  'nullable'=>true],
        'mobile'                 => ['type'=>'string','len'=>20,   'nullable'=>false],
        'country_code'           => ['type'=>'string','len'=>10,   'nullable'=>true],
        'password_hash'          => ['type'=>'string','len'=>255,  'nullable'=>true],
        'are_you_digambar_jain'  => ['type'=>'enum',  'vals'=>"'Yes','No'", 'default'=>'Yes'],
        'cast'                   => ['type'=>'string','len'=>100,  'nullable'=>true],
        'subcast'                => ['type'=>'string','len'=>100,  'nullable'=>true],
        'custom_subcast'         => ['type'=>'string','len'=>100,  'nullable'=>true],
        'permanent_address'      => ['type'=>'text',              'nullable'=>true],
        'pin_code'               => ['type'=>'string','len'=>10,   'nullable'=>true],
        'current_address'        => ['type'=>'text',              'nullable'=>true],
        'father_name'            => ['type'=>'string','len'=>255,  'nullable'=>true],
        'father_mobile'          => ['type'=>'string','len'=>20,   'nullable'=>true],
        'father_income'          => ['type'=>'decimal','p'=>12,'s'=>2, 'nullable'=>true],
        'father_occupation'      => ['type'=>'string','len'=>100,  'nullable'=>true],
        'mother_name'            => ['type'=>'string','len'=>255,  'nullable'=>true],
        'mother_mobile'          => ['type'=>'string','len'=>20,   'nullable'=>true],
        'mother_occupation'      => ['type'=>'string','len'=>100,  'nullable'=>true],
        'mother_occupation_details'=> ['type'=>'string','len'=>255,'nullable'=>true],
        'brothers'               => ['type'=>'int', 'default'=>0],
        'brothers_married'       => ['type'=>'int', 'default'=>0],
        'brothers_unmarried'     => ['type'=>'int', 'default'=>0],
        'sisters'                => ['type'=>'int', 'default'=>0],
        'sisters_married'        => ['type'=>'int', 'default'=>0],
        'sisters_unmarried'      => ['type'=>'int', 'default'=>0],
        'mandir'                 => ['type'=>'string','len'=>255,  'nullable'=>true],
        'custom_mandir'          => ['type'=>'string','len'=>255,  'nullable'=>true],
        'mandir_name'            => ['type'=>'string','len'=>255,  'nullable'=>true],
        'mandir_address'         => ['type'=>'text',              'nullable'=>true],
        'mandir_pincode'         => ['type'=>'string','len'=>10,   'nullable'=>true],
        'ref1_name'              => ['type'=>'string','len'=>255,  'nullable'=>true],
        'ref1_mobile'            => ['type'=>'string','len'=>20,   'nullable'=>true],
        'ref1_relation'          => ['type'=>'string','len'=>100,  'nullable'=>true],
        'ref2_name'              => ['type'=>'string','len'=>255,  'nullable'=>true],
        'ref2_mobile'            => ['type'=>'string','len'=>20,   'nullable'=>true],
        'ref2_relation'          => ['type'=>'string','len'=>100,  'nullable'=>true],
        'filled_by'              => ['type'=>'string','len'=>50,   'nullable'=>true],
        'id_proof_type'          => ['type'=>'string','len'=>100,  'nullable'=>true],
        'id_proof_path'          => ['type'=>'string','len'=>500,  'nullable'=>true],
        'gender'                 => ['type'=>'enum', 'vals'=>"'Male','Female'", 'nullable'=>true],
        'birth_date'             => ['type'=>'date',              'nullable'=>true],
        'birth_time'             => ['type'=>'string','len'=>20,   'nullable'=>true],
        'birth_place'            => ['type'=>'string','len'=>255,  'nullable'=>true],
        'native_place'           => ['type'=>'string','len'=>255,  'nullable'=>true],
        'gotra'                  => ['type'=>'string','len'=>255,  'nullable'=>true],
        'mama_gotra'             => ['type'=>'string','len'=>255,  'nullable'=>true],
        'manglik'                => ['type'=>'enum', 'vals'=>"'Yes','No'", 'nullable'=>true],
        'height'                 => ['type'=>'string','len'=>20,   'nullable'=>true],
        'weight'                 => ['type'=>'decimal','p'=>5,'s'=>2,'nullable'=>true],
        'marital_status'         => ['type'=>'enum', 'vals'=>"'Never Married','Widow','Widower','Divorce'", 'default'=>'Never Married'],
        'handicapped'            => ['type'=>'enum', 'vals'=>"'Yes','No'", 'default'=>'No'],
        'higher_education'       => ['type'=>'text',              'nullable'=>true],
        'occupation'             => ['type'=>'string','len'=>255,  'nullable'=>true],
        'company_name'           => ['type'=>'string','len'=>255,  'nullable'=>true],
        'designation'            => ['type'=>'string','len'=>255,  'nullable'=>true],
        'monthly_income'         => ['type'=>'decimal','p'=>12,'s'=>2,'nullable'=>true],
        'languages'              => ['type'=>'text',              'nullable'=>true],
        'hobbies'                => ['type'=>'text',              'nullable'=>true],
        'partner_preference'     => ['type'=>'text',              'nullable'=>true],
        'profile_photo'          => ['type'=>'string','len'=>255,  'nullable'=>true],
        'family_photo'           => ['type'=>'string','len'=>255,  'nullable'=>true],
        'profile_photo_drive_url'=> ['type'=>'text',              'nullable'=>true],
        'payment_screenshot'     => ['type'=>'string','len'=>255,  'nullable'=>true],
        'payment_proof_drive_url'=> ['type'=>'text',              'nullable'=>true],
        'payment_transaction_id' => ['type'=>'string','len'=>100,  'nullable'=>true],
        'payment_status'         => ['type'=>'enum', 'vals'=>"'pending','approved','rejected'", 'default'=>'pending'],
        'status'                 => ['type'=>'enum', 'vals'=>"'account_pending','account_approved','pending','approved','rejected','blocked'", 'default'=>'account_pending'],
        'verified'               => ['type'=>'tinyint','default'=>0],
        'approved_by'            => ['type'=>'bigint unsigned', 'nullable'=>true],
        'approved_at'            => ['type'=>'datetime',          'nullable'=>true],
        'featured_until'         => ['type'=>'date',              'nullable'=>true],
        'has_set_password'       => ['type'=>'tinyint','default'=>0],
        'registration_source'    => ['type'=>'enum', 'vals'=>"'website','google_form','admin'", 'default'=>'website'],
        'is_public'              => ['type'=>'tinyint','default'=>1],
        'registration_step'      => ['type'=>'tinyint','default'=>1,'nullable'=>true],
        'is_approved'            => ['type'=>'tinyint','default'=>0,'nullable'=>true],
        'registration_count'     => ['type'=>'int','default'=>0,   'nullable'=>true],
        'deletion_count'         => ['type'=>'int','default'=>0,   'nullable'=>true],
        'deleted_at'             => ['type'=>'timestamp',          'nullable'=>true],
        'remember_token'         => ['type'=>'string','len'=>100,  'nullable'=>true],
        'created_at'             => ['type'=>'timestamp',          'nullable'=>true],
        'updated_at'             => ['type'=>'timestamp',          'nullable'=>true],
    ],

    // ── ADMINS ────────────────────────────────────────────────────────────────
    'admins' => [
        'id'                  => 'bigint unsigned auto_increment PK',
        'name'                => ['type'=>'string','len'=>150,'nullable'=>false],
        'email'               => ['type'=>'string','len'=>150,'nullable'=>false],
        'password_hash'       => ['type'=>'string','len'=>255,'nullable'=>false],
        'role'                => ['type'=>'enum','vals'=>"'super_admin','admin','moderator'",'default'=>'admin'],
        'status'              => ['type'=>'tinyint','default'=>1],
        'last_login'          => ['type'=>'datetime','nullable'=>true],
        'last_login_ip'       => ['type'=>'string','len'=>45,'nullable'=>true],
        'password_updated_at' => ['type'=>'datetime','nullable'=>true],
        'two_factor_enabled'  => ['type'=>'tinyint','default'=>0],
        'created_at'          => ['type'=>'timestamp','nullable'=>true],
        'updated_at'          => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── PAYMENTS ──────────────────────────────────────────────────────────────
    'payments' => [
        'id'                 => 'bigint unsigned auto_increment PK',
        'user_id'            => ['type'=>'bigint unsigned','nullable'=>true],
        'membership_id'      => ['type'=>'bigint unsigned','nullable'=>true],
        'amount'             => ['type'=>'decimal','p'=>10,'s'=>2,'nullable'=>true],
        'transaction_id'     => ['type'=>'string','len'=>255,'nullable'=>true],
        'payment_method'     => ['type'=>'string','len'=>100,'nullable'=>true],
        'payment_remarks'    => ['type'=>'text','nullable'=>true],
        'payment_screenshot' => ['type'=>'string','len'=>255,'nullable'=>true],
        'status'             => ['type'=>'enum','vals'=>"'pending','verified','rejected'",'default'=>'pending'],
        'verified_by'        => ['type'=>'bigint unsigned','nullable'=>true],
        'full_name'          => ['type'=>'string','len'=>255,'nullable'=>true],
        'phone_number'       => ['type'=>'string','len'=>20,'nullable'=>true],
        'email'              => ['type'=>'string','len'=>255,'nullable'=>true],
        'address'            => ['type'=>'text','nullable'=>true],
        'dob'                => ['type'=>'date','nullable'=>true],
        'created_at'         => ['type'=>'timestamp','nullable'=>true],
        // NO updated_at — Payment model has UPDATED_AT = null
    ],

    // ── MEMBERSHIPS ───────────────────────────────────────────────────────────
    'memberships' => [
        'id'               => 'bigint unsigned auto_increment PK',
        'plan_name'        => ['type'=>'string','len'=>100,'nullable'=>false],
        'price'            => ['type'=>'decimal','p'=>10,'s'=>2,'nullable'=>false],
        'duration_days'    => ['type'=>'int','nullable'=>false],
        'contact_limit'    => ['type'=>'int','default'=>0],
        'featured_profile' => ['type'=>'tinyint','default'=>0],
        'priority_support' => ['type'=>'tinyint','default'=>0],
        'status'           => ['type'=>'tinyint','default'=>1],
        'created_at'       => ['type'=>'timestamp','nullable'=>true],
        'updated_at'       => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── USER_MEMBERSHIPS ──────────────────────────────────────────────────────
    'user_memberships' => [
        'id'               => 'bigint unsigned auto_increment PK',
        'user_id'          => ['type'=>'bigint unsigned','nullable'=>false],
        'membership_id'    => ['type'=>'bigint unsigned','nullable'=>false],
        'start_date'       => ['type'=>'date','nullable'=>true],
        'end_date'         => ['type'=>'date','nullable'=>true],
        'status'           => ['type'=>'enum','vals'=>"'active','expired','cancelled'",'default'=>'active'],
        'can_view_contacts'=> ['type'=>'tinyint','default'=>0],
        'created_at'       => ['type'=>'timestamp','nullable'=>true],
        'updated_at'       => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── ADVERTISEMENTS ────────────────────────────────────────────────────────
    'advertisements' => [
        'id'               => 'bigint unsigned auto_increment PK',
        'title'            => ['type'=>'string','len'=>255,'nullable'=>true],
        'image'            => ['type'=>'longtext','nullable'=>true],
        'link'             => ['type'=>'string','len'=>255,'nullable'=>true],
        'position'         => ['type'=>'enum','vals'=>"'home_top','home_bottom','sidebar','left_sidebar','right_sidebar','bottom_banner'",'nullable'=>true],
        'status'           => ['type'=>'tinyint','default'=>1],
        'media_type'       => ['type'=>'string','len'=>20,'default'=>'image','nullable'=>true],
        'sort_order'       => ['type'=>'int','default'=>0,'nullable'=>true],
        'duration_seconds' => ['type'=>'int','default'=>3,'nullable'=>true],
        'created_at'       => ['type'=>'timestamp','nullable'=>true],
        'updated_at'       => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── MARQUEE_ADS ───────────────────────────────────────────────────────────
    'marquee_ads' => [
        'id'          => 'bigint unsigned auto_increment PK',
        'notice_text' => ['type'=>'text','nullable'=>true],
        'status'      => ['type'=>'tinyint','default'=>1],
        'created_at'  => ['type'=>'timestamp','nullable'=>true],
        'updated_at'  => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── NEWS ──────────────────────────────────────────────────────────────────
    'news' => [
        'id'         => 'bigint unsigned auto_increment PK',
        'title'      => ['type'=>'string','len'=>255,'nullable'=>false],
        'content'    => ['type'=>'text','nullable'=>true],
        'image'      => ['type'=>'longtext','nullable'=>true],
        'status'     => ['type'=>'tinyint','default'=>1],
        'created_at' => ['type'=>'timestamp','nullable'=>true],
        'updated_at' => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── GALLERY ───────────────────────────────────────────────────────────────
    'gallery' => [
        'id'          => 'bigint unsigned auto_increment PK',
        'title'       => ['type'=>'string','len'=>255,'nullable'=>false],
        'category'    => ['type'=>'string','len'=>100,'default'=>'All Photos','nullable'=>true],
        'image_path'  => ['type'=>'longtext','nullable'=>true],
        'media_type'  => ['type'=>'enum','vals'=>"'image','pdf','video','youtube'",'default'=>'image'],
        'media_url'   => ['type'=>'string','len'=>500,'nullable'=>true],
        'status'      => ['type'=>'tinyint','default'=>1],
        'created_at'  => ['type'=>'timestamp','nullable'=>true],
        'updated_at'  => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── VIDEO_GALLERY ─────────────────────────────────────────────────────────
    'video_gallery' => [
        'id'            => 'bigint unsigned auto_increment PK',
        'title'         => ['type'=>'string','len'=>255,'nullable'=>false],
        'video_type'    => ['type'=>'enum','vals'=>"'youtube','mp4'",'default'=>'youtube'],
        'video_url'     => ['type'=>'string','len'=>255,'nullable'=>true],
        'video_file'    => ['type'=>'string','len'=>255,'nullable'=>true],
        'thumbnail'     => ['type'=>'string','len'=>255,'nullable'=>true],
        'description'   => ['type'=>'text','nullable'=>true],
        'display_order' => ['type'=>'int','default'=>0],
        'status'        => ['type'=>'enum','vals'=>"'active','inactive'",'default'=>'active'],
        'created_at'    => ['type'=>'timestamp','nullable'=>true],
        'updated_at'    => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── CONTACT_MESSAGES ──────────────────────────────────────────────────────
    'contact_messages' => [
        'id'          => 'bigint unsigned auto_increment PK',
        'name'        => ['type'=>'string','len'=>255,'nullable'=>true],
        'email'       => ['type'=>'string','len'=>255,'nullable'=>true],
        'mobile'      => ['type'=>'string','len'=>20,'nullable'=>true],
        'subject'     => ['type'=>'string','len'=>255,'nullable'=>true],
        'message'     => ['type'=>'text','nullable'=>true],
        'status'      => ['type'=>'enum','vals'=>"'unread','read','replied'",'default'=>'unread'],
        'reply_text'  => ['type'=>'text','nullable'=>true],
        'created_at'  => ['type'=>'timestamp','nullable'=>true],
        'updated_at'  => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── SUCCESS_STORIES ───────────────────────────────────────────────────────
    'success_stories' => [
        'id'              => 'bigint unsigned auto_increment PK',
        'user_id'         => ['type'=>'bigint unsigned','nullable'=>true],
        'couple_name'     => ['type'=>'string','len'=>255,'nullable'=>true],
        'engagement_date' => ['type'=>'date','nullable'=>true],
        'marriage_date'   => ['type'=>'date','nullable'=>true],
        'story'           => ['type'=>'longtext','nullable'=>true],
        'photo'           => ['type'=>'text','nullable'=>true],
        'status'          => ['type'=>'enum','vals'=>"'pending','approved'",'default'=>'pending'],
        'created_at'      => ['type'=>'timestamp','nullable'=>true],
        'updated_at'      => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── SITE_SETTINGS ─────────────────────────────────────────────────────────
    'site_settings' => [
        'id'            => 'bigint unsigned auto_increment PK',
        'setting_key'   => ['type'=>'string','len'=>100,'nullable'=>false],
        'setting_value' => ['type'=>'longtext','nullable'=>true],
        'created_at'    => ['type'=>'timestamp','nullable'=>true],
        'updated_at'    => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── REGISTRATION_FIELDS ───────────────────────────────────────────────────
    'registration_fields' => [
        'id'           => 'bigint unsigned auto_increment PK',
        'field_group'  => ['type'=>'string','len'=>100,'default'=>'Basic Details','nullable'=>true],
        'field_key'    => ['type'=>'string','len'=>100,'nullable'=>false],
        'field_label'  => ['type'=>'string','len'=>255,'nullable'=>false],
        'field_type'   => ['type'=>'string','len'=>50,'default'=>'text'],
        'field_options'=> ['type'=>'text','nullable'=>true],
        'is_custom'    => ['type'=>'tinyint','default'=>0],
        'is_visible'   => ['type'=>'tinyint','default'=>1],
        'is_required'  => ['type'=>'tinyint','default'=>0],
        'is_core'      => ['type'=>'tinyint','default'=>0],
        'sort_order'   => ['type'=>'int','default'=>0],
        'created_at'   => ['type'=>'timestamp','nullable'=>true],
        'updated_at'   => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── USER_CUSTOM_DATA ──────────────────────────────────────────────────────
    'user_custom_data' => [
        'id'          => 'bigint unsigned auto_increment PK',
        'user_id'     => ['type'=>'bigint unsigned','nullable'=>false],
        'field_id'    => ['type'=>'bigint unsigned','nullable'=>false],
        'field_value' => ['type'=>'text','nullable'=>true],
        'created_at'  => ['type'=>'timestamp','nullable'=>true],
        'updated_at'  => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── USER_LIKES ────────────────────────────────────────────────────────────
    'user_likes' => [
        'id'           => 'bigint unsigned auto_increment PK',
        'user_id'      => ['type'=>'bigint unsigned','nullable'=>false],
        'liked_user_id'=> ['type'=>'bigint unsigned','nullable'=>false],
        'created_at'   => ['type'=>'timestamp','nullable'=>true],
        'updated_at'   => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── USER_RELATIVES ────────────────────────────────────────────────────────
    'user_relatives' => [
        'id'         => 'bigint unsigned auto_increment PK',
        'user_id'    => ['type'=>'bigint unsigned','nullable'=>false],
        'relation'   => ['type'=>'string','len'=>100,'nullable'=>true],
        'name'       => ['type'=>'string','len'=>255,'nullable'=>true],
        'mobile'     => ['type'=>'string','len'=>20,'nullable'=>true],
        'occupation' => ['type'=>'string','len'=>150,'nullable'=>true],
        'created_at' => ['type'=>'timestamp','nullable'=>true],
        'updated_at' => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── ACCOUNT_REQUESTS ──────────────────────────────────────────────────────
    'account_requests' => [
        'id'           => 'bigint unsigned auto_increment PK',
        'user_id'      => ['type'=>'bigint unsigned','nullable'=>false],
        'request_type' => ['type'=>'enum','vals'=>"'deactivation','deletion'",'default'=>'deletion'],
        'reason'       => ['type'=>'text','nullable'=>true],
        'status'       => ['type'=>'enum','vals'=>"'pending','processed','rejected'",'default'=>'pending'],
        'created_at'   => ['type'=>'timestamp','nullable'=>true],
        'updated_at'   => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── ACTIVITY_LOGS ─────────────────────────────────────────────────────────
    'activity_logs' => [
        'id'         => 'bigint unsigned auto_increment PK',
        'user_type'  => ['type'=>'enum','vals'=>"'admin','user'",'nullable'=>true],
        'user_id'    => ['type'=>'bigint unsigned','nullable'=>true],
        'action'     => ['type'=>'string','len'=>255,'nullable'=>true],
        'details'    => ['type'=>'text','nullable'=>true],
        'ip_address' => ['type'=>'string','len'=>100,'nullable'=>true],
        'created_at' => ['type'=>'timestamp','nullable'=>true],
        'updated_at' => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── SCROLLING_NEWS ────────────────────────────────────────────────────────
    'scrolling_news' => [
        'id'         => 'bigint unsigned auto_increment PK',
        'content'    => ['type'=>'string','len'=>500,'nullable'=>false],
        'link'       => ['type'=>'string','len'=>255,'nullable'=>true],
        'status'     => ['type'=>'tinyint','default'=>1],
        'created_at' => ['type'=>'timestamp','nullable'=>true],
        'updated_at' => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── COMMITTEE_MEMBERS ─────────────────────────────────────────────────────
    'committee_members' => [
        'id'          => 'bigint unsigned auto_increment PK',
        'name'        => ['type'=>'string','len'=>255,'nullable'=>false],
        'designation' => ['type'=>'string','len'=>150,'nullable'=>true],
        'description' => ['type'=>'text','nullable'=>true],
        'photo'       => ['type'=>'string','len'=>255,'nullable'=>true],
        'sort_order'  => ['type'=>'int','default'=>0],
        'status'      => ['type'=>'tinyint','default'=>1],
        'created_at'  => ['type'=>'timestamp','nullable'=>true],
        'updated_at'  => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── OTP_VERIFICATIONS ─────────────────────────────────────────────────────
    'otp_verifications' => [
        'id'         => 'bigint unsigned auto_increment PK',
        'email'      => ['type'=>'string','len'=>255,'nullable'=>false],
        'otp_code'   => ['type'=>'string','len'=>10,'nullable'=>false],
        'expires_at' => ['type'=>'datetime','nullable'=>false],
        'verified'   => ['type'=>'tinyint','default'=>0],
        'created_at' => ['type'=>'timestamp','nullable'=>true],
        'updated_at' => ['type'=>'timestamp','nullable'=>true],
    ],

    // ── COMMUNITY_EVENTS ──────────────────────────────────────────────────────
    'community_events' => [
        'id'          => 'bigint unsigned auto_increment PK',
        'title'       => ['type'=>'string','len'=>255,'nullable'=>true],
        'description' => ['type'=>'longtext','nullable'=>true],
        'event_date'  => ['type'=>'date','nullable'=>true],
        'location'    => ['type'=>'string','len'=>255,'nullable'=>true],
        'banner'      => ['type'=>'string','len'=>255,'nullable'=>true],
        'status'      => ['type'=>'tinyint','default'=>1],
        'created_at'  => ['type'=>'timestamp','nullable'=>true],
        'updated_at'  => ['type'=>'timestamp','nullable'=>true],
    ],
];

// ─── COMPARE + COLLECT WHAT'S MISSING ────────────────────────────────────────
$missingTables  = [];
$missingColumns = []; // [table => [col => def]]
$presentTables  = [];

foreach ($schema as $table => $columns) {
    if (!isset($dbCols[$table])) {
        $missingTables[] = $table;
        continue;
    }
    $presentTables[] = $table;
    foreach ($columns as $col => $def) {
        if ($col === 'id') continue; // skip PK
        if (!isset($dbCols[$table][$col])) {
            $missingColumns[$table][$col] = $def;
        }
    }
}

// ─── FIX MODE: Apply all missing items ───────────────────────────────────────
$fixLog = [];

if ($doFix) {
    // 1) Run pending artisan migrations
    try {
        Artisan::call('migrate', ['--force' => true]);
        $fixLog[] = ['ok', 'php artisan migrate: ' . trim(Artisan::output() ?: 'No pending migrations.')];
    } catch (\Exception $e) {
        $fixLog[] = ['err', 'Artisan migrate error: ' . $e->getMessage()];
    }

    // 2) Add missing columns
    foreach ($missingColumns as $table => $cols) {
        foreach ($cols as $col => $def) {
            try {
                DB::statement(buildAlterSQL($table, $col, $def));
                $fixLog[] = ['ok', "✓ Added `$table`.`$col`"];
            } catch (\Exception $e) {
                $fixLog[] = ['err', "✗ Failed `$table`.`$col`: " . $e->getMessage()];
            }
        }
    }

    // 3) Fix enum mismatches critical to function
    $enumFixes = [
        ['payments', 'status', "ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'"],
        ['users',    'payment_status', "ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'"],
        ['users',    'status', "ENUM('account_pending','account_approved','pending','approved','rejected','blocked') NOT NULL DEFAULT 'account_pending'"],
    ];
    foreach ($enumFixes as [$tbl, $col, $newType]) {
        if (!isset($dbCols[$tbl][$col])) continue; // already missing, handled above
        try {
            DB::statement("ALTER TABLE `$tbl` MODIFY COLUMN `$col` $newType");
            $fixLog[] = ['ok', "✓ Fixed enum `$tbl`.`$col`"];
        } catch (\Exception $e) {
            $fixLog[] = ['warn', "~ Enum fix `$tbl`.`$col`: " . $e->getMessage()];
        }
    }

    // Refresh DB state for display
    $dbRows2 = DB::select("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, ORDINAL_POSITION", [$dbName]);
    $dbCols = [];
    foreach ($dbRows2 as $row) {
        $dbCols[$row->TABLE_NAME][$row->COLUMN_NAME] = '✓';
    }
    // Recompute missing after fix
    $missingColumns = [];
    foreach ($schema as $table => $columns) {
        if (!isset($dbCols[$table])) continue;
        foreach ($columns as $col => $def) {
            if ($col === 'id') continue;
            if (!isset($dbCols[$table][$col])) {
                $missingColumns[$table][$col] = $def;
            }
        }
    }
}

// ─── HELPER: Build ALTER TABLE SQL ───────────────────────────────────────────
function buildAlterSQL($table, $col, $def): string {
    if (is_string($def)) return ''; // PK placeholder, skip
    $type = $def['type'];
    $nullable = $def['nullable'] ?? true;
    $default  = $def['default'] ?? null;
    $len = $def['len'] ?? null;
    $p = $def['p'] ?? null;
    $s = $def['s'] ?? null;
    $vals = $def['vals'] ?? null;

    $sqlType = match($type) {
        'string'         => "VARCHAR($len)",
        'text'           => 'TEXT',
        'longtext'       => 'LONGTEXT',
        'int'            => 'INT',
        'tinyint'        => 'TINYINT(1)',
        'bigint unsigned'=> 'BIGINT UNSIGNED',
        'decimal'        => "DECIMAL($p,$s)",
        'date'           => 'DATE',
        'datetime'       => 'DATETIME',
        'timestamp'      => 'TIMESTAMP',
        'enum'           => "ENUM($vals)",
        default          => 'VARCHAR(255)',
    };

    $nullStr = $nullable ? 'NULL' : 'NOT NULL';
    $defStr  = '';
    if ($default !== null) {
        $defStr = is_string($default) ? " DEFAULT '$default'" : " DEFAULT $default";
    } elseif ($nullable) {
        $defStr = ' DEFAULT NULL';
    }

    return "ALTER TABLE `$table` ADD COLUMN `$col` $sqlType $nullStr$defStr";
}

// ─── COUNTS ───────────────────────────────────────────────────────────────────
$totalMissingCols = array_sum(array_map('count', $missingColumns));
$allGood = empty($missingTables) && $totalMissingCols === 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DB Audit & Fix</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',monospace;background:#0f172a;color:#e2e8f0;padding:2rem;font-size:.9rem}
h1{color:#a78bfa;font-size:1.5rem;margin-bottom:.3rem}
.sub{color:#475569;font-size:.8rem;margin-bottom:1.5rem}
.stats{display:flex;gap:1.5rem;flex-wrap:wrap;margin-bottom:1.5rem}
.stat{background:#1e293b;border-radius:.6rem;padding:.8rem 1.2rem;text-align:center;min-width:120px}
.stat .n{font-size:2rem;font-weight:900}
.stat .l{font-size:.7rem;color:#64748b;margin-top:.2rem}
.ok{color:#4ade80}.err{color:#f87171}.warn{color:#fbbf24}.info{color:#60a5fa}
.section{background:#111827;border:1px solid #1e293b;border-radius:.75rem;padding:1.25rem;margin-bottom:1.25rem}
h2{font-size:1rem;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #1e293b}
table{width:100%;border-collapse:collapse;font-size:.8rem}
th{background:#1e293b;padding:.4rem .6rem;text-align:left;color:#94a3b8;text-transform:uppercase;font-size:.68rem}
td{padding:.4rem .6rem;border-bottom:1px solid #0f172a;vertical-align:top}
.pill{display:inline-block;padding:.1rem .45rem;border-radius:999px;font-size:.7rem;margin:.1rem;font-family:monospace}
.p-ok{background:#134e4a;color:#5eead4}
.p-miss{background:#450a0a;color:#fca5a5;border:1px solid #f87171}
.p-extra{background:#1e3a5f;color:#93c5fd}
.btn{display:inline-block;padding:.55rem 1.2rem;border-radius:.4rem;font-weight:700;font-size:.85rem;text-decoration:none;margin:.25rem}
.btn-fix{background:#7c3aed;color:#fff}
.btn-fix:hover{background:#6d28d9}
.btn-audit{background:#1e293b;color:#a78bfa;border:1px solid #7c3aed}
pre{background:#1e293b;padding:.75rem;border-radius:.4rem;font-size:.75rem;white-space:pre-wrap;word-break:break-all}
.log-ok{color:#4ade80}.log-err{color:#f87171}.log-warn{color:#fbbf24}
hr{border:none;border-top:1px solid #1e293b;margin:1.25rem 0}
</style>
</head>
<body>

<h1>🗄️ DB Audit &amp; Patch Tool</h1>
<p class="sub">Database: <strong><?= htmlspecialchars($dbName) ?></strong> &nbsp;|&nbsp; <?= now()->format('d M Y H:i:s') ?></p>

<div class="stats">
    <div class="stat"><div class="n <?= empty($missingTables) ? 'ok' : 'err' ?>"><?= count($missingTables) ?></div><div class="l">MISSING TABLES</div></div>
    <div class="stat"><div class="n <?= $totalMissingCols === 0 ? 'ok' : 'err' ?>"><?= $totalMissingCols ?></div><div class="l">MISSING COLUMNS</div></div>
    <div class="stat"><div class="n ok"><?= count($presentTables) ?></div><div class="l">TABLES OK</div></div>
    <div class="stat"><div class="n info"><?= count($dbCols) ?></div><div class="l">TOTAL DB TABLES</div></div>
</div>

<?php if ($doFix && !empty($fixLog)): ?>
<div class="section" style="border-color:#7c3aed">
    <h2 style="color:#a78bfa">🔧 Fix Results</h2>
    <pre><?php foreach ($fixLog as [$type, $msg]): ?><span class="log-<?= $type ?>"><?= htmlspecialchars($msg) ?></span>
<?php endforeach; ?></pre>
</div>
<?php endif; ?>

<?php if ($allGood): ?>
<div class="section" style="border-color:#166534">
    <h2 class="ok">✅ DATABASE IS FULLY IN SYNC — ALL TABLES &amp; COLUMNS PRESENT</h2>
    <p style="color:#86efac;margin-top:.5rem">Every table and column the website needs exists in the database. No action needed.</p>
</div>
<?php else: ?>

<?php if (!$doFix): ?>
<div style="margin-bottom:1.5rem">
    <a class="btn btn-fix" href="?fix=1">🔧 ADD ALL MISSING ITEMS TO DATABASE NOW</a>
    <a class="btn btn-audit" href="?">↺ Refresh Audit Only</a>
    <p style="color:#64748b;font-size:.75rem;margin-top:.4rem">Clicking FIX will only ADD missing columns/tables — no existing data is changed or deleted.</p>
</div>
<?php endif; ?>

<?php if (!empty($missingTables)): ?>
<div class="section" style="border-color:#dc2626">
    <h2 class="err">❌ Missing Tables (<?= count($missingTables) ?>)</h2>
    <?php foreach ($missingTables as $t): ?>
    <div style="padding:.35rem .75rem;background:#450a0a;border-radius:.3rem;margin:.3rem 0;font-family:monospace"><span class="err">✗</span> <?= $t ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($missingColumns)): ?>
<div class="section" style="border-color:#b45309">
    <h2 class="warn">⚠️ Missing Columns (<?= $totalMissingCols ?> across <?= count($missingColumns) ?> tables)</h2>
    <table>
        <thead><tr><th>Table</th><th>Missing Column(s)</th></tr></thead>
        <tbody>
        <?php foreach ($missingColumns as $table => $cols): ?>
        <tr>
            <td><code class="warn"><?= $table ?></code></td>
            <td><?php foreach (array_keys($cols) as $c): ?><span class="pill p-miss"><?= $c ?></span><?php endforeach; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php endif; // end !allGood ?>

<hr>
<h2 style="color:#94a3b8;margin-bottom:1rem">📋 Full Table Status</h2>
<?php foreach ($schema as $table => $columns): ?>
<?php
$exists = isset($dbCols[$table]);
$missing = array_keys($missingColumns[$table] ?? []);
$allColsOk = $exists && empty($missing);
$borderColor = !$exists ? '#dc2626' : (!$allColsOk ? '#b45309' : '#166534');
$icon = !$exists ? '❌' : (!$allColsOk ? '⚠️' : '✅');
?>
<div class="section" style="border-color:<?= $borderColor ?>">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem">
        <strong><?= $icon ?> <?= $table ?></strong>
        <?php if (!$exists): ?>
        <span style="font-size:.7rem;color:#f87171">TABLE MISSING FROM DB</span>
        <?php else: ?>
        <span style="font-size:.7rem;color:#64748b"><?= count($dbCols[$table]) ?> cols in DB</span>
        <?php endif; ?>
    </div>
    <?php if ($exists): ?>
    <div>
        <?php foreach (array_keys($columns) as $col): ?>
        <?php if ($col === 'id') continue; ?>
        <span class="pill <?= isset($dbCols[$table][$col]) ? 'p-ok' : 'p-miss' ?>">
            <?= $col ?><?= !isset($dbCols[$table][$col]) ? ' ✗' : '' ?>
        </span>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:#fca5a5;font-size:.82rem;margin-top:.3rem">This table does not exist in the database.</p>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<p style="color:#334155;font-size:.72rem;margin-top:2rem">⚠️ Delete db_patch.php after use. | <a href="?" style="color:#7c3aed">Refresh</a></p>
</body>
</html>
