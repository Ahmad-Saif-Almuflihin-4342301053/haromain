<div class="space-y-6 select-none">
    <div>
        <label class="block text-sm font-semibold mb-2">Nama Paket</label>
        <input type="text"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-400! focus:border-amber-400! transition"
            placeholder="Cari nama paket..." wire:model.debounce.500ms="namaPaket" />
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Kategori Paket</label>
        <div class="flex flex-col gap-1">
            @foreach ($this->kategoriPaket as $key => $item)
                <label wire:key="labelKatPaket{{ $key }}"
                    class="flex items-center gap-2 text-sm bg-gray-100 px-2 py-1 rounded cursor-pointer">
                    <input value="{{ $item }}" type="checkbox" wire:model="selectedKategoriPaket"
                        class="accent-amber-400 border-gray-300 rounded focus:ring-amber-400! checked:bg-amber-400!" />
                    {{ $item }}
                </label>
            @endforeach
        </div>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Bulan Keberangkatan</label>
        <input type="month" wire:model="bulanTahunKeberangkatan"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-400! focus:border-amber-400! transition" />
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Harga Paket</label>
        <div class="flex flex-col gap-1">
            @foreach ($filterHargaPaket as $key => $item)
                <label class="flex items-center gap-2 text-sm">
                    <input value="{{ $key }}" wire:model="selectedHargaPaket" type="radio" name="harga"
                        class="accent-amber-400! border-gray-300 focus:ring-amber-400! checked:bg-amber-400!" />
                    {{ $item['label'] }}
                </label>
            @endforeach
        </div>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Kota / Bandara Keberangkatan</label>
        <select wire:model="selectedKotaKebarangkatan"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-400! focus:border-amber-400! transition">
            <option value="">Semua</option>
            @foreach ($this->kotaBerangkat as $item)
                <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-2">Kota / Bandara Tujuan</label>
        <select wire:model="selectedKotaTujuan"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-400! focus:border-amber-400! transition">
            <option value="">Semua</option>
            @foreach ($this->kotaTujuan as $item)
                <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Jarak Hotel ke Masjidil Haram</label>
        <div class="flex flex-col gap-1">
            @foreach ($filterArahHotel as $key => $item)
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="jarak" value="{{ $key }}" wire:model="selectedJarakHotel"
                        class="accent-amber-400 border-gray-300 focus:ring-amber-400! checked:bg-amber-400!" />
                    {{ $item['label'] }}
                </label>
            @endforeach
        </div>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Durasi Perjalanan</label>
        <div class="flex flex-col gap-1">
            @foreach ($filterDurasiPerjalanan as $key => $item)
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="durasi" wire:model="selectedDurasiPerjalanan"
                        value="{{ $key }}"
                        class="accent-amber-400 border-gray-300 focus:ring-amber-400! checked:bg-amber-400!" />
                    {{ $item['label'] }}
                </label>
            @endforeach

        </div>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Jenis Penerbangan</label>
        <div class="flex flex-col gap-1">
            <label class="flex items-center gap-2 text-sm bg-gray-100 px-2 py-1 rounded cursor-pointer">
                <input type="checkbox" wire:model="selectedJenis" value="Direct"
                    class="accent-amber-400 border-gray-300 rounded focus:ring-amber-400! checked:bg-amber-400!" />
                Direct
            </label>
            <label class="flex items-center gap-2 text-sm bg-gray-100 px-2 py-1 rounded cursor-pointer">
                <input type="checkbox" wire:model="selectedJenis" value="Transit"
                    class="accent-amber-400 border-gray-300 rounded focus:ring-amber-400! checked:bg-amber-400!" />
                Transit
            </label>
        </div>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">Maskapai Penerbangan</label>
        <select wire:model="selectedMaskapaiPenerbangan"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-amber-400! focus:border-amber-400! transition">
            <option value="">Semua</option>
            @foreach ($this->maskapaiList as $item)
                <option value="{{ strtolower($item['Maskapai']) }}">{{ $item['Maskapai'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex flex-col space-y-2 pt-4">
        <button @click="open = false" wire:loading.attr="disabled" wire:click="applyFilter()" type="button"
            class="bg-amber-400! hover:bg-amber-500! text-white px-4 py-2 rounded-lg font-semibold w-full transition">
            <span wire:loading.remove wire:target="applyFilter">Terapkan Filter</span>
            <span wire:loading wire:target="applyFilter">
                <span class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Loading...
                </span>
            </span>
        </button>
        <button @click="open = false" wire:click="resetFilter" wire:loading.attr="disabled" type="button"
            class="border-2 border-amber-400! text-amber-500! bg-white hover:bg-amber-50 px-4 py-2 rounded-lg font-semibold w-full transition">Reset</button>
    </div>
</div>
