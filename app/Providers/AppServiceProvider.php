<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\CustomUserProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'has_set_password')) {
                    \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                        $table->boolean('has_set_password')->default(true);
                    });
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'registration_count')) {
                    \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                        $table->integer('registration_count')->default(1);
                    });
                    \Illuminate\Support\Facades\DB::table('users')->whereNull('registration_count')->update(['registration_count' => 1]);
                }
                if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'deletion_count')) {
                    \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                        $table->integer('deletion_count')->default(0);
                    });
                    \Illuminate\Support\Facades\DB::table('users')->whereNull('deletion_count')->update(['deletion_count' => 0]);
                    \Illuminate\Support\Facades\DB::table('users')->where('status', 'deleted')->where('deletion_count', 0)->update(['deletion_count' => 1]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if DB connection or permissions issue during boot
        }

        Auth::provider('custom-users', function ($app, array $config) {
            return new CustomUserProvider($config['model']);
        });

        view()->composer(['layouts.header', 'layouts.footer', 'home', 'layouts.app', 'user.gallery'], function ($view) {
            $settings = [];
            $left_ads = [];
            $right_ads = [];
            $footer_ads = [];
            $scrolling_news = [];

            try {
                $settings = \App\Models\Setting::pluck('setting_value', 'setting_key')->toArray();

                // Visitor counter is tracked in HomeController (session-based, once per unique session).
                // Do NOT increment here — this view composer fires multiple times per page load.

                $ads = \App\Models\Advertisement::where('status', true)->orderBy('id', 'desc')->get()->toArray();
                foreach ($ads as $ad) {
                    if (in_array($ad['position'], ['left_sidebar', 'left_side'])) {
                        $left_ads[] = $ad;
                    } elseif (in_array($ad['position'], ['right_sidebar', 'right_side'])) {
                        $right_ads[] = $ad;
                    } elseif ($ad['position'] === 'footer') {
                        $footer_ads[] = $ad;
                    }
                }

                $scrolling_news = \DB::table('scrolling_news')->where('status', true)->orderBy('created_at', 'desc')->get()->toArray();
            } catch (\Exception $e) {}

            $is_logged_in = false;
            $is_approved = false;
            $user_gender = null;
            $hdr_user_name = 'User';
            $hdr_profile_img = 'https://ui-avatars.com/api/?name=User&background=random';

            if (Auth::guard('web')->check()) {
                $is_logged_in = true;
                $user = Auth::guard('web')->user();
                if ($user->status === 'approved') {
                    $is_approved = true;
                }
                $user_gender = $user->gender;
                $hdr_user_name = $user->full_name;

                $photoExists = !empty($user->profile_photo) && (str_starts_with($user->profile_photo, 'data:image/') || resolve_media_path($user->profile_photo) !== null);

                if ($photoExists) {
                    $hdr_profile_img = route('image.serve', ['file' => $user->profile_photo]);
                } else {
                    $hdr_profile_img = 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=random';
                }
            } elseif (Auth::guard('admin')->check()) {
                $is_logged_in = true;
                $is_approved = true;
                $hdr_user_name = Auth::guard('admin')->user()->name;
            }

            $view->with(compact(
                'settings',
                'left_ads',
                'right_ads',
                'footer_ads',
                'scrolling_news',
                'is_logged_in',
                'is_approved',
                'user_gender',
                'hdr_user_name',
                'hdr_profile_img'
            ));
        });
    }
}
