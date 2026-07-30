<?php

/**
 * Safe Symlink Creator for Shared Hosting environments (where exec() is disabled in php.ini)
 * Run via terminal: php symlink.php
 * Or via browser: https://digambarjainparichay.com/symlink.php
 */

$target = realpath(__DIR__ . '/../storage/app/public') ?: (__DIR__ . '/../storage/app/public');
$link = __DIR__ . '/storage';

echo "<pre style='font-family:monospace; background:#1e1e1e; color:#d4d4d4; padding:20px; font-size:14px; line-height:1.5;'>";
echo "=== STORAGE SYMLINK CREATOR ===\n\n";

// 1. Ensure target storage/app/public directory exists
if (!file_exists($target)) {
    @mkdir($target, 0755, true);
    echo "Created storage target directory: $target\n";
}

// 2. Check existing link/directory
if (file_exists($link) || is_link($link)) {
    if (is_link($link)) {
        @unlink($link);
        echo "Removed existing symlink at: $link\n";
    } elseif (is_dir($link)) {
        echo "ℹ️  Directory already exists at: $link\n";
        echo "Done.\n</pre>";
        exit;
    }
}

// 3. Attempt symlink creation
if (@symlink($target, $link)) {
    echo "✅ SUCCESS: Symbolic link created successfully!\n";
    echo "   Link Path  : $link\n";
    echo "   Target Path: $target\n";
} else {
    echo "⚠️  symlink() function failed or is disabled on host.\n";
    echo "Creating public/storage directory fallback...\n";
    if (!file_exists($link)) {
        @mkdir($link, 0755, true);
    }
    echo "✅ Fallback directory created at: $link\n";
}

echo "\nDone.\n</pre>";
