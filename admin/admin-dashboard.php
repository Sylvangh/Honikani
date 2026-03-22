<?php
// admin-dashboard.php
ob_start(); // Start output buffering

// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Include config and authentication
require_once "../config.php";
require_once "../includes/auth.php";
checkAdmin();

// ----------------- Handle Add Product -----------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $tmpName = $_FILES['image']['tmp_name'];

    if ($_FILES['image']['error'] !== 0) {
        die("Upload error. Please try again.");
    }

    try {
        // Upload to Cloudinary
        $result = $cloudinary->uploadApi()->upload($tmpName);

        // Get Cloudinary image URL
        $imageUrl = $result['secure_url'];

        // Save product to database
        $stmt = $db->prepare("INSERT INTO products (name, price, quantity, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $price, $quantity, $imageUrl]);

        $successMessage = "Product added successfully!";
    } catch (\Cloudinary\Api\Exception\ApiError $e) {
        // Cloudinary API error
        $errorMessage = "Cloudinary Upload Error: " . $e->getMessage();
    } catch (\Exception $e) {
        // General error
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// ----------------- Handle Product Search -----------------
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $db->prepare("SELECT * FROM products WHERE name ILIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = $db->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<link rel="stylesheet" href="../assets/css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<h2 class="title">Admin Dashboard</h2>

<?php if (!empty($successMessage)): ?>
    <div class="success-msg"><?= htmlspecialchars($successMessage) ?></div>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="error-msg"><?= htmlspecialchars($errorMessage) ?></div>
<?php endif; ?>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search Products..." value="<?= htmlspecialchars($search) ?>">
    <button>Search</button>
</form>

<hr>

<h3>Add Product</h3>
<form method="POST" enctype="multipart/form-data" class="form-box">
    <input name="name" placeholder="Product Name" required>
    <input name="price" type="number" step="0.01" placeholder="Price" required>
    <input name="quantity" type="number" placeholder="Quantity" min="1" required>
    <input type="file" name="image" accept="image/*" required>
    <button name="add_product">Add Product</button>
    <a href="product-orders.php" class="orders-btn">View All Orders</a>
</form>

<hr>

<h3>Products</h3>

<?php foreach ($products as $p): ?>
<div class="card">
    <img src="<?= htmlspecialchars($p['image']) ?>" onclick="openImage(this)">
    <div class="card-body">
        <b><?= htmlspecialchars($p['name']) ?></b>
        <span>Qty: <?= htmlspecialchars($p['quantity']) ?></span>
        <span class="price">₱<?= htmlspecialchars($p['price']) ?></span>

        <div class="card-actions">
            <a href="delete-product.php?id=<?= $p['id'] ?>" 
               onclick="return confirm('Delete this product?')" 
               class="delete-btn">Delete</a>

            <a href="edit-product.php?id=<?= $p['id'] ?>" class="edit-btn">Edit Product</a>
            <a href="product-orders.php?product_id=<?= $p['id'] ?>" class="orders-btn">View Orders</a>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div id="imgModal" class="img-modal" onclick="closeImage()">
    <img id="modalImg">
</div>

<script>
function openImage(img) {
    const modal = document.getElementById("imgModal");
    const modalImg = document.getElementById("modalImg");

    modal.style.display = "flex";
    modalImg.src = img.src;
}

function closeImage() {
    document.getElementById("imgModal").style.display = "none";
}
</script>
