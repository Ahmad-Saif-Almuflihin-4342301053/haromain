<div x-data="{
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
    <!-- Banner Hero Compact -->
    <div class="relative w-full h-56 md:h-64 lg:h-72">
        <!-- Background image -->
        <img src="{{ url('/') }}/assets/konten/{{ @$this->galeriHeader->GambarKonten }}" alt="Galeri"
            class="absolute inset-0 w-full h-full object-cover object-center" />

        <!-- Overlay -->
        <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-transparent"></div>

        <!-- CONTENT CONTAINER -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 h-full flex flex-col justify-end pb-6 md:pb-8">
            <div class="max-w-2xl text-left">
                <h1 class="text-white text-3xl md:text-4xl font-bold mb-2">
                    Panduan & Simulasi Haromain
                </h1>
                <p class="text-gray-200 text-sm md:text-base">
                    Video panduan, tutorial, dan simulasi penggunaan Haromain dalam pelayanan jamaah.
                </p>
            </div>
        </div>
    </div>

    {{-- Video Gallery - Dikelompokkan berdasarkan Kategori --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-3xl font-bold mb-2">{{ @$this->galeriTitle->JudulKonten }}</h2>
            <p class="text-gray-600">{{ @$this->galeriTitle->IsiKonten }}</p>
        </div>

        <!-- Video Sections - Grouped by Kategori -->
        @forelse($videosByKategori as $videoGroup)
        {{-- Setiap kategori video tampil terpisah --}}
        <div class="mb-12">
            <div class="mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 capitalize">
                    {{ $videoGroup['kategori'] }}
                </h2>
                <div class="h-1 w-20 bg-amber-400 mt-2"></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($videoGroup['items'] as $video)
                @php
                if (strpos($video['id'], 'http') !== false) {
                //get id
                $pattern =
                '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts|live)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
                if (preg_match($pattern, $video['id'], $match)) {
                $video['id'] = $match[1];
                }
                }
                @endphp
                <div class="relative group cursor-pointer" @click="openModal('{{ $video['id'] }}')">
                    <img src="https://img.youtube.com/vi/{{ $video['id'] }}/hqdefault.jpg"
                        class="rounded-xl w-full aspect-video object-cover shadow-lg group-hover:brightness-75 transition"
                        alt="{{ $video['nama'] }}">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="bg-black/60 rounded-full p-3">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="my-2.5 font-semibold">{{ $video['nama'] }}</h3>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <!-- Fallback jika tidak ada data video -->
        <div class="mb-10">
            <h2 class="text-lg md:text-xl font-bold mb-4">Dokumentasi Keberangkatan & Kepulangan</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <template
                    x-for="video in [
                        {id: 'm-j1LRgaJ7o'},
                        {id: 'Z36U8RsmpxA'},
                        {id: 'vFraQQttWYw'},
                        {id: 'gMNBcSsswWc'}
                    ]"
                    :key="video.id">
                    <div class="relative group cursor-pointer" @click="openModal(video.id)">
                        <img :src="'https://img.youtube.com/vi/' + video.id + '/hqdefault.jpg'"
                            class="rounded-xl w-full aspect-video object-cover shadow-lg group-hover:brightness-75 transition"
                            alt="Video Thumbnail">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-black/60 rounded-full p-3">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Image Gallery - Dikelompokkan berdasarkan Kategori --}}
    <!-- Image Sections - Grouped by Kategori -->
    @forelse($imagesByKategori as $kategori => $imageGroup)
    {{-- Setiap kategori gambar tampil terpisah --}}
    <div class="max-w-7xl mx-auto px-4 pb-16">
        <div class="mb-6">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 capitalize">
                {{ $kategori }}
            </h3>
            <div class="h-1 w-20 bg-amber-400 mt-2"></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 image-viewer">
            @foreach ($imageGroup['items'] as $image)
            <img src="{{ $image['KontenGaleri'] }}" data-original="{{ $image['KontenGaleri'] }}"
                class="rounded-xl w-full aspect-4/3 object-cover shadow-lg cursor-pointer hover:brightness-75 transition"
                alt="{{ $image['NamaGaleri'] }}">
            <h3 class="my-2.5 font-semibold">{{ $image['NamaGaleri'] }}</h3>
            @endforeach
        </div>

        @if ($this->hasMoreImages($kategori))
        <div class="text-center mt-8">
            <button wire:click="loadMore('{{ $kategori }}')"
                class="inline-block! bg-amber-400! hover:bg-amber-500! text-white! px-8 py-3 rounded-lg font-semibold transition text-base">
                Muat Lebih Banyak
            </button>
        </div>
        @endif
    </div>
    @empty
    <!-- Fallback jika tidak ada data gambar -->
    {{-- <div class="max-w-7xl mx-auto px-4 pb-16">
            <h3 class="text-lg md:text-xl font-bold mb-4">Dokumentasi Foto Jamaah & Kegiatan</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 image-viewer">
                <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=600"
                    data-original="https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200"
                    class="rounded-xl w-full aspect-4/3 object-cover shadow-lg cursor-pointer group-hover:brightness-75 transition"
                    alt="Galeri Jamaah 1">
                <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?w=600"
                    data-original="https://images.unsplash.com/photo-1464983953574-0892a716854b?w=1200"
                    class="rounded-xl w-full aspect-4/3 object-cover shadow-lg cursor-pointer group-hover:brightness-75 transition"
                    alt="Galeri Jamaah 2">
                <img src="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=600"
                    data-original="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=1200"
                    class="rounded-xl w-full aspect-4/3 object-cover shadow-lg cursor-pointer group-hover:brightness-75 transition"
                    alt="Galeri Jamaah 3">
                <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?w=600"
                    data-original="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?w=1200"
                    class="rounded-xl w-full aspect-4/3 object-cover shadow-lg cursor-pointer group-hover:brightness-75 transition"
                    alt="Galeri Jamaah 4">
                <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?w=600"
                    data-original="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?w=1200"
                    class="rounded-xl w-full aspect-4/3 object-cover shadow-lg cursor-pointer group-hover:brightness-75 transition"
                    alt="Galeri Jamaah 5">
                <img src="https://images.unsplash.com/photo-1519985176271-adb1088fa94c?w=600"
                    data-original="https://images.unsplash.com/photo-1519985176271-adb1088fa94c?w=1200"
                    class="rounded-xl w-full aspect-4/3 object-cover shadow-lg cursor-pointer group-hover:brightness-75 transition"
                    alt="Galeri Jamaah 7">
            </div>
        </div> --}}
    @endforelse




    <!-- Modal Video Player -->
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
        @click.self="closeModal()">
        <div class="relative w-full max-w-2xl aspect-video bg-black rounded-xl overflow-hidden">
            <button @click="closeModal()"
                class="absolute top-2 right-2 z-10 bg-white/80 hover:bg-white text-gray-800 rounded-full p-2 shadow">
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

</div>