<?php include '../includes/header.php'; ?>
<?php include '../includes/nav.php'; ?>
<?php include '../includes/auth_check.php'; ?>

<?php
$is_admin = ($_SESSION['user_type'] == 'admin');
$photos = array_diff(scandir('../uploads/photos'), array('.', '..'));
$files = array_diff(scandir('../uploads/files'), array('.', '..'));

function getFileIcon($filename) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    switch ($ext) {
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
?>

<div class="panel">
    <h1>Explorador de Archivos</h1>
    <input type="text" id="search" placeholder="Buscar...">
    <select id="sort">
        <option value="name_asc">Nombre Ascendente</option>
        <option value="name_desc">Nombre Descendente</option>
        <option value="date_asc">Fecha Ascendente</option>
        <option value="date_desc">Fecha Descendente</option>
    </select>
    <div class="tabs">
        <button class="tab-button active" data-tab="photos-tab">Fotos</button>
        <button class="tab-button" data-tab="files-tab">Archivos</button>
    </div>
    <div id="photos-tab" class="tab-content active">
        <h3>Fotos</h3>
        <div class="file-explorer" id="photos-explorer">
            <?php foreach ($photos as $photo): ?>
                <div class="file-item" data-name="<?php echo $photo; ?>" data-date="<?php echo filemtime('../uploads/photos/' . $photo); ?>">
                    <img src="../uploads/photos/<?php echo $photo; ?>" alt="<?php echo $photo; ?>" class="file-thumb" onclick="expandImage(this)">
                    <p><?php echo $photo; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="files-tab" class="tab-content">
        <h3>Archivos</h3>
        <div class="file-explorer" id="files-explorer">
            <?php foreach ($files as $file): ?>
                <div class="file-item" data-name="<?php echo $file; ?>" data-date="<?php echo filemtime('../uploads/files/' . $file); ?>">
                    <i class="<?php echo getFileIcon($file); ?> file-icon"></i>
                    <p><?php echo $file; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>

<div id="fullscreen-container" style="display: none;">
    <button id="close-btn" class="nav-btn" onclick="closeFullscreen()"><i class="fas fa-times"></i></button>
    <button id="prev-btn" class="nav-btn" onclick="prevImage(event)"><i class="fas fa-chevron-left"></i></button>
    <div class="fullscreen-img" id="fullscreen-img"></div>
    <button id="next-btn" class="nav-btn" onclick="nextImage(event)"><i class="fas fa-chevron-right"></i></button>
    <?php if ($is_admin): ?>
        <div class="admin-options">
            <i class="fas fa-ellipsis-v" onclick="toggleAdminMenu(event)"></i>
            <div id="admin-menu" style="display: none;">
                <button onclick="downloadImage(event)">Descargar</button>
                <button onclick="deleteImage(event)">Borrar</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const searchInput = document.getElementById('search');
    const sortSelect = document.getElementById('sort');
    const photosExplorer = document.getElementById('photos-explorer');
    const filesExplorer = document.getElementById('files-explorer');
    let currentImageIndex = 0;
    let images = [];

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(tab => tab.classList.remove('active'));
            
            button.classList.add('active');
            document.getElementById(button.getAttribute('data-tab')).classList.add('active');
        });
    });

    searchInput.addEventListener('input', function() {
        filterFiles();
    });

    sortSelect.addEventListener('change', function() {
        sortFiles();
    });

    function filterFiles() {
        const query = searchInput.value.toLowerCase();
        document.querySelectorAll('.file-item').forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            if (name.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function sortFiles() {
        const sortBy = sortSelect.value;
        const files = Array.from(document.querySelectorAll('.file-item'));

        files.sort((a, b) => {
            const nameA = a.getAttribute('data-name').toLowerCase();
            const nameB = b.getAttribute('data-name').toLowerCase();
            const dateA = parseInt(a.getAttribute('data-date'));
            const dateB = parseInt(b.getAttribute('data-date'));

            switch (sortBy) {
                case 'name_asc':
                    return nameA.localeCompare(nameB);
                case 'name_desc':
                    return nameB.localeCompare(nameA);
                case 'date_asc':
                    return dateA - dateB;
                case 'date_desc':
                    return dateB - dateA;
                default:
                    return 0;
            }
        });

        files.forEach(file => {
            file.parentElement.appendChild(file);
        });
    }

    window.expandImage = function(img) {
        images = Array.from(document.querySelectorAll('.file-thumb'));
        currentImageIndex = images.indexOf(img);

        const fullscreenContainer = document.getElementById('fullscreen-container');
        const fullscreenImg = document.getElementById('fullscreen-img');
        fullscreenImg.innerHTML = `<img src="${img.src}" alt="Full Image">`;
        fullscreenContainer.style.display = 'flex';
    };

    window.closeFullscreen = function() {
        const fullscreenContainer = document.getElementById('fullscreen-container');
        fullscreenContainer.style.display = 'none';
    };

    window.prevImage = function(event) {
        event.stopPropagation();
        if (currentImageIndex > 0) {
            currentImageIndex--;
        } else {
            currentImageIndex = images.length - 1;
        }
        updateFullscreenImage();
    };

    window.nextImage = function(event) {
        event.stopPropagation();
        if (currentImageIndex < images.length - 1) {
            currentImageIndex++;
        } else {
            currentImageIndex = 0;
        }
        updateFullscreenImage();
    };

    function updateFullscreenImage() {
        const img = images[currentImageIndex];
        const fullscreenImg = document.getElementById('fullscreen-img');
        fullscreenImg.innerHTML = `<img src="${img.src}" alt="Full Image">`;
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft') {
            prevImage(event);
        } else if (event.key === 'ArrowRight') {
            nextImage(event);
        } else if (event.key === 'Escape') {
            closeFullscreen();
        }
    });

    window.toggleAdminMenu = function(event) {
        event.stopPropagation();
        const adminMenu = document.getElementById('admin-menu');
        adminMenu.style.display = adminMenu.style.display === 'none' ? 'block' : 'none';
    };

    window.downloadImage = function(event) {
        event.stopPropagation();
        const img = images[currentImageIndex];
        const link = document.createElement('a');
        link.href = img.src;
        link.download = img.src.split('/').pop();
        link.click();
    };

    window.deleteImage = function(event) {
        event.stopPropagation();
        const img = images[currentImageIndex];
        const filename = img.src.split('/').pop();
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../php/delete_image.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (xhr.responseText === 'success') {
                    alert('Imagen eliminada con éxito');
                    images.splice(currentImageIndex, 1);
                    closeFullscreen();
                    img.parentElement.remove();
                } else {
                    alert('Error al eliminar la imagen: ' + xhr.responseText);
                }
            }
        };
        xhr.send('filename=' + encodeURIComponent(filename));
    };
});
</script>
