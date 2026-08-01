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
            'image' => 'nullable|image|max:10240', // 10MB max
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                try {
                    $file = $request->file('image');
                    $filename = time() . '_news_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $filename);
                    $imagePath = 'uploads/' . $filename;
                } catch (\Exception $uploadErr) {
                    $path = $request->file('image')->getRealPath();
                    $type = $request->file('image')->getClientMimeType();
                    $data = file_get_contents($path);
                    $imagePath = 'data:' . $type . ';base64,' . base64_encode($data);
                }
            }

            News::create([
                'title' => $request->title,
                'content' => $request->content,
                'image' => $imagePath,
                'status' => true,
            ]);

            return back()->with('success', 'Announcement posted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to publish announcement: ' . $e->getMessage());
        }
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

        $data = [
            'status' => true,
        ];

        // Fill notice_text and/or advertisement_text depending on existing DB columns
        if (\Illuminate\Support\Facades\Schema::hasColumn('marquee_ads', 'notice_text')) {
            $data['notice_text'] = $request->notice_text;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('marquee_ads', 'advertisement_text')) {
            $data['advertisement_text'] = $request->notice_text;
        }

        MarqueeAd::create($data);

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
            'image' => 'nullable|image|max:10240',
            'status' => 'nullable|boolean',
        ]);

        try {
            $updateData = [
                'title' => $request->title,
                'content' => $request->content,
                'status' => $request->has('status'),
            ];

            if ($request->hasFile('image')) {
                try {
                    $file = $request->file('image');
                    $filename = time() . '_news_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $filename);
                    $updateData['image'] = 'uploads/' . $filename;
                } catch (\Exception $uploadErr) {
                    $path = $request->file('image')->getRealPath();
                    $type = $request->file('image')->getClientMimeType();
                    $data = file_get_contents($path);
                    $updateData['image'] = 'data:' . $type . ';base64,' . base64_encode($data);
                }
            }

            $news->update($updateData);

            return back()->with('success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update announcement: ' . $e->getMessage());
        }
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

        $updateData = [
            'status' => $request->has('status'),
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('marquee_ads', 'notice_text')) {
            $updateData['notice_text'] = $request->notice_text;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('marquee_ads', 'advertisement_text')) {
            $updateData['advertisement_text'] = $request->notice_text;
        }

        $marquee->update($updateData);

        return back()->with('success', 'Marquee notice updated successfully.');
    }
}
