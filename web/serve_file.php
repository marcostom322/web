<?php
require 'includes/auth.php';
redirectIfNotLoggedIn();

$file = $_GET['file'] ?? '';
$file = basename($file); // Sanitize input

$allowed_directories = ['imagenes', 'archivos'];
$found = false;

foreach ($allowed_directories as $directory) {
    $file_path = __DIR__ . "/uploads/$directory/$file";
    if (file_exists($file_path) && is_readable($file_path)) {
        $found = true;
        break;
    }
}

if ($found) {
    // Obtener la información del archivo
    $file_info = mime_content_type($file_path);
    $file_size = filesize($file_path);
    $file_name = basename($file_path);

    // Enviar los encabezados apropiados
    header("Content-Type: $file_info");
    header("Content-Length: $file_size");
    header("Content-Disposition: inline; filename=\"$file_name\"");
    readfile($file_path);
    exit();
} else {
    // Archivo no encontrado o no accesible
    header("HTTP/1.0 404 Not Found");
    echo "Archivo no encontrado.";
}
?>
