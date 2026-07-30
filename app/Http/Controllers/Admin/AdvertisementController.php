<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AdvertisementController extends Controller
{
    /**
     * Display listing of advertisements.
     */
    public function index()
    {
        // Dynamic schema check/alter
        if (!Schema::hasColumn('advertisements', 'media_type')) {
            Schema::table('advertisements', function (Blueprint $table) {
                $table->string('media_type', 20)->default('image')->after('image');
            });
        }

        $ads = Advertisement::orderBy('created_at', 'desc')->get();
        return view('admin.cms.ads.index', compact('ads'));
    }

    /**
     * Store new advertisement banner.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
            'position' => 'required|string|max:50',
            'image' => 'required|file|mimes:jpeg,jpg,png,webp,gif,mp4,webm|max:20480', // 20MB max for videos
        ]);

        $file = $request->file('image');
        $mime = $file->getClientMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $is_video = str_contains($mime, 'video') || in_array($ext, ['mp4', 'webm']);

        $upload_dir = public_path('uploads/ads');
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = 'ad_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move($upload_dir, $filename);
        $dbPath = 'uploads/ads/' . $filename;

        Advertisement::create([
            'title' => $request->title,
            'link' => $request->link,
            'position' => $request->position,
            'image' => $dbPath,
            'media_type' => $is_video ? 'video' : 'image',
            'status' => true,
        ]);

        return back()->with('success', 'Advertisement saved successfully.');
    }

    /**
     * Update the specified advertisement.
     */
    public function update(Request $request, Advertisement $ad)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
            'position' => 'required|string|max:50',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif,mp4,webm|max:20480',
            'status' => 'nullable|boolean',
        ]);

        $updateData = [
            'title' => $request->title,
            'link' => $request->link,
            'position' => $request->position,
            'status' => $request->has('status'),
        ];

        if ($request->hasFile('image')) {
            // Delete old file
            if ($ad->image && file_exists(public_path($ad->image))) {
                @unlink(public_path($ad->image));
            }

            $file = $request->file('image');
            $mime = $file->getClientMimeType();
            $ext = strtolower($file->getClientOriginalExtension());
            $is_video = str_contains($mime, 'video') || in_array($ext, ['mp4', 'webm']);

            $upload_dir = public_path('uploads/ads');
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filename = 'ad_' . time() . '_' . uniqid() . '.' . $ext;
            $file->move($upload_dir, $filename);
            
            $updateData['image'] = 'uploads/ads/' . $filename;
            $updateData['media_type'] = $is_video ? 'video' : 'image';
        }

        $ad->update($updateData);

        return back()->with('success', 'Advertisement updated successfully.');
    }

    /**
     * Toggle advertisement status.
     */
    public function toggle(Advertisement $ad)
    {
        $ad->update(['status' => !$ad->status]);
        return back()->with('success', 'Advertisement status toggled.');
    }

    /**
     * Delete advertisement.
     */
    public function destroy(Advertisement $ad)
    {
        if ($ad->image && file_exists(public_path($ad->image))) {
            @unlink(public_path($ad->image));
        }
        $ad->delete();
        return back()->with('success', 'Advertisement deleted.');
    }
}
