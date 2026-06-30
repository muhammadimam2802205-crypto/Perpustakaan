<script>
    // Toggle sidebar on mobile
    document.querySelector('.sidebar-toggle')?.addEventListener('click', function(e) {
        e.preventDefault();

        if (window.innerWidth <= 768) {
            document.querySelector('.main-sidebar')?.classList.toggle('active');
        }
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            const sidebar = document.querySelector('.main-sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle');

            if (
                sidebar &&
                toggleBtn &&
                sidebar.classList.contains('active')
            ) {
                if (
                    !sidebar.contains(e.target) &&
                    !toggleBtn.contains(e.target)
                ) {
                    sidebar.classList.remove('active');
                }
            }
        }
    });

    // Auto dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';

            setTimeout(() => {
                alert.remove();
            }, 500);
        });
    }, 5000);
</script>