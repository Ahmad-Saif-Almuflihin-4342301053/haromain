<div>
    <!-- Hero Slider Section -->
    <section class="relative h-screen pt-20 z-10" x-data="{ currentSlide: 0, slides: {{ count($this->listSlider) }} }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % slides }, 5000)">
        @foreach ($this->listSlider as $key => $slider)
            <!-- Slide 1 -->
            <div x-show="currentSlide === {{ $key }}" x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-1000" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('{{ asset('assets/banner/') }}/{{ $slider['Gambar'] }}');">
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
            class="hidden md:flex absolute left-4 md:left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 md:w-14 md:h-14 bg-black/25! hover:bg-black/50! rounded-full items-center justify-center transition-all duration-300 group">
            <svg class="w-6 h-6 md:w-7 md:h-7 text-white group-hover:scale-110 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Next Button -->
        <button @click="currentSlide = currentSlide < slides - 1 ? currentSlide + 1 : 0"
            class="hidden md:flex absolute right-4 md:right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 md:w-14 md:h-14 bg-black/25! hover:bg-black/50! rounded-full items-center justify-center transition-all duration-300 group">
            <svg class="w-6 h-6 md:w-7 md:h-7 text-white group-hover:scale-110 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Slider Indicators -->
        <div class="absolute bottom-8 md:bottom-12 left-0 right-0 flex justify-center space-x-3 z-10">
            <template x-for="i in slides" :key="i">
                <button @click="currentSlide = i - 1"
                    class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full transition-all duration-300"
                    :class="currentSlide === i - 1 ? 'bg-white' : 'bg-white/50'"></button>
            </template>
        </div>


    </section>
    <!-- Challenge Understanding Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4!">
            {{-- judul memahami tentang --}}
            <h2 class="text-3xl! md:text-4xl! font-bold text-center mb-12! md:mb-16!">
                {{ @$this->memahamiTentang->JudulKonten }}</h2>

            <div class="grid grid-cols-1 lg:grid-cols-[4fr_6fr]! gap-8 lg:gap-12">
                <div class="order-2 lg:order-1 ">
                    {{-- gambar memahami tentang --}}
                    <img src="{{ url('assets/konten') }}/{{ @$this->memahamiTentang->GambarKonten }}" alt="Kaaba"
                        class="w-full h-64 md:h-80 lg:h-96 object-cover rounded-2xl shadow-lg">
                </div>

                <div class="order-1 lg:order-2 prose! prose-ul:list-disc!  prose-li:marker:text-black!">
                    {{-- isi memahami tentang --}}
                    {!! @$this->memahamiTentang->IsiKonten !!}
                </div>
            </div>
        </div>
    </section>
    <!-- System Role Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            {{-- judul peran haromain --}}
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12! md:mb-16">
                {{ @$this->peranHaromain->JudulKonten }}
            </h2>

            <div class="prose! max-w-none!">
                {{-- isi peran haromain --}}
                {!! @$this->peranHaromain->IsiKonten !!}
            </div>
            @php
                $items = $this->peranHaromainList;
                $count = count($items);
                $fullRowsCount = intdiv($count, 3) * 3;
                $remainder = $count % 3;
            @endphp

            {{-- list peran haromain --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                {{-- FULL ROW ITEMS --}}
                @for ($i = 0; $i < $fullRowsCount; $i++)
                    <div class="bg-white p-6 rounded-2xl text-center w-full max-w-sm mx-auto">
                        <div class="w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <img src="{{ url('assets/konten') }}/{{ $items[$i]['GambarKonten'] }}" alt="">
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $items[$i]['JudulKonten'] }}
                        </h3>
                    </div>
                @endfor

                {{-- REMAINDER ITEMS (1 or 2) --}}
                @if ($remainder > 0)
                    <div class="md:col-span-3 flex justify-center gap-8">
                        @for ($i = $fullRowsCount; $i < $count; $i++)
                            <div class="bg-white p-6 rounded-2xl text-center w-full max-w-sm">
                                <div class="w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <img src="{{ url('assets/konten') }}/{{ $items[$i]['GambarKonten'] }}"
                                        alt="">
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ $items[$i]['JudulKonten'] }}
                                </h3>
                            </div>
                        @endfor
                    </div>
                @endif
            </div>
            <div class="prose! max-w-none! mt-12! mx-auto leading-relaxed">
                {{-- footer peran haromain --}}
                {!! @$this->peranHaromainFooter->IsiKonten !!}
            </div>
        </div>
    </section>
    <!-- About Haromain Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            {{-- judul kenal lebih --}}
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12!">{{ @$this->kenalLebih->JudulKonten }}</h2>
            <div class="bg-cover bg-center rounded-3xl p-8 md:p-16 shadow-xl mb-8 min-h-70 flex items-center justify-center"
                style="background-image: url('{{ asset('assets/background/bgTentangHaromain.png') }}');">
                <div class="bg-white px-8 py-8 md:px-16 md:py-12 rounded-3xl shadow-2xl max-w-2xl">
                    <img src="{{ asset('assets/logo/Logo-black.svg') }}" alt="Haromain Logo"
                        class="h-25 md:h-30 mx-auto">
                </div>
            </div>
            {{-- isi kenal lebih --}}
            <div class="text-gray-800 prose! max-w-none! leading-relaxed text-center mx-auto mb-16 mt-10!">
                {!! @$this->kenalLebih->IsiKonten !!}
            </div>
        </div>
    </section>
    <section class="py-2 md:py-2!  bg-amber-50!">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Haromain Journey -->
            <div class="mb-16! mt-16!">
                <h3 class="text-3xl md:text-4xl font-bold text-center mb-8! text-amber-400">
                    {{ @$this->haromainBersama->JudulKonten }}</h3>
                <div class="text-gray-800 prose! max-w-none! leading-relaxed text-center mx-auto mb-16 mt-10!">
                    {!! @$this->haromainBersama->IsiKonten !!}
                </div>
            </div>
        </div>
    </section>
    <!-- Features Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            {{-- fitur unggulan judul --}}
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-4">{{ @$this->fiturUnggulan->JudulKonten }}</h2>
            {{-- fitur unggulan sub judul --}}
            <p class="text-center text-gray-600 mb-12! md:mb-16!">{{ @$this->fiturUnggulan->IsiKonten }}</p>
            {{-- list fitur unggulan --}}
            @foreach ($this->fiturUnggulanList as $key => $item)
                @php
                    $ganjil = $key % 2 == 1;
                @endphp
                @if (!$ganjil)
                    <div class="mb-16">
                        <div class="grid grid-cols-1 lg:grid-cols-[4fr_6fr]! gap-8 lg:gap-12 items-start">
                            <div>
                                <img src="{{ url('assets/konten') }}/{{ $item['GambarKonten'] }}"
                                    alt="Sebelum Keberangkatan"
                                    class="w-full h-96! md:h-96 object-cover rounded-2xl shadow-lg">
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-2xl md:text-3xl font-bold mb-4!">{{ $item['JudulKonten'] }}</h3>
                                <div
                                    class="prose! max-w-none! leading-relaxed! prose-ul:list-disc!  prose-li:marker:text-black!">
                                    {!! $item['IsiKonten'] !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-16">
                        <div class="grid grid-cols-1 lg:grid-cols-[6fr_4fr]! gap-8 lg:gap-12 items-start">
                            <div class="space-y-6 ">
                                <h3 class="text-2xl md:text-3xl font-bold mb-8">{{ $item['JudulKonten'] }}</h3>
                                <div
                                    class="prose! max-w-none! leading-relaxed! prose-ul:list-disc!  prose-li:marker:text-black!">
                                    {!! $item['IsiKonten'] !!}
                                </div>
                            </div>
                            <div class="order-1 lg:order-2">
                                <img src="{{ url('assets/konten') }}/{{ $item['GambarKonten'] }}"
                                    alt="Saat Perjalanan"
                                    class="w-full h-96 md:h-96 object-cover rounded-2xl shadow-lg">
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </section>
    <!-- Target Users Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            {{-- judul dan sub judul siapa yang --}}
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-4">{{ @$this->siapaYang->JudulKonten }}</h2>
            <p class="text-center text-gray-600 mb-12! md:mb-16!">{{ @$this->siapaYang->IsiKonten }}</p>
            {{-- list siapa yang --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($this->siapaYangList as $item)
                    <!-- Travel -->
                    <div class="group flex flex-col items-center md:items-start">
                        <div class="w-20 h-20 flex items-center justify-center md:justify-start mb-2 transition-colors">
                            <img src="{{ url('assets/konten') }}/{{ $item['GambarKonten'] }}"
                                alt="{{ $item['JudulKonten'] }}">
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900 text-center md:text-left">{{ $item['JudulKonten'] }}</h3>
                        <p class="text-gray-600 leading-relaxed text-center md:text-left">{{ $item['IsiKonten'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Conditions Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-[4fr_6fr]! gap-8 lg:gap-12 ">
                {{-- gambar --}}
                <div>
                    @php
                        $gambarKonten = @$this->kondisiPengguna->GambarKonten;
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
                        <a href="{{ $gambarKonten }}" target="_blank" class="block relative group">
                            <img src="{{ $youtubeThumbnail }}" alt="Video Preview"
                                class="w-full h-64 md:h-64 object-cover rounded-2xl shadow-lg">
                            <div
                                class="absolute inset-0 flex items-center justify-center transition-all group-hover:bg-black/10 rounded-2xl">
                                <div
                                    class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @else
                        <img src="{{ url('assets/konten') }}/{{ $gambarKonten }}" alt="Masjid Nabawi"
                            class="w-full h-64 md:h-64 object-cover rounded-2xl shadow-lg">
                    @endif
                </div>
                {{-- isi kondisi pengguna --}}
                {{-- judul kondisi pengguna --}}
                <div class="">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4!">{{ @$this->kondisiPengguna->JudulKonten }}</h2>
                    <div
                        class="text-gray-800 prose! max-w-none! leading-relaxed mx-auto mb-16 mt-10!  prose-ul:list-disc!  prose-li:marker:text-black!">
                        {!! @$this->kondisiPengguna->IsiKonten !!}
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- Partners Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 ">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 md:mb-16!">Mitra Travel Haromain</h2>
            <div class="flex justify-center">
                @php
                    $mitraItems = $this->mitraTravelList;
                    $mitraCount = count($mitraItems);
                    $mitraFullRowsCount = intdiv($mitraCount, 4) * 4;
                    $mitraRemainder = $mitraCount % 4;
                @endphp

                {{-- FULL ROW ITEMS --}}
                @for ($i = 0; $i < $mitraFullRowsCount; $i++)
                    <div
                        class="bg-white p-6 rounded-2xl text-center w-full max-w-sm mx-auto">
                        <img src="{{ url('assets/logo-kantor') }}/{{ $mitraItems[$i]['GambarKonten'] }}"
                            alt="{{ $mitraItems[$i]['JudulKonten'] }}" class="max-w-full h-auto">
                    </div>
                @endfor

                {{-- REMAINDER ITEMS --}}
                @if ($mitraRemainder > 0)
                    <div class="md:col-span-3 flex justify-center gap-8">
                        @for ($i = $mitraFullRowsCount; $i < $mitraCount; $i++)
                            <div
                                class="flex items-center justify-center p-6 bg-white rounded-xl hover:shadow-lg transition-shadow w-full max-w-xs">
                                <img src="{{ url('assets/logo-kantor') }}/{{ $mitraItems[$i]['GambarKonten'] }}"
                                    alt="{{ $mitraItems[$i]['JudulKonten'] }}" class="max-w-full h-auto">
                            </div>
                        @endfor
                    </div>
                @endif

            </div>
        </div>
    </section>
    <!-- Testimonials Section -->
    {{-- <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-4!">{{ @$this->testimonial->JudulKonten }}</h2>
            <p class="text-center text-gray-600 mb-12! md:mb-16!">{{ @$this->testimonial->IsiKonten }}</p>

            <div class="relative" x-data="{ currentSlide: 0, totalSlides: {{ count($this->testimonialList) }} }" x-init="setInterval(() => { currentSlide = (currentSlide + 1) % totalSlides }, 5000)">
                <div class="min-h-80 md:min-h-72 lg:min-h-72 flex items-stretch relative">
                    @foreach ($this->testimonialList as $key => $item)
                        <!-- Slide 1 -->
                        <div x-show="currentSlide === {{ $key }}"
                            x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full absolute inset-0"
                            style="position: absolute;">
                            @foreach ($item as $testimoni)
                                <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col h-full">
                                    <p class="text-gray-700 leading-relaxed mb-6 italic flex-1 line-clamp-7">
                                        {{ $testimoni['IsiKonten'] }}
                                    </p>
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                            {{ strtoupper(substr($testimoni['JudulKonten'], 0, 1)) }}</div>
                                        <div class="ml-4">
                                            <p class="font-bold text-gray-900">{{ $testimoni['JudulKonten'] }}</p>
                                            <p class="text-sm text-gray-600">User</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                </div>
                <!-- Navigation -->
                <div class="flex justify-center items-center space-x-4 mt-8">
                    <button @click="currentSlide = currentSlide > 0 ? currentSlide - 1 : totalSlides - 1"
                        class="w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex space-x-2">
                        <template x-for="i in totalSlides" :key="i">
                            <button @click="currentSlide = i - 1" class="w-2.5 h-2.5 rounded-full transition-all"
                                :class="currentSlide === i - 1 ? 'bg-amber-500! w-8' : 'bg-gray-300'"></button>
                        </template>
                    </div>
                    <button @click="currentSlide = currentSlide < totalSlides - 1 ? currentSlide + 1 : 0"
                        class="w-10 h-10 rounded-full bg-white shadow-md flex items-center justify-center hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- FAQ Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-4">{{ @$this->pertanyaan->JudulKonten }}</h2>
            <p class="text-center text-gray-600 mb-12! md:mb-16!">{{ @$this->pertanyaan->IsiKonten }}</p>
            {{-- list pertanyaan --}}
            <div class="max-w-3xl mx-auto space-y-4" x-data="{ openFaq: 0 }">
                @foreach ($this->pertanyaanList as $key => $pertanyaan)
                    <!-- FAQ 1 -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button @click="openFaq = openFaq === {{ $key }} ? 0 : {{ $key }}"
                            class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 transition flex justify-between items-center">
                            <span class="font-semibold text-gray-900">{{ $pertanyaan['JudulKonten'] }}</span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform"
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
                            class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <p class="text-gray-700 leading-relaxed">
                                {{ $pertanyaan['IsiKonten'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- CTA Section -->
    <section class="py-16 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">

            <div class="grid grid-cols-1 lg:grid-cols-[4fr_6fr]! gap-8 lg:gap-12  mb-16">
                <div>
                    <img src="{{ url('assets/konten') }}/{{ @$this->caraMemulai['GambarKonten'] }}"
                        alt="Couple Planning" class="w-full h-64 md:h-80 object-cover rounded-2xl shadow-lg">
                </div>

                <div class="">
                    <h2 class="text-2xl md:text-3xl font-bold mb-4!">{{ @$this->caraMemulai['JudulKonten'] }}</h2>
                    <div class=" prose! max-w-none! prose-ul:list-disc!  prose-li:marker:text-black!">
                        {!! @$this->caraMemulai['IsiKonten'] !!}
                    </div>
                    <a href="{{ @$this->actionButton['IsiKonten'] }}" target="_blank"
                        class="inline-block bg-amber-400 hover:bg-amber-500 text-white px-8 py-3 rounded-lg font-semibold shadow-lg transition-all duration-300 hover:scale-105 mt-3!">
                        Diskusi Kebutuhan Lapangan
                    </a>
                </div>
            </div>

            <!-- Final CTA Banner -->
            <div class="bg-linear-to-r from-amber-400! to-amber-500! rounded-3xl p-8 md:p-12 text-center shadow-2xl">
                <h3 class="text-2xl md:text-4xl font-bold text-white mb-6">Ibadah Lebih Aman, Travel Lebih Profesional
                </h3>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ @$this->actionButton['IsiKonten'] }}" target="_blank"
                        class="inline-block bg-white text-amber-600 px-8 py-3 rounded-lg font-semibold shadow-lg hover:bg-gray-100 transition-all duration-300 hover:scale-105">
                        Daftar Paket Umroh via Haromain
                    </a>
                    <a href="{{ @$this->actionButton['IsiKonten'] }}" target="_blank"
                        class="inline-block bg-amber-700 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:bg-amber-800 transition-all duration-300 hover:scale-105">
                        Diskusi Kerjasama Travel
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
