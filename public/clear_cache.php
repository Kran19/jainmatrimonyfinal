<?php

// Set execution time limit
set_time_limit(60);

$bootstrapCachePath = dirname(__DIR__) . '/bootstrap/cache';

$filesDeleted = [];
$filesFailed = [];

// 1. Manually delete the serialized route and config cache files if they exist
if (is_dir($bootstrapCachePath)) {
    $files = scandir($bootstrapCachePath);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.gitignore') {
            continue;
        }
        $filePath = $bootstrapCachePath . '/' . $file;
        if (is_file($filePath)) {
            if (@unlink($filePath)) {
                $filesDeleted[] = $file;
            } else {
                $filesFailed[] = $file;
            }
        }
    }
}

// 2. Programmatically execute optimize:clear via Laravel Artisan
$artisanOutput = "";
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Boot the console kernel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Call optimize:clear
    $status = \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $artisanOutput = \Illuminate\Support\Facades\Artisan::output();
} catch (\Throwable $e) {
    $artisanOutput = "Artisan failed: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'deleted_files' => $filesDeleted,
    'failed_files' => $filesFailed,
    'artisan_output' => explode("\n", trim($artisanOutput))
], JSON_PRETTY_PRINT);
