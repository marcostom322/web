<?php
session_start();
include 'db.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$upload_status = [];

// Función para crear directorios si no existen
function createDirectoryIfNotExists($dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Comprobar si hay archivos seleccionados
    if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
        createDirectoryIfNotExists('../uploads/photos/');
        createDirectoryIfNotExists('../uploads/files/');
        
        foreach ($_FILES['files']['name'] as $key => $name) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $target_dir = '../uploads/photos/';
            } else {
                $target_dir = '../uploads/files/';
            }

            $target_file = $target_dir . basename($name);
            if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO uploads (user_id, file_name, file_path, file_type) VALUES (?, ?, ?, ?)");
                $file_type = mime_content_type($target_file);
                $stmt->bind_param("isss", $user_id, $name, $target_file, $file_type);
                if ($stmt->execute()) {
                    $upload_status[] = "Archivo '$name' subido con éxito.";
                } else {
                    $upload_status[] = "Error al guardar en la base de datos para el archivo '$name'.";
                }
                $stmt->close();
            } else {
                $upload_status[] = "Error al subir el archivo '$name'.";
            }
        }
    } else {
        $upload_status[] = "No se seleccionaron archivos para subir.";
    }
} else {
    $upload_status[] = "Método de solicitud no válido.";
}

// Añadir registros de depuración
error_log("POST: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subida de Archivos</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="panel2">
        <h1>Resultado de la Subida de Archivos</h1>
        <ul>
            <?php foreach ($upload_status as $status): ?>
                <li><?php echo $status; ?></li>
            <?php endforeach; ?>
        </ul>
        <button onclick="window.location.href='../pages/file_explorer.php'">Aceptar</button>
    </div>
</body>
</html>
