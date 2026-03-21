<?php
session_start();
require_once __DIR__ . '/../config.php';

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Login</title>
<link rel="stylesheet" href="../assets/css/user-login.css">
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">
    <!-- CSS Logo -->
    <div class="logo-container">
        <div class="kakanin-logo">
            <div class="eye left-eye"></div>
            <div class="eye right-eye"></div>
        </div>
    </div>

<div class="animated-text">HONIKANI</div>

    <?php if($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="login-form">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p class="register-link">Don't have an account? <a href="register.php">Register Here</a></p>
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

    const deltaX = (x - centerX)/15; // divide to reduce movement
    const deltaY = (y - centerY)/15;

    leftEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
    rightEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
}

// Listen for mouse move and touch move
document.addEventListener('mousemove', moveEyes);
document.addEventListener('touchmove', moveEyes);
</script>

</body>
</html>
