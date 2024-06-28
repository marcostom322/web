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
    <h1>Gestión de Archivos</h1>
    <!-- Aquí iría el código para gestionar archivos -->
</div>
<?php include '../includes/footer.php'; ?>
