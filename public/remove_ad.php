<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ads = \App\Models\Advertisement::all();
foreach ($ads as $ad) {
    if (stripos($ad->title, 'lighthouse') !== false || stripos($ad->image_path, 'lighthouse') !== false || stripos($ad->image, 'lighthouse') !== false) {
        $ad->delete();
        echo "Deleted lighthouse ad.\n";
    }
}
echo "Done.";
