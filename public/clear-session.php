<?php
/**
 * Session + Cache Cleaner - REMOVE AFTER USE
 * http://127.0.0.1:8000/clear-session.php
 */
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
    die('Access denied.');
}

echo "<pre style='font-family:monospace; background:#1e1e1e; color:#d4d4d4; padding:20px; font-size:13px; line-height:1.6;'>";
echo "=== SESSION + CACHE CLEANER ===\n\n";

$laravelRoot = dirname(__DIR__);

// 1. Delete all session files
$sessionPath = $laravelRoot . '/storage/framework/sessions';
$count = 0;
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            $count++;
        }
    }
}
echo "✅ Deleted $count session file(s)\n";

// 2. Delete cache files
$cachePath = $laravelRoot . '/storage/framework/cache/data';
$cacheCount = 0;
if (is_dir($cachePath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            unlink($file->getPathname());
            $cacheCount++;
        }
    }
}
echo "✅ Deleted $cacheCount cache file(s)\n";

// 3. Clear compiled views
$viewsPath = $laravelRoot . '/storage/framework/views';
$viewCount = 0;
if (is_dir($viewsPath)) {
    foreach (glob($viewsPath . '/*.php') as $file) {
        unlink($file);
        $viewCount++;
    }
}
echo "✅ Deleted $viewCount compiled view(s)\n";

echo "\n✅ All done! Now:\n";
echo "1. Close ALL browser tabs for this site\n";
echo "2. Open a fresh browser tab\n";
echo "3. Visit http://127.0.0.1:8000/login\n";
echo "4. 419 error should be gone\n";
echo "\n⚠️  DELETE THIS FILE after use: public/clear-session.php\n";
echo "</pre>";
