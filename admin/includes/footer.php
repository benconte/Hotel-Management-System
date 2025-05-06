</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggler = document.getElementById('sidebarToggler');
            const sidebar = document.querySelector('.sidebar');
            const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
            
            // Function to toggle sidebar on mobile
            function toggleSidebar() {
                if (window.innerWidth < 768) {
                    sidebar.classList.toggle('show');
                    sidebarBackdrop.classList.toggle('show');
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                }
            }
            
            // Toggle sidebar when navbar toggler is clicked
            sidebarToggler.addEventListener('click', toggleSidebar);
            
            // Close sidebar when backdrop is clicked (mobile only)
            sidebarBackdrop.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>