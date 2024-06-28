<?php
include '../php/db.php';
include '../includes/auth_check.php';

$result = $conn->query("SELECT * FROM markers");
$markers = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $markers[] = $row;
    }
}

echo json_encode($markers);
$conn->close();
?>
