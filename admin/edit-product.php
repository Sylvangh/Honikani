<?php
session_start();
require_once __DIR__ . '/../config.php';

$id = $_GET['id'] ?? 0;

// 1️⃣ Fetch product
$stmt = $db->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    die("Product not found");
}

// 2️⃣ Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $newImage = $p['image']; // keep old image by default

    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];

        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) die("Invalid file type");

        $uploadDir = "../uploads/";
        $newImage = uniqid() . "." . $ext;
        move_uploaded_file($tmpName, $uploadDir . $newImage);

        // delete old image
        if ($p['image'] && file_exists($uploadDir . $p['image'])) {
            unlink($uploadDir . $p['image']);
        }
    }

    // update DB
    $stmt = $db->prepare("UPDATE products SET name=?, price=?, quantity=?, image=? WHERE id=?");
    $stmt->execute([$name, $price, $quantity, $newImage, $id]);

    header("Location: admin-dashboard.php");
    exit();
}
?>

<h2>Edit Product</h2>

<form method="POST" enctype="multipart/form-data">
    <input name="name" value="<?= htmlspecialchars($p['name']) ?>" required><br><br>
    <input name="price" type="number" step="0.01" value="<?= $p['price'] ?>" required><br><br>
    <input name="quantity" type="number" value="<?= $p['quantity'] ?>" min="0" required><br><br>
    <p>Current Image:</p>
    <img src="../uploads/<?= $p['image'] ?>" width="150"><br><br>
    <input type="file" name="image" accept="image/*"><br><small>Leave blank to keep current image</small><br><br>
    <button>Update Product</button>
</form>