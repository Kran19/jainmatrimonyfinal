<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::create('/image', 'GET', ['file' => 'imports/profile_photos/Shivani_Jain_profile.jpg'])
);

header('Content-Type: text/plain; charset=utf-8');
echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Headers:\n";
foreach ($response->headers->all() as $name => $values) {
    echo "  {$name}: " . implode(', ', $values) . "\n";
}

$content = $response->getContent();
echo "Content Length: " . strlen($content) . "\n";
if (strlen($content) < 500) {
    echo "Content Body:\n" . $content . "\n";
} else {
    echo "Content (First 100 bytes): " . substr($content, 0, 100) . "...\n";
}
