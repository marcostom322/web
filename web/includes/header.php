<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/assets/images/favicon.JPEG">
    <!-- Dynamic Title -->
    <title><?php echo isset($page_title) ? $page_title : 'Marcos'; ?></title>
</head>
<body>
    <header>
        <nav>
            <ul style="float: left;">
                <li><a href="/index.php">Inicio</a></li>
                <li><a href="/pages/portfolio.php">Portfolio</a></li>
            </ul>
            <ul style="float: right;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/pages/dashboard.php">Dashboard</a></li>
                    <li><a href="/logout.php">Cerrar sesión</a></li>
                <?php else: ?>
                    <li><a href="/pages/login.php">Iniciar sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>
</html>
