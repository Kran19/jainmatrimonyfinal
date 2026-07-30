<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AdvertisementController extends Controller
{
    /**
     * Self-healing DB schema helper to guarantee all missing columns are added in production.
     */
    protected function ensureSchema()
    {
        if (Schema::hasTable('advertisements')) {
            if (!Schema::hasColumn('advertisements', 'updated_at')) {
                try {
                    Schema::table('advertisements', function (Blueprint $table) {
                        $table->timestamp('updated_at')->nullable();
                    });
                } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('advertisements', 'media_type')) {
                try {
                    Schema::table('advertisements', function (Blueprint $table) {
                        $table->string('media_type', 20)->default('image');
                    });
                } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('advertisements', 'sort_order')) {
                try {
                    Schema::table('advertisements', function (Blueprint $table) {
                        $table->integer('sort_order')->default(0);
                    });
                } catch (\Exception $e) {}
            }
            if (!Schema::hasColumn('advertisements', 'duration_seconds')) {
                try {
                    Schema::table('advertisements', function (Blueprint $table) {
                        $table->integer('duration_seconds')->default(3);
                    });
                } catch (\Exception $e) {}
            }

            // Remove printmines.com redirection links automatically
            try {
                DB::table('advertisements')
                    ->where('link', 'like', '%printmines%')
                    ->update(['link' => null]);
            } catch (\Exception $e) {}
        }
    }

    /**
     * Display listing of advertisements.
     */
    public function index()
    {
        $this->ensureSchema();

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
        $this->ensureSchema();

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

        $insertData = [
            'title' => $request->title,
            'link' => $request->link,
            'position' => $request->position,
            'image' => $dbPath,
            'media_type' => $is_video ? 'video' : 'image',
            'sort_order' => $request->input('sort_order', 0),
            'duration_seconds' => $request->input('duration_seconds', 3),
            'status' => true,
        ];
        if (Schema::hasColumn('advertisements', 'created_at')) {
            $insertData['created_at'] = now();
        }
        if (Schema::hasColumn('advertisements', 'updated_at')) {
            $insertData['updated_at'] = now();
        }

        DB::table('advertisements')->insert($insertData);

        return back()->with('success', 'Advertisement campaign published successfully.');
    }

    /**
     * Update the specified advertisement.
     */
    public function update(Request $request, $id)
    {
        $this->ensureSchema();

        $ad = Advertisement::findOrFail($id);

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

        if (Schema::hasColumn('advertisements', 'updated_at')) {
            $updateData['updated_at'] = now();
        }

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

        DB::table('advertisements')->where('id', $ad->id)->update($updateData);

        return back()->with('success', 'Advertisement campaign updated successfully.');
    }

    /**
     * Toggle advertisement active status.
     */
    public function toggle($id)
    {
        $this->ensureSchema();

        $ad = Advertisement::findOrFail($id);
        $updateData = ['status' => !$ad->status];
        if (Schema::hasColumn('advertisements', 'updated_at')) {
            $updateData['updated_at'] = now();
        }
        DB::table('advertisements')->where('id', $ad->id)->update($updateData);

        return back()->with('success', 'Advertisement status toggled successfully.');
    }

    /**
     * Delete advertisement.
     */
    public function destroy($id)
    {
        $this->ensureSchema();

        $ad = Advertisement::findOrFail($id);
        if ($ad->image && file_exists(public_path($ad->image))) {
            @unlink(public_path($ad->image));
        }
        $ad->delete();
        return back()->with('success', 'Advertisement deleted successfully.');
    }
}
