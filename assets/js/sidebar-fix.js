document.addEventListener('DOMContentLoaded', function () {
    function ajustarSidebar() {
        const sidebar = document.querySelector('#sidebar');
        const menuBar = document.querySelector('#sidebar .menu-bar');

        if (!sidebar || !menuBar) return;

        const sidebarTop = sidebar.getBoundingClientRect().top;
        const alturaViewport = window.innerHeight;

        const alturaDisponivel = alturaViewport - sidebarTop - 10;

        menuBar.style.height = alturaDisponivel + 'px';
        menuBar.style.overflowY = 'auto';
    }

    ajustarSidebar();
    window.addEventListener('resize', ajustarSidebar);
});
