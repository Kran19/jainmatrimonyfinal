<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    /**
     * Serve and stream images and media files securely.
     */
    public function serve(Request $request)
    {
        $filePath = urldecode($request->query('file'));

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

        // Apply strict security checks ONLY for highly sensitive documents (ID Proofs, Payment Screenshots)
        $isSensitiveDocument = false;
        if (Str::contains($filePath, ['_idproof_', '_payment_', 'idproof', 'payment_screenshot', 'payment_proofs', 'receipts'])) {
            $isSensitiveDocument = true;
        }

        if ($isSensitiveDocument) {
            $isAdmin = Auth::guard('admin')->check();
            $isUserApproved = Auth::guard('web')->check() && 
                              Auth::guard('web')->user()->status === 'approved';
            
            if (!$isAdmin && !$isUserApproved) {
                return abort(403, 'Unauthorized access to confidential candidate media.');
            }
        }

        // 2. Normalize relative file path
        $safePath = str_replace(['..', '\\'], ['', '/'], $filePath);
        $safePath = ltrim($safePath, '/');
        
        $cleanPathNoStorage = preg_replace('#^(storage/|public/)#', '', $safePath);
        $cleanPathNoUploads = preg_replace('#^(uploads/|storage/uploads/|public/uploads/)#', '', $safePath);
        $cleanPathNoImports = preg_replace('#^(imports/|private/imports/|public/imports/)#', '', $safePath);

        // Multiple locations to search for the file
        $possiblePaths = [
            // Private Storage (Secure imports and private uploads)
            storage_path('app/private/' . $safePath),
            storage_path('app/private/imports/' . $cleanPathNoImports),
            storage_path('app/private/uploads/' . $cleanPathNoUploads),

            // Public Storage (New uploaded files)
            storage_path('app/public/' . $safePath),
            storage_path('app/public/' . $cleanPathNoStorage),
            storage_path('app/public/uploads/' . $cleanPathNoUploads),

            // Legacy Public Directory
            public_path($safePath),
            public_path('uploads/' . $cleanPathNoUploads),
            public_path('imports/' . $cleanPathNoImports),

            // General Storage Root
            storage_path('app/' . $safePath),
            base_path($safePath),
            base_path('../digambar-samaj/' . $safePath),
            base_path('../digambar-samaj/public/' . $safePath),
        ];

        $foundPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_file($path)) {
                $foundPath = $path;
                break;
            }
        }

        // 3. Smart Glob Fallback if exact filename fails (e.g. timestamp mismatches like 1785322671 vs 1785362050)
        if (!$foundPath) {
            $filename = basename($safePath);
            $searchPattern = null;

            if (preg_match('/_photo_(.+)$/', $filename, $matches)) {
                $searchPattern = '*_photo_*' . $matches[1];
            } elseif (preg_match('/_family_(.+)$/', $filename, $matches)) {
                $searchPattern = '*_family_*' . $matches[1];
            } elseif (preg_match('/_idproof_(.+)$/', $filename, $matches)) {
                $searchPattern = '*_idproof_*' . $matches[1];
            } elseif (preg_match('/_payment_(.+)$/', $filename, $matches)) {
                $searchPattern = '*_payment_*' . $matches[1];
            }

            if ($searchPattern) {
                $searchDirs = [
                    storage_path('app/public/uploads'),
                    storage_path('app/private/imports/profile_photos'),
                    storage_path('app/private/imports/payment_proofs'),
                    public_path('uploads'),
                    public_path('imports/profile_photos'),
                    public_path('imports/payment_proofs'),
                ];

                foreach ($searchDirs as $dir) {
                    if (is_dir($dir)) {
                        $matches = glob($dir . '/' . $searchPattern);
                        if (!empty($matches) && is_file($matches[0])) {
                            $foundPath = $matches[0];
                            break;
                        }
                    }
                }
            }
        }

        if ($foundPath) {
            $mime = @mime_content_type($foundPath) ?: 'application/octet-stream';
            
            $ext = strtolower(pathinfo($foundPath, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg'])) {
                $mime = 'image/jpeg';
            } elseif ($ext === 'png') {
                $mime = 'image/png';
            } elseif ($ext === 'webp') {
                $mime = 'image/webp';
            } elseif ($ext === 'pdf') {
                $mime = 'application/pdf';
            } elseif ($ext === 'mp4') {
                $mime = 'video/mp4';
            } elseif ($ext === 'webm') {
                $mime = 'video/webm';
            } elseif ($ext === 'ogv') {
                $mime = 'video/ogg';
            }

            return response()->file($foundPath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
                'Access-Control-Allow-Origin' => '*'
            ]);
        }

        return abort(404);
    }
}
