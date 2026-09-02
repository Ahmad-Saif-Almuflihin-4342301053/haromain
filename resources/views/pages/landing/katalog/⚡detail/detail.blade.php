<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-col! md:flex-row! gap-8">
        <!-- Main Content -->
        <div class="flex-1! min-w-0">
            <div class="flex items-center gap-2 mb-2">
                <span
                    class="inline-flex items-center gap-1 bg-blue-600 text-white text-xs font-semibold px-5 py-2 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Rekomendasi
                </span>
            </div>

            <h1 class="text-2xl! md:text-3xl! font-bold mb-2">Paket Umroh Hemat Ramadhan</h1>
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                <div class="flex items-center gap-1"><svg class="w-4 h-4 text-amber-400" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"
                            fill="none" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    </svg>10 Maret 2026</div>
                <div class="flex items-center gap-1"><svg class="w-4 h-4 text-amber-400" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>9 Hari</div>
            </div>

            <!-- Carousel (Flowbite) -->
            <div id="carouselExample" class="relative mb-6" data-carousel="slide">
                <div class="relative h-56 md:h-72 overflow-hidden rounded-xl">
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img src="https://images.unsplash.com/photo-1564769625905-50e93615e769?w=600"
                            class="absolute block w-full h-full object-cover" alt="...">
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img src="https://images.unsplash.com/photo-1591604021695-0c69b7c05981?w=600"
                            class="absolute block w-full h-full object-cover" alt="...">
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item="active">
                        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600"
                            class="absolute block w-full h-full object-cover" alt="...">
                    </div>
                </div>
                <!-- Slider controls -->
                <button type="button"
                    class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-prev>
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/80 group-hover:bg-white/90">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                </button>
                <button type="button"
                    class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-next>
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/80 group-hover:bg-white/90">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Tabs (Flowbite) -->
            <ul class="flex flex-wrap text-sm font-medium text-center border-b border-gray-200 mb-4" id="tabExample"
                data-tabs-toggle="#tabContent" role="tablist">
                <li class="me-2">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="desc-tab" data-tabs-target="#desc"
                        type="button" role="tab" aria-controls="desc" aria-selected="true">Deskripsi</button>
                </li>
                <li class="me-2">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="syarat-tab" data-tabs-target="#syarat"
                        type="button" role="tab" aria-controls="syarat" aria-selected="false">Syarat &
                        Ketentuan</button>
                </li>
                <li class="me-2">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="itenerary-tab"
                        data-tabs-target="#itenerary" type="button" role="tab" aria-controls="itenerary"
                        aria-selected="false">Itinerary</button>
                </li>
                <li>
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="hotel-tab" data-tabs-target="#hotel"
                        type="button" role="tab" aria-controls="hotel" aria-selected="false">Hotel &
                        Maskapai</button>
                </li>
            </ul>
            <div id="tabContent">
                <div class="hidden p-4" id="desc" role="tabpanel" aria-labelledby="desc-tab">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                        labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                        laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                        voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                        non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>
                <div class="hidden p-4" id="syarat" role="tabpanel" aria-labelledby="syarat-tab">
                    <p>Syarat & Ketentuan paket umroh ...</p>
                </div>
                <div class="hidden " id="itenerary" role="tabpanel" aria-labelledby="itenerary-tab">
                    <div class="flex flex-col gap-4">
                        <!-- Item 1 -->
                        <div class="rounded-xl border border-gray-200 p-4 bg-white">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-amber-500 font-semibold text-sm">Selasa, 10 Maret 2026</span>
                                <span class="text-gray-400 text-xs">| Jakarta - Madinah</span>
                            </div>
                            <div class="font-bold text-gray-800 mb-1">Keberangkatan Jakarta - Madinah</div>
                            <div class="text-gray-600 text-sm">Kumpul di bandara, briefing, penerbangan ke Madinah
                            </div>
                        </div>
                        <!-- Item 2 -->
                        <div class="rounded-xl border border-gray-200 p-4 bg-white">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-amber-500 font-semibold text-sm">Rabu, 11 Maret 2026</span>
                                <span class="text-gray-400 text-xs">| Madinah</span>
                            </div>
                            <div class="font-bold text-gray-800 mb-1">Ziarah Madinah</div>
                            <div class="text-gray-600 text-sm">Masjid Nabawi, Raudhah, Jabal Uhud</div>
                        </div>
                        <!-- Item 3 -->
                        <div class="rounded-xl border border-gray-200 p-4 bg-white">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-amber-500 font-semibold text-sm">Kamis, 12 Maret 2026</span>
                                <span class="text-gray-400 text-xs">| Madinah - Makkah</span>
                            </div>
                            <div class="font-bold text-gray-800 mb-1">Perjalanan Madinah - Makkah</div>
                            <div class="text-gray-600 text-sm">Miqot Bir Ali dan perjalanan ke Makkah</div>
                        </div>
                        <!-- Item 4 -->
                        <div class="rounded-xl border border-gray-200 p-4 bg-white">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="text-amber-500 font-semibold text-sm">Jum'at, 13 Maret 2026</span>
                                <span class="text-gray-400 text-xs">| Makkah</span>
                            </div>
                            <div class="font-bold text-gray-800 mb-1">Umroh Pertama</div>
                            <div class="text-gray-600 text-sm">Tawaf, Sa'i, Tahallul</div>
                        </div>
                    </div>
                </div>
                <div class="hidden" id="hotel" role="tabpanel" aria-labelledby="hotel-tab">
                    <!-- Hotel Section -->
                    <div class="mb-6">
                        <div class="font-bold text-lg mb-4 text-gray-800">Hotel</div>
                        <div class="flex flex-col gap-4">
                            <!-- Hotel 1 -->
                            <div class="flex gap-4 rounded-xl border border-gray-200 p-4 bg-white">
                                <img src="https://images.unsplash.com/photo-1564769625905-50e93615e769?w=1920"
                                    alt="Manazel Al Safiyah" class="w-32 h-24 object-cover rounded-lg border">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Manazel Al Safiyah</div>
                                    <div class="text-sm text-gray-500 mb-1">Lokasi <span
                                            class="font-medium text-gray-700">Madinah</span></div>
                                    <div class="text-xs text-gray-500">Check In:<span
                                            class="font-medium text-gray-700"> 11 Maret 2026, 08:00</span></div>
                                    <div class="text-xs text-gray-500">Check Out:<span
                                            class="font-medium text-gray-700"> 12 Maret 2026, 08:00</span></div>
                                </div>
                            </div>
                            <!-- Hotel 2 -->
                            <div class="flex gap-4 rounded-xl border border-gray-200 p-4 bg-white">
                                <img src="https://images.unsplash.com/photo-1564769625905-50e93615e769?w=1920"
                                    alt="Al Safwah Towers Hotel" class="w-32 h-24 object-cover rounded-lg border">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Al Safwah Towers Hotel</div>
                                    <div class="text-sm text-gray-500 mb-1">Lokasi <span
                                            class="font-medium text-gray-700">Makkah</span></div>
                                    <div class="text-xs text-gray-500">Check In:<span
                                            class="font-medium text-gray-700"> 12 Maret 2026, 14:00</span></div>
                                    <div class="text-xs text-gray-500">Check Out:<span
                                            class="font-medium text-gray-700"> 14 Maret 2026, 14:00</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Maskapai Section -->
                    <div>
                        <div class="font-bold text-lg mb-4 text-gray-800">Maskapai</div>
                        <div class="flex flex-col! gap-4">
                            <!-- Flight 1 -->
                            <div
                                class="flex flex-col! md:flex-row! gap-4 rounded-xl border border-gray-200 p-4 bg-white">
                                <img src="https://images.unsplash.com/photo-1564769625905-50e93615e769?w=1920"
                                    alt="Saudia Airlines"
                                    class="w-full md:w-40! h-24! object-cover rounded-lg border">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 mb-1">Keberangkatan : <span
                                            class="text-amber-500">Saudia Airlines</span></div>
                                    <div class="flex flex-col md:flex-row md:items-center md:gap-4 text-sm">
                                        <div class="flex-1">
                                            <div class="text-gray-700 font-small">Soekarno-Hatta International Airport
                                                (Jakarta, ID)</div>
                                            <div class="text-xs text-amber-500">11 Maret 2026, 10:00</div>
                                        </div>
                                        <div
                                            class="flex items-center justify-center text-amber-400 font-bold text-xl mx-2">
                                            →</div>
                                        <div class="flex-1">
                                            <div class="text-gray-700 font-small">King Abdulaziz International Airport
                                                (Jeddah, SA)</div>
                                            <div class="text-xs text-amber-500">11 Maret 2026, 15:55</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Flight 2 -->
                            <div
                                class="flex flex-col! md:flex-row! gap-4 rounded-xl border border-gray-200 p-4 bg-white">
                                <img src="https://images.unsplash.com/photo-1564769625905-50e93615e769?w=1920"
                                    alt="Saudia Airlines"
                                    class="w-full md:w-40! h-24! object-cover rounded-lg border">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900 mb-1">Kedatangan : <span
                                            class="text-amber-500">Saudia Airlines</span></div>
                                    <div class="flex flex-col md:flex-row md:items-center md:gap-4 text-sm">
                                        <div class="flex-1">
                                            <div class="text-gray-700 font-small">King Abdulaziz International Airport
                                                (Jeddah, SA)</div>
                                            <div class="text-xs text-amber-500">20 Maret 2026, 16:00</div>
                                        </div>
                                        <div
                                            class="flex items-center justify-center text-amber-400 font-bold text-xl mx-2">
                                            →</div>
                                        <div class="flex-1">
                                            <div class="text-gray-700 font-small">Soekarno-Hatta International Airport
                                                (Jakarta, ID)</div>
                                            <div class="text-xs text-amber-500">20 Maret 2026, 22:45</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="w-full md:w-80! shrink-0 flex flex-col gap-6">
            <!-- Informasi Travel -->
            <div class="bg-white rounded-xl shadow p-4 border border-gray-200">
                <div class="font-semibold text-gray-800 mb-3 flex items-center gap-2 text-xl">
                    Informasi Travel
                </div>
                <ul class="flex flex-col gap-3 text-sm text-gray-700">
                    <li class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded bg-amber-400">
                            <img src="{{ asset('assets/icon/bx-building-w.svg') }}" class="w-6 h-6" alt="">
                        </div>
                        <div class="text-gray-500">
                            Nama Perusahaan
                            <br>
                            <span class="font-semibold text-black">PT NuraniTours</span>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded bg-amber-400">
                            <img src="{{ asset('assets/icon/bx-phone.svg') }}" class="w-6 h-6" alt="">
                        </div>
                        <div class="text-gray-500">
                            Nomor Telepon
                            <br>
                            <span class="font-semibold text-black">09084728436434</span>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded bg-amber-400">
                            <img src="{{ asset('assets/icon/bx-envelope.svg') }}" class="w-6 h-6" alt="">
                        </div>
                        <div class="text-gray-500">
                            Alamat Email
                            <br>
                            <span class="font-semibold text-black">email@mail.com</span>
                        </div>
                    </li>

                </ul>
            </div>

            <!-- Harga Paket -->
            <div class="bg-white rounded-xl shadow p-4 border border-gray-200 flex flex-col gap-2">
                <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2 text-xl">
                    Harga Paket
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between font-semibold text-sm">
                        <span>Rp28.900.000 <span class="text-xs text-gray-400">(QUAD)</span></span>
                    </div>
                    <div class="flex items-center justify-between font-semibold text-sm">
                        <span>Rp30.500.000 <span class="text-xs text-gray-400">(TRIPLE)</span></span>
                    </div>
                    <div class="flex items-center justify-between font-semibold text-sm">
                        <span>Rp31.400.000 <span class="text-xs text-gray-400">(DOUBLE)</span></span>

                    </div>
                </div>
            </div>

            <!-- Informasi Kuota -->
            <div class="bg-white rounded-xl shadow p-4 border border-gray-200 flex flex-col gap-2">
                <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2 text-xl">

                    Informasi Kuota
                </div>
                <ul class="flex flex-col gap-3 text-sm text-gray-700">
                    <li class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded bg-amber-400">
                            <img src="{{ asset('assets/icon/bx-building-w.svg') }}" class="w-6 h-6" alt="">
                        </div>
                        <div class="text-gray-500">
                            Total Kuota
                            <br>
                            <span class="font-semibold text-black">20</span>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded bg-amber-400">
                            <img src="{{ asset('assets/icon/bx-phone.svg') }}" class="w-6 h-6" alt="">
                        </div>
                        <div class="text-gray-500">
                            Total Terpakai
                            <br>
                            <span class="font-semibold text-black">9</span>
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded bg-amber-400">
                            <img src="{{ asset('assets/icon/bx-envelope.svg') }}" class="w-6 h-6" alt="">
                        </div>
                        <div class="text-gray-500">
                            Kuota Tersedia
                            <br>
                            <span class="font-semibold text-black">11</span>
                        </div>
                    </li>

                </ul>
            </div>
        </aside>
    </div>
</div>
