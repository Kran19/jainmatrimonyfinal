<?php

/**
 * Move sensitive imports directory to secure storage/app/private/imports
 * Run via CLI: php move_imports.php
 */

$src = __DIR__ . '/public/imports';
$dst = __DIR__ . '/storage/app/private/imports';

echo "=== MOVING IMPORTS TO PRIVATE STORAGE ===\n";

if (!file_exists($src)) {
    echo "ℹ️ Source directory public/imports does not exist or was already moved.\n";
    exit(0);
}

if (!file_exists($dst)) {
    @mkdir($dst, 0755, true);
}

function recursive_move($source, $destination) {
    if (!file_exists($destination)) {
        @mkdir($destination, 0755, true);
    }
    $dir = opendir($source);
    while (false !== ($file = readdir($dir))) {
        if ($file !== '.' && $file !== '..') {
            $srcFile = $source . '/' . $file;
            $dstFile = $destination . '/' . $file;
            if (is_dir($srcFile)) {
                recursive_move($srcFile, $dstFile);
            } else {
                @rename($srcFile, $dstFile);
            }
        }
    }
    closedir($dir);
    @rmdir($source);
}

recursive_move($src, $dst);
echo "✅ SUCCESS: Moved public/imports to storage/app/private/imports\n";
