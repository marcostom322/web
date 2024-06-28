<?php
session_start();
include 'db.php';
include 'auth_check.php';

if ($_SESSION['user_type'] != 'admin') {
    die("No tienes permiso para realizar esta acción.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filename = $_POST['filename'];
    $filepath = "../uploads/photos/$filename";

    if (file_exists($filepath)) {
        unlink($filepath);
        $stmt = $conn->prepare("DELETE FROM uploads WHERE file_name = ?");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $stmt->close();
        echo "success";
    } else {
        echo "Archivo no encontrado.";
    }
}
?>
