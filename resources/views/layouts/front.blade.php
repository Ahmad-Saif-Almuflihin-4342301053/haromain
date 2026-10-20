<!DOCTYPE html>
<html lang="en">

<head>
    <title>Haromain</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/front-page/css/normalize.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/front-page/icomoon/icomoon.css') }}">
    <link rel="stylesheet" type="text/css" media="all"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/front-page/css/vendor.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/front-page/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/front-page/custom.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @stack('css')

    <script src="{{ asset('assets/front-page/js/modernizr.js') }}" defer></script>
    <script src="{{ asset('assets/front-page/js/jquery-1.11.0.min.js') }}"></script>
    @include('layouts.partials.google-tag')
</head>

<body>

    <div class="preloader-wrapper">
        <div class="preloader"></div>
    </div>

    <header id="header">
        <div id="header-wrap">
            <nav class="primary-nav">
                <div class="container">
                    <div class="row d-flex align-items-center">

                        <div class="col-lg-2 col-md-2">
                            <div class="main-logo" style="padding: 1rem;">
                                <a href="{{ url('/') }}">
                                    <img src="{{ asset('assets/haromainv0.png') }}" alt="logo">
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-10 col-md-10">
                            <div class="navbar">
                                <div id="main-nav" class="stellarnav d-flex justify-content-end right">
                                    <ul class="menu-list">

                                        <li>
                                            <a href="{{ url('/auth/login') }}" class="item-anchor btn-yellow"
                                                data-effect="Contact">
                                                Login
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main>
        @yield('content')
    </main>

    <footer id="footer" style="background-color: #FFF8EC; padding-bottom: 30px">
        <div class="container">
            <div class="footer-menu-list">
                <div class="row d-flex flex-wrap justify-content-between">

                    <div class="col-md-5">
                        <div class="py-4">
                            <h2><b>Haromain</b></h2>
                            <p>
                                Sebuah Aplikasi yang dirancang untuk menjadi Pendamping Setia Perjalanan Anda.
                                Dengan HAROMAIN, Perjalanan menjadi NYAMAN dan Keluarga dirumah pun TENANG.
                            </p>

                            <p style="font-weight: 600; color:black;">Kontak Kami</p>

                            <div class="contact-list"
                                style="font-size: 14px; display: grid; grid-template-columns: auto auto;">
                                <div style="margin-bottom: 15px;">
                                    <img src="{{ asset('assets/admin-page/icon/wa.svg') }}" style="width: 30px;">
                                    <a href="https://api.whatsapp.com/send/?phone=6287777235337">+62 877-7723-5337</a>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <img src="{{ asset('assets/admin-page/icon/gmail.svg') }}" style="width: 30px;">
                                    <a href="mailto:92haromain@gmail.com">92haromain@gmail.com</a>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <img src="{{ asset('assets/admin-page/icon/ig.png') }}" style="width: 30px;">
                                    <a href="#">Haromain</a>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <img src="{{ asset('assets/admin-page/icon/fb.png') }}" style="width: 30px;">
                                    <a href="#">Haromain</a>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <img src="{{ asset('assets/admin-page/icon/twitter.png') }}" style="width: 30px;">
                                    <a href="#">@haromain735087</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7 padding-medium text-center">
                        <img src="{{ asset('assets/admin-page/icon/footer2.svg') }}" style="max-width: 400px;">

                        <div class="d-flex justify-content-center" style="margin: 2rem 0 0 0;">
                            <a href="https://play.google.com/store/apps/details?id=com.tiap.haromain.haromain">
                                <img src="{{ asset('assets/admin-page/icon/gplay.png') }}" style="width:200px">
                            </a>

                            &nbsp;&nbsp;&nbsp;

                            <a href="https://apps.apple.com/id/app/haromain/id6736605994">
                                <img src="{{ asset('assets/admin-page/icon/appstore.png') }}" style="width:200px">
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </footer>

    {{-- JS --}}
    <script src="{{ asset('assets/front-page/js/script.js') }}"></script>
    <script src="{{ asset('assets/front-page/js/plugins.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('js')
</body>

</html>
