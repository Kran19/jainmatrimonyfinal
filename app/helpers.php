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
            storage_path('app/private/imports/' . $cleanPathNoImports),
            storage_path('app/private/imports/profile_photos/' . $filename),
            storage_path('app/private/imports/payment_proofs/' . $filename),
            storage_path('app/public/' . $safePath),
            storage_path('app/public/' . $cleanPathNoStorage),
            storage_path('app/public/uploads/' . $cleanPathNoUploads),
            public_path('uploads/' . $cleanPathNoUploads),
            public_path('imports/' . $cleanPathNoImports),
            public_path('imports/profile_photos/' . $filename),
            public_path('imports/payment_proofs/' . $filename),
            base_path($safePath),
            base_path('../digambar-samaj/' . $safePath),
            base_path('../digambar-samaj/uploads/' . $cleanPathNoUploads),
        ];

        foreach ($possibleExactPaths as $path) {
            if (!empty($path) && file_exists($path) && is_file($path)) {
                return $path;
            }
        }

        // Direct search in common directories by filename
        $searchDirs = [
            storage_path('app/private/imports/profile_photos'),
            storage_path('app/private/imports/payment_proofs'),
            storage_path('app/private/imports'),
            storage_path('app/private/uploads'),
            storage_path('app/private'),
            storage_path('app/public/uploads'),
            storage_path('app/public'),
            public_path('imports/profile_photos'),
            public_path('imports/payment_proofs'),
            public_path('imports'),
            public_path('uploads'),
            public_path(),
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
