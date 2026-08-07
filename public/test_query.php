<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

header('Content-Type: text/plain; charset=utf-8');

// Simulate image request in-process
$imgRequest = Illuminate\Http\Request::create(
    '/image', 
    'GET', 
    ['file' => 'imports/profile_photos/Shivani_Jain_profile.jpg']
);

$controller = app()->make(App\Http\Controllers\ImageController::class);

try {
    $response = $controller->serve($imgRequest);
    
    $statusCode = $response->getStatusCode();
    echo "Status Code: $statusCode\n";
    
    foreach ($response->headers->all() as $name => $values) {
        echo "Header {$name}: " . implode(', ', $values) . "\n";
    }
    
    if ($response instanceof Symfony\Component\HttpFoundation\BinaryFileResponse) {
        $file = $response->getFile();
        echo "Type: BinaryFileResponse\n";
        echo "File Path: " . $file->getPathname() . "\n";
        echo "File Exists: " . (file_exists($file->getPathname()) ? 'YES' : 'NO') . "\n";
        echo "File Size: " . $file->getSize() . " bytes\n";
    } else {
        $content = $response->getContent();
        echo "Type: PlainResponse\n";
        echo "Content Length: " . strlen($content) . "\n";
        echo "First 200 chars: " . htmlspecialchars(substr($content, 0, 200)) . "\n";
    }
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
