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
    <h1><i class="icon fas fa-sticky-note"></i>&nbspNotas</h1>
    <?php if ($message): ?>
        <p class="message <?php echo $message_type; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" class="add-note-form" enctype="multipart/form-data">
        <div class="image-preview" id="image-preview"></div>
        <textarea name="note" placeholder="Escribe una nueva nota..." required oninput="autoResize(this)" id="note-textarea"></textarea>
        <div class="form-group">
            <input type="file" id="file-input" name="images[]" multiple style="display: none;">
            <button type="button" id="add-file-button"><i class="fas fa-paperclip"></i></button>
            <select name="marker_id">
                <option value="">Seleccionar marcador (opcional)</option>
                <?php
                $query = "SELECT id, name FROM markers";
                $result = $conn->query($query);
                if (!$result) {
                    echo "<p>Error al obtener los marcadores: " . $conn->error . "</p>";
                } else {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <button type="submit" name="add_note"><i class="fas fa-plus"></i> Agregar Nota</button>
    </form>
    <div class="sort-search-container">
        <input type="text" id="search-bar" placeholder="Buscar notas...">
        <select id="sort-select">
            <option value="asc">Fecha Ascendente</option>
            <option value="desc">Fecha Descendente</option>
        </select>
    </div>
    <div class="notes-list">
        <?php foreach ($notes as $note): ?>
            <div class="note" style="background-color: <?php echo $colors[array_rand($colors)]; ?>;">
                <p><?php echo nl2br(htmlspecialchars($note['note'])); ?></p>
                <?php 
                if ($note['image_path']): 
                    $images = json_decode($note['image_path'], true);
                    foreach ($images as $image): 
                        $file_extension = pathinfo($image, PATHINFO_EXTENSION);
                        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                            <img src="../<?php echo $image; ?>" alt="Note Image" style="max-width: 100px; margin-right: 10px;" onclick="openImageFullscreen(this)">
                        <?php else: ?>
                            <div class="file-icon"><i class="fas fa-file"></i> <a href="../<?php echo $image; ?>" target="_blank">Ver archivo</a></div>
                        <?php endif; 
                    endforeach; 
                endif; 
                ?>
                <?php if ($note['marker_name']): ?>
                    <p><a href="../map/index.php?marker_id=<?php echo $note['marker_id']; ?>">Ver en el mapa: <?php echo htmlspecialchars($note['marker_name']); ?></a></p>
                <?php endif; ?>
                <p class="note-footer">Creado por: <?php echo htmlspecialchars($note['username']); ?></p>
                <button class="edit-button" onclick="showEditForm(<?php echo $note['id']; ?>)">Editar</button>
                <form method="post" class="edit-note-form" id="edit-form-<?php echo $note['id']; ?>" enctype="multipart/form-data" style="display: none;">
                    <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                    <textarea name="note" oninput="autoResize(this)"><?php echo htmlspecialchars($note['note']); ?></textarea>
                    <input type="file" name="images[]" multiple accept="image/*, .pdf, .doc, .docx">
                    <select name="marker_id">
                        <option value="">Seleccionar marcador (opcional)</option>
                        <?php
                        $query = "SELECT id, name FROM markers";
                        $result = $conn->query($query);
                        if (!$result) {
                            echo "<option>Error al obtener los marcadores: " . $conn->error . "</option>";
                        } else {
                            while ($row = $result->fetch_assoc()) {
                                $selected = $row['id'] == $note['marker_id'] ? 'selected' : '';
                                echo "<option value=\"{$row['id']}\" $selected>{$row['name']}</option>";
                            }
                        }
                        ?>
                    </select>
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

