document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.admin-sidebar-group-toggle').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            toggle.parentElement.classList.toggle('open');
        });
    });
});