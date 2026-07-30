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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadDir = storage_path('app/public/uploads');

            if (!file_exists($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $filename = 'gallery_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

            try {
                $file->move($uploadDir, $filename);
                $imagePath = 'storage/uploads/' . $filename;
            } catch (\Exception $e) {
                // Fallback to Base64 encoding if disk move fails
                $type = $file->getClientMimeType();
                $data = file_get_contents($file->getRealPath());
                $imagePath = 'data:' . $type . ';base64,' . base64_encode($data);
            }
        }

        if (!$imagePath) {
            return back()->withInput()->with('error', 'Failed to process uploaded image.');
        }

        Gallery::create([
            'title' => trim($request->title),
            'category' => trim($request->category),
            'image_path' => $imagePath,
            'media_type' => 'image',
            'status' => true,
        ]);

        return back()->with('success', 'Photo added to gallery successfully.');
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
            'video_url' => 'nullable|url',
            'video_file' => 'nullable|url',
            'thumbnail' => 'nullable|url',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);

        if ($request->video_type === 'youtube' && empty($request->video_url)) {
            return back()->withInput()->with('error', 'YouTube URL is required when YouTube source is selected.');
        }

        if ($request->video_type === 'mp4' && empty($request->video_file)) {
            return back()->withInput()->with('error', 'MP4 File Link is required when MP4 source is selected.');
        }

        VideoGallery::create([
            'title' => trim($request->title),
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
