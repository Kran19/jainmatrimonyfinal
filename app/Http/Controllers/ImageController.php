<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Stream base64 database images as binary responses.
     */
    public function serve(Request $request)
    {
        $filePath = $request->query('file');

        if (empty($filePath)) {
            return abort(404);
        }

        // 1. If it's a base64 data string
        if (Str::startsWith($filePath, 'data:')) {
            try {
                list($type, $data) = explode(';', $filePath);
                list(, $data) = explode(',', $data);
                $binaryData = base64_decode($data);
                $mime = str_replace('data:', '', $type);

                return response($binaryData)
                    ->header('Content-Type', $mime)
                    ->header('Cache-Control', 'public, max-age=86400');
            } catch (\Exception $e) {
                return abort(404);
            }
        }

        // Apply security checks for sensitive candidate files
        $isSensitive = false;
        if (Str::contains($filePath, ['_photo_', '_family_', '_idproof_', '_payment_', 'profile_photos', 'receipts', 'payment_screenshot'])) {
            $isSensitive = true;
        }

        if ($isSensitive) {
            $isAdmin = \Illuminate\Support\Facades\Auth::guard('admin')->check();
            $isUserApproved = \Illuminate\Support\Facades\Auth::guard('web')->check() && 
                              \Illuminate\Support\Facades\Auth::guard('web')->user()->status === 'approved';
            
            if (!$isAdmin && !$isUserApproved) {
                return abort(403, 'Unauthorized access to candidate media.');
            }
        }

        // 2. If it's a local relative file path
        $safePath = str_replace(['..', '\\'], ['', '/'], $filePath);
        
        // Try multiple paths to locate the file, especially since new storage is in storage/app/public
        $possiblePaths = [
            public_path($safePath),
            storage_path('app/public/' . str_replace('storage/', '', $safePath)),
            storage_path('app/' . $safePath),
            base_path($safePath)
        ];

        $foundPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_file($path)) {
                $foundPath = $path;
                break;
            }
        }

        if ($foundPath) {
            $mime = mime_content_type($foundPath);
            
            $ext = strtolower(pathinfo($foundPath, PATHINFO_EXTENSION));
            if ($ext === 'mp4') {
                $mime = 'video/mp4';
            } elseif ($ext === 'webm') {
                $mime = 'video/webm';
            } elseif ($ext === 'ogv') {
                $mime = 'video/ogg';
            }

            return response()->file($foundPath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400'
            ]);
        }

        return abort(404);
    }
}
