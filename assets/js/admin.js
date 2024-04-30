window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const passwordIcons = document.querySelectorAll('.password-icon');
    passwordIcons.forEach(function (icon) {
        icon.addEventListener('click', function () {
            const input = icon.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<i class="fas fa-eye-slash eye-icon"></i>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<i class="fas fa-eye eye-icon"></i>';
            }
        });
    });
});
