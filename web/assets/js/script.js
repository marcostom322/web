document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript cargado correctamente');
    
    // Mostrar lentamente la barra de navegación
    const header = document.querySelector('header');
    header.style.opacity = '1';

    // Efecto de hover en los botones
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('mouseover', function() {
            this.style.backgroundColor = '#555';
        });
        button.addEventListener('mouseout', function() {
            this.style.backgroundColor = '#333';
        });
    });
});
