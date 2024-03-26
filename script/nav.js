document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');

    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        content.classList.add('collapsed');
    }

    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
        menuToggle.style.display = 'block'; 
    });
});
