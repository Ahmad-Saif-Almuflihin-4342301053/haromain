<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Haromain - {{ $menu ?? '' }}</title>

    @php
        $version = file_exists(public_path('assets/admin-page/css/custom.css'))
            ? filemtime(public_path('assets/admin-page/css/custom.css')) . '_v2'
            : time();
    @endphp

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/mdi/css/materialdesignicons.min.css?v=' . $version) }}">
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/flag-icon-css/css/flag-icon.min.css?v=' . $version) }}">
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/css/vendor.bundle.base.css?v=' . $version) }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/jquery-bar-rating/css-stars.css?v=' . $version) }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/font-awesome/css/font-awesome.min.css?v=' . $version) }}" />
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/select2/select2.min.css?v=' . $version) }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/datatable/datatable.css?v=' . $version) }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/admin-page/vendors/select2-bootstrap-theme/select2-bootstrap.min.css?v=' . $version) }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/datepicker/datepicker.css?v=' . $version) }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css?v=' . $version) }}">
    <link rel="stylesheet" href="{{ asset('assets/admin-page/vendors/toggle/toggle.css?v=' . $version) }}" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/imgareaselect/0.9.10/css/imgareaselect-default.css"
        referrerpolicy="no-referrer" />
    <!-- endinject -->

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/admin-page/css/demo_1/style.css?v=' . $version) }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin-page/css/custom.css?v=' . $version) }}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" />

    <!-- plugins:js -->
    <script src="{{ asset('assets/admin-page/vendors/js/vendor.bundle.base.js?v=' . $version) }}"></script>
    <!-- endinject -->

    <style>
        .hidden {
            display: none;
        }
    </style>
    @stack('css')
</head>

<body>
    <div class="container-scroller">
        @include('layouts.sidebar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            @include('layouts.topbar')
            @yield('content')
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/admin-page/vendors/jquery-bar-rating/jquery.barrating.min.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/chart.js/Chart.min.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/flot/jquery.flot.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/flot/jquery.flot.resize.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/flot/jquery.flot.categories.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/flot/jquery.flot.fillbetween.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/flot/jquery.flot.stack.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/select2/select2.min.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js?v=' . $version) }}"></script>

    <!-- inject:js -->
    <script src="{{ asset('assets/admin-page/js/off-canvas.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/js/hoverable-collapse.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/js/misc.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/js/file-upload.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/datatable/jquery.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/datatable/datatable.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/datatable/buttons.min.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/datatable/jszip.min.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/datatable/buttons-html5.min.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/datepicker/datepicker.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/vendors/toggle/toggle.js?v=' . $version) }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('assets/admin-page/js/selectarea.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/js/crop.js?v=' . $version) }}"></script>
    <script src="{{ asset('assets/admin-page/js/sidebar-active.js?v=' . $version) }}"></script>


    <script>
        const baseURL = "{{ url('/') }}/";

        function toast(icon, text) {
            Swal.fire({
                icon: icon,
                text: text,
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            })
        }

        function alert2(icon, title, text) {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
            })
        }

        function format_nominal(nominal) {
            if (typeof nominal === 'number') {
                return nominal.toLocaleString();
            }
            if (!nominal) return '';
            let unformattedNumber = nominal.replace(/,/g, '').replace(/[^\d-]/g, "");
            return Number(unformattedNumber).toLocaleString();
        }

        function formatRupiah(angka, prefix) {
            var number_string = String(angka).replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            var decimal = split[1] !== undefined ? split[1] : '';
            var koma = decimal.length > 2 ? decimal.substr(0, 2) : decimal;

            rupiah = split[1] !== undefined ? rupiah + ',' + koma : rupiah;
            var minus = angka < 0 ? '- ' : '';
            return prefix === undefined ? minus + rupiah : (rupiah ? 'Rp. ' + minus + rupiah : '');
        }

        function format_date_ymd(originalDateString) {
            if (!originalDateString || originalDateString === '0000-00-00 00:00:00') return '';
            return originalDateString.split(" ")[0];
        }

        function format_date_dmy(originalDateString) {
            if (!originalDateString || originalDateString === '0000-00-00 00:00:00') return '';
            let [date] = originalDateString.split(" ");
            let [y, m, d] = date.split("-");
            return `${d}-${m}-${y}`;
        }

        function format_date_hm(originalDateString) {
            if (!originalDateString || originalDateString === '0000-00-00 00:00:00') return '';
            let time = originalDateString.split(" ")[1];
            let [h, min] = time.split(":");
            return `${h}:${min}`;
        }

        $(document).ready(function() {
            $('.select2').select2();
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
        });
    </script>

    {{-- Include additional scripts --}}
    @if (isset($scripts))
        @if (is_array($scripts))
            @foreach ($scripts as $script)
                @include($script)
            @endforeach
        @else
            @include($scripts)
        @endif
    @endif

    @stack('js')

</body>

</html>
