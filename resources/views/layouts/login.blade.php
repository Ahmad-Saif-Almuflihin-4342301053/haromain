<!DOCTYPE html>
<html lang="en">

<head>
    <title>Haromain - {{ $menu ?? '' }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('assets/front-page/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front-page/icomoon/icomoon.css') }}">
    <link rel="stylesheet" media="all"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('assets/front-page/css/vendor.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front-page/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front-page/custom.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="{{ asset('assets/front-page/js/modernizr.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">

    @stack('css')
</head>

<body class="bg-cover"
    style="background-image: url('{{ asset('assets/front-page/images/login-background.svg') }}'); height: 100vh;">

    <div class="preloader-wrapper">
        <div class="preloader"></div>
    </div>

    @yield('content')

    <script src="{{ asset('assets/front-page/js/script.js') }}"></script>
    <script src="{{ asset('assets/front-page/js/plugins.js') }}"></script>


    <script src="{{ asset('assets/front-page/js/jquery-1.11.0.min.js') }}"></script>
    <script src="{{ asset('assets/front-page/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/front-page/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

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
            });
        }

        function alert2(icon, title, text) {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
            });
        }
    </script>

@stack('js')
</body>

</html>
