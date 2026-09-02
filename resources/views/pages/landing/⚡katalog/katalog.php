<?php

use App\Models\MstPaket;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Services\Landing\KatalogService;

new #[Layout('layouts::landing')] class extends Component {
    // filter data
    public array $selectedKategoriPaket = [];
    public string $namaPaket = '';
    public string $bulanTahunKeberangkatan = '';
    public string $selectedHargaPaket = '';
    public string $selectedKotaKebarangkatan = '';
    public string $selectedKotaTujuan = '';
    public string $selectedJarakHotel = '';
    public string $selectedDurasiPerjalanan = '';
    public array $selectedJenis = [];
    public string $selectedMaskapaiPenerbangan = '';
    public string $selectedOrderBy = '';

    public int $page = 1;
    public int $lastPage = 1;

    public array $itemsPaket = [];


    #[Computed]
    public $filterHargaPaket = [
        ['label' => '≤ 30 juta', 'value' => '30000000', 'param' => 'max'],
        ['label' => '30 - 40 juta', 'value' => '30000000-40000000', 'param' => 'min-max'],
        ['label' => '≥ 40 juta', 'value' => '40000000', 'param' => 'min'],
    ];

    #[Computed]
    public $filterArahHotel = [
        ['label' => '≤ 500 meter', 'value' => '500', 'param' => 'max'],
        ['label' => '500 - 1000 meter', 'value' => '500-1000', 'param' => 'min-max'],
        ['label' => '≥ 1000 meter', 'value' => '1000', 'param' => 'min'],
    ];

    #[Computed]
    public $filterDurasiPerjalanan = [
        ['label' => '≤ 9 hari', 'value' => '9', 'param' => 'max'],
        ['label' => '10 - 15 hari', 'value' => '10-15', 'param' => 'min-max'],
        ['label' => '≥ 15 hari', 'value' => '15', 'param' => 'min'],
    ];

    #[Computed]
    public $orderByFilter = [
        ['label' => 'Paling Awal', 'value' => 'date_asc'],
        ['label' => 'Paling Akhir', 'value' => 'date_desc'],
        ['label' => 'Termahal', 'value' => 'harga_desc'],
        ['label' => 'Termurah', 'value' => 'harga_asc'],
    ];

    #[Computed]
    public function katalogHeader()
    {
        return app(KatalogService::class)->getKatalogHeader();
    }

    #[Computed]
    public function kategoriPaket()
    {
        return app(KatalogService::class)->getKategoriPaket();
    }

    #[Computed]
    public function kotaBerangkat()
    {
        return app(KatalogService::class)->getKotaBerangkat();
    }

    #[Computed]
    public function kotaTujuan()
    {
        return app(KatalogService::class)->getKotaTujuan();
    }

    #[Computed]
    public function maskapaiList()
    {
        return app(KatalogService::class)->getMaskapaiList();
    }

    #[Computed]
    public function katalogFooter()
    {
        return app(KatalogService::class)->getKatalogFooter();
    }

    #[Computed]
    public function dataPaket()
    {
        $parameters = [
            'NamaPaket' => $this->namaPaket,
            'KategoriPaket' => $this->selectedKategoriPaket,
            'BulanTahunBerangkat' => $this->bulanTahunKeberangkatan,
            'HargaPaket' => $this->filterHargaPaket[$this->selectedHargaPaket] ?? null,
            'KotaKebarangkatan' => $this->selectedKotaKebarangkatan,
            'KotaTujuan' => $this->selectedKotaTujuan,
            'JarakHotel' => $this->filterArahHotel[$this->selectedJarakHotel] ?? null,
            'DurasiPerjalanan' => $this->filterDurasiPerjalanan[$this->selectedDurasiPerjalanan] ?? null,
            'JenisPenerbangan' => $this->selectedJenis,
            'MaskapaiPenerbangan' => $this->selectedMaskapaiPenerbangan,
            'OrderBy' => $this->orderByFilter[$this->selectedOrderBy]['value'] ?? null,
            'Page' => $this->page,
        ];

        $data = app(KatalogService::class)->getKatalogList($parameters);
        $newData = array_map(
            fn($item) => $item->toArray(),
            $data->items()
        );
        $this->lastPage = $data->lastPage();
        return $newData;
    }

    public function updated($field)
    {
        if ($field === 'selectedOrderBy') {
            $this->applyFilter();
        }
    }

    public function applyFilter()
    {
        $this->page = 1;

        $this->itemsPaket = [];
        $this->itemsPaket = array_merge($this->itemsPaket, $this->dataPaket());
    }

    public function resetFilter()
    {
        $this->selectedKategoriPaket = [];
        $this->namaPaket = '';
        $this->bulanTahunKeberangkatan = '';
        $this->page = 1;
        $this->selectedHargaPaket = '';
        $this->selectedKotaKebarangkatan = '';
        $this->selectedKotaTujuan = '';
        $this->selectedJarakHotel = '';
        $this->selectedDurasiPerjalanan = '';
        $this->selectedJenis = [];
        $this->selectedMaskapaiPenerbangan = '';

        $this->itemsPaket = [];
        $this->itemsPaket = array_merge($this->itemsPaket, $this->dataPaket());
    }

    public function loadMore()
    {
        $this->page += 1;
        $newData = $this->dataPaket();
        $this->itemsPaket = array_merge($this->itemsPaket, $newData);
    }

    public function mount()
    {
        $this->itemsPaket = $this->dataPaket();
    }

};
