<?php
require '../includes/auth.php';
redirectIfNotLoggedIn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h1>Dashboard</h1>
        <div class="dashboard-buttons">
            <button onclick="location.href='/pages/upload.php'">Subir Archivos</button>
            <button onclick="location.href='/pages/view_uploads.php'">Ver Archivos Subidos</button>
            <button onclick="location.href='/pages/notes.php'">Notas</button>
            <button onclick="location.href='/pages/album.php'">Álbum</button>
        </div>
        <div class="recent-activities">
            <h2>Actividades Recientes</h2>
            <!-- Aquí se listarían las actividades recientes -->
            <ul>
                <!-- Ejemplo de actividades -->
                <li>Usuario1 subió archivo1.jpg</li>
                <li>Usuario2 añadió una nueva nota</li>
            </ul>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>


