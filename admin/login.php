<?php
session_start();

// Hardcoded admin username and password
$admin_username = "admin";
$admin_password = "1234"; // you can change this

$error = ""; // initialize error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check against hardcoded credentials
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_id'] = 1; // assign a session id
        header("Location: admin-dashboard.php"); // redirect to admin dashboard
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>


<link rel="stylesheet" href="../assets/css/login.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>