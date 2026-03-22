<?php
session_start();
require_once __DIR__ . '/../config.php';

$id = $_GET['id'] ?? 0;

// ----------------- Fetch product -----------------
$stmt = $db->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    die("Product not found");
}

// ----------------- Handle POST update -----------------
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $newImage = $p['image']; // keep old image by default

    if (!empty($_FILES['image']['name'])) {
        $tmpName = $_FILES['image']['tmp_name'];

        try {
            // Upload new image to Cloudinary
            $result = $cloudinary->uploadApi()->upload($tmpName);
            $newImage = $result['secure_url'];

        } catch (\Cloudinary\Api\Exception\ApiError $e) {
            $errorMessage = "Cloudinary Upload Error: " . $e->getMessage();
        } catch (\Exception $e) {
            $errorMessage = "Error uploading image: " . $e->getMessage();
        }
    }

    if (!$errorMessage) {
        // Update product in DB
        $stmt = $db->prepare("UPDATE products SET name=?, price=?, quantity=?, image=? WHERE id=?");
        $stmt->execute([$name, $price, $quantity, $newImage, $id]);

        $successMessage = "Product updated successfully!";
        // Optionally redirect to dashboard after update
        header("Location: admin-dashboard.php?success=1");
        exit();
    }
}
?>

<h2>Edit Product</h2>

<?php if (!empty($errorMessage)): ?>
    <div style="color:red;"><?= htmlspecialchars($errorMessage) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <input name="name" value="<?= htmlspecialchars($p['name']) ?>" required><br><br>
    <input name="price" type="number" step="0.01" value="<?= htmlspecialchars($p['price']) ?>" required><br><br>
    <input name="quantity" type="number" value="<?= htmlspecialchars($p['quantity']) ?>" min="0" required><br><br>

    <p>Current Image:</p>
    <img src="<?= htmlspecialchars($p['image']) ?>" width="150"><br><br>

    <input type="file" name="image" accept="image/*"><br>
    <small>Leave blank to keep current image</small><br><br>

    <button>Update Product</button>
</form>
