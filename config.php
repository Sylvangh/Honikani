<?php
// ----------------- Database Setup -----------------
$host = "dpg-d6qvp3i4d50c73bkp6n0-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "registrations_ghe3";
$user = "registrations_ghe3_user";
$pass = "7t55ce58WYKrOEF9AINd1aWTCnizNiTj";

try {
    $db = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $pass
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// ----------------- Composer Autoload -----------------
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Composer autoload not found. Run 'composer install' in the project root.");
}
require $autoloadPath;

// ----------------- Cloudinary Setup -----------------
use Cloudinary\Cloudinary;

// ✅ Hardcoded credentials (for immediate testing)
// Cloud name must be exactly what Cloudinary shows in your dashboard
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dyjsfdndc',        // your real Cloudinary cloud name
        'api_key'    => '868362577675189',  // API key
        'api_secret' => 'F656DmKNnTFYX55Ps8av50MrQyg', // API secret
    ],
    'url' => ['secure' => true]
]);
