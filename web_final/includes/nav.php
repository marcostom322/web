<?php
session_start();
?>
<nav>
    <div class="nav-left">
        <a href="/index.php"><i class="fas fa-home"></i>&nbsp Inicio</a>
        <!--a href="/portfolio.php"><i class="fas fa-briefcase"></i>&nbsp Portfolio</a-->
    </div>
    <div class="nav-right">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="user-name"><?php echo $_SESSION['username']; ?></span>
            <a href="/dashboard.php" class="dashboard-btn"><i class="fas fa-tachometer-alt"></i>&nbsp Panel</a>
            <div class="dropdown">
                <button class="dropbtn"><?php echo $_SESSION['username']; ?> <i class="fas fa-caret-down"></i></button>
                <div class="dropdown-content">
                    <a href="/profile.php"><i class="fas fa-user"></i> Perfil</a>
                    <a href="/settings.php"><i class="fas fa-cog"></i> Configuración</a>
                    <a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i>&nbsp Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</nav>
