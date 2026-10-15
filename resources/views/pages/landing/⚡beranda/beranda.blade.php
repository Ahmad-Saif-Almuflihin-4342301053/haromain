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

    <!-- Section 1: Pelayanan Lebih Baik, Kepercayaan Jamaah Meningkat -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-8 md:mb-10 text-gray-900">
                Pelayanan Lebih Baik, Kepercayaan Jamaah Meningkat
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Kolom Kiri: Gambar -->
                <div>
                    <img src="{{ asset('assets/revisi-landing/image1.jpeg') }}" alt="Pelayanan Lebih Baik"
                        class="w-full h-72 md:h-84 lg:h-96 object-cover rounded-2xl shadow-md">
                </div>

                <!-- Kolom Kanan: Teks & Poin -->
                <div class="space-y-4">
                    <p class="text-gray-700 text-base md:text-lg leading-relaxed">
                        Jamaah dan keluarga tidak hanya mempertimbangkan harga dan fasilitas, tetapi juga kualitas pelayanan, kemudahan memperoleh informasi, serta pendampingan selama perjalanan.
                    </p>

                    <ul class="list-disc pl-5 space-y-2 text-gray-700 text-sm md:text-base leading-relaxed">
                        <li>Administrasi yang rumit dapat memperlambat pelayanan.</li>
                        <li>Informasi yang tersebar menyulitkan jamaah dan petugas.</li>
                        <li>Koordinasi perjalanan membutuhkan sistem yang lebih tertata.</li>
                        <li>Keluarga memerlukan akses informasi tentang perjalanan jamaah.</li>
                    </ul>

                    <p class="text-gray-900 font-medium leading-relaxed pt-2">
                        Haromain membantu travel memberikan pelayanan yang lebih terhubung, transparan, dan profesional dalam setiap tahapan perjalanan jamaah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Bukan Sekadar Aplikasi, Haromain Meningkatkan Standar Pelayanan Travel -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 text-gray-900">
                Bukan Sekadar Aplikasi, Haromain Meningkatkan Standar Pelayanan Travel
            </h2>
            <p class="text-center text-gray-600 max-w-3xl mx-auto mb-8 md:mb-10 text-base md:text-lg leading-relaxed">
                Haromain menyatukan pelayanan jamaah dari pendaftaran hingga kepulangan. Travel bekerja lebih tertata, jamaah mendapatkan pelayanan yang lebih baik, dan keluarga merasa lebih tenang.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 text-center">
                <!-- Travel Lebih Profesional -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/konten/leadership.svg') }}" alt="Travel" class="w-12 h-12">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Travel Lebih Profesional</h3>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        Administrasi, pembayaran, dokumen, manifest, dan perjalanan jamaah dikelola dalam satu sistem.
                    </p>
                </div>

                <!-- Jamaah Lebih Terlayani -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/konten/social-justice.svg') }}" alt="Jamaah" class="w-12 h-12">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Jamaah Lebih Terlayani</h3>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        Informasi, itinerary, dokumen, komunikasi, dan pendampingan tersedia dalam satu aplikasi.
                    </p>
                </div>

                <!-- Kepercayaan Semakin Kuat -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/konten/check.svg') }}" alt="Kepercayaan" class="w-12 h-12">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Kepercayaan Semakin Kuat</h3>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        Pelayanan yang transparan dan terhubung memberikan alasan lebih kuat bagi masyarakat untuk memilih travel Anda.
                    </p>
                </div>
            </div>

            <p class="text-center text-gray-800 font-medium max-w-2xl mx-auto mt-8 md:mt-10 leading-relaxed">
                Ketika pelayanan meningkat, kepercayaan tumbuh—dan peluang mendapatkan lebih banyak jamaah ikut terbuka.
            </p>
        </div>
    </section>

    <!-- Section 3: Satu Aplikasi untuk Setiap Tahapan Perjalanan (Fitur Unggulan) -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 text-gray-900">
                Satu Aplikasi untuk Setiap Tahapan Perjalanan
            </h2>
            <p class="text-center text-gray-600 max-w-3xl mx-auto pb-16 text-base md:text-lg leading-relaxed">
                Haromain membantu travel memberikan pelayanan yang terintegrasi sejak jamaah memilih paket, mempersiapkan keberangkatan, menjalani perjalanan ibadah, hingga kembali ke Tanah Air.
            </p>

            <div>
                <!-- Tahap A: Sebelum Keberangkatan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start pb-16 md:pb-24">
                    <div>
                        <img src="{{ asset('assets/revisi-landing/image2.jpeg') }}"
                            alt="Sebelum Keberangkatan"
                            class="w-full h-auto rounded-2xl shadow-md object-cover">
                    </div>
                    <div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                            A. Sebelum Keberangkatan
                        </h3>
                        <h4 class="text-lg md:text-xl font-bold text-gray-900 mb-3">
                            Pendaftaran dan Administrasi Lebih Mudah
                        </h4>
                        <ul class="list-disc pl-5 space-y-1.5 text-gray-700 text-sm md:text-base leading-relaxed">
                            <li>Katalog dan detail paket Haji dan Umrah</li>
                            <li>Konsultasi melalui WhatsApp</li>
                            <li>Pendaftaran dan pembayaran melalui aplikasi</li>
                            <li>Unggah dan pemeriksaan dokumen jamaah</li>
                            <li>Pengelolaan tiket, visa, dan dokumen SISKOPATUH</li>
                            <li>Pengelolaan data, pembayaran, dan manifest jamaah</li>
                        </ul>
                        <p class="text-gray-700 text-sm md:text-base mt-4 leading-relaxed">
                            Jamaah mendapatkan proses yang lebih mudah, sementara administrasi travel menjadi lebih tertata.
                        </p>
                    </div>
                </div>

                <!-- Tahap B: Saat Perjalanan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start pb-16 md:pb-24">
                    <div class="order-2 md:order-1">
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                            B. Saat Perjalanan
                        </h3>
                        <h4 class="text-lg md:text-xl font-bold text-gray-900 mb-3">
                            Jamaah Terpantau, Keluarga Lebih Tenang
                        </h4>
                        <ul class="list-disc pl-5 space-y-1.5 text-gray-700 text-sm md:text-base leading-relaxed">
                            <li>Itinerary dan agenda perjalanan</li>
                            <li>Informasi serta pengumuman kepada jamaah</li>
                            <li>Live tracking posisi jamaah</li>
                            <li>Voice Meet untuk komunikasi kelompok</li>
                            <li>Koordinasi antara travel, Tour Leader, Mutowif, dan jamaah</li>
                            <li>Keluarga dapat melihat posisi dan agenda jamaah</li>
                            <li>Keluarga dapat menghubungi jamaah atau petugas melalui WhatsApp</li>
                            <li>Bantuan koordinasi ketika jamaah terpisah dari rombongan</li>
                        </ul>
                        <p class="text-gray-700 text-sm md:text-base mt-4 leading-relaxed">
                            Travel dan petugas lebih mudah mendampingi rombongan, jamaah merasa lebih aman, dan keluarga tetap dapat memantau orang tercinta dari rumah.
                        </p>
                    </div>
                    <div class="order-1 md:order-2">
                        <img src="{{ asset('assets/revisi-landing/image3.jpeg') }}"
                            alt="Saat Perjalanan"
                            class="w-full h-auto rounded-2xl shadow-md object-cover">
                    </div>
                </div>

                <!-- Tahap C: Setelah Kepulangan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start">
                    <div>
                        <img src="{{ asset('assets/revisi-landing/image4.jpeg') }}"
                            alt="Setelah Kepulangan"
                            class="w-full h-auto rounded-2xl shadow-md object-cover">
                    </div>
                    <div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                            C. Setelah Kepulangan
                        </h3>
                        <h4 class="text-lg md:text-xl font-bold text-gray-900 mb-3">
                            Hubungan dengan Jamaah Tetap Terjaga
                        </h4>
                        <ul class="list-disc pl-5 space-y-1.5 text-gray-700 text-sm md:text-base leading-relaxed">
                            <li>Testimoni dan evaluasi perjalanan</li>
                            <li>Komunikasi lanjutan melalui WhatsApp</li>
                            <li>Informasi paket dan program berikutnya</li>
                            <li>Komunitas alumni jamaah</li>
                            <li>Peluang referral dan keberangkatan berikutnya</li>
                        </ul>
                        <p class="text-gray-700 text-sm md:text-base mt-4 leading-relaxed">
                            Pelayanan tidak berhenti setelah kepulangan, tetapi berkembang menjadi hubungan jangka panjang dengan jamaah.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: Satu Sistem, Manfaat untuk Semua (Target Pengguna) -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 text-gray-900">
                Satu Sistem, Manfaat untuk Semua
            </h2>
            <p class="text-center text-gray-600 max-w-3xl mx-auto mb-12 md:mb-16 text-base md:text-lg leading-relaxed">
                Haromain menghubungkan travel, Tour Leader/Mutowif, jamaah, dan keluarga dalam satu pelayanan yang terintegrasi.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10 text-center">
                <!-- Travel -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/konten/suitcase.svg') }}" alt="Travel" class="w-12 h-12">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Travel</h3>
                    <h4 class="text-base font-semibold text-gray-800 mb-2">Operasional Lebih Tertata</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Administrasi, pembayaran, dokumen, manifest, dan perjalanan jamaah dapat dikelola dalam satu sistem.
                    </p>
                </div>

                <!-- Tour Leader / Mutowif -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/konten/map-book.svg') }}" alt="Tour Leader/Mutowif" class="w-12 h-12">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Tour Leader / Mutowif</h3>
                    <h4 class="text-base font-semibold text-gray-800 mb-2">Pendampingan Lebih Terarah</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Itinerary, komunikasi, dan pemantauan jamaah membantu koordinasi rombongan selama perjalanan.
                    </p>
                </div>

                <!-- Jamaah -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/konten/user-group.svg') }}" alt="Jamaah" class="w-12 h-12">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Jamaah</h3>
                    <h4 class="text-base font-semibold text-gray-800 mb-2">Perjalanan Lebih Nyaman</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Pendaftaran, dokumen, informasi, agenda, dan pendampingan perjalanan tersedia dalam satu aplikasi.
                    </p>
                </div>

                <!-- Keluarga -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/konten/family.svg') }}" alt="Keluarga" class="w-12 h-12">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Keluarga</h3>
                    <h4 class="text-base font-semibold text-gray-800 mb-2">Lebih Tenang dari Rumah</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Keluarga dapat melihat posisi dan agenda serta menghubungi jamaah atau petugas melalui WhatsApp.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Ketika Jamaah Terpisah dari Rombongan (Kondisi Penggunaan) -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Video / Thumbnail -->
                <div>
                    @php
                        $gambarKonten = @$this->kondisiPengguna->GambarKonten ?? 'https://www.youtube.com/watch?v=m-j1LRgaJ7o';
                        $isYoutube = strpos($gambarKonten, 'youtu') !== false;
                        $youtubeThumbnail = null;

                        if ($isYoutube) {
                            preg_match(
                                '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
                                $gambarKonten,
                                $matches,
                            );
                            if (isset($matches[1])) {
                                $youtubeThumbnail = "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
                            }
                        }
                    @endphp

                    @if ($isYoutube && $youtubeThumbnail)
                        <a href="{{ $gambarKonten }}" target="_blank" class="block relative group overflow-hidden rounded-2xl shadow-md">
                            <img src="{{ $youtubeThumbnail }}" alt="Video Simulasi Ketika Terpisah dari Rombongan"
                                class="w-full h-auto rounded-2xl shadow-md object-cover group-hover:scale-105 transition-transform duration-300">
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-all">
                                <div
                                    class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @else
                        <img src="{{ url('assets/konten') }}/{{ $gambarKonten }}" alt="Kondisi Penggunaan"
                            class="w-full h-auto rounded-2xl shadow-md object-cover">
                    @endif
                </div>

                <!-- Text Content -->
                <div class="space-y-4">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                        Ketika Jamaah Terpisah dari Rombongan
                    </h2>
                    <p class="text-gray-700 leading-relaxed text-base md:text-lg">
                        Dalam situasi city tour atau area yang ramai, jamaah dapat terpisah dari rombongan. Video ini menunjukkan bagaimana peta Haromain membantu jamaah menemukan jalan kembali atau memudahkan Mutowif menjemput jamaah berdasarkan posisi yang terpantau.
                    </p>

                    <ul class="list-disc pl-5 space-y-2 text-gray-700 text-sm md:text-base leading-relaxed">
                        <li>Jamaah dapat melihat posisi dan arah melalui peta Haromain.</li>
                        <li>Jamaah yang mampu menggunakan navigasi dapat kembali menuju titik rombongan.</li>
                        <li>Mutowif dapat melihat posisi jamaah yang memerlukan bantuan.</li>
                        <li>Posisi pada peta membantu Mutowif menuju lokasi penjemputan.</li>
                        <li>Penanganan dapat dilakukan lebih cepat tanpa meninggalkan koordinasi rombongan.</li>
                    </ul>

                    <p class="text-gray-900 font-medium leading-relaxed pt-2">
                        Haromain tidak menggantikan peran Mutowif, tetapi menjadi alat bantu saat kondisi perjalanan tidak berjalan sesuai rencana.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 6: Mitra Travel Haromain -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 md:mb-16 text-gray-900">
                Mitra Travel Haromain
            </h2>

            <div class="flex justify-center items-center flex-wrap gap-12 md:gap-20">
                <!-- Logo Haromain (Besar) -->
                <img src="{{ asset('img/haromain.jpg') }}" alt="Haromain" class="w-60 md:w-80 h-auto object-contain">

                <!-- Logo Izzati Tour & Travel (Besar) -->
                <img src="{{ asset('img/izzati.jpg') }}" alt="Izzati Tour & Travel" class="w-60 md:w-80 h-auto object-contain">
            </div>
        </div>
    </section>

    <!-- Section FAQ -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 text-gray-900">
                {{ @$this->pertanyaan->JudulKonten ?? 'Pertanyaan Umum' }}
            </h2>
            <p class="text-center text-gray-600 mb-8 md:mb-10 max-w-2xl mx-auto text-base md:text-lg">
                {{ @$this->pertanyaan->IsiKonten }}
            </p>

            <div class="max-w-3xl mx-auto space-y-4" x-data="{ openFaq: 0 }">
                @foreach ($this->pertanyaanList as $key => $pertanyaan)
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-xs">
                        <button @click="openFaq = openFaq === {{ $key }} ? null : {{ $key }}"
                            class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 transition flex justify-between items-center cursor-pointer">
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
                            class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <p class="text-gray-700 leading-relaxed text-sm md:text-base">
                                {{ $pertanyaan['IsiKonten'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section 7: Final CTA Banner -->
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-linear-to-r from-amber-400 to-amber-500 rounded-3xl p-8 md:p-12 text-center shadow-xl">
                <h3 class="text-2xl md:text-4xl font-bold text-white mb-3">
                    Tingkatkan Pelayanan, Tumbuhkan Kepercayaan Jamaah
                </h3>
                <p class="text-white/95 text-base md:text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
                    Hadirkan pelayanan digital yang terintegrasi untuk travel, Tour Leader/Mutowif, jamaah, dan keluarga bersama Haromain.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ @$this->actionButton['IsiKonten'] ?? 'https://wa.me/' }}" target="_blank"
                        class="inline-flex items-center justify-center gap-2.5 bg-white text-amber-600 px-8 py-3.5 rounded-xl font-semibold shadow-md hover:bg-gray-100 transition-all duration-300 hover:scale-105 cursor-pointer">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Diskusikan Kerja Sama Haromain</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
