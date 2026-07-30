<?php
$pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=digambarfinal;charset=utf8mb4", "root", "");
$stmt = $pdo->prepare("SELECT password, password_hash, has_set_password, mobile FROM users WHERE email = ?");
$stmt->execute(['colav87282@barumart.com']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "user details:\n";
print_r($user);

echo "\nPassword verify checks:\n";
echo "verify against password: " . (password_verify('8527419638', $user['password']) ? 'YES' : 'NO') . "\n";
echo "verify against password_hash: " . (password_verify('8527419638', $user['password_hash']) ? 'YES' : 'NO') . "\n";
