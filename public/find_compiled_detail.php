<?php
header('Content-Type: text/plain; charset=utf-8');

$dir = __DIR__ . '/../storage/framework/views';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'user/detail.blade.php') !== false) {
        file_put_contents(__DIR__ . '/compiled_detail_code.txt', $content);
        echo "Found compiled detail view: " . basename($file) . "\n";
        exit;
    }
}

echo "Compiled detail view not found.\n";
