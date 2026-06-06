    </div><!-- /.content -->
</div><!-- /.main-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Theme toggle
(function(){
    var btn = document.getElementById('themeToggle');
    if(!btn) return;
    var html = document.documentElement;
    var icon = btn.querySelector('i');
    function setTheme(t){
        html.setAttribute('data-theme', t);
        localStorage.setItem('theme', t);
        icon.className = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
    var current = html.getAttribute('data-theme') || 'light';
    setTheme(current);
    btn.addEventListener('click', function(){
        var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        setTheme(next);
    });
})();

// Close sidebar when clicking a nav link on mobile
document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var navLinks = sidebar ? sidebar.querySelectorAll('.nav-link') : [];
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
            }
        });
    });

    // Close sidebar when clicking overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
});
</script>
</body>
</html>
