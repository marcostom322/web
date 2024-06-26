<?php 
include '../includes/header.php'; 
include '../includes/nav.php'; 
include '../includes/auth_check.php'; 
include '../php/notes.php';

$colors = [
    'rgba(27, 0, 58, 0.9)',
    'rgba(35, 4, 67, 0.9)',
    'rgba(43, 6, 77, 0.9)',
    'rgba(52, 8, 86, 0.9)',
    'rgba(61, 11, 96, 0.9)',
    'rgba(87, 41, 117, 0.9)',
    'rgba(129, 92, 151, 0.9)'
];
?>
<div class="notes-container">
    <h2>Notas</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" class="add-note-form" enctype="multipart/form-data">
        <div class="image-preview" id="image-preview"></div>
        <textarea name="note" placeholder="Escribe una nueva nota..." required oninput="autoResize(this)" id="note-textarea"></textarea>
        <input type="file" id="file-input" name="image[]" style="display: none;" multiple>
        <button type="button" id="add-file-button"><i class="fas fa-paperclip"></i></button>
        <button type="submit" name="add_note"><i class="fas fa-plus"></i> Agregar Nota</button>
    </form>
    <div class="notes-list">
        <?php foreach ($notes as $note): ?>
            <div class="note" style="background-color: <?php echo $colors[array_rand($colors)]; ?>;">
                <p><?php echo nl2br(htmlspecialchars($note['note'])); ?></p>
                <?php if ($note['image_path']): ?>
                    <img src="../<?php echo $note['image_path']; ?>" alt="Note Image" style="max-width: 100%; margin-top: 10px;">
                <?php endif; ?>
                <p class="note-footer">Creado por: <?php echo htmlspecialchars($note['username']); ?></p>
                <button class="edit-button" onclick="showEditForm(<?php echo $note['id']; ?>)">Editar</button>
                <form method="post" class="edit-note-form" id="edit-form-<?php echo $note['id']; ?>" enctype="multipart/form-data" style="display: none;">
                    <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                    <textarea name="note" oninput="autoResize(this)"><?php echo htmlspecialchars($note['note']); ?></textarea>
                    <input type="file" name="image" accept="image/*">
                    <button type="submit" name="edit_note"><i class="fas fa-check"></i> Aceptar</button>
                </form>
                <form method="post">
                    <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                    <button type="submit" name="delete_note" class="delete-note"><i class="fas fa-trash-alt"></i> Eliminar</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
