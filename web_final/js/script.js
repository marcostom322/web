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
});

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

function showEditForm(noteId) {
    const form = document.getElementById(`edit-form-${noteId}`);
    form.style.display = 'block';
}
