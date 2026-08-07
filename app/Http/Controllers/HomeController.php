<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\Advertisement;
use App\Models\MarqueeAd;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Display the matrimony homepage.
     */
    public function index(Request $request)
    {
        // Daily check fallback: Deactivate expired member profiles
        \Illuminate\Support\Facades\Cache::remember('daily_expired_profiles_check', 86400, function() {
            try {
                DB::table('users')
                    ->where('status', 'approved')
                    ->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', now()->toDateString())
                    ->update([
                        'status' => 'deactivated',
                        'is_approved' => false,
                        'verified' => false,
                        'is_public' => false
                    ]);
            } catch (\Exception $e) {
                // Ignore DB exceptions
            }
            return true;
        });

        // 1. Fetch site settings with caching
        $settings = \Illuminate\Support\Facades\Cache::remember('site_settings', 300, function() {
            return Setting::pluck('setting_value', 'setting_key')->toArray();
        });

        // 2. Visitor counter — count each unique visitor.
        //    Excludes admins, bots, and browser prefetch requests.
        try {
            $isBotOrPrefetch = $request->hasHeader('X-Purpose') || $request->hasHeader('Sec-Purpose')
                || $request->header('Purpose') === 'prefetch';

            if (!Auth::guard('admin')->check() && !$isBotOrPrefetch) {
                // If this session has not been counted yet
                if (!$request->session()->has('visitor_counted')) {
                    $request->session()->put('visitor_counted', true);

                    // Fetch current count to safely increment
                    $currentValue = DB::table('site_settings')
                        ->where('setting_key', 'visitor_count')
                        ->value('setting_value');

                    $newCount = (int)$currentValue + 1;

                    DB::table('site_settings')->updateOrInsert(
                        ['setting_key' => 'visitor_count'],
                        ['setting_value' => (string)$newCount, 'updated_at' => now()]
                    );

                    // Bust cache so the dashboard/site shows the fresh count
                    \Illuminate\Support\Facades\Cache::forget('site_settings');
                }

                // Force reload the settings array so the current page load has the updated count
                $settings = Setting::pluck('setting_value', 'setting_key')->toArray();
                \Illuminate\Support\Facades\Cache::put('site_settings', $settings, 300);
            }
        } catch (\Exception $e) {
            // Silence DB exception if read-only or table missing
        }

        // 3. Fetch active advertisements with a short cache duration (10s) to reflect production updates instantly
        $advertisements = \Illuminate\Support\Facades\Cache::remember('active_advertisements', 10, function() {
            try {
                $query = Advertisement::where(function($q) {
                    $q->where('status', true)->orWhere('status', 1)->orWhere('status', '1');
                });
                if (\Illuminate\Support\Facades\Schema::hasColumn('advertisements', 'sort_order')) {
                    $query->orderBy('sort_order', 'asc');
                }
                return $query->orderBy('created_at', 'desc')->get()->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        $home_top_ads = array_values(array_filter($advertisements, fn($ad) => $ad['position'] === 'home_top'));
        $left_sidebar_ads = array_values(array_filter($advertisements, fn($ad) => in_array($ad['position'], ['left', 'left_side', 'left_sidebar'])));
        $right_sidebar_ads = array_values(array_filter($advertisements, fn($ad) => in_array($ad['position'], ['right', 'right_side', 'right_sidebar'])));
        $bottom_ads = array_values(array_filter($advertisements, fn($ad) => in_array($ad['position'], ['bottom', 'bottom_banner', 'home_bottom', 'footer'])));
        $home_bottom_ads = !empty($bottom_ads) ? $bottom_ads : array_values(array_filter($advertisements, fn($ad) => $ad['position'] === 'home_bottom'));
        $latest_profiles_bottom_ads = array_values(array_filter($advertisements, fn($ad) => $ad['position'] === 'latest_profiles_bottom'));
        $footer_ads = $bottom_ads;

        // 4. Determine user auth status & view access permissions
        $is_logged_in = false;
        $is_approved = false;
        $user_gender = null;

        if (Auth::guard('web')->check()) {
            $is_logged_in = true;
            $user = Auth::guard('web')->user();
            if ($user->status === 'approved') {
                $is_approved = true;
            }
            $user_gender = $user->gender;
        } elseif (Auth::guard('admin')->check()) {
            $is_logged_in = true;
            $is_approved = true;
        }

        // 5. Fetch marquee advertisements notice texts with caching
        $marquee_ads_text = \Illuminate\Support\Facades\Cache::remember('active_marquee_ads_text', 300, function() {
            $list = [];
            try {
                $marquee_items = MarqueeAd::where(function($q) {
                    $q->where('status', true)->orWhere('status', 1)->orWhere('status', '1');
                })->orderBy('created_at', 'desc')->get();

                foreach ($marquee_items as $item) {
                    $text = $item->notice_text ?? ($item->advertisement_text ?? '');
                    if (!empty(trim($text))) {
                        $list[] = trim($text);
                    }
                }
            } catch (\Exception $e) {}
            return $list;
        });

        // Fallback default marquee notice if no active items exist in DB
        if (empty($marquee_ads_text)) {
            $marquee_ads_text[] = 'दिगम्बर जैन परिचय मेत्रीमोनीयल दिगम्बर जैन समाज के विवाह योग्य युवक-युवतियों के जीवनसाथी चयन में सहायक एकमात्र वेबसाईट';
        }

        // 6. Fetch scrolling news with caching
        $scrolling_news = \Illuminate\Support\Facades\Cache::remember('active_scrolling_news', 300, function() {
            try {
                return DB::table('scrolling_news')->where('status', true)->orderBy('created_at', 'desc')->get()->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        // 7. Determine gender filter for latest profiles
        $latest_gender = $request->query('latest_gender');
        if (!in_array($latest_gender, ['Girl', 'Boy'])) {
            if ($is_logged_in && $user_gender) {
                $latest_gender = ($user_gender === 'Male' || $user_gender === 'Boy') ? 'Girl' : 'Boy';
            } else {
                $latest_gender = 'Girl';
            }
        }

        $gender_db = ($latest_gender === 'Girl') ? 'Female' : 'Male';
        
        $index_profiles = \Illuminate\Support\Facades\Cache::remember('index_profiles_' . $gender_db, 180, function() use ($gender_db) {
            $index_profiles = [];
            try {
                $profiles = User::approved()
                    ->where('gender', $gender_db)
                    ->orderBy('id', 'desc')
                    ->limit(4)
                    ->get();


                $delay = 0;
                foreach ($profiles as $p) {
                    $p->delay = $delay;
                    $delay += 100;

                    // Calculate age
                    $age = 'N/A';
                    if (!empty($p->birth_date)) {
                        $bday = new \DateTime($p->birth_date);
                        $today = new \DateTime('today');
                        $age = $bday->diff($today)->y;
                    }
                    $p->computed_age = $age;

                    // Fallback image URL
                    $photoExists = !empty($p->profile_photo) && (str_starts_with($p->profile_photo, 'data:image/') || resolve_media_path($p->profile_photo) !== null);

                    if ($photoExists) {
                        $p->computed_img = route('image.serve', ['file' => $p->profile_photo]);
                    } else {
                        $p->computed_img = 'https://ui-avatars.com/api/?name=' . urlencode($p->full_name) . '&background=random';
                    }

                    $index_profiles[] = $p;
                }
            } catch (\Exception $e) {}
            return $index_profiles;
        });

        return view('home', compact(
            'settings',
            'home_top_ads',
            'home_bottom_ads',
            'latest_profiles_bottom_ads',
            'left_sidebar_ads',
            'right_sidebar_ads',
            'bottom_ads',
            'footer_ads',
            'marquee_ads_text',
            'scrolling_news',
            'latest_gender',
            'index_profiles',
            'is_logged_in',
            'is_approved'
        ));
    }
}
