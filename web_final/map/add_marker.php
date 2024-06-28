<?php
include '../php/db.php';
include '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $activity = $_POST['activity'];
    $icon_type = $_POST['icon_type'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $stmt = $conn->prepare("INSERT INTO markers (name, activity, icon_type, latitude, longitude) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdd", $name, $activity, $icon_type, $latitude, $longitude);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Marcador agregado exitosamente', 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al agregar el marcador: ' . $conn->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
