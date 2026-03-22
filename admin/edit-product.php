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
/* Page background */
body {
    background-color: #fff7f0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Heading */
h2 {
    text-align: center;
    margin-top: 20px;
    font-size: 1.8rem;
}

/* Form container */
form {
    max-width: 95%;
    width: 400px;
    margin: 30px auto;
    padding: 25px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    box-sizing: border-box;
}

/* Labels */
form label {
    display: block;
    margin-bottom: 6px;
    font-size: 1.1rem;
}

/* Inputs */
form input[type="text"],
form input[type="number"],
form input[type="file"] {
    width: 100%;
    padding: 14px;
    margin-bottom: 15px;
    border-radius: 12px;
    border: 1px solid #ccc;
    font-size: 1rem;
    box-sizing: border-box;
}

/* Buttons */
form button {
    width: 100%;
    padding: 16px;
    background-color: #f28c6b; /* warm accent color for kakanin theme */
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #e67652;
}

/* Current Image */
img.current-image {
    display: block;
    margin-bottom: 15px;
    max-width: 100%;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

/* Small text under file input */
form small {
    display: block;
    margin-top: -10px;
    margin-bottom: 10px;
    font-size: 0.9rem;
    color: #666;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    form {
        padding: 20px;
        width: 90%;
    }

    h2 {
        font-size: 1.5rem;
    }

    form input[type="text"],
    form input[type="number"],
    form input[type="file"],
    form button {
        font-size: 1rem;
        padding: 12px;
    }
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
