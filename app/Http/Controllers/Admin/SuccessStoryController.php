<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;

class SuccessStoryController extends Controller
{
    /**
     * List all success stories.
     */
    public function index()
    {
        $stories = SuccessStory::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.cms.stories.index', compact('stories'));
    }

    /**
     * Update status (approve/reject).
     */
    public function updateStatus(Request $request, SuccessStory $story)
    {
        $request->validate([
            'status' => 'required|in:pending,approved',
        ]);

        $story->update(['status' => $request->status]);

        return back()->with('success', 'Success story status updated successfully.');
    }

    /**
     * Delete success story.
     */
    public function destroy(SuccessStory $story)
    {
        $story->delete();
        return back()->with('success', 'Success story deleted successfully.');
    }
}
