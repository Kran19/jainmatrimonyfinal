<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\MarqueeAd;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display listing of news and marquee ads.
     */
    public function index()
    {
        $newsItems = News::orderBy('created_at', 'desc')->paginate(10, ['*'], 'news_page');
        $marqueeItems = MarqueeAd::orderBy('created_at', 'desc')->paginate(5, ['*'], 'marquee_page');

        return view('admin.cms.news.index', compact('newsItems', 'marqueeItems'));
    }

    /**
     * Store a new announcement.
     */
    public function storeNews(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048', // 2MB max
        ]);

        $base64Image = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->getRealPath();
            $type = $request->file('image')->getClientMimeType();
            $data = file_get_contents($path);
            $base64Image = 'data:' . $type . ';base64,' . base64_encode($data);
        }

        News::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $base64Image,
            'status' => true,
        ]);

        return back()->with('success', 'Announcement posted successfully.');
    }

    /**
     * Toggle news status.
     */
    public function toggleNews(News $news)
    {
        $news->update(['status' => !$news->status]);
        return back()->with('success', 'Announcement status updated.');
    }

    /**
     * Delete news announcement.
     */
    public function destroyNews(News $news)
    {
        $news->delete();
        return back()->with('success', 'Announcement deleted.');
    }

    /**
     * Store a marquee notice.
     */
    public function storeMarquee(Request $request)
    {
        $request->validate([
            'notice_text' => 'required|string|max:500',
        ]);

        MarqueeAd::create([
            'notice_text' => $request->notice_text,
            'status' => true,
        ]);

        return back()->with('success', 'Marquee notice added successfully.');
    }

    /**
     * Toggle marquee notice status.
     */
    public function toggleMarquee(MarqueeAd $marquee)
    {
        $marquee->update(['status' => !$marquee->status]);
        return back()->with('success', 'Marquee notice status updated.');
    }

    /**
     * Delete marquee notice.
     */
    public function destroyMarquee(MarqueeAd $marquee)
    {
        $marquee->delete();
        return back()->with('success', 'Marquee notice deleted.');
    }

    /**
     * Update the news announcement.
     */
    public function updateNews(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        $updateData = [
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->has('status'),
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->getRealPath();
            $type = $request->file('image')->getClientMimeType();
            $data = file_get_contents($path);
            $updateData['image'] = 'data:' . $type . ';base64,' . base64_encode($data);
        }

        $news->update($updateData);

        return back()->with('success', 'Announcement updated successfully.');
    }

    /**
     * Update the marquee notice.
     */
    public function updateMarquee(Request $request, MarqueeAd $marquee)
    {
        $request->validate([
            'notice_text' => 'required|string|max:500',
            'status' => 'nullable|boolean',
        ]);

        $marquee->update([
            'notice_text' => $request->notice_text,
            'status' => $request->has('status'),
        ]);

        return back()->with('success', 'Marquee notice updated successfully.');
    }
}
