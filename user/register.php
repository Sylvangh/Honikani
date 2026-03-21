<?php
session_start();
require_once __DIR__ . '/../config.php';

$error = "";
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $error = "Passwords do not match";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
            $stmt->execute([$email, $password_hash]);
            $success = "Registration successful. You can now login!";
        } catch(PDOException $e) {
            if ($e->getCode() === '23505') {
                $error = "Email already exists";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Registration</title>
<link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>

<div class="register-wrapper">

    <!-- Kakanin Logo -->
    <div class="logo-container">
        <div class="kakanin-logo">
            <div class="eye left-eye"></div>
            <div class="eye right-eye"></div>
        </div>
    </div>

    <h1 class="animated-text">Register your account</h1>

    <?php if($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST" class="register-form">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>

    <p class="login-link">Already have an account? <a href="login.php">Login Here</a></p>
</div>

<script>
// Animate title color
const animatedText = document.querySelector('.animated-text');
const colors = ['#ff9800','#34c759','#a65125','#ff5252','#ffd700'];
let colorIndex = 0;
setInterval(()=>{
    animatedText.style.color=colors[colorIndex];
    colorIndex=(colorIndex+1)%colors.length;
},1000);

// Kakanin eyes follow cursor or touch anywhere
const leftEye = document.querySelector('.left-eye');
const rightEye = document.querySelector('.right-eye');

function moveEyes(e){
    let x, y;
    if(e.touches){ // touch screen
        x = e.touches[0].clientX;
        y = e.touches[0].clientY;
    } else { // mouse
        x = e.clientX;
        y = e.clientY;
    }
    const rect = document.querySelector('.kakanin-logo').getBoundingClientRect();
    const centerX = rect.left + rect.width/2;
    const centerY = rect.top + rect.height/2;

    const deltaX = (x - centerX)/15;
    const deltaY = (y - centerY)/15;

    leftEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
    rightEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
}

document.addEventListener('mousemove', moveEyes);
document.addEventListener('touchmove', moveEyes);
</script>

</body>
</html>