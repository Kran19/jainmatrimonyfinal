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
        $rawFile = $request->query('file');

        if (empty($rawFile)) {
            return $this->fallbackNotFound();
        }

        $filePath = urldecode($rawFile);

        // 1. Base64 data strings
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
                return $this->fallbackNotFound();
            }
        }

        // Apply strict security checks ONLY for highly sensitive documents (ID Proofs, Payment Screenshots)
        $isSensitiveDocument = false;
        if (Str::contains($filePath, ['_idproof_', '_payment_', 'idproof', 'payment_screenshot', 'payment_proofs', 'receipts'])) {
            $isSensitiveDocument = true;
        }

        if ($isSensitiveDocument) {
            $isAdmin = Auth::guard('admin')->check();
            $webUser = Auth::guard('web')->user();
            
            $isOwner = false;
            if ($webUser) {
                $filename = basename($filePath);
                if (!empty($webUser->payment_screenshot) && Str::contains($webUser->payment_screenshot, $filename)) {
                    $isOwner = true;
                } elseif (!empty($webUser->id_proof_path) && Str::contains($webUser->id_proof_path, $filename)) {
                    $isOwner = true;
                } elseif (!empty($webUser->profile_photo) && Str::contains($webUser->profile_photo, $filename)) {
                    $isOwner = true;
                } elseif (Str::contains($filename, 'payment_' . $webUser->id . '_') || Str::contains($filename, 'idproof_' . $webUser->id . '_')) {
                    $isOwner = true;
                }
            }

            $isUserApproved = $webUser && $webUser->status === 'approved';
            
            if (!$isAdmin && !$isUserApproved && !$isOwner) {
                return abort(403, 'Unauthorized access to confidential candidate media.');
            }
        }

        // 2. Normalize relative file path
        $safePath = str_replace(['..', '\\'], ['', '/'], $filePath);
        $safePath = ltrim($safePath, '/');
        
        $cleanPathNoStorage = preg_replace('#^(storage/|public/)#i', '', $safePath);
        $cleanPathNoUploads = preg_replace('#^(uploads/|storage/uploads/|public/uploads/)#i', '', $safePath);
        $cleanPathNoImports = preg_replace('#^(imports/|private/imports/|public/imports/)#i', '', $safePath);
        $filename = basename($safePath);

        // Directories to search in priority order
        $searchDirs = [
            public_path('uploads/receipts'),
            public_path('uploads'),
            storage_path('app/public/uploads/receipts'),
            storage_path('app/public/uploads'),
            storage_path('app/private/uploads/receipts'),
            storage_path('app/private/uploads'),
            storage_path('app/private/imports/profile_photos'),
            storage_path('app/private/imports/family_photos'),
            storage_path('app/private/imports/payment_proofs'),
            storage_path('app/private/imports'),
            storage_path('app/private'),
            storage_path('app/public'),
            public_path('imports/profile_photos'),
            public_path('imports/payment_proofs'),
            public_path('imports'),
            public_path(),
            base_path('uploads/receipts'),
            base_path('uploads'),
            base_path('../digambar-samaj/uploads/receipts'),
            base_path('../digambar-samaj/uploads'),
            base_path('../digambar-samaj'),
            base_path(),
        ];

        // 3. Resolve media file path across all local & production storage locations
        $foundPath = resolve_media_path($filePath);

        // 4. Case-insensitive and timestamp-independent search fallback if needed
        if (!$foundPath) {
            foreach ($searchDirs as $dir) {
                $matched = $this->findMatchingFile($dir, $filename);
                if ($matched) {
                    $foundPath = $matched;
                    break;
                }
            }
        }

        if ($foundPath && is_file($foundPath)) {
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

        return $this->fallbackNotFound();
    }

    /**
     * Case-insensitive and partial suffix file finder for legacy / timestamped uploads.
     */
    private function findMatchingFile($dir, $filename)
    {
        if (!is_dir($dir)) {
            return null;
        }

        $dh = @opendir($dir);
        if (!$dh) {
            return null;
        }

        $targetLower = strtolower($filename);
        
        // Extract suffix after _photo_, _family_, _idproof_, _payment_
        $cleanSuffix = null;
        if (preg_match('/_(photo|family|idproof|payment)_(.+)$/i', $filename, $m)) {
            $cleanSuffix = strtolower($m[2]);
        } elseif (preg_match('/^([0-9]+)_(.+)$/', $filename, $m)) {
            $cleanSuffix = strtolower($m[2]);
        }

        $bestMatch = null;
        while (($file = readdir($dh)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $dir . '/' . $file;
            if (!is_file($filePath)) {
                continue;
            }

            $fileLower = strtolower($file);

            // Exact case-insensitive match
            if ($fileLower === $targetLower) {
                closedir($dh);
                return $filePath;
            }

            // Suffix match if timestamp prefix differs
            if ($cleanSuffix && strpos($fileLower, $cleanSuffix) !== false) {
                $bestMatch = $filePath;
            }
        }
        closedir($dh);

        return $bestMatch;
    }

    /**
     * Fallback when file is truly missing (returns default SVG avatar or 404).
     */
    private function fallbackNotFound()
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>';
        
        return response($svg, 404)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache');
    }
}
