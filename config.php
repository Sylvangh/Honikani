<?php
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

/* =========================
   CLOUDINARY SETUP
   ========================= */

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Composer autoload not found. Run 'composer install' in the project root.");
}
require $autoloadPath;

use Cloudinary\Cloudinary;

// ✅ Hardcoded credentials (immediate working)
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'Root',
        'api_key'    => '423985652246424',
        'api_secret' => '3DsXen4ig5ES4cUVEhL7EhmFJ9g',
    ],
    'url' => ['secure' => true]
]);
