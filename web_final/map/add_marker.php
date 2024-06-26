<?php
include '../php/db.php';
include '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $activity = $_POST['activity'];
    $icon = $_POST['icon'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $stmt = $conn->prepare("INSERT INTO markers (name, activity, icon, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdd", $name, $activity, $icon, $latitude, $longitude);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Marcador agregado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al agregar el marcador: ' . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
