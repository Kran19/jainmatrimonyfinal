<?php
header('Content-Type: text/plain; charset=utf-8');

$dir = __DIR__ . '/../storage/framework/views';
$files = glob($dir . '/*.php');

$deleted = 0;
foreach ($files as $file) {
    if (is_file($file)) {
        @unlink($file);
        $deleted++;
    }
}

echo "Deleted {$deleted} compiled view files from {$dir}.\n";
