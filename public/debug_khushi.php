<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$user = DB::table('users')->where('profile_id', 'JDM111824')->first();

header('Content-Type: application/json');
echo json_encode($user, JSON_PRETTY_PRINT);
