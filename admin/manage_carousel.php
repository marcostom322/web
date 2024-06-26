<?php 
include '../includes/header.php'; 
include '../includes/nav.php'; 
include '../includes/auth_check.php';
if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>
<div class="panel">
    <h1>Gestión del Carrusel</h1>
    <!-- Aquí iría el código para gestionar el carrusel -->
</div>
<?php include '../includes/footer.php'; ?>
