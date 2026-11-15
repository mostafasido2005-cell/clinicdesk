</div>
<!-- /.wrapper -->

<footer class="main-footer">
    <strong>Copyright &copy; <?= date('Y') ?> 
        <a href="#"><?= APP_NAME ?></a>.
    </strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.0
    </div>
</footer>

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- Toastr -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/toastr/toastr.min.js"></script>
<!-- Chart.js -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/chart.js/Chart.min.js"></script>
<!-- Moment.js -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/plugins/moment/moment.min.js"></script>
<!-- AdminLTE -->
<script src="<?= BASE_URL ?>/public/assets/adminlte/dist/js/adminlte.min.js"></script>

<script>
// Initialize DataTables
$(document).ready(function() {
    if ($('.data-table').length) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
            }
        });
    }

    // Dark Mode
    const darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'enabled') {
        document.getElementById('appBody').classList.add('dark-mode');
        document.getElementById('darkModeIcon').classList.replace('fa-moon', 'fa-sun');
    }

    document.getElementById('darkModeToggle')?.addEventListener('click', function(e) {
        e.preventDefault();
        const body = document.getElementById('appBody');
        const icon = document.getElementById('darkModeIcon');
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            icon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('darkMode', 'disabled');
        } else {
            body.classList.add('dark-mode');
            icon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('darkMode', 'enabled');
        }
    });

    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Notification polling
    function checkNotifications() {
        $.get('<?= BASE_URL ?>/index.php?page=notifications&action=count', function(data) {
            if (data.count > 0) {
                $('.notification-badge').text(data.count).show();
            } else {
                $('.notification-badge').hide();
            }
        });
    }
    setInterval(checkNotifications, 30000);
});
</script>

</body>
</html>