<?php
session_start();
require_once __DIR__ . '/../config.php';

// ----------------- Handle Status Update -----------------
if (isset($_GET['status'], $_GET['order_id'])) {
    $stmt = $db->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$_GET['status'], $_GET['order_id']]);
    header("Location: product-orders.php");
    exit();
}

// ----------------- Handle Delete Order -----------------
if (isset($_GET['delete_order'])) {
    $stmt = $db->prepare("DELETE FROM orders WHERE id=?");
    $stmt->execute([$_GET['delete_order']]);
    header("Location: product-orders.php");
    exit();
}

// ----------------- Search -----------------
$search = $_GET['search'] ?? '';

if ($search) {
    $stmt = $db->prepare("
        SELECT orders.*, users.email, products.name AS product_name, products.image
        FROM orders
        JOIN users ON orders.user_id = users.id
        JOIN products ON orders.product_id = products.id
        WHERE users.email ILIKE ?
        ORDER BY orders.id DESC
    ");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $db->query("
        SELECT orders.*, users.email, products.name AS product_name, products.image
        FROM orders
        JOIN users ON orders.user_id = users.id
        JOIN products ON orders.product_id = products.id
        ORDER BY orders.id DESC
    ");
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<h2>All Orders</h2>
<a href="admin-dashboard.php" class="back-btn">← Back to Dashboard</a>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search Customer by Email" value="<?= htmlspecialchars($search) ?>">
    <button>Search</button>
</form>

<hr>

<div class="orders-container">
<?php if (empty($orders)): ?>
    <p>No orders found.</p>
<?php endif; ?>

<?php foreach($orders as $o): ?>
<div class="order-card">

    <!-- ✅ Use Cloudinary URL directly -->
    <img src="<?= htmlspecialchars($o['image']) ?>" onclick="openViewer(this)" alt="<?= htmlspecialchars($o['product_name']) ?>">

    <h3><?= htmlspecialchars($o['product_name']) ?></h3>

    <p><b>Order ID:</b> <?= $o['id'] ?></p>
    <p><b>Customer:</b> <?= htmlspecialchars($o['email']) ?> (ID: <?= $o['user_id'] ?>)</p>
    <p><b>Quantity:</b> <?= (int)$o['quantity'] ?></p>
    <p><b>Total:</b> ₱<?= htmlspecialchars($o['total']) ?></p>
    <p><b>Status:</b> <?= ucfirst($o['status']) ?></p>

    <div class="order-actions">
        <?php if ($o['status'] !== 'paid'): ?>
            <a href="?order_id=<?= $o['id'] ?>&status=paid" class="btn-paid">Mark as Paid</a>
        <?php endif; ?>

        <?php if ($o['status'] !== 'pending'): ?>
            <a href="?order_id=<?= $o['id'] ?>&status=pending" class="btn-pending">Mark as Pending</a>
        <?php endif; ?>

        <a href="?delete_order=<?= $o['id'] ?>" 
           onclick="return confirm('Delete this order?')" 
           class="btn-delete">Delete</a>
    </div>

</div>
<?php endforeach; ?>
</div>

<!-- Fullscreen Image Viewer -->
<div id="viewer" class="img-viewer" onclick="closeViewer()">
    <img id="viewerImg">
</div>

<script>
function openViewer(img) {
    document.getElementById("viewer").style.display = "flex";
    document.getElementById("viewerImg").src = img.src;
}

function closeViewer() {
    document.getElementById("viewer").style.display = "none";
}
</script>
