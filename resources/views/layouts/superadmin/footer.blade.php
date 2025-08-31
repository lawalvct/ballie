    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');

            if (sidebar.classList.contains('sidebar-mobile-hidden')) {
                sidebar.classList.remove('sidebar-mobile-hidden');
                sidebar.classList.add('sidebar-mobile-visible');
                overlay.style.display = 'block';
            } else {
                sidebar.classList.add('sidebar-mobile-hidden');
                sidebar.classList.remove('sidebar-mobile-visible');
                overlay.style.display = 'none';
            }
        }

        // Close mobile menu when clicking on overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('mobile-menu-overlay');
            if (overlay) {
                overlay.addEventListener('click', toggleMobileMenu);
            }
        });
    </script>
</body>
</html>
