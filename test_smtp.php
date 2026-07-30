<?php
// Quick SMTP test script - run via: php test_smtp.php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host     = 'smtp.hostinger.com';
$port     = 465;
$username = 'help@digambarjainparichay.com';
$password = 'King@0706';
$to       = 'help@digambarjainparichay.com'; // send to self as a test

$protocol = 'ssl://';
echo "Connecting to {$host}:{$port}...\n";
$socket = @fsockopen($protocol . $host, $port, $errno, $errstr, 15);
if (!$socket) {
    die("Connection FAILED: $errstr ($errno)\n");
}
echo "Connected!\n";

function smtp_read($socket) {
    $resp = '';
    while (substr($resp, 3, 1) != ' ') {
        $resp = fgets($socket, 256);
        echo "< $resp";
    }
    return $resp;
}

smtp_read($socket);
fwrite($socket, "EHLO smtp.hostinger.com\r\n");
smtp_read($socket);
fwrite($socket, "AUTH LOGIN\r\n");
smtp_read($socket);
fwrite($socket, base64_encode($username) . "\r\n");
smtp_read($socket);
fwrite($socket, base64_encode($password) . "\r\n");
$authResp = smtp_read($socket);

if (strpos($authResp, '235') !== false) {
    echo "\n✅ SMTP AUTH SUCCESSFUL! Mail will be delivered.\n";
} else {
    echo "\n❌ SMTP AUTH FAILED. Check credentials.\n";
}

fwrite($socket, "QUIT\r\n");
fclose($socket);
