<?php
require_once "../config.php";

$id = $_GET['id'];

// 1. GET IMAGE NAME FIRST
$stmt = $db->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product) {
    $image = $product['image'];

    // 2. DELETE IMAGE FILE
    if ($image && file_exists("../uploads/" . $image)) {
        unlink("../uploads/" . $image);
    }

    // 3. DELETE PRODUCT FROM DB
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: admin-dashboard.php");
?>