<?php
/**
 * Run pending migrations via HTTP.
 * Visit: http://127.0.0.1:8000/run_migrate.php
 * DELETE THIS FILE after use!
 */
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Illuminate\Support\Facades\Artisan;

echo "Running migrations...\n\n";
try {
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    echo $output ?: "Done!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n\nDone. Please DELETE public/run_migrate.php now for security!\n";
