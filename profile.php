<?php 
include 'includes/header.php'; 
include 'includes/nav.php'; 
include 'includes/auth_check.php';

$user = getUserDetails($_SESSION['username']);
?>
<div class="profile-container">
    <h2>Perfil de Usuario</h2>
    <p>Nombre de Usuario: <?php echo $user['username']; ?></p>
    <p>Nombre: <?php echo $user['name']; ?></p>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Tipo de Usuario: <?php echo $user['user_type']; ?></p>
</div>
<?php include 'includes/footer.php'; ?>
