<div>
    <!-- Hero Slider Section -->
    <section class="relative h-screen pt-20 z-10" x-data="{ currentSlide: 0, slides: {{ count($this->listSlider) }} }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % slides }, 5000)">
        @foreach ($this->listSlider as $key => $slider)
            <!-- Slide 1 -->
            @php
                $bannerFile = $slider['Gambar'] ?? '';
                if ($bannerFile && file_exists(public_path('assets/banner/' . $bannerFile))) {
                    $bannerUrl = asset('assets/banner/' . $bannerFile);
                } elseif ($bannerFile && file_exists(public_path('assets/konten/' . $bannerFile))) {
                    $bannerUrl = asset('assets/konten/' . $bannerFile);
                } else {
                    $fallbackBanners = [
                        asset('assets/banner/1788334763.jpg'),
                        asset('assets/banner/1788334800.jpg'),
                        asset('assets/banner/1769871663.jpg'),
                        asset('assets/banner/1771813736.jpg'),
                    ];
                    $bannerUrl = $fallbackBanners[$key % count($fallbackBanners)];
                }
            @endphp
            <div x-show="currentSlide === {{ $key }}" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-1000" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ $bannerUrl }}');">
                <!-- Gradient Overlay from bottom to top -->
                <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-transparent"></div>
                <div class="relative h-full flex items-end pb-24 md:pb-32">
                    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 w-full">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-7 lg:gap-12 items-end">
                            <!-- Title - Left -->
                            <div class="text-white text-center sm:text-center! md:text-center! lg:text-left!">
                                <h1 class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight">
                                    {{ $slider['Keterangan1'] }}
                                </h1>
                            </div>
                            <!-- Subtitle - Right -->
                            <div
                                class="text-white mt-6 lg:mt-0 text-center sm:text-center! md:text-center! lg:text-left!">
                                <p class="text-md md:text-base lg:text-lg text-gray-200 leading-relaxed">
                                    {{ $slider['Keterangan2'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Navigation Arrows (hidden on small screens, visible md and up) -->
        <!-- Previous Button -->
        <button @click="currentSlide = currentSlide > 0 ? currentSlide - 1 : slides - 1"
            class="hidden md:flex absolute left-4 md:left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 md:w-14 md:h-14 bg-black/25! hover:bg-black/50! rounded-full items-center justify-center transition-all duration-300 group cursor-pointer">
            <svg class="w-6 h-6 md:w-7 md:h-7 text-white group-hover:scale-110 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Next Button -->
        <button @click="currentSlide = currentSlide < slides - 1 ? currentSlide + 1 : 0"
            class="hidden md:flex absolute right-4 md:right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 md:w-14 md:h-14 bg-black/25! hover:bg-black/50! rounded-full items-center justify-center transition-all duration-300 group cursor-pointer">
            <svg class="w-6 h-6 md:w-7 md:h-7 text-white group-hover:scale-110 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Slider Indicators -->
        <div class="absolute bottom-8 md:bottom-12 left-0 right-0 flex justify-center space-x-3 z-10">
            <template x-for="i in slides" :key="i">
                <button @click="currentSlide = i - 1"
                    class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full transition-all duration-300 cursor-pointer"
                    :class="currentSlide === i - 1 ? 'bg-white' : 'bg-white/50'"></button>
            </template>
        </div>
    </section>

    <!-- Section 1: Pelayanan Lebih Baik -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 md:mb-10 text-gray-900">
                {{ @$this->memahamiTentang->JudulKonten }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Kolom Kiri: Gambar -->
                <div>
                    <img src="{{ asset('assets/konten/' . (@$this->memahamiTentang->GambarKonten ?: 'image1.jpeg')) }}"
                        alt="{{ @$this->memahamiTentang->JudulKonten }}"
                        class="w-full h-72 md:h-84 lg:h-96 object-cover rounded-2xl shadow-md">
                </div>

                <!-- Kolom Kanan: Teks & Poin (Format HTML dari DB) -->
                <div class="space-y-3">
                    {!! @$this->memahamiTentang->IsiKonten !!}
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Bukan Sekadar Aplikasi, Haromain Meningkatkan Standar Pelayanan Travel -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 text-gray-900">
                {{ @$this->peranHaromain->JudulKonten }}
            </h2>
            <p class="text-center text-gray-600 max-w-3xl mx-auto mb-8 md:mb-10 text-base md:text-lg leading-normal">
                {!! @$this->peranHaromain->IsiKonten !!}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 text-center">
                @foreach ($this->peranHaromainList as $item)
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 flex items-center justify-center mb-3">
                            <img src="{{ asset('assets/konten/' . $item['GambarKonten']) }}" alt="{{ $item['JudulKonten'] }}" class="w-12 h-12">
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $item['JudulKonten'] }}</h3>
                        <p class="text-gray-600 leading-normal text-sm md:text-base">
                            {!! $item['IsiKonten'] !!}
                        </p>
                    </div>
                @endforeach
            </div>

            <p class="text-center text-gray-800 font-medium max-w-2xl mx-auto mt-8 md:mt-10 leading-normal">
                {!! @$this->peranHaromainFooter->IsiKonten !!}
            </p>
        </div>
    </section>

    <!-- Section 3: Satu Aplikasi untuk Setiap Tahapan Perjalanan (Fitur Unggulan) -->
    <section id="fitur" class="py-12 md:py-16 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 text-gray-900">
                {{ @$this->fiturUnggulan->JudulKonten }}
            </h2>
            <p class="text-center text-gray-600 max-w-3xl mx-auto pb-12 md:pb-16 text-base md:text-lg leading-normal">
                {!! @$this->fiturUnggulan->IsiKonten !!}
            </p>

            <div>
                @foreach ($this->fiturUnggulanList as $key => $item)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start {{ $loop->last ? '' : 'pb-12 md:pb-16' }}">
                        <div class="{{ $key % 2 == 1 ? 'order-1 md:order-2' : '' }}">
                            <img src="{{ asset('assets/konten/' . ($item['GambarKonten'] ?: 'image' . ($key + 2) . '.jpeg')) }}"
                                alt="{{ $item['JudulKonten'] }}"
                                class="w-full h-auto rounded-2xl shadow-md object-cover">
                        </div>
                        <div class="{{ $key % 2 == 1 ? 'order-2 md:order-1' : '' }}">
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                                {{ $item['JudulKonten'] }}
                            </h3>
                            {!! $item['IsiKonten'] !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 4: Satu Sistem, Manfaat untuk Semua (Target Pengguna) -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 text-gray-900">
                {{ @$this->siapaYang->JudulKonten }}
            </h2>
            <p class="text-center text-gray-600 max-w-3xl mx-auto mb-10 md:mb-12 text-base md:text-lg leading-normal">
                {!! @$this->siapaYang->IsiKonten !!}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10 text-center">
                @foreach ($this->siapaYangList as $item)
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 flex items-center justify-center mb-3">
                            <img src="{{ asset('assets/konten/' . $item['GambarKonten']) }}" alt="{{ $item['JudulKonten'] }}" class="w-12 h-12">
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $item['JudulKonten'] }}</h3>
                        {!! $item['IsiKonten'] !!}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 5: Ketika Jamaah Terpisah dari Rombongan (Video Modal Player) -->
    <section class="pt-10 pb-6 md:pt-14 md:pb-8 bg-white" x-data="{
        showModal: false,
        videoId: null,
        openModal(id) {
            this.videoId = id;
            this.showModal = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.showModal = false;
            this.videoId = null;
            document.body.classList.remove('overflow-hidden');
        }
    }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Video / Thumbnail Container -->
                <div>
                    @php
                        $gambarKonten = @$this->kondisiPengguna->GambarKonten ?? 'https://www.youtube.com/watch?v=m-j1LRgaJ7o';
                        $isYoutube = strpos($gambarKonten, 'youtu') !== false;
                        $youtubeThumbnail = null;
                        $youtubeId = null;

                        if ($isYoutube) {
                            preg_match(
                                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                                $gambarKonten,
                                $matches,
                            );
                            if (isset($matches[1])) {
                                $youtubeId = $matches[1];
                                $youtubeThumbnail = "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
                            }
                        }
                    @endphp

                    @if ($isYoutube && $youtubeThumbnail)
                        <div @click="openModal('{{ $youtubeId }}')"
                            class="block relative group overflow-hidden rounded-2xl shadow-md cursor-pointer">
                            <img src="{{ $youtubeThumbnail }}" alt="{{ @$this->kondisiPengguna->JudulKonten }}"
                                class="w-full h-auto rounded-2xl shadow-md object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-all">
                                <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @else
                        <img src="{{ asset('assets/konten/' . $gambarKonten) }}" alt="{{ @$this->kondisiPengguna->JudulKonten }}"
                            class="w-full h-auto rounded-2xl shadow-md object-cover">
                    @endif
                </div>

                <!-- Text Content -->
                <div class="space-y-3">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                        {{ @$this->kondisiPengguna->JudulKonten }}
                    </h2>
                    {!! @$this->kondisiPengguna->IsiKonten !!}
                </div>
            </div>
        </div>

        <!-- Modal Video Player Pop-up (Persis Seperti di Galeri) -->
        <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
            @click.self="closeModal()" x-cloak style="display: none;">
            <div class="relative w-full max-w-3xl aspect-video bg-black rounded-2xl overflow-hidden mx-4 shadow-2xl">
                <button @click="closeModal()"
                    class="absolute top-3 right-3 z-10 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow transition cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <template x-if="videoId">
                    <iframe :src="'https://www.youtube.com/embed/' + videoId + '?autoplay=1'" title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen class="w-full h-full"></iframe>
                </template>
            </div>
        </div>
    </section>

    <!-- Section 6: Mitra Travel Haromain -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-2 md:mb-3 text-gray-900">
                {{ @$this->mitraTravelHeader->JudulKonten ?? 'Mitra Travel Haromain' }}
            </h2>
            <p class="text-center text-gray-600 max-w-2xl mx-auto mb-10 md:mb-14 text-sm md:text-base leading-normal">
                {{ @$this->mitraTravelHeader->IsiKonten ?? 'Bersama mitra travel, Haromain membantu menghadirkan pelayanan Haji dan Umrah yang lebih terintegrasi, profesional, dan terpercaya.' }}
            </p>

            @php
                $mitraList = collect($this->mitraTravelList)->take(15);
                $total = $mitraList->count();
            @endphp

            @if ($total < 4)
                <!-- KONDISI 1: LOGO SEDIKIT (1 - 3 LOGO) -> TETAP BESAR & GAGAH SEPERTI AWAL -->
                <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 32px 48px; max-width: 1000px; margin: 0 auto;">
                    @forelse ($mitraList as $mitra)
                        <div style="min-width: 220px; max-width: 280px; height: 110px; display: flex; align-items: center; justify-content: center; padding: 6px;">
                            <img src="{{ asset('assets/konten/' . $mitra['GambarKonten']) }}" 
                                 alt="{{ $mitra['JudulKonten'] }}" 
                                 style="max-width: 100%; max-height: 90px; width: auto; height: auto; object-fit: contain; display: block;">
                        </div>
                    @empty
                        <div class="text-gray-400 text-sm">Belum ada mitra travel terdaftar.</div>
                    @endforelse
                </div>
            @else
                <!-- KONDISI 2: LOGO BANYAK (>= 4 LOGO) -> FORMASI WEB SUMMIT RESPONSIF (MAKS 5 KOLOM, MAKS 3 BARIS) -->
                <style>
                    .travel-logo-container {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        align-items: center;
                        gap: 20px 24px;
                        width: 100%;
                        max-width: 1280px;
                        margin: 0 auto;
                    }
                    .travel-logo-item {
                        box-sizing: border-box;
                        flex: 0 0 calc(50% - 16px);
                        max-width: calc(50% - 16px);
                        height: 80px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 6px;
                    }
                    @media (min-width: 640px) {
                        .travel-logo-item {
                            flex: 0 0 calc(33.333% - 18px);
                            max-width: calc(33.333% - 18px);
                            height: 90px;
                            padding: 8px;
                        }
                    }
                    @media (min-width: 1024px) {
                        .travel-logo-item {
                            flex: 0 0 calc(20% - 20px);
                            max-width: calc(20% - 20px);
                            height: 96px;
                            padding: 8px 12px;
                        }
                    }
                </style>

                <div class="travel-logo-container">
                    @foreach ($mitraList as $mitra)
                        <div class="travel-logo-item">
                            <img src="{{ asset('assets/konten/' . $mitra['GambarKonten']) }}" 
                                 alt="{{ $mitra['JudulKonten'] }}" 
                                 style="max-width: 100%; max-height: 72px; width: auto; height: auto; object-fit: contain; display: block;">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Section FAQ -->
    <section id="faq" class="py-12 md:py-16 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-2 md:mb-3 text-gray-900">
                {{ @$this->pertanyaan->JudulKonten ?? 'Pertanyaan Umum' }}
            </h2>
            <p class="text-center text-gray-600 mb-4 md:mb-6 max-w-2xl mx-auto text-base md:text-lg leading-normal">
                {{ @$this->pertanyaan->IsiKonten }}
            </p>

            <div class="max-w-3xl mx-auto space-y-3" x-data="{ openFaq: 0 }">
                @foreach ($this->pertanyaanList as $key => $pertanyaan)
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-xs">
                        <button @click="openFaq = openFaq === {{ $key }} ? null : {{ $key }}"
                            class="w-full px-5 py-3 md:py-3.5 text-left bg-white hover:bg-gray-50 transition flex justify-between items-center cursor-pointer">
                            <span class="font-semibold text-gray-900">{{ $pertanyaan['JudulKonten'] }}</span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform duration-200"
                                :class="{ 'rotate-180': openFaq === {{ $key }} }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === {{ $key }}"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            class="px-5 py-3.5 bg-gray-50 border-t border-gray-100">
                            <p class="text-gray-700 leading-normal text-sm md:text-base">
                                {{ $pertanyaan['IsiKonten'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 7: Final CTA Banner -->
    <section class="pt-2 pb-8 md:pt-4 md:pb-12 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-linear-to-r from-amber-400 to-amber-500 rounded-3xl p-6 md:p-8 text-center shadow-xl">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-2 md:mb-3 text-gray-900">
                    Tingkatkan Pelayanan, Tumbuhkan Kepercayaan Jamaah
                </h2>
                <p class="text-white/95 text-base md:text-lg max-w-2xl mx-auto mb-6 leading-normal">
                    Hadirkan pelayanan digital yang terintegrasi untuk travel, Tour Leader/Mutowif, jamaah, dan keluarga bersama Haromain.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ @$this->actionButton['IsiKonten'] ?? 'https://wa.me/' }}" target="_blank"
                        class="inline-flex items-center justify-center gap-2.5 bg-amber-400 hover:bg-amber-500 text-white px-8 py-3.5 rounded-xl font-semibold shadow-md transition-all duration-300 hover:scale-105 cursor-pointer">
                        <svg class="w-5 h-5 fill-white text-white" fill="white" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Diskusikan Kerja Sama Haromain</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
