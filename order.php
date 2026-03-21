<?php
session_start();
require_once "config.php";

$user = $_SESSION['user_id'];
$product = $_POST['product_id'];
$qty = $_POST['qty'];

// 1️⃣ Get product price
$stmt = $db->prepare("SELECT price, quantity FROM products WHERE id=?");
$stmt->execute([$product]);
$p = $stmt->fetch();

// 2️⃣ Check if enough stock
if ($qty > $p['quantity']) {
    die("Not enough stock available!");
}

// 3️⃣ Calculate total
$total = $p['price'] * $qty;

// 4️⃣ Insert order
$stmt = $db->prepare("INSERT INTO orders (user_id, product_id, quantity, total, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->execute([$user, $product, $qty, $total]);

// 5️⃣ Reduce stock
$stmt2 = $db->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
$stmt2->execute([$qty, $product]);

// 6️⃣ Redirect to user orders
header("Location: user/orders.php");
exit();
?>