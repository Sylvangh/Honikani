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
        $tmpName = $_FILES['image']['tmp_name'];

        // Upload to Cloudinary
        try {
            $result = $cloudinary->uploadApi()->upload($tmpName);
            $newImage = $result['secure_url'];
        } catch (\Cloudinary\Api\Exception\ApiError $e) {
            die("Cloudinary Upload Error: " . $e->getMessage());
        }
    }

    // update DB
    $stmt = $db->prepare("UPDATE products SET name=?, price=?, quantity=?, image=? WHERE id=?");
    $stmt->execute([$name, $price, $quantity, $newImage, $id]);

    header("Location: admin-dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product</title>

<!-- Link existing style.css -->
<link rel="stylesheet" href="../assets/css/style.css">

<style>
/* Additional page-specific styles */
form {
    max-width: 500px;
    margin: 30px auto;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

form input[type="text"],
form input[type="number"],
form input[type="file"],
form button {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

form button {
    background-color: #4CAF50;
    color: #fff;
    border: none;
    cursor: pointer;
}

form button:hover {
    background-color: #45a049;
}

img.current-image {
    display: block;
    margin-bottom: 15px;
    max-width: 200px;
    border-radius: 8px;
}
</style>
</head>
<body>

<h2 style="text-align:center; margin-top:20px;">Edit Product</h2>

<form method="POST" enctype="multipart/form-data">
    <label>Product Name</label>
    <input name="name" value="<?= htmlspecialchars($p['name']) ?>" required>

    <label>Price</label>
    <input name="price" type="number" step="0.01" value="<?= $p['price'] ?>" required>

    <label>Quantity</label>
    <input name="quantity" type="number" value="<?= $p['quantity'] ?>" min="0" required>

    <label>Current Image:</label>
    <img src="<?= htmlspecialchars($p['image']) ?>" class="current-image" alt="Current Product Image">

    <label>New Image (optional)</label>
    <input type="file" name="image" accept="image/*">
    <small>Leave blank to keep current image</small>

    <button type="submit">Update Product</button>
</form>

</body>
</html>
