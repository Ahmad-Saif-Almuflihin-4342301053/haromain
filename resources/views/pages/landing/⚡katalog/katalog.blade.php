<div class="bg-gray-100 min-h-screen">
    <!-- Banner Katalog -->
    <div class="relative w-full h-56 md:h-64 lg:h-72  ">
        <img src="{{ url('assets/konten') }}/{{ @$this->katalogHeader->GambarKonten }}" alt="Kabah"
            class="absolute inset-0 w-full h-full object-cover object-center" />
        <div class="absolute inset-0 h-full bg-linear-to-t from-black via-black/40 to-transparent"></div>
        <div class="max-w-7xl mx-auto  h-56 py-8 flex items-end">
            <h1 class="relative z-10 text-white text-3xl md:text-4xl font-bold mb-6 ml-3">
                {{ @$this->katalogHeader->JudulKonten }}</h1>
        </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Filter Sidebar (Desktop) -->
            <aside class="hidden md:block md:w-1/4! w-full bg-white rounded-xl shadow p-6 mb-6 md:mb-0 md:self-start">
                @include('pages.landing.⚡katalog.katalog-filter')

            </aside>

            <!-- Offcanvas Filter (Mobile) -->
            <div x-data="{ open: false }" class="md:hidden">
                <!-- Floating Button -->
                <button @click="open = true"
                    class="fixed z-50 bottom-20 right-6 bg-amber-500! hover:bg-amber-400! text-white rounded-full shadow-lg p-4 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-6.414 6.414A2 2 0 0013 14.586V19a1 1 0 01-1.447.894l-2-1A1 1 0 019 18v-3.414a2 2 0 00-.293-1.293L2.293 6.707A1 1 0 012 6V4z" />
                    </svg>
                </button>
                <!-- Offcanvas -->
                <div x-show="open" x-transition:enter="transition-opacity ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 flex">
                    <div class="fixed inset-0 bg-black/50!" @click="open = false"></div>
                    <aside
                        class="relative ml-auto w-11/12 max-w-sm bg-white h-full shadow-xl rounded-l-xl p-6 overflow-y-auto">
                        <div class="flex items-center justify-between mb-6 mt-18">
                            <h2 class="text-lg font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-6.414 6.414A2 2 0 0013 14.586V19a1 1 0 01-1.447.894l-2-1A1 1 0 019 18v-3.414a2 2 0 00-.293-1.293L2.293 6.707A1 1 0 012 6V4z" />
                                </svg>
                                Filter Paket
                            </h2>
                            <button @click="open = false"
                                class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                        </div>
                        @include('pages.landing.⚡katalog.katalog-filter')
                    </aside>
                </div>
            </div>

            <!-- Main Katalog Content -->
            <section class="md:w-3/4! w-full">
                @php
                    $jml = count($itemsPaket);
                @endphp
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-2">
                    <div class="text-sm text-gray-600">Menampilkan {{ $jml > 0 ? 1 : 0 }} - {{ $jml }} paket
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">Urutkan</span>
                        <select class="border border-gray-300 rounded! px-3 py-2 text-sm"
                            wire:model.live="selectedOrderBy">
                            <option value="">Default</option>
                            @foreach ($this->orderByFilter as $key => $item)
                                <option value="{{ $key }}">{{ $item['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Overlay Loading saat applyFilter atau loading paket -->
                <div wire:loading.flex wire:target="applyFilter,resetFilter,selectedOrderBy"
                    class="fixed inset-0 z-50 bg-black/30 flex items-center justify-center">
                    <div class="flex flex-col items-center">
                        <svg class="animate-spin h-10 w-10 text-amber-400 mb-4" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span class="text-white font-semibold">Memuat data paket...</span>
                    </div>
                </div>

                <!-- Skeleton Loader -->
                <div class="w-full" wire:loading wire:target="applyFilter,resetFilter,selectedOrderBy">
                    @include('pages.landing.⚡katalog.katalog-skeleton', ['count' => 6])
                </div>

                <!-- Paket Card List -->
                <div wire:loading.remove wire:target="applyFilter, resetFilter"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($itemsPaket as $key => $item)
                        <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100 + ({{ $key }} * 80))" x-show="show"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-6"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="bg-white rounded-xl shadow px-4 pb-4 border border-gray-300 flex flex-col">
                            <div class="relative -mx-4 mt-0 mb-2">
                                <div
                                    class="absolute inset-0 rounded-t-xl border-t border-x border-gray-300 pointer-events-none z-10">
                                </div>
                                <img wire:key="paket-image-{{ $item['IDPaket'] }}"
                                    :src="imgError ? '{{ asset('assets/icon/notfound.png') }}' :
                                        '{{ url('assets/gambar-paket') }}/{{ $item['Gambar1'] }}'"
                                    alt="Paket" class="rounded-t-xl w-full h-40! object-cover relative z-0"
                                    x-data="{ imgError: false }" x-on:error="imgError = true"
                                    x-bind:class="imgError ? 'error-img' : ''" x-init="$watch('show', value => { if (value && !$el.complete && !imgError) { $el.src = $el.src; } })" />
                            </div>
                            <div class="flex justify-start mt-2 mb-2">
                                <span
                                    class="inline-flex items-center gap-1 bg-blue-600 text-white text-xs font-semibold px-5 py-2 rounded-full">
                                    {{ $item['KategoriPaket'] }}
                                </span>
                            </div>
                            <div class="flex-1 flex flex-col">
                                <h3 class="font-bold text-lg mb-2">{{ $item['NamaPaket'] }}</h3>
                                <div class="text-sm text-gray-600 mb-1 flex items-center gap-2">
                                    <img src="{{ asset('assets/icon/bx-building.svg') }}" alt="">
                                    {{ $item['NamaTravel'] }}
                                </div>
                                <div class="text-sm text-gray-600 mb-1 flex items-center gap-2">
                                    <img src="{{ asset('assets/icon/bx-calendar.svg') }}" alt="">
                                    {{ date('d F Y', strtotime($item['TglBerangkat'])) }} &bull;
                                    {{ \Carbon\Carbon::parse($item['TglBerangkat'])->diffInDays(\Carbon\Carbon::parse($item['TglPulang'])) }}
                                    Hari
                                </div>
                                <div class="text-sm text-gray-600 mb-1 flex items-center gap-2">
                                    <img src="{{ asset('assets/icon/bxs-plane-alt.svg') }}" alt="">
                                    {{ $item['Maskapai'] }} ({{ $item['JenisPenerbangan'] }})
                                </div>
                                <div class="text-sm text-gray-600 mb-1 flex items-center gap-2">
                                    <img src="{{ asset('assets/icon/bxs-map-pin.svg') }}" alt="">
                                    {{ $item['JarakHotel'] }} m ke Masjidil Haram
                                </div>

                                <div class="text-sm text-gray-600  flex items-center mt-2">
                                    Harga Mulai
                                </div>
                                <div class="font-bold text-black text-xl ">Rp
                                    {{ number_format($item['BiayaQuad'], 0, ',', '.') }}</div>
                                {{-- <div class="text-amber-500 text-sm font-semibold">Cashback Rp 1.000.000</div> --}}
                            </div>
                            <a wire:navigate href="{{ url('katalog/detail') }}"
                                class="mt-4 bg-amber-400! hover:bg-amber-500! text-white text-center font-semibold py-2 rounded">Lihat
                                Detail</a>
                        </div>
                    @endforeach
                </div>
                @if ($lastPage > $page)
                    <div class="flex justify-center mt-8">
                        <button wire:click="loadMore" wire:loading.attr="disabled"
                            class="w-full bg-amber-400! hover:bg-amber-500! text-white px-6 py-2 rounded-lg font-semibold transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="loadMore">Load More</span>
                            <span wire:loading wire:target="loadMore">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    Loading...
                                </span>
                            </span>
                        </button>
                    </div>
                @endif
            </section>
        </div>
    </div>
    <!-- Promo Section -->
    <div class="max-w-7xl mx-auto px-4 pb-12">
        <hr class="mb-8">
        <div class="bg-white rounded-xl p-6 md:p-8 shadow mb-6">
            <div class="prose! max-w-none! leading-relaxed prose-ul:list-disc!  prose-li:marker:text-black!">
                {!! @$this->katalogFooter->IsiKonten !!}
            </div>
            <p class="mb-6 text-gray-600 text-xs md:text-sm">{{ @$this->katalogFooter->JudulKonten }}</p>
            <div class="bg-amber-400 rounded-xl p-6 md:p-8 flex flex-col items-center justify-center">
                <div class="text-white text-lg md:text-2xl! font-semibold text-center mb-4">Ibadah Lebih Aman, Travel
                    Lebih Profesional</div>
                <a href="#"
                    class="inline-block border border-white text-white px-6 py-2 rounded-lg font-semibold transition text-base">Daftar
                    Paket Umroh via Haromain</a>
            </div>
        </div>
    </div>
</div>
