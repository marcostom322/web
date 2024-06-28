<?php
include '../php/db.php';
include '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM markers WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Marcador eliminado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el marcador: ' . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
