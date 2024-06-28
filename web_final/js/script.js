document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.dropdown');
    const dropbtn = document.querySelector('.dropbtn');
    
    if (dropbtn && dropdown) {
        dropbtn.addEventListener('click', function() {
            dropdown.classList.toggle('show');
        });
    }

    // Carousel functionality
    let carouselIndex = 0;
    const carouselImages = document.querySelectorAll('.carousel img');
    if (carouselImages.length > 0) {
        carouselImages[carouselIndex].style.display = 'block';
        setInterval(() => {
            carouselImages[carouselIndex].style.display = 'none';
            carouselIndex = (carouselIndex + 1) % carouselImages.length;
            carouselImages[carouselIndex].style.display = 'block';
        }, 3000);
    }

    const textarea = document.getElementById('note-textarea');
    const imagePreview = document.getElementById('image-preview');
    const fileInput = document.getElementById('file-input');
    
    if (textarea && imagePreview && fileInput) {
        textarea.addEventListener('paste', function(event) {
            const items = (event.clipboardData || event.originalEvent.clipboardData).items;
            for (const item of items) {
                if (item.kind === 'file' && item.type.startsWith('image/')) {
                    const file = item.getAsFile();
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Agregar animación de carga
                        imagePreview.innerHTML = `<div class="loader"></div>`;
                        imagePreview.style.display = 'block';
    
                        setTimeout(() => {
                            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Pasted Image" class="fade-in">`;
                            // Create a new file object
                            const newFile = new File([file], file.name, { type: file.type });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(newFile);
                            fileInput.files = dataTransfer.files;
                        }, 500); // Tiempo de animación de carga
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        document.getElementById('add-file-button').addEventListener('click', function() {
            document.getElementById('file-input').click();
        });
        
        fileInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const fileReader = new FileReader();
                fileReader.onload = function(e) {
                    const fileType = file.type.split('/')[0];
                    if (fileType === 'image') {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '100px';
                        img.style.marginRight = '10px';
                        imagePreview.appendChild(img);
                    } else {
                        const icon = document.createElement('div');
                        icon.innerHTML = '<i class="fas fa-file"></i> ' + file.name;
                        icon.style.marginRight = '10px';
                        imagePreview.appendChild(icon);
                    }
                }
                fileReader.readAsDataURL(file);
            });
        });
    }

    // Ampliación de imagen al hacer clic
    document.querySelectorAll('.note img').forEach(img => {
        img.addEventListener('click', function() {
            const overlay = document.createElement('div');
            overlay.classList.add('image-overlay');
            const fullImage = document.createElement('img');
            fullImage.src = img.src;
            fullImage.classList.add('full-image');
            overlay.appendChild(fullImage);
            document.body.appendChild(overlay);

            overlay.addEventListener('click', function() {
                document.body.removeChild(overlay);
            });
        });
    });

    // Sorting functionality
    document.getElementById('sort-select').addEventListener('change', function() {
        sortNotes(this.value);
    });

    // Search functionality
    document.getElementById('search-bar').addEventListener('input', function() {
        filterNotes(this.value);
    });
});

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

function showEditForm(noteId) {
    const form = document.getElementById(`edit-form-${noteId}`);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function openImageFullscreen(img) {
    const overlay = document.createElement('div');
    overlay.classList.add('image-overlay');
    overlay.innerHTML = `<img src="${img.src}" alt="Full Image" class="full-image" />`;
    document.body.appendChild(overlay);
    overlay.addEventListener('click', () => {
        document.body.removeChild(overlay);
    });
}

function sortNotes(order) {
    const notesList = document.querySelector('.notes-list');
    const notes = Array.from(notesList.getElementsByClassName('note'));
    notes.sort((a, b) => {
        const dateA = new Date(a.querySelector('.note-footer').innerText.split(': ')[1]);
        const dateB = new Date(b.querySelector('.note-footer').innerText.split(': ')[1]);
        return order === 'asc' ? dateA - dateB : dateB - dateA;
    });
    notes.forEach(note => notesList.appendChild(note));
}

function filterNotes(query) {
    const notesList = document.querySelector('.notes-list');
    const notes = notesList.getElementsByClassName('note');
    Array.from(notes).forEach(note => {
        const noteText = note.querySelector('p').innerText.toLowerCase();
        if (noteText.includes(query.toLowerCase())) {
            note.style.display = 'block';
        } else {
            note.style.display = 'none';
        }
    });
}
