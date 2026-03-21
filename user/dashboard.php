<?php
// dashboard.php
ob_start(); // start output buffering
// Prevent browser caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Include config and auth
require_once "../config.php";
require_once "../includes/auth.php";
checkUser(); // This now reliably redirects if not logged in

$user_id = $_SESSION['user_id'];
// ----------------- Handle Order Submission -----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_product'])) {
    $product_id = $_POST['product_id'];
    $qty = intval($_POST['qty']);

    // Fetch product info
    $stmt = $db->prepare("SELECT price, quantity FROM products WHERE id=?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $availableQty = (int)$product['quantity'];

    if ($product && $availableQty >= $qty) {
        $total = $product['price'] * $qty;

        $stmt = $db->prepare("INSERT INTO orders (user_id, product_id, quantity, total, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $product_id, $qty, $total]);

        $stmt2 = $db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt2->execute([$qty, $product_id]);

        echo "<script>alert('Ordered successfully!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('Not enough stock!'); window.location.href='dashboard.php';</script>";
        exit();
    }
}


// ----------------- Fetch All Products -----------------
$stmt = $db->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Available Kakanin</title>

<link rel="stylesheet" href="../assets/css/user-dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>


<!-- Top Bar -->
<div class="top-bar">
    <!-- Left: App Name -->
    <div class="left-bar">
        <div class="animated-text">HONIKANI</div>
    </div>

<!-- Center: Kakanin Logo -->
<div class="center-bar">
    <div class="kakanin-chat">

        <!-- Messenger Style Note -->
        <a href="https://www.facebook.com/ma.honey.dolorito" 
           target="_blank" 
           class="chat-bubble" 
           id="chatBubble">
           Hi! 👋
        </a>

        <!-- Kakanin Logo -->
        <div class="kakanin-logo">
            <div class="eye left-eye"></div>
            <div class="eye right-eye"></div>
        </div>

    </div>
</div>
    <!-- Right: Logout -->
    <div class="right-bar">
        <a href="login.php" class="logout-btn">
    <i class="fas fa-sign-out-alt"></i> Logout
</a>
    </div>
</div>
<a href="orders.php" class="orders-btn">
    <i class="fas fa-shopping-cart"></i> <!-- Font Awesome cart icon -->
    View Your Orders
</a>
<h2>Available Kakanin</h2>


<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search Products..." value="<?= htmlspecialchars($search) ?>">

    <button type="submit"><i class="fas fa-search"></i> Search</button>

    <a href="dashboard.php" class="cancel-btn"><i class="fas fa-times"></i> Cancel</a>
</form>

<div class="container">
    <?php if(empty($products)): ?>
        <p style="text-align:center; color:#555; margin-top:20px;">No products available.</p>
    <?php endif; ?>

    <?php foreach($products as $p): ?>
        <div class="card">
            <img src="../uploads/<?= $p['image'] ?>" onclick="openViewer(this)" alt="<?= htmlspecialchars($p['name']) ?>">

      <h3><?= htmlspecialchars($p['name']) ?></h3>


<p class="info">
    <i class="fas fa-box"></i> <!-- Quantity icon -->
    Stock: <?= (int)$p['quantity'] ?>
</p>

<p class="info price">
    <i class="fas fa-peso-sign"></i> <!-- Price icon -->
    Price: <?= $p['price'] ?>
</p>


           <form method="POST" class="order-form">
    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">

   <input type="number" name="qty" min="1" placeholder="Quantity" required>

    <button name="order_product" type="submit">Order</button>
</form>
        </div>
    <?php endforeach; ?>
</div>

<!-- Fullscreen Image Viewer -->
<div id="viewer" class="img-viewer" onclick="closeViewer()">
    <img id="viewerImg">
</div>

<script>
// Image Viewer
function openViewer(img) {
    const viewer = document.getElementById("viewer");
    const viewerImg = document.getElementById("viewerImg");
    viewer.style.display = "flex";
    viewerImg.src = img.src;
}
function closeViewer() {
    document.getElementById("viewer").style.display = "none";
}

// Kakanin eyes follow cursor or touch
const leftEye = document.querySelector('.left-eye');
const rightEye = document.querySelector('.right-eye');

function moveEyes(e){
    let x, y;
    if(e.touches){
        x = e.touches[0].clientX;
        y = e.touches[0].clientY;
    } else {
        x = e.clientX;
        y = e.clientY;
    }
    const rect = document.querySelector('.kakanin-logo').getBoundingClientRect();
    const centerX = rect.left + rect.width/2;
    const centerY = rect.top + rect.height/2;

    const deltaX = (x - centerX)/30;
    const deltaY = (y - centerY)/30;

    leftEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
    rightEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
}

document.addEventListener('mousemove', moveEyes);
document.addEventListener('touchmove', moveEyes);
</script>



<script>
const bubble = document.getElementById("chatBubble");

function getGreeting() {
    const hour = new Date().getHours();

    if (hour < 12) return "Magandang umaga ☀️";
    if (hour < 18) return "Magandang hapon 🌤️";
    return "Magandang gabi 🌙";
}

const messages = [
    "Chat the seller 😄",
    "Order na po kayo 🍡",
    "Masarap po today!",
    "Fresh kakanin ngayon 🤤",
    "May promo kami 🎉",
    "Available pa po 😍",
    "Click me 😍",
    "Hello po 👋"
];

function updateBubble() {
    const greeting = getGreeting();
    const randomMsg = messages[Math.floor(Math.random() * messages.length)];

    bubble.innerText = greeting + "!\n" + randomMsg;
}

// initial load
updateBubble();

// change every 5 seconds
setInterval(updateBubble, 5000);
</script>
</body>
</html>
