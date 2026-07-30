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
        // Dynamic schema check/alter to self-heal columns
        if (Schema::hasTable('advertisements')) {
            if (!Schema::hasColumn('advertisements', 'media_type')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->string('media_type', 20)->default('image');
                });
            }
            if (!Schema::hasColumn('advertisements', 'sort_order')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->integer('sort_order')->default(0);
                });
            }
            if (!Schema::hasColumn('advertisements', 'duration_seconds')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->integer('duration_seconds')->default(3);
                });
            }
            if (!Schema::hasColumn('advertisements', 'updated_at')) {
                Schema::table('advertisements', function (Blueprint $table) {
                    $table->timestamp('updated_at')->nullable();
                });
            }

            // Remove printmines.com redirection links automatically
            \Illuminate\Support\Facades\DB::table('advertisements')
                ->where('link', 'like', '%printmines%')
                ->update(['link' => null]);
        }

        $ads = Advertisement::orderBy('position', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.cms.ads.index', compact('ads'));
    }

    /**
     * Store new advertisement campaign.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
            'position' => 'required|string|in:left_sidebar,right_sidebar,bottom_banner,home_top,home_bottom,sidebar',
            'image' => 'required|file|mimes:jpeg,jpg,png,webp,gif,mp4,webm,mov,avi|max:20480',
            'sort_order' => 'nullable|integer|min:0',
            'duration_seconds' => 'nullable|integer|min:1|max:60',
        ]);

        $file = $request->file('image');
        $mime = $file->getClientMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $is_video = str_contains($mime, 'video') || in_array($ext, ['mp4', 'webm', 'mov', 'avi']);

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
            'sort_order' => $request->input('sort_order', 0),
            'duration_seconds' => $request->input('duration_seconds', 3),
            'status' => true,
        ]);

        return back()->with('success', 'Advertisement campaign published successfully.');
    }

    /**
     * Update the specified advertisement.
     */
    public function update(Request $request, Advertisement $ad)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'nullable|url|max:255',
            'position' => 'required|string|in:left_sidebar,right_sidebar,bottom_banner,home_top,home_bottom,sidebar',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp,gif,mp4,webm,mov,avi|max:20480',
            'sort_order' => 'nullable|integer|min:0',
            'duration_seconds' => 'nullable|integer|min:1|max:60',
            'status' => 'nullable|boolean',
        ]);

        $updateData = [
            'title' => $request->title,
            'link' => $request->link,
            'position' => $request->position,
            'sort_order' => $request->input('sort_order', 0),
            'duration_seconds' => $request->input('duration_seconds', 3),
            'status' => $request->has('status'),
        ];

        if ($request->hasFile('image')) {
            // Delete old media file
            if ($ad->image && file_exists(public_path($ad->image))) {
                @unlink(public_path($ad->image));
            }

            $file = $request->file('image');
            $mime = $file->getClientMimeType();
            $ext = strtolower($file->getClientOriginalExtension());
            $is_video = str_contains($mime, 'video') || in_array($ext, ['mp4', 'webm', 'mov', 'avi']);

            $upload_dir = public_path('uploads/ads');
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filename = 'ad_' . time() . '_' . uniqid() . '.' . $ext;
            $file->move($upload_dir, $filename);
            
            $updateData['image'] = 'uploads/ads/' . $filename;
            $updateData['media_type'] = $is_video ? 'video' : 'image';
        }

        \Illuminate\Support\Facades\DB::table('advertisements')->where('id', $ad->id)->update($updateData);

        return back()->with('success', 'Advertisement campaign updated successfully.');
    }

    /**
     * Toggle advertisement active status.
     */
    public function toggle(Advertisement $ad)
    {
        \Illuminate\Support\Facades\DB::table('advertisements')->where('id', $ad->id)->update(['status' => !$ad->status]);
        return back()->with('success', 'Advertisement status toggled successfully.');
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
        return back()->with('success', 'Advertisement deleted successfully.');
    }
}
