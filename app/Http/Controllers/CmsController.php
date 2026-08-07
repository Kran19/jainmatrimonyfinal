<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\News;

class CmsController extends Controller
{
    /**
     * Display the About page.
     */
    public function about()
    {
        $about_us = Setting::where('setting_key', 'about_us')->value('setting_value') ?? '';
        $about_us_en = Setting::where('setting_key', 'about_us_en')->value('setting_value') ?? '';
        return view('cms.about', compact('about_us', 'about_us_en'));
    }

    /**
     * Display the Contact page.
     */
    public function contact()
    {
        return view('cms.contact');
    }

    /**
     * Handle support query form submissions.
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:200',
            'message' => 'required|string',
        ]);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'created_at' => now(),
        ];

        $phone = $request->input('phone');

        try {
            // First try inserting with 'phone' column (legacy schema)
            DB::table('contact_messages')->insert(array_merge($data, ['phone' => $phone]));
        } catch (\Exception $e) {
            try {
                // Fallback to 'mobile' column (new schema)
                DB::table('contact_messages')->insert(array_merge($data, ['mobile' => $phone]));
            } catch (\Exception $ex) {
                return back()->with('error', 'Failed to send message: ' . $ex->getMessage());
            }
        }

        return back()->with('success', 'Your message has been sent successfully.');
    }

    /**
     * Display the Privacy Policy.
     */
    public function privacy()
    {
        $privacy_policy = Setting::where('setting_key', 'privacy_policy')->value('setting_value') ?? '';
        return view('cms.privacy', compact('privacy_policy'));
    }

    /**
     * Display the Terms & Conditions.
     */
    public function terms()
    {
        $terms_conditions = Setting::where('setting_key', 'terms_conditions')->value('setting_value') ?? '';
        return view('cms.terms', compact('terms_conditions'));
    }

    /**
     * Display the Community Initiatives / Committee page.
     */
    public function community()
    {
        $community_content = Setting::where('setting_key', 'community_content')->value('setting_value') ?? '';
        $committeeMembers = \App\Models\CommitteeMember::where('status', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        return view('cms.community', compact('community_content', 'committeeMembers'));
    }


    /**
     * Display the News & Updates listing page.
     */
    public function news()
    {
        $news_items = News::where('status', true)->orderBy('created_at', 'desc')->get();
        return view('cms.news', compact('news_items'));
    }
}
