<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'php/auth.php';
    $username = $_POST['username'];
    $password = $_POST['password'];
    $error = login($username, $password);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    <div class="login-container">
        <h2>Login</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    </div>
</body>
</html>
