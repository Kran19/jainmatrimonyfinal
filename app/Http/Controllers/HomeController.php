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
        // 1. Fetch site settings
        $settings = Setting::pluck('setting_value', 'setting_key')->toArray();

        // 2. Increment visitor count
        try {
            $visitorCount = isset($settings['visitor_count']) ? (int)$settings['visitor_count'] + 1 : 1;
            Setting::updateOrCreate(
                ['setting_key' => 'visitor_count'],
                ['setting_value' => (string)$visitorCount]
            );
            $settings['visitor_count'] = $visitorCount;
        } catch (\Exception $e) {
            // Silence DB exception if read-only or table missing
        }

        // 3. Fetch active advertisements
        $advertisements = [];
        try {
            $query = Advertisement::where('status', true);
            if (\Illuminate\Support\Facades\Schema::hasColumn('advertisements', 'sort_order')) {
                $query->orderBy('sort_order', 'asc');
            }
            $advertisements = $query->orderBy('created_at', 'desc')->get()->toArray();
        } catch (\Exception $e) {}

        $home_top_ads = array_values(array_filter($advertisements, fn($ad) => $ad['position'] === 'home_top'));
        $left_sidebar_ads = array_values(array_filter($advertisements, fn($ad) => in_array($ad['position'], ['left', 'left_side', 'left_sidebar'])));
        $right_sidebar_ads = array_values(array_filter($advertisements, fn($ad) => in_array($ad['position'], ['right', 'right_side', 'right_sidebar'])));
        $bottom_ads = array_values(array_filter($advertisements, fn($ad) => in_array($ad['position'], ['bottom', 'bottom_banner', 'home_bottom', 'footer'])));
        $home_bottom_ads = !empty($bottom_ads) ? $bottom_ads : array_values(array_filter($advertisements, fn($ad) => $ad['position'] === 'home_bottom'));
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

        // 5. Fetch marquee advertisements notice texts
        $marquee_ads_text = [];
        try {
            $marquee_items = MarqueeAd::where('status', true)->orderBy('created_at', 'desc')->get();
            foreach ($marquee_items as $item) {
                $text = $item->notice_text ?? ($item->advertisement_text ?? '');
                if (!empty($text)) {
                    $marquee_ads_text[] = htmlspecialchars($text);
                }
            }
        } catch (\Exception $e) {}

        // 6. Fetch scrolling news
        $scrolling_news = [];
        try {
            $scrolling_news = DB::table('scrolling_news')->where('status', true)->orderBy('created_at', 'desc')->get()->toArray();
        } catch (\Exception $e) {}

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
        
        $index_profiles = [];
        try {
            $profiles = User::whereIn('status', ['approved', 'pending'])
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

        return view('home', compact(
            'settings',
            'home_top_ads',
            'home_bottom_ads',
            'left_sidebar_ads',
            'right_sidebar_ads',
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
