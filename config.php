<?php
$host = "dpg-d6qvp3i4d50c73bkp6n0-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "registrations_ghe3";
$user = "registrations_ghe3_user";
$pass = "7t55ce58WYKrOEF9AINd1aWTCnizNiTj";

try {
    // Added sslmode=require
    $db = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $pass
    );

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: confirm connection
    // echo "Connected successfully!";
} catch(PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

/* =========================
   CLOUDINARY SETUP
   ========================= */

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Composer autoload not found. Run 'composer install' in the project root.");
}
require $autoloadPath;

use Cloudinary\Cloudinary;

// Use environment variables for Cloudinary
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => getenv('sylvan'),
        'api_key'    => getenv('868362577675189'),
        'api_secret' => getenv('F656DmKNnTFYX55Ps8av50MrQyg'),
    ],
    'url' => ['secure' => true]
]);
?>
