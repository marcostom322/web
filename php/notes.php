<?php
session_start();
include 'db.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$message = "";

function create_note_directory($note_id) {
    $note_dir = '/uploads/notes/' . $note_id;
    if (!is_dir($note_dir)) {
        mkdir($note_dir, 0777, true);
    }
    return $note_dir;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_note'])) {
        $note = $_POST['note'];
        $image_path = null;

        // Insertar la nota primero para obtener el ID
        $stmt = $conn->prepare("INSERT INTO notes (user_id, note) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("is", $user_id, $note);
            if ($stmt->execute()) {
                $note_id = $stmt->insert_id;
                $stmt->close();

                // Crear directorio para la nota
                $note_dir = create_note_directory($note_id);

                // Guardar la imagen si existe
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $image_path = $note_dir . '/' . basename($_FILES['image']['name']);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                        // Actualizar la nota con la ruta de la imagen
                        $stmt = $conn->prepare("UPDATE notes SET image_path = ? WHERE id = ?");
                        if ($stmt) {
                            $stmt->bind_param("si", $image_path, $note_id);
                            if ($stmt->execute()) {
                                $message = "Nota agregada con éxito.";
                            } else {
                                $message = "Error al agregar la imagen: " . $stmt->error;
                            }
                            $stmt->close();
                        }
                    } else {
                        $message = "Error al mover la imagen.";
                    }
                }
            } else {
                $message = "Error al agregar la nota: " . $stmt->error;
            }
        } else {
            $message = "Error al preparar la declaración: " . $conn->error;
        }
    } elseif (isset($_POST['edit_note'])) {
        $note_id = $_POST['note_id'];
        $note = $_POST['note'];
        $image_path = null;

        // Crear directorio para la nota si no existe
        $note_dir = create_note_directory($note_id);

        // Guardar la imagen si existe
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_path = $note_dir . '/' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                // Actualizar la nota con la ruta de la imagen
                $stmt = $conn->prepare("UPDATE notes SET note = ?, image_path = ? WHERE id = ? AND user_id = ?");
                if ($stmt) {
                    $stmt->bind_param("ssii", $note, $image_path, $note_id, $user_id);
                    if ($stmt->execute()) {
                        $message = "Nota actualizada con éxito.";
                    } else {
                        $message = "Error al actualizar la nota: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $message = "Error al mover la imagen.";
            }
        } else {
            $stmt = $conn->prepare("UPDATE notes SET note = ? WHERE id = ? AND user_id = ?");
            if ($stmt) {
                $stmt->bind_param("sii", $note, $note_id, $user_id);
                if ($stmt->execute()) {
                    $message = "Nota actualizada con éxito.";
                } else {
                    $message = "Error al actualizar la nota: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    } elseif (isset($_POST['delete_note'])) {
        $note_id = $_POST['note_id'];
        $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $note_id, $user_id);
            if ($stmt->execute()) {
                $message = "Nota eliminada con éxito.";
            } else {
                $message = "Error al eliminar la nota: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error al preparar la declaración: " . $conn->error;
        }
    }
}

$notes = [];
if ($user_type == 'admin') {
    $stmt = $conn->prepare("SELECT notes.*, users.username FROM notes JOIN users ON notes.user_id = users.id");
} else {
    $stmt = $conn->prepare("SELECT notes.*, users.username FROM notes JOIN users ON notes.user_id = users.id WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}
?>
