<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}" class="scroll-smooth">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config("app.name") }}</title>

        @vite(["resources/css/app.css", "resources/js/app.js"])
        @livewireStyles
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />

        @include('layouts.partials.google-tag')
    </head>

    <body class="antialiased">
        <!-- Loading Overlay -->
        <div id="page-loader"
            class="fixed inset-0 bg-white z-9999 flex items-center justify-center transition-opacity duration-500">
            <div class="text-center">
                <img src="{{ asset("assets/logo/Logo-black.svg") }}" alt="Haromain Logo"
                    class="h-16 mx-auto mb-4 animate-pulse">
                <div class="flex space-x-2 justify-center">
                    <div class="w-3 h-3 bg-amber-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-3 h-3 bg-amber-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-3 h-3 bg-amber-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>

        <!-- Livewire Navigation Loading -->
        <div wire:loading.delay class="fixed inset-0 bg-black/30 z-9998 flex items-center justify-center">
            <div class="bg-white rounded-2xl p-8 shadow-2xl">
                <div class="text-center">
                    <img src="{{ asset("assets/logo/Logo-black.svg") }}" alt="Haromain Logo" class="h-12 mx-auto mb-4">
                    <div class="flex space-x-2 justify-center">
                        <div class="w-3 h-3 bg-amber-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                        <div class="w-3 h-3 bg-amber-400 rounded-full animate-bounce" style="animation-delay: 0.2s">
                        </div>
                        <div class="w-3 h-3 bg-amber-400 rounded-full animate-bounce" style="animation-delay: 0.4s">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $activeHome = request()->is('/');
            $activeKatalog = request()->is('katalog*');
            $activeGaleri = request()->is('galeri*');
            $activeHubungi = request()->is('hubungi-kami*');
            $isHome = request()->is('/');
        @endphp

        <!-- Navbar -->
        <nav class="w-full z-50 top-0 start-0 transition-all duration-300 {{ request()->routeIs('home') ? 'fixed bg-transparent' : 'sticky bg-white! border-b border-gray-200' }}"
            x-data="{
                scrolled: (window.pageYOffset > 50) || (window.location.hash === '#fitur' || window.location.hash === '#faq'),
                mobileMenuOpen: false,
                activeSection: (window.location.hash === '#fitur') ? 'fitur' : ((window.location.hash === '#faq') ? 'faq' : 'home'),
                updateActive() {
                    if (!{{ $isHome ? 'true' : 'false' }}) return;
                    const faqEl = document.getElementById('faq');
                    const fiturEl = document.getElementById('fitur');
                    if (faqEl) {
                        const faqRect = faqEl.getBoundingClientRect();
                        const isBottom = (window.innerHeight + window.pageYOffset) >= (document.documentElement.scrollHeight - 80);
                        if ((faqRect.top <= 250 && faqRect.bottom >= 100) || (isBottom && faqRect.top < window.innerHeight)) {
                            this.activeSection = 'faq';
                            return;
                        }
                    }
                    if (fiturEl) {
                        const fiturRect = fiturEl.getBoundingClientRect();
                        if (fiturRect.top <= 250 && fiturRect.bottom >= 150) {
                            this.activeSection = 'fitur';
                            return;
                        }
                    }
                    if (window.pageYOffset < 350) {
                        this.activeSection = 'home';
                        return;
                    }
                    this.activeSection = '';
                }
            }"
            @scroll.window.passive="scrolled = window.pageYOffset > 50; updateActive()"
            @resize.window.passive="updateActive()"
            @hashchange.window="
                setTimeout(() => {
                    scrolled = window.pageYOffset > 50;
                    updateActive();
                }, 50)
            "
            x-init="
                scrolled = (window.pageYOffset > 50) || (window.location.hash === '#fitur' || window.location.hash === '#faq');
                updateActive();
                $nextTick(() => {
                    scrolled = (window.pageYOffset > 50) || (window.location.hash === '#fitur' || window.location.hash === '#faq');
                    updateActive();
                    setTimeout(() => {
                        scrolled = window.pageYOffset > 50;
                        updateActive();
                    }, 250);
                    setTimeout(() => {
                        scrolled = window.pageYOffset > 50;
                        updateActive();
                    }, 500);
                });
                document.addEventListener('livewire:navigated', () => {
                    scrolled = window.pageYOffset > 50;
                    updateActive();
                    setTimeout(() => {
                        scrolled = window.pageYOffset > 50;
                        updateActive();
                    }, 100);
                });
            "
            :class="{
                'bg-white shadow-md': scrolled || mobileMenuOpen,
                'bg-white': mobileMenuOpen
            }">
            <div class="max-w-7xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="/" class="flex items-center space-x-3">
                    <img :src="(scrolled || mobileMenuOpen || !{{ request()->routeIs('home') ? 'true' : 'false' }}) ?
                    '{{ asset('assets/logo/Logo-black.svg') }}' :
                    '{{ asset('assets/logo/Logo.svg') }}'"
                        alt="Haromain Logo" class="h-8 md:h-10 transition-all duration-300"
                        :class="{ 'opacity-80': mobileMenuOpen, 'opacity-100': !mobileMenuOpen }">
                </a>
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm rounded-lg md:hidden focus:outline-none transition-all duration-300"
                    :class="mobileMenuOpen
                        ?
                        'bg-white text-gray-900 shadow-none' :
                        (((scrolled || !{{ request()->routeIs('home') ? 'true' : 'false' }})) ? 'text-gray-500' :
                            'text-white')">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5 transition-all duration-300" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
                <div class="hidden md:flex md:items-center md:space-x-2">
                    <a href="{{ url('/') }}" wire:navigate
                        @if ($isHome) @click.prevent="activeSection = 'home'; window.scrollTo({ top: 0, behavior: 'smooth' }); history.pushState(null, '', '{{ url('/') }}')" @endif
                        class="py-2 px-3 rounded transition-color cursor-pointer"
                        :class="(scrolled || !{{ $isHome ? 'true' : 'false' }}) ?
                        ({{ $isHome ? "activeSection === 'home'" : '$activeHome' }} ? 'text-amber-400! hover:text-amber-400!' : 'text-gray-900! hover:text-amber-400!') :
                        ({{ $isHome ? "activeSection === 'home'" : '$activeHome' }} ? 'text-amber-400! hover:text-amber-400!' : 'text-white! hover:text-amber-400!')">
                        Beranda
                    </a>
                    <a href="{{ $isHome ? '#fitur' : url('/#fitur') }}"
                        @if ($isHome) @click.prevent="activeSection = 'fitur'; scrolled = true; document.getElementById('fitur')?.scrollIntoView({ behavior: 'smooth' })" @endif
                        class="py-2 px-3 rounded transition-color cursor-pointer"
                        :class="(scrolled || !{{ $isHome ? 'true' : 'false' }}) ?
                        ({{ $isHome ? "activeSection === 'fitur'" : 'false' }} ? 'text-amber-400! hover:text-amber-400!' : 'text-gray-900! hover:text-amber-400!') :
                        ({{ $isHome ? "activeSection === 'fitur'" : 'false' }} ? 'text-amber-400! hover:text-amber-400!' : 'text-white! hover:text-amber-400!')">
                        Fitur
                    </a>
                    <a href="{{ url('/galeri') }}" wire:navigate class="py-2 px-3 rounded transition-color"
                        :class="(scrolled || !{{ $isHome ? 'true' : 'false' }}) ?
                        '{{ $activeGaleri ? 'text-amber-400! hover:text-amber-400!' : 'text-gray-900! hover:text-amber-400!' }}' :
                        '{{ $activeGaleri ? 'text-amber-400! hover:text-amber-400!' : 'text-white! hover:text-amber-400!' }}'">
                        Panduan
                    </a>
                    <a href="{{ $isHome ? '#faq' : url('/#faq') }}"
                        @if ($isHome) @click.prevent="activeSection = 'faq'; scrolled = true; document.getElementById('faq')?.scrollIntoView({ behavior: 'smooth' })" @endif
                        class="py-2 px-3 rounded transition-color cursor-pointer"
                        :class="(scrolled || !{{ $isHome ? 'true' : 'false' }}) ?
                        ({{ $isHome ? "activeSection === 'faq'" : 'false' }} ? 'text-amber-400! hover:text-amber-400!' : 'text-gray-900! hover:text-amber-400!') :
                        ({{ $isHome ? "activeSection === 'faq'" : 'false' }} ? 'text-amber-400! hover:text-amber-400!' : 'text-white! hover:text-amber-400!')">
                        FAQ
                    </a>
                    <a href="{{ url('/hubungi-kami') }}" wire:navigate class="py-2 px-3 rounded transition-color"
                        :class="(scrolled || !{{ $isHome ? 'true' : 'false' }}) ?
                        '{{ $activeHubungi ? 'text-amber-400! hover:text-amber-400!' : 'text-gray-900! hover:text-amber-400!' }}' :
                        '{{ $activeHubungi ? 'text-amber-400! hover:text-amber-400!' : 'text-white! hover:text-amber-400!' }}'">
                        Hubungi Kami
                    </a>
                </div>
                <!-- Mobile menu -->
                <div x-show="mobileMenuOpen" x-transition:enter="transition-all ease-out duration-500"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition-all ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
                    class="absolute top-full left-0 right-0 bg-white shadow-lg md:hidden z-40"
                    style="will-change: opacity, transform;">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="{{ url('/') }}" wire:navigate
                            @click="mobileMenuOpen = false; @if ($isHome) activeSection = 'home'; $nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' })) @endif"
                            class="block px-3 py-2 rounded-md hover:text-amber-400! hover:bg-gray-100 transition-all duration-200"
                            :class="{{ $isHome ? "activeSection === 'home'" : '$activeHome' }} ? 'text-amber-400!' : 'text-gray-900!'">Beranda</a>
                        <a href="{{ $isHome ? '#fitur' : url('/#fitur') }}"
                            @click="mobileMenuOpen = false; @if ($isHome) activeSection = 'fitur'; scrolled = true; $nextTick(() => document.getElementById('fitur')?.scrollIntoView({ behavior: 'smooth' })) @endif"
                            class="block px-3 py-2 rounded-md hover:text-amber-400! hover:bg-gray-100 transition-all duration-200"
                            :class="{{ $isHome ? "activeSection === 'fitur'" : 'false' }} ? 'text-amber-400!' : 'text-gray-900!'">Fitur</a>
                        <a href="{{ url('/galeri') }}" wire:navigate @click="mobileMenuOpen = false"
                            class="block px-3 py-2 rounded-md {{ $activeGaleri ? 'text-amber-400!' : 'text-gray-900!' }} hover:text-amber-400! hover:bg-gray-100 transition-all duration-200">Panduan</a>
                        <a href="{{ $isHome ? '#faq' : url('/#faq') }}"
                            @click="mobileMenuOpen = false; @if ($isHome) activeSection = 'faq'; scrolled = true; $nextTick(() => document.getElementById('faq')?.scrollIntoView({ behavior: 'smooth' })) @endif"
                            class="block px-3 py-2 rounded-md hover:text-amber-400! hover:bg-gray-100 transition-all duration-200"
                            :class="{{ $isHome ? "activeSection === 'faq'" : 'false' }} ? 'text-amber-400!' : 'text-gray-900!'">FAQ</a>
                        <a href="{{ url('/hubungi-kami') }}" wire:navigate @click="mobileMenuOpen = false"
                            class="block px-3 py-2 rounded-md {{ $activeHubungi ? 'text-amber-400!' : 'text-gray-900!' }} hover:text-amber-400! hover:bg-gray-100 transition-all duration-200">Hubungi
                            Kami</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="{{ request()->routeIs("home") ? "z-10" : "" }}">
            {{ $slot }}
        </main>

        @php
            $globalSetting = app(\App\Services\Landing\BerandaService::class)->getGlobalSetting();
        @endphp

        <!-- Floating Konsultasi Button -->
        <a href="https://api.whatsapp.com/send/?phone={{ $globalSetting["WhatsApp"] ?? "6287777235337" }}"
            target="_blank"
            class="fixed bottom-6 right-6 z-50 bg-amber-500 hover:bg-amber-400 text-white px-5 md:px-6 py-2.5 md:py-3 rounded-full shadow-lg flex items-center space-x-1 transition-all duration-300 hover:scale-105">
            <img src="{{ asset("assets/icon/whatsapp.svg") }}" alt="">
            <span class="font-medium text-sm md:text-base">Konsultasi</span>
        </a>

        <!-- Footer -->
        <footer class="bg-amber-800 text-white">
            <div class="max-w-7xl mx-auto px-4 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Company Info -->
                    <div class="space-y-4">
                        <img src="{{ asset("assets/logo/Logo.svg") }}" alt="Haromain Logo" class="h-10">
                        <p class="text-sm text-gray-200 leading-relaxed">
                            {{ $globalSetting["DeskripsiFooter"] ?? "Haromain adalah platform pendampingan jamaah haji dan umroh yang dikembangkan dari pengalaman serta kondisi nyata di lapangan." }}
                        </p>
                        <div class="flex space-x-4">
                            <a href="{{ $globalSetting["Instagram"] ?? "https://www.instagram.com/haromain.indonesia" }}"
                                class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition mr-0!">
                                <img src="{{ asset("assets/icon/bxl-instagram-alt.svg") }}" alt="Instagram">
                            </a>
                            <a href="{{ $globalSetting["Facebook"] ?? "https://web.facebook.com/profile.php?id=61584806686309" }}"
                                class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                                <img src="{{ asset("assets/icon/bxl-facebook-circle.svg") }}" alt="Facebook">

                            </a>
                            <a href="{{ $globalSetting["TikTok"] ?? "https://www.tiktok.com/@haromain.indonesi?_r=1&_t=ZS-92LGAA16RhB/" }}"
                                class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition">
                                <img src="{{ asset("assets/icon/bxl-tiktok.svg") }}" alt="TikTok">
                            </a>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                        <div class="space-y-3 text-sm text-gray-200">
                            <a class="flex items-center space-x-3"
                                href="https://api.whatsapp.com/send/?phone={{ $globalSetting["WhatsApp"] ?? "6287777235337" }}"
                                target="_blank">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $globalSetting["WhatsAppText"] ?? "+62 877 7723 5337" }}</span>
                            </a>
                            <a class="flex items-center space-x-3"
                                href="mailto:{{ $globalSetting["Email"] ?? "haromain@afindo-inf.com" }}"
                                target="_blank">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $globalSetting["Email"] ?? "haromain@afindo-inf.com" }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <div class="space-y-2 text-sm text-gray-200">
                            <a href="{{ route("privacy-policy") }}"
                                class="block hover:text-amber-300 transition">Kebijakan Privasi</a>
                            <span class="block hover:text-amber-300 transition">Hak Cipta</span>
                            <!-- App Store & Play Store -->
                            <div class="mt-4 flex space-x-3">
                                <a href="{{ $globalSetting["AppStore"] ?? "https://apps.apple.com/id/app/haromain/id6736605994" }}"
                                    target="_blank" class="block">
                                    <img src="{{ asset("assets/admin-page/icon/appstore.png") }}"
                                        alt="Download on the App Store" class="h-10 w-auto">
                                </a>
                                <a href="{{ $globalSetting["PlayStore"] ?? "https://play.google.com/store/apps/details?id=com.tiap.haromain.haromain" }}"
                                    target="_blank" class="block">
                                    <img src="{{ asset("assets/admin-page/icon/gplay.png") }}"
                                        alt="Get it on Google Play" class="h-10 w-auto">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-amber-700 text-center text-sm text-gray-300">
                    <p>&copy; {{ date("Y") }} Haromain. All rights reserved.</p>
                </div>
            </div>
        </footer>

        @livewireScripts
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

        <!-- Page Loading Script -->
        <script>
            // Initial page load
            document.addEventListener('DOMContentLoaded', function() {
                const loader = document.getElementById('page-loader');

                window.addEventListener('load', function() {
                    setTimeout(function() {
                        loader.style.opacity = '0';
                        setTimeout(function() {
                            loader.style.display = 'none';
                        }, 500);
                    }, 300);
                });

                if (window.location.hash) {
                    setTimeout(function() {
                        const el = document.querySelector(window.location.hash);
                        if (el) el.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }, 400);
                }
            });

            // Livewire navigation loading
            document.addEventListener('livewire:navigating', function() {
                // console.log('Navigation started...');
            });

            document.addEventListener('livewire:navigated', function() {
                // console.log('Navigation completed!');
                if (window.location.hash) {
                    const el = document.querySelector(window.location.hash);
                    if (el) {
                        setTimeout(function() {
                            el.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }, 100);
                    } else {
                        window.scrollTo(0, 0);
                    }
                } else {
                    window.scrollTo(0, 0);
                }
                // untuk inisiasi ulang flowbite js, jika menggunakan livewire navigate
                if (window.initFlowbite) {
                    initFlowbite();
                }
                // Hide loader if still visible after navigation
                const loader = document.getElementById('page-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 500);
                }

                // Track page view on Livewire navigation
                if (typeof gtag === 'function') {
                    gtag('config', 'AW-17481952180', {
                        'page_path': window.location.pathname
                    });
                }
            });
        </script>
    </body>

</html>
