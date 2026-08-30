<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages with status highlights and filters.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // Status Filter
        $statusFilter = $request->input('status');
        if ($statusFilter === 'pending') {
            $query->where(function ($q) {
                $q->whereIn('status', ['unread', 'read', 'pending'])
                  ->orWhereNull('status')
                  ->orWhere('status', '');
            });
        } elseif ($statusFilter === 'responded') {
            $query->where(function ($q) {
                $q->whereIn('status', ['replied', 'responded']);
            });
        }

        // Search Filter
        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%")
                  ->orWhere('message', 'like', "%{$s}%");
            });
        }

        // Sorting: pending / unread messages at the top, then recent first
        $messages = $query->orderByRaw("CASE WHEN status = 'replied' OR status = 'responded' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Statistics
        $totalCount = ContactMessage::count();
        $pendingCount = ContactMessage::where(function ($q) {
            $q->whereIn('status', ['unread', 'read', 'pending'])
              ->orWhereNull('status')
              ->orWhere('status', '');
        })->count();
        $respondedCount = ContactMessage::where(function ($q) {
            $q->whereIn('status', ['replied', 'responded']);
        })->count();

        return view('admin.cms.contacts', compact('messages', 'totalCount', 'pendingCount', 'respondedCount', 'statusFilter'));
    }

    /**
     * Toggle or update the response status of a contact message.
     */
    public function toggleStatus(Request $request, $id)
    {
        $msg = ContactMessage::findOrFail($id);

        if ($request->has('status')) {
            $newStatus = $request->input('status');
        } else {
            // Auto toggle: if replied, switch back to unread; otherwise mark replied
            $current = $msg->status ?: 'unread';
            $newStatus = ($current === 'replied' || $current === 'responded') ? 'unread' : 'replied';
        }

        $msg->status = $newStatus;
        if ($request->filled('reply_text')) {
            $msg->reply_text = $request->input('reply_text');
        }
        $msg->save();

        $statusLabel = ($newStatus === 'replied' || $newStatus === 'responded') ? 'Responded / Replied (उत्तर दिया गया)' : 'Pending Response (उत्तर बाकी)';

        return back()->with('success', "Query status updated to: {$statusLabel}");
    }

    /**
     * Remove the specified contact message.
     */
    public function destroy($id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Contact message deleted successfully.');
    }
}
