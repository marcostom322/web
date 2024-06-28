<?php 
include 'includes/header.php'; 
include 'includes/nav.php'; 
include 'includes/auth_check.php';
?>
<div class="panel">
    <h1>Panel de Control</h1>
    <div class="panel-sections">
        <a href="pages/notes.php">
            <i class="icon fas fa-sticky-note"></i> Notas
        </a>
        <a href="pages/upload_files.php">
            <i class="icon fas fa-upload"></i> Subir Archivos
        </a>
        <a href="pages/file_explorer.php">
            <i class="icon fas fa-folder-open"></i> Explorador de Archivos
        </a>
        <a href="map/index.php">
            <i class="icon fas fa-map-marker-alt"></i> Sitios
        </a> <!-- Nuevo enlace a Sitios -->
        <?php if ($_SESSION['user_type'] == 'admin'): ?>
            <a href="admin/manage_users.php">
                <i class="icon fas fa-users-cog"></i> Gestión de Usuarios
            </a>
            <a href="admin/manage_files.php">
                <i class="icon fas fa-file-alt"></i> Gestión de Archivos
            </a>
            <a href="admin/manage_carousel.php">
                <i class="icon fas fa-images"></i> Gestión del Carrusel
            </a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
