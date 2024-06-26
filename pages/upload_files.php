<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>
<?php include '../includes/auth_check.php'; ?>
<div class="panel">
    <h1>Subir Archivos</h1>
    <form action="../php/upload.php" method="post" enctype="multipart/form-data">
        <div class="upload-section" id="upload-section">
            <input type="file" id="file" name="files[]" multiple onchange="previewFiles()">
            <label for="file" id="upload-label">Haz clic para seleccionar archivos o arrástralos aquí</label>
        </div>
        <button type="submit" id="upload-btn" disabled>Subir Archivos</button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>

<script>
function previewFiles() {
    const preview = document.getElementById('upload-section');
    const label = document.getElementById('upload-label');
    const files = document.getElementById('file').files;
    const uploadBtn = document.getElementById('upload-btn');
    
    preview.innerHTML = '';
    preview.appendChild(label); // Reinsert label to ensure it's always there

    if (files.length > 0) {
        uploadBtn.disabled = false; // Enable the upload button
    } else {
        uploadBtn.disabled = true; // Disable the upload button if no files selected
    }

    for (const file of files) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.classList.add('file-item');

            if (file.type.startsWith('image/')) {
                div.innerHTML = `<img src="${e.target.result}" alt="${file.name}" class="file-thumb"><p>${file.name}</p>`;
            } else {
                div.innerHTML = `<i class="${getFileIcon(file.name)} file-icon"></i><p>${file.name}</p>`;
            }

            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}

function getFileIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    switch (ext) {
        case 'jpg': case 'jpeg': case 'png': case 'gif':
            return 'fas fa-file-image';
        case 'pdf':
            return 'fas fa-file-pdf';
        case 'doc': case 'docx':
            return 'fas fa-file-word';
        case 'xls': case 'xlsx':
            return 'fas fa-file-excel';
        case 'ppt': case 'pptx':
            return 'fas fa-file-powerpoint';
        default:
            return 'fas fa-file';
    }
}
</script>
