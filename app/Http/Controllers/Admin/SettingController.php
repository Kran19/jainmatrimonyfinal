<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display site settings configuration dashboard.
     */
    public function index()
    {
        // Fetch settings from database and map into key => value array
        $settings = Setting::pluck('setting_value', 'setting_key')->toArray();

        // Fallback default configurations (Core PHP + Laravel combined)
        $defaults = [
            // Laravel Existing Defaults
            'upi_id' => 'digambarsamaj@bank',
            'support_phone' => '+91 98765 43210',
            'support_email' => 'support@jaindigambarmatrimony.com',
            'min_registration_age' => '18',

            // Core PHP Migrated Defaults
            'payment_enabled' => '0',
            'show_matrimony_book_fee' => '0',
            'auto_approve' => '0',
            'show_home_top_ads' => '1',
            'contact_email' => 'digambarjainparichay@gmail.com',
            'contact_phone' => '+91 7575005121',
            'contact_address' => '23-A, Shubhlaxmi Palace, Opp. Money Plant Junction, Bhuyangdev Cross Road, Sola Road, Ahmedabad-380061.',
            'payment_qr_code' => 'assets/images/qr_code.jpg',
            'home_title' => 'Digambar Jain Matrimony',
            'home_tagline' => '(established in 2026)',
            'hero_heading' => 'The most trusted<br>matrimony<br>service for<br>Digambar Jain!',
            'hero_description' => 'This website is created only for the Digambar Jain community to help eligible young men and women of the entire Digambar Jain society find their suitable life partner.',
            'hero_banner' => '',
            'show_hero_left_ad' => '1',
            'show_hero_right_ad' => '1',
            'show_hero_bottom_ad' => '1',
            'about_youtube' => '',
            'about_us' => '',
            'terms_conditions' => '',
            'privacy_policy' => '',
            'community_content' => '<div class="text-center">
<i class="fas fa-users text-6xl text-primary mb-6"></i>
<h1 class="text-4xl font-bold text-dark mb-4">Our Community Initiatives</h1>
<p class="text-gray-600 text-lg mb-8">This page is under development. We will soon update this space with details about our social programs, community gatherings, and upcoming initiatives for the Digambar Jain Samaj.</p>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200"><i class="fas fa-hand-holding-heart text-3xl text-secondary mb-4"></i><h3 class="text-xl font-bold text-dark mb-2">Charity Drives</h3><p class="text-gray-600 text-sm">Supporting the underprivileged and providing essential resources.</p></div>
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200"><div class="text-4xl text-primary font-bold mb-3">卐</div><h3 class="text-xl font-bold text-dark mb-2">Spiritual Events</h3><p class="text-gray-600 text-sm">Organizing regular poojas, aartis, and spiritual discourses.</p></div>
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200"><i class="fas fa-ring text-3xl text-accent mb-4"></i><h3 class="text-xl font-bold text-dark mb-2">Parichay Sammelan</h3><p class="text-gray-600 text-sm">Hosting matchmaking events for prospective girls and boys.</p></div>
</div></div>',
        ];

        // Default fallbacks for Terms & Privacy if empty
        if (empty(trim(strip_tags($settings['terms_conditions'] ?? '')))) {
            $defaultTermsPath = base_path('../digambar-samaj/includes/default_terms.php');
            if (file_exists($defaultTermsPath)) {
                require_once $defaultTermsPath;
                if (isset($default_terms)) {
                    $defaults['terms_conditions'] = $default_terms;
                }
            }
        }

        if (empty(trim(strip_tags($settings['privacy_policy'] ?? '')))) {
            $defaultPrivacyPath = base_path('../digambar-samaj/includes/default_privacy.php');
            if (file_exists($defaultPrivacyPath)) {
                require_once $defaultPrivacyPath;
                if (isset($default_privacy)) {
                    $defaults['privacy_policy'] = $default_privacy;
                }
            }
        }

        // Merge database settings over default fallbacks
        $settings = array_merge($defaults, $settings);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update site settings in database.
     */
    public function update(Request $request)
    {
        // Handle File Uploads (Base64 data URI matching Core PHP storage)
        if ($request->hasFile('payment_qr_code_file') && $request->file('payment_qr_code_file')->isValid()) {
            $file = $request->file('payment_qr_code_file');
            $mimeType = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $qrPath = 'data:' . $mimeType . ';base64,' . $base64;

            Setting::updateOrCreate(
                ['setting_key' => 'payment_qr_code'],
                ['setting_value' => $qrPath]
            );
        }

        if ($request->hasFile('hero_banner_file') && $request->file('hero_banner_file')->isValid()) {
            $file = $request->file('hero_banner_file');
            $mimeType = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $heroPath = 'data:' . $mimeType . ';base64,' . $base64;

            Setting::updateOrCreate(
                ['setting_key' => 'hero_banner'],
                ['setting_value' => $heroPath]
            );
        }

        // List of checkbox toggle keys to ensure '0' is stored if unchecked
        $toggleKeys = [
            'payment_enabled',
            'show_matrimony_book_fee',
            'auto_approve',
            'show_home_top_ads',
            'show_hero_left_ad',
            'show_hero_right_ad',
            'show_hero_bottom_ad',
        ];

        foreach ($toggleKeys as $toggleKey) {
            $val = $request->has($toggleKey) ? (string)$request->input($toggleKey) : '0';
            Setting::updateOrCreate(
                ['setting_key' => $toggleKey],
                ['setting_value' => $val]
            );
        }

        // Process all other inputs
        $inputs = $request->except([
            '_token',
            'payment_qr_code_file',
            'hero_banner_file',
        ]);

        foreach ($inputs as $key => $value) {
            if (in_array($key, $toggleKeys)) {
                continue; // Already processed
            }
            // Sanitize key
            $key = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
            if (empty($key)) continue;

            Setting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => (string)$value]
            );
        }

        return back()->with('success', 'Site settings updated successfully.');
    }
}
