<?php
session_start();
include '../config.php';
include 'db.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$message = "";
$message_type = "";

function create_note_directory($note_id) {
    $note_dir = '../uploads/notes/' . $note_id;
    if (!is_dir($note_dir)) {
        mkdir($note_dir, 0777, true);
    }
    return $note_dir;
}

function delete_note_directory($note_id) {
    $note_dir = '../uploads/notes/' . $note_id;
    if (is_dir($note_dir)) {
        $files = glob($note_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($note_dir);
    }
}

function addNoteHistory($noteId, $oldNote, $newNote, $userId) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notes_history (note_id, user_id, old_note, new_note) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iiss", $noteId, $userId, $oldNote, $newNote);
        $stmt->execute();
        $stmt->close();
    } else {
        $error_message = "Error al preparar la declaración para notes_history: " . $conn->error;
        error_log($error_message);
        if (DISPLAY_ERRORS) {
            global $message;
            global $message_type;
            $message = $error_message;
            $message_type = "error";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_note'])) {
        $note = $_POST['note'];
        $marker_id = isset($_POST['marker_id']) && !empty($_POST['marker_id']) ? $_POST['marker_id'] : null;
        $image_paths = [];

        // Validar marker_id
        if ($marker_id !== null) {
            $stmt = $conn->prepare("SELECT id FROM markers WHERE id = ?");
            $stmt->bind_param("i", $marker_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $marker_id = null;
            }
            $stmt->close();
        }

        // Insertar la nota
        $stmt = $conn->prepare("INSERT INTO notes (user_id, note, marker_id) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isi", $user_id, $note, $marker_id);
            if ($stmt->execute()) {
                $note_id = $stmt->insert_id;
                $stmt->close();
                $note_dir = create_note_directory($note_id);
                foreach ($_FILES['images']['name'] as $key => $name) {
                    if ($_FILES['images']['error'][$key] == 0) {
                        $image_path = $note_dir . '/' . basename($name);
                        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $image_path)) {
                            $image_paths[] = 'uploads/notes/' . $note_id . '/' . basename($name);
                        } else {
                            $error_message = "Error al mover el archivo subido.";
                            error_log($error_message);
                            if (DISPLAY_ERRORS) {
                                $message = $error_message;
                                $message_type = "error";
                            }
                        }
                    }
                }
                if (!empty($image_paths)) {
                    $image_paths_json = json_encode($image_paths);
                    $stmt = $conn->prepare("UPDATE notes SET image_path = ? WHERE id = ?");
                    if ($stmt) {
                        $stmt->bind_param("si", $image_paths_json, $note_id);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $error_message = "Error al preparar la declaración para actualizar image_path: " . $conn->error;
                        error_log($error_message);
                        if (DISPLAY_ERRORS) {
                            $message = $error_message;
                            $message_type = "error";
                        }
                    }
                }
                $message = "Nota agregada con éxito.";
                $message_type = "success";
            } else {
                $error_message = "Error al ejecutar la declaración para insertar nota: " . $stmt->error;
                error_log($error_message);
                $message = "Error al agregar la nota.";
                $message_type = "error";
                if (DISPLAY_ERRORS) {
                    $message = $error_message;
                }
            }
        } else {
            $error_message = "Error al preparar la declaración para insertar nota: " . $conn->error;
            error_log($error_message);
            $message = "Error al preparar la declaración.";
            $message_type = "error";
            if (DISPLAY_ERRORS) {
                $message = $error_message;
            }
        }
    } elseif (isset($_POST['edit_note'])) {
        $note_id = $_POST['note_id'];
        $note = $_POST['note'];
        $marker_id = isset($_POST['marker_id']) && !empty($_POST['marker_id']) ? $_POST['marker_id'] : null;
        $image_paths = [];

        // Validar marker_id
        if ($marker_id !== null) {
            $stmt = $conn->prepare("SELECT id FROM markers WHERE id = ?");
            $stmt->bind_param("i", $marker_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $marker_id = null;
            }
            $stmt->close();
        }

        $note_dir = create_note_directory($note_id);
        $stmt = $conn->prepare("SELECT note, image_path FROM notes WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $note_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $oldNote = $row['note'];
            $oldImagePaths = json_decode($row['image_path'], true);

            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $image_path = $note_dir . '/' . basename($name);
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $image_path)) {
                        $image_paths[] = 'uploads/notes/' . $note_id . '/' . basename($name);
                    } else {
                        $error_message = "Error al mover el archivo subido.";
                        error_log($error_message);
                        if (DISPLAY_ERRORS) {
                            $message = $error_message;
                            $message_type = "error";
                        }
                    }
                }
            }
            $newImagePaths = array_merge($oldImagePaths, $image_paths);
            $newImagePathsJson = json_encode($newImagePaths);

            $stmt = $conn->prepare("UPDATE notes SET note = ?, image_path = ?, marker_id = ? WHERE id = ? AND user_id = ?");
            if ($stmt) {
                $stmt->bind_param("ssiii", $note, $newImagePathsJson, $marker_id, $note_id, $user_id);
                $stmt->execute();
                addNoteHistory($note_id, $oldNote, $note, $user_id);
                $stmt->close();
                $message = "Nota actualizada con éxito.";
                $message_type = "success";
            } else {
                $error_message = "Error al preparar la declaración para actualizar nota con imagen: " . $conn->error;
                error_log($error_message);
                if (DISPLAY_ERRORS) {
                    $message = $error_message;
                    $message_type = "error";
                }
            }
        } else {
            $error_message = "Error al preparar la declaración para seleccionar nota: " . $conn->error;
            error_log($error_message);
            if (DISPLAY_ERRORS) {
                $message = $error_message;
                $message_type = "error";
            }
        }
    } elseif (isset($_POST['delete_note'])) {
        $note_id = $_POST['note_id'];
        delete_note_directory($note_id);
        $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $note_id, $user_id);
            $stmt->execute();
            $stmt->close();
            $message = "Nota eliminada con éxito.";
            $message_type = "success";
        } else {
            $error_message = "Error al preparar la declaración para eliminar nota: " . $conn->error;
            error_log($error_message);
            $message = "Error al preparar la declaración.";
            $message_type = "error";
            if (DISPLAY_ERRORS) {
                $message = $error_message;
            }
        }
    }
}

