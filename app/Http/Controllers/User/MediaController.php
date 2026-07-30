<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gallery;
use App\Models\VideoGallery;
use App\Models\SuccessStory;

class MediaController extends Controller
{
    /**
     * Display photo, PDF, and video galleries.
     */
    public function gallery()
    {
        // 1. Fetch approved gallery items
        $galleryItems = Gallery::where('status', true)->orderBy('created_at', 'desc')->get();

        $photos = [];
        $pdfs = [];
        $galleryVideos = [];

        foreach ($galleryItems as $item) {
            $type = $item->media_type ?? 'image';
            if ($type === 'image' || empty($type)) {
                $photos[] = $item;
            } elseif ($type === 'pdf') {
                $pdfs[] = $item;
            } elseif ($type === 'video' || $type === 'youtube') {
                $galleryVideos[] = $item;
            }
        }

        // 2. Fetch standalone video gallery items
        $videos = [];
        try {
            $standaloneVideos = VideoGallery::where('status', 'active')
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($standaloneVideos as $vid) {
                $videos[] = [
                    'id' => $vid->id,
                    'source' => 'standalone',
                    'title' => $vid->title,
                    'video_type' => $vid->video_type,
                    'video_url' => $vid->video_url,
                    'video_file' => $vid->video_file,
                    'thumbnail' => $vid->thumbnail,
                    'description' => $vid->description,
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist or be empty
        }

        // 3. Merge gallery videos
        foreach ($galleryVideos as $gv) {
            $videos[] = [
                'id' => $gv->id,
                'source' => 'gallery',
                'title' => $gv->title ?: 'Video',
                'video_type' => $gv->media_type === 'video' ? 'mp4' : 'youtube',
                'video_url' => $gv->media_url,
                'video_file' => str_replace('../', '', $gv->image_path ?? ''),
                'thumbnail' => '',
                'description' => $gv->category ?? '',
            ];
        }

        return view('user.gallery', compact('photos', 'pdfs', 'videos'));
    }

    /**
     * Display approved success stories.
     */
    public function successStories()
    {
        $stories = SuccessStory::where('status', 'approved')->orderBy('id', 'desc')->get();
        return view('user.success-stories', compact('stories'));
    }

    /**
     * Show form to submit success story.
     */
    public function addSuccessStory()
    {
        // Enforce user is approved (handled by middleware but let's double check for safety)
        if (Auth::user()->status !== 'approved') {
            return redirect()->route('waiting.approval');
        }

        return view('user.add-success-story');
    }

    /**
     * Store new success story with Base64 couple photo.
     */
    public function storeSuccessStory(Request $request)
    {
        if (Auth::user()->status !== 'approved') {
            return redirect()->route('waiting.approval');
        }

        $request->validate([
            'couple_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'story' => 'required|string',
            'photo' => 'required|image|max:10240', // Max 10MB
        ]);

        // Process photo to Base64 Data URI
        $file = $request->file('photo');
        $imageBytes = file_get_contents($file->getRealPath());
        $mimeType = $file->getClientMimeType();
        $base64 = base64_encode($imageBytes);
        $photoBase64 = 'data:' . $mimeType . ';base64,' . $base64;

        SuccessStory::create([
            'user_id' => Auth::id(),
            'couple_name' => $request->couple_name,
            'city' => $request->city,
            'story' => $request->story,
            'photo' => $photoBase64,
            'status' => 'pending',
        ]);

        return redirect()->route('stories')->with('success', 'Your success story has been submitted successfully! It will appear on the website once approved by the admin.');
    }
}
