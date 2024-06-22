<?php
require '../includes/auth.php';
redirectIfNotLoggedIn();

require '../includes/db.php';

$page_title = "Subir Archivos";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $image_dir = "../uploads/imagenes/";
    $file_dir = "../uploads/archivos/";

    // Crear directorios si no existen
    if (!is_dir($image_dir)) {
        mkdir($image_dir, 0777, true);
    }
    if (!is_dir($file_dir)) {
        mkdir($file_dir, 0777, true);
    }

    $file_name = basename($_FILES["file"]["name"]);
    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_image_types = array("jpg", "png", "jpeg", "gif");
    $allowed_file_types = array("pdf");

    // Definir el directorio de destino
    if (in_array($file_type, $allowed_image_types)) {
        $target_dir = $image_dir;
    } else {
        $target_dir = $file_dir;
    }

    $target_file = $target_dir . $file_name;

    // Validar si el archivo ya existe
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        exit();
    }

    // Validar el tamaño del archivo
    if ($_FILES["file"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        exit();
    }

    // Validar el tipo de archivo
    if (!in_array($file_type, array_merge($allowed_image_types, $allowed_file_types))) {
        echo "Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed.";
        exit();
    }

    // Subir el archivo
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $file_url = $target_file;
        $file_type = mime_content_type($file_url);
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h1>Subir Archivos</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="file">Selecciona un archivo:</label>
            <input type="file" name="file" id="file" required>
            <button type="submit">Subir</button>
        </form>

        <?php if (isset($file_url)): ?>
            <h3>Archivo subido:</h3>
            <?php if (strpos($file_type, 'image') !== false): ?>
                <img src="../serve_file.php?file=<?php echo urlencode('imagenes/' . basename($file_url)); ?>" alt="Vista previa de la imagen" style="max-width: 100%;">
            <?php else: ?>
                <p><a href="../serve_file.php?file=<?php echo urlencode('archivos/' . basename($file_url)); ?>" target="_blank">Ver archivo subido</a></p>
            <?php endif; ?>
        <?php endif; ?>

        <div class="recent-uploads">
            <h2>Últimos Archivos Subidos</h2>
            <h3>Imágenes</h3>
            <ul>
                <?php
                $images = array_diff(scandir($image_dir), array('.', '..'));
                foreach ($images as $image) {
                    echo '<li><a href="../serve_file.php?file=' . urlencode('imagenes/' . $image) . '">' . htmlspecialchars($image) . '</a></li>';
                }
                ?>
            </ul>
            <h3>Otros Archivos</h3>
            <ul>
                <?php
                $files = array_diff(scandir($file_dir), array('.', '..'));
                foreach ($files as $file) {
                    echo '<li><a href="../serve_file.php?file=' . urlencode('archivos/' . $file) . '">' . htmlspecialchars($file) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