$notes = [];
if ($user_type == 'admin') {
    $stmt = $conn->prepare("SELECT notes.*, users.username, markers.name as marker_name FROM notes JOIN users ON notes.user_id = users.id LEFT JOIN markers ON notes.marker_id = markers.id");
} else {
    $stmt = $conn->prepare("SELECT notes.*, users.username, markers.name as marker_name FROM notes JOIN users ON notes.user_id = users.id LEFT JOIN markers ON notes.marker_id = markers.id WHERE notes.user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
    }
}
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
    }
} else {
    $error_message = "Error al preparar la declaración para seleccionar notas: " . $conn->error;
    error_log($error_message);
    if (DISPLAY_ERRORS) {
        $message = $error_message;
        $message_type = "error";
    }
}
function filter_and_sort_notes($conn, $user_type, $user_id, $order, $search) {
    $order_by = $order == 'asc' ? 'ASC' : 'DESC';
    $search_term = '%' . $conn->real_escape_string($search) . '%';

    if ($user_type == 'admin') {
        $stmt = $conn->prepare("SELECT notes.*, users.username, markers.name as marker_name 
                                FROM notes 
                                JOIN users ON notes.user_id = users.id 
                                LEFT JOIN markers ON notes.marker_id = markers.id 
                                WHERE notes.note LIKE ? 
                                ORDER BY notes.created_at $order_by");
        $stmt->bind_param("s", $search_term);
    } else {
        $stmt = $conn->prepare("SELECT notes.*, users.username, markers.name as marker_name 
                                FROM notes 
                                JOIN users ON notes.user_id = users.id 
                                LEFT JOIN markers ON notes.marker_id = markers.id 
                                WHERE notes.user_id = ? AND notes.note LIKE ? 
                                ORDER BY notes.created_at $order_by");
        $stmt->bind_param("is", $user_id, $search_term);
    }

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $notes = [];
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
        $stmt->close();
        return $notes;
    } else {
        return [];
    }
}
?>



