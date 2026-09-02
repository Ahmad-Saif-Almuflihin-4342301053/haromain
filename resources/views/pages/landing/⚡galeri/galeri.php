<?php

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Services\Landing\GaleriService;

new #[Layout('layouts::landing')] class extends Component {
    use WithPagination;

    protected $galeriService;

    public $videosByKategori = [];
    public $imagesByKategori = [];
    public $imagesPerKategori = [];

    public function boot(GaleriService $galeriService)
    {
        $this->galeriService = $galeriService;
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Load videos grouped by kategori
        $this->videosByKategori = $this->galeriService->getVideosGroupedByKategori();

        // Load images grouped by kategori
        $imagesGrouped = $this->galeriService->getImagesGroupedByKategori();

        foreach ($imagesGrouped as $kategori => $images) {
            $this->imagesByKategori[$kategori] = [
                'items' => $images->take($this->getImagesPerKategori($kategori))->toArray(),
                'total' => $images->count(),
            ];
        }
    }

    public function getImagesPerKategori($kategori)
    {
        return $this->imagesPerKategori[$kategori] ?? 8;
    }

    public function loadMore($kategori)
    {
        $currentLimit = $this->getImagesPerKategori($kategori);
        $this->imagesPerKategori[$kategori] = $currentLimit + 4;

        // Reload images for this category
        $imagesGrouped = $this->galeriService->getImagesGroupedByKategori();

        if (isset($imagesGrouped[$kategori])) {
            $this->imagesByKategori[$kategori] = [
                'items' => $imagesGrouped[$kategori]->take($this->imagesPerKategori[$kategori])->toArray(),
                'total' => $imagesGrouped[$kategori]->count(),
            ];
        }
    }

    public function hasMoreImages($kategori)
    {
        if (!isset($this->imagesByKategori[$kategori])) {
            return false;
        }

        return count($this->imagesByKategori[$kategori]['items']) < $this->imagesByKategori[$kategori]['total'];
    }

    #[Computed]
    public function galeriHeader()
    {
        return $this->galeriService->getGaleriHeader();
    }

    #[Computed]
    public function galeriFooter()
    {
        return $this->galeriService->getGaleriFooter();
    }

    #[Computed]
    public function galeriTitle()
    {
        return $this->galeriService->getGaleriTitle();
    }
};
