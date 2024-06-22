<?php
require '../includes/auth.php';
redirectIfNotLoggedIn();

require '../includes/db.php';

$page_title = "Notas";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $note = $_POST['note'];
    $stmt = $pdo->prepare("INSERT INTO notes (user_id, note) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $note]);
}

$stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notas</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main>
        <h1>Notas</h1>
        <form method="POST" action="notes.php">
            <textarea name="note" required></textarea>
            <button type="submit">Guardar Nota</button>
        </form>
        <div class="notes">
            <?php foreach ($notes as $note): ?>
                <div class="note-item">
                    <p><?php echo $note['note']; ?></p>
                    <a href="edit_note.php?id=<?php echo $note['id']; ?>">Editar</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
