<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Models\News;
use App\Models\ContactMessage;

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
        $settings = Setting::pluck('setting_value', 'setting_key')->toArray();
        return view('cms.contact', compact('settings'));
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

        $name = trim($request->input('name'));
        $email = trim($request->input('email'));
        $phone = trim($request->input('phone'));
        $subject = trim($request->input('subject'));
        $message = trim($request->input('message'));

        try {
            ContactMessage::create([
                'name' => $name,
                'email' => $email,
                'mobile' => $phone,
                'subject' => $subject,
                'message' => $message,
                'status' => 'unread',
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            try {
                DB::table('contact_messages')->insert([
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $phone,
                    'subject' => $subject,
                    'message' => $message,
                    'status' => 'unread',
                    'created_at' => now(),
                ]);
            } catch (\Exception $ex) {
                return back()->with('error', 'Failed to save message: ' . $ex->getMessage());
            }
        }

        // Send Email Notification to digambarjainparichay@gmail.com with Reply-To set to sender's email
        try {
            $adminEmail = 'digambarjainparichay@gmail.com';
            $emailSubject = "[Contact Query] " . $subject . " - from " . $name;
            $userPhone = $phone ?: 'Not Provided';

            $htmlBody = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div style="background: linear-gradient(135deg, #0f1754 0%, #1e293b 100%); padding: 24px; text-align: center; color: #ffffff;">
                    <h2 style="margin: 0; font-size: 20px; font-weight: 800; letter-spacing: 0.5px;">à¤¦à¤¿à¤—à¤®à¥à¤¬à¤° à¤œà¥ˆà¤¨ à¤ªà¤°à¤¿à¤šà¤¯ à¤¸à¤®à¥à¤®à¥‡à¤²à¤¨ à¤¸à¤®à¤¿à¤¤à¤¿</h2>
                    <p style="margin: 6px 0 0 0; font-size: 13px; color: #cbd5e1;">New Contact Inquiry / User Query</p>
                </div>
                <div style="padding: 24px; color: #1e293b; line-height: 1.6;">
                    <p style="font-size: 15px; margin-top: 0; color: #334155;">A new user query has been submitted via the website contact form.</p>
                    
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin: 18px 0;">
                        <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 6px 0; font-weight: bold; color: #64748b; width: 120px;">Sender Name:</td>
                                <td style="padding: 6px 0; font-weight: bold; color: #0f172a;">' . htmlspecialchars($name) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; font-weight: bold; color: #64748b;">Email Address:</td>
                                <td style="padding: 6px 0;"><a href="mailto:' . htmlspecialchars($email) . '" style="color: #2563eb; font-weight: bold; text-decoration: none;">' . htmlspecialchars($email) . '</a></td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; font-weight: bold; color: #64748b;">Phone / Mobile:</td>
                                <td style="padding: 6px 0; font-weight: bold; color: #0f172a;">' . htmlspecialchars($userPhone) . '</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 0; font-weight: bold; color: #64748b;">Subject:</td>
                                <td style="padding: 6px 0; font-weight: bold; color: #0f172a;">' . htmlspecialchars($subject) . '</td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin: 20px 0;">
                        <h4 style="margin: 0 0 8px 0; font-size: 13px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Message / Query:</h4>
                        <div style="background: #f1f5f9; border-left: 4px solid #0f1754; padding: 14px 16px; border-radius: 4px; font-size: 14px; color: #334155; white-space: pre-line;">
                            ' . nl2br(htmlspecialchars($message)) . '
                        </div>
                    </div>

                    <div style="margin-top: 28px; text-align: center;">
                        <a href="mailto:' . htmlspecialchars($email) . '?subject=Re:%20' . rawurlencode($subject) . '" 
                           style="display: inline-block; background: #0f1754; color: #ffffff; text-decoration: none; padding: 12px 28px; font-weight: bold; font-size: 14px; border-radius: 8px; box-shadow: 0 2px 4px rgba(15,23,84,0.3);">
                            âœ‰ï¸ Click to Reply to ' . htmlspecialchars($name) . '
                        </a>
                    </div>
                    <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 12px;">You can directly click "Reply" in your email client to reply to ' . htmlspecialchars($email) . '.</p>
                </div>
                <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px; text-align: center; font-size: 12px; color: #94a3b8;">
                    Digambar Jain Parichay Sammelan Samiti &bull; Contact Inquiries
                </div>
            </div>';

            Mail::html($htmlBody, function ($mail) use ($adminEmail, $emailSubject, $email, $name) {
                $mail->to($adminEmail)
                     ->replyTo($email, $name)
                     ->subject($emailSubject);
            });
        } catch (\Exception $e) {
            logger()->error("Failed sending contact notification email: " . $e->getMessage());
        }

        return back()->with('success', 'Your message has been sent successfully. Our team will get back to you shortly.');
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

    /**
     * Display the Our Sarankshak page.
     */
    public function sarankshak()
    {
        $sarankshakMembers = \App\Models\SarankshakMember::where('status', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
            
        return view('cms.sarankshak', compact('sarankshakMembers'));
    }
}
