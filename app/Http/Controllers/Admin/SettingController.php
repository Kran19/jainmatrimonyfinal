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

        // Fallback default configurations
        $defaults = [
            'upi_id' => 'digambarsamaj@bank',
            'support_phone' => '+91 98765 43210',
            'support_email' => 'support@jaindigambarmatrimony.org',
            'min_registration_age' => '18',
        ];

        // Merge database settings over default fallbacks
        $settings = array_merge($defaults, $settings);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update site settings in database.
     */
    public function update(Request $request)
    {
        $inputs = $request->except('_token');

        foreach ($inputs as $key => $value) {
            Setting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }

        return back()->with('success', 'Site settings updated successfully.');
    }
}
