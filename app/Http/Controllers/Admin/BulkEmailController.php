<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class BulkEmailController extends Controller
{
    /**
     * Display bulk email form.
     */
    public function index()
    {
        $users = User::whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('full_name', 'asc')
            ->select('id', 'full_name', 'email')
            ->get();

        return view('admin.cms.bulk-email', compact('users'));
    }

    /**
     * Send bulk email.
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'users' => 'required|array|min:1',
            'users.*' => 'required|email',
        ]);

        $subject = $request->subject;
        $messageBody = $request->message;
        $emails = $request->users;

        $successCount = 0;
        $failCount = 0;

        foreach ($emails as $email) {
            if (\App\Services\EmailService::sendHtml($email, $subject, $messageBody)) {
                $successCount++;
            } else {
                $failCount++;
            }
            // Add a tiny delay to avoid spam/rate limits
            usleep(300000); // 0.3 seconds
        }

        return back()->with('success', "Bulk emails sent successfully to {$successCount} users. Failed: {$failCount}.");
    }
}
