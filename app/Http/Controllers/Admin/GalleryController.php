<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\VideoGallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display listing of media gallery items.
     */
    public function index()
    {
        $galleryItems = Gallery::orderBy('created_at', 'desc')->paginate(9, ['*'], 'photo_page');
        $videoItems = VideoGallery::orderBy('display_order', 'asc')->paginate(6, ['*'], 'video_page');

        return view('admin.cms.gallery.index', compact('galleryItems', 'videoItems'));
    }

    /**
     * Store new Photo Gallery item.
     */
    public function storePhoto(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'image' => 'required|image|max:2048', // 2MB max
        ]);

        $base64Image = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->getRealPath();
            $type = $request->file('image')->getClientMimeType();
            $data = file_get_contents($path);
            $base64Image = 'data:' . $type . ';base64,' . base64_encode($data);
        }

        Gallery::create([
            'title' => $request->title,
            'category' => $request->category,
            'image_path' => $base64Image,
            'media_type' => 'image',
            'status' => true,
        ]);

        return back()->with('success', 'Photo added to gallery.');
    }

    /**
     * Toggle photo status.
     */
    public function togglePhoto(Gallery $photo)
    {
        $photo->update(['status' => !$photo->status]);
        return back()->with('success', 'Photo status updated.');
    }

    /**
     * Delete photo from gallery.
     */
    public function destroyPhoto(Gallery $photo)
    {
        $photo->delete();
        return back()->with('success', 'Photo removed from gallery.');
    }

    /**
     * Store new Video item.
     */
    public function storeVideo(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video_type' => 'required|in:youtube,mp4',
            'video_url' => 'required_if:video_type,youtube|url|nullable',
            'video_file' => 'required_if:video_type,mp4|url|nullable',
            'thumbnail' => 'nullable|url',
            'description' => 'nullable|string',
            'display_order' => 'integer',
        ]);

        VideoGallery::create([
            'title' => $request->title,
            'video_type' => $request->video_type,
            'video_url' => $request->video_url,
            'video_file' => $request->video_file,
            'thumbnail' => $request->thumbnail,
            'description' => $request->description,
            'display_order' => $request->display_order ?? 0,
            'status' => 'active',
        ]);

        return back()->with('success', 'Video link saved successfully.');
    }

    /**
     * Toggle video status.
     */
    public function toggleVideo(VideoGallery $video)
    {
        $newStatus = $video->status === 'active' ? 'inactive' : 'active';
        $video->update(['status' => $newStatus]);
        return back()->with('success', 'Video status updated.');
    }

    /**
     * Delete video.
     */
    public function destroyVideo(VideoGallery $video)
    {
        $video->delete();
        return back()->with('success', 'Video deleted.');
    }
}
