<?php 
include 'includes/header.php'; 
include 'includes/nav.php'; 
include 'includes/auth_check.php';
?>
<div class="panel">
    <h1>Panel de Control</h1>
    <div class="panel-sections">
        <a href="pages/notes.php">Notas</a>
        <a href="pages/upload_files.php">Subir Archivos</a>
        <a href="pages/file_explorer.php">Explorador de Archivos</a>
        <a href="map/index.php">Sitios</a> <!-- Nuevo enlace a Sitios -->
        <?php if ($_SESSION['user_type'] == 'admin'): ?>
            <a href="admin/manage_users.php">Gestión de Usuarios</a>
            <a href="admin/manage_files.php">Gestión de Archivos</a>
            <a href="admin/manage_carousel.php">Gestión del Carrusel</a>
        <?php endif; ?>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
