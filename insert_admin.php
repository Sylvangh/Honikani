<?php
require_once "config.php";

// Set admin username and password
$username = 'admin';
$password = '1234';

// Hash password using PHP
$pass_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$stmt = $db->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
$stmt->execute([$username, $pass_hash]);

echo "Admin user created!";
?>