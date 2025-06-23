<!-- jQuery library js -->
    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <!-- Apex Chart js -->
    <script src="{{ asset('assets/js/lib/apexcharts.min.js') }}"></script>
    <!-- Data Table js -->
    <script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
    <!-- jQuery UI js -->
    <script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
    <!-- Vector Map js -->
    <script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- Popup js -->
    <script src="{{ asset('assets/js/lib/magnifc-popup.min.js') }}"></script>
    <!-- Slick Slider js -->
    <script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
    <!-- prism js -->
    <script src="{{ asset('assets/js/lib/prism.js') }}"></script>
    <!-- file upload js -->
    <script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>
    <!-- audioplayer -->
    <script src="{{ asset('assets/js/lib/audioplayer.js') }}"></script>

    <!-- main js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <?php echo (isset($script) ? $script   : '')?>

    <!-- Page Loader Script -->
    <script>
        $(document).ready(function() {
            // Hide loader when page is fully loaded
            $(window).on('load', function() {
                $('#page-loader').removeClass('active');
            });

            // Show loader when clicking on links that navigate to new pages
            $(document).on('click', 'a:not([href^="#"]):not([href^="javascript:"]):not([target="_blank"])', function() {
                $('#page-loader').addClass('active');
            });

            // Show loader when submitting forms
            $(document).on('submit', 'form', function() {
                $('#page-loader').addClass('active');
            });

            // Handle browser back/forward buttons
            $(window).on('beforeunload', function() {
                $('#page-loader').addClass('active');
            });
        });
    </script>
