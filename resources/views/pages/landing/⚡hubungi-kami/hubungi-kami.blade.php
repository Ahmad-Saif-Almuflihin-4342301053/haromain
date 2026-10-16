<div class="bg-white min-h-screen">
    @php
        $setting = $this->globalSetting ?? app(\App\Services\Landing\BerandaService::class)->getGlobalSetting();
    @endphp

    <!-- Banner Hubungi Kami -->
    <div class="relative w-full h-56 md:h-64 lg:h-72">
        <img src="{{ asset('assets/revisi-landing/image6.jpeg') }}" alt="Hubungi Kami Haromain"
            class="absolute inset-0 w-full h-full object-cover object-center" />
        <div class="absolute inset-0 h-full bg-linear-to-t from-black via-black/40 to-transparent"></div>
        <div class="max-w-7xl mx-auto h-56 md:h-64 lg:h-72 px-4 py-8 flex flex-col justify-end">
            <h1 class="relative z-10 text-white text-3xl md:text-4xl font-bold mb-2">Hubungi Kami</h1>
            <p class="relative z-10 text-gray-200 text-sm md:text-base leading-normal max-w-2xl">
                Konsultasikan kebutuhan pelayanan digital travel Anda bersama tim Haromain.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-10">
                <!-- Kantor Haromain -->
                <div>
                    <div class="font-bold text-3xl mb-3">Kantor Haromain</div>
                    <div class="flex items-start gap-2 mb-2">
                        <img src="{{ asset('assets/icon/bxs-map.svg') }}" class="w-5 h-5 mt-1 text-amber-400 shrink-0"
                            alt="Map">
                        <span class="text-gray-700 text-sm leading-normal">
                            {{ $setting['alamat'] ?? $setting['Alamat'] ?? 'Jl. Prof. Muh. Yamin No. 32 Pandanwangi, Kec. Diwek, Kabupaten Jombang, Jawa Timur 61471' }}
                        </span>
                    </div>
                </div>

                <!-- Kontak Kami -->
                <div>
                    <div class="font-bold text-3xl mb-3">Kontak Kami</div>
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ asset('assets/icon/bxs-phone.svg') }}" class="w-5 h-5 text-amber-400 shrink-0"
                            alt="Phone">
                        <a href="https://api.whatsapp.com/send/?phone={{ $setting['whatsapp'] ?? $setting['WhatsApp'] ?? '6287777235337' }}" 
                           target="_blank" 
                           class="text-gray-700 hover:text-amber-500 transition text-sm">
                            {{ $setting['WhatsAppText'] ?? $setting['whatsapp'] ?? '+62 877 7723 5337' }}
                            <span class="text-xs text-gray-400 ml-1">Customer Service</span>
                        </a>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ asset('assets/icon/bxs-envelope-p.svg') }}" class="w-5 h-5 text-amber-400 shrink-0"
                            alt="Email">
                        <a href="mailto:{{ $setting['email'] ?? $setting['Email'] ?? 'haromainindonesia0@gmail.com' }}" 
                           target="_blank" 
                           class="text-gray-700 hover:text-amber-500 transition text-sm">
                            {{ $setting['email'] ?? $setting['Email'] ?? 'haromainindonesia0@gmail.com' }}
                        </a>
                    </div>
                </div>

                <!-- Jam Operasional -->
                <div>
                    <div class="font-bold text-3xl mb-3">Jam Operasional</div>
                    <div class="flex items-center gap-2 mb-2">
                        <img src="{{ asset('assets/icon/bxs-time-five.svg') }}" class="w-5 h-5 text-amber-400 shrink-0"
                            alt="Time">
                        <span class="text-gray-700 text-sm">Senin – Sabtu: <span class="font-bold">08.00 – 17.00 WIB</span></span>
                    </div>
                    <div class="text-gray-700 text-sm ml-7">Minggu dan hari libur nasional: Tutup</div>
                </div>
            </div>
        </div>
    </div>
</div>

