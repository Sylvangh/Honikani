<?php
require_once "../config.php";
require_once "../includes/auth.php";
checkUser();

$user_id = $_SESSION['user_id'];

// ----------------- Cancel Order -----------------
if (isset($_GET['cancel'])) {
    $order_id = intval($_GET['cancel']);

    $stmt = $db->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Return stock
        $stmt2 = $db->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
        $stmt2->execute([$order['quantity'], $order['product_id']]);

        // Delete order
        $stmt3 = $db->prepare("DELETE FROM orders WHERE id=?");
        $stmt3->execute([$order_id]);

        header("Location: orders.php");
        exit();
    }
}

// ----------------- Fetch User Orders -----------------
$stmt = $db->prepare("
    SELECT orders.*, products.name, products.image
    FROM orders
    JOIN products ON orders.product_id = products.id
    WHERE orders.user_id = ?
    ORDER BY orders.id DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);



// ----------------- Handle Product Search -----------------
$search = $_GET['search'] ?? '';

$sql = "
    SELECT orders.*, products.name, products.image
    FROM orders
    JOIN products ON orders.product_id = products.id
    WHERE orders.user_id = ?
";

$params = [$user_id];

if ($search) {
    $sql .= " AND products.name ILIKE ?";
    $params[] = "%$search%";
}

$sql .= " ORDER BY orders.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/user-order.css">
</head>
<body>

<h2><i class="fas fa-box-open"></i> Your Orders</h2>
<a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search Products..." value="<?= htmlspecialchars($search) ?>">

    <button type="submit"><i class="fas fa-search"></i> Search</button>

    <a href="orders.php" class="cancel-btn"><i class="fas fa-times"></i> Cancel</a>
</form>


<div class="container">
    <?php if(empty($orders)): ?>
        <p style="text-align:center; color:#555; margin-top:20px;">No orders yet.</p>
    <?php endif; ?>

    <?php foreach($orders as $o): ?>
        <div class="card">
            <img src="../uploads/<?= $o['image'] ?>" onclick="openViewer(this)" alt="<?= htmlspecialchars($o['name']) ?>">

            <h3><?= htmlspecialchars($o['name']) ?></h3>

            <p class="info">
                <i class="fas fa-box"></i>
                Quantity: <?= $o['quantity'] ?>
            </p>

            <p class="info price">
                <i class="fas fa-peso-sign"></i>
                Total: ₱<?= $o['total'] ?>
            </p>

            <p class="info status">
                <i class="fas fa-info-circle"></i>
                Status: <?= ucfirst($o['status']) ?>
            </p>

            <a href="https://www.facebook.com/ma.honey.dolorito" target="_blank" class="chat-btn">
                <i class="fas fa-comment-dots"></i> Chat with Seller
            </a>

            <?php if($o['status'] === 'pending'): ?>
                <a href="?cancel=<?= $o['id'] ?>" onclick="return confirm('Cancel this order?')" class="delete-btn">
                    <i class="fas fa-times-circle"></i> Cancel Order
                </a>
            <?php endif; ?>
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

</body>
</html>