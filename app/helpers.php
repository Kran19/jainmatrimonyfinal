<?php

if (!function_exists('resolve_media_path')) {
    /**
     * Resolve the absolute filesystem path for an image or media file across local and production storage locations.
     *
     * @param string|null $filePath
     * @return string|null
     */
    function resolve_media_path(?string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        if (str_starts_with($filePath, 'data:image/')) {
            return $filePath;
        }

        $safePath = str_replace(['..', '\\'], ['', '/'], urldecode($filePath));
        $safePath = ltrim($safePath, '/');

        $cleanPathNoStorage = preg_replace('#^(storage/|public/)#i', '', $safePath);
        $cleanPathNoUploads = preg_replace('#^(uploads/|storage/uploads/|public/uploads/)#i', '', $safePath);
        $cleanPathNoImports = preg_replace('#^(imports/|private/imports/|public/imports/)#i', '', $safePath);
        $filename = basename($safePath);

        $possibleExactPaths = [
            public_path($safePath),
            storage_path('app/private/' . $safePath),
            storage_path('app/private/uploads/receipts/' . $filename),
            storage_path('app/private/uploads/' . $cleanPathNoUploads),
            storage_path('app/private/imports/' . $cleanPathNoImports),
            storage_path('app/private/imports/profile_photos/' . $filename),
            storage_path('app/private/imports/family_photos/' . $filename),
            storage_path('app/private/imports/payment_proofs/' . $filename),
            storage_path('app/public/' . $safePath),
            storage_path('app/public/' . $cleanPathNoStorage),
            storage_path('app/public/uploads/' . $cleanPathNoUploads),
            storage_path('app/public/uploads/receipts/' . $filename),
            public_path('uploads/' . $cleanPathNoUploads),
            public_path('uploads/receipts/' . $filename),
            public_path('imports/' . $cleanPathNoImports),
            public_path('imports/profile_photos/' . $filename),
            public_path('imports/family_photos/' . $filename),
            public_path('imports/payment_proofs/' . $filename),
            base_path($safePath),
            base_path('uploads/' . $cleanPathNoUploads),
            base_path('uploads/receipts/' . $filename),
            base_path('../digambar-samaj/' . $safePath),
            base_path('../digambar-samaj/uploads/' . $cleanPathNoUploads),
            base_path('../digambar-samaj/uploads/receipts/' . $filename),
        ];

        foreach ($possibleExactPaths as $path) {
            if (!empty($path) && file_exists($path) && is_file($path)) {
                return $path;
            }
        }

        // Direct search in common directories by filename
        $searchDirs = [
            storage_path('app/public/uploads/receipts'),
            storage_path('app/public/uploads'),
            storage_path('app/public'),
            storage_path('app/private/uploads/receipts'),
            storage_path('app/private/uploads'),
            storage_path('app/private/imports/profile_photos'),
            storage_path('app/private/imports/family_photos'),
            storage_path('app/private/imports/payment_proofs'),
            storage_path('app/private/imports'),
            storage_path('app/private'),
            public_path('uploads/receipts'),
            public_path('uploads'),
            public_path('imports/profile_photos'),
            public_path('imports/family_photos'),
            public_path('imports/payment_proofs'),
            public_path('imports'),
            public_path(),
            base_path('uploads/receipts'),
            base_path('uploads'),
            base_path('../digambar-samaj/uploads/receipts'),
            base_path('../digambar-samaj/uploads'),
            base_path('../digambar-samaj'),
        ];

        $filenameLower = strtolower($filename);
        foreach ($searchDirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            $targetFile = $dir . '/' . $filename;
            if (file_exists($targetFile) && is_file($targetFile)) {
                return $targetFile;
            }
            $files = @scandir($dir);
            if ($files) {
                foreach ($files as $f) {
                    if (strtolower($f) === $filenameLower) {
                        $candidate = $dir . '/' . $f;
                        if (is_file($candidate)) {
                            return $candidate;
                        }
                    }
                }
            }
        }

        return null;
    }
}

if (!function_exists('format_weight')) {
    /**
     * Clean and format weight value ensuring 'kg' appears exactly once.
     *
     * @param string|null $weight
     * @return string
     */
    function format_weight(?string $weight): string
    {
        if (empty($weight)) {
            return 'N/A';
        }
        $clean = trim(preg_replace('/(\s*kg)+/i', '', $weight));
        return !empty($clean) ? $clean . ' kg' : 'N/A';
    }
}

if (!function_exists('format_birth_time')) {
    /**
     * Clean and format birth time to 12-hour AM/PM format (e.g. 02:01 AM or 02:05 PM).
     *
     * @param string|null $time
     * @return string
     */
    function format_birth_time(?string $time): string
    {
        if (empty($time) || trim($time) === '' || strtoupper(trim($time)) === 'N/A') {
            return 'N/A';
        }
        $timeStr = trim($time);
        $timestamp = strtotime($timeStr);
        if ($timestamp !== false) {
            return date('h:i A', $timestamp);
        }
        return $timeStr;
    }
}

