<?php

namespace App\Services\Landing;

use App\Models\MstGaleri;
use App\Models\MstKonten;
use Illuminate\Support\Collection;

class GaleriService
{
    /**
     * Get videos grouped by category
     */
    public function getVideosGroupedByKategori()
    {
        return MstGaleri::where('JenisGaleri', 'video')
            ->get()
            ->groupBy('KategoriGaleri')
            ->map(function ($items, $kategori) {
                return [
                    'kategori' => $kategori,
                    'items' => $items->map(function ($item) {
                        $id = $item->KontenGaleri;
                        // Extract YouTube ID if it's a URL
                        if (filter_var($id, FILTER_VALIDATE_URL)) {
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $id, $matches);
                            if (isset($matches[1])) {
                                $id = $matches[1];
                            }
                        }
                        return [
                            'id' => $id,
                            'nama' => $item->NamaGaleri,
                        ];
                    })->toArray()
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get images grouped by category
     */
    public function getImagesGroupedByKategori()
    {
        return MstGaleri::where('JenisGaleri', 'gambar')
            ->get()
            ->map(function ($item) {
                if (!filter_var($item->KontenGaleri, FILTER_VALIDATE_URL)) {
                    $item->KontenGaleri = asset('assets/galeri/' . $item->KontenGaleri);
                }
                return $item;
            })
            ->groupBy('KategoriGaleri');
    }

    /**
     * Get videos by category
     */
    public function getVideosByKategori($kategori)
    {
        return MstGaleri::where('JenisGaleri', 'video')
            ->where('KategoriGaleri', $kategori)
            ->get();
    }

    /**
     * Get all videos
     */
    public function getAllVideos()
    {
        return MstGaleri::where('JenisGaleri', 'video')->get();
    }

    /**
     * Get images with pagination
     */
    public function getImages($perPage = 6)
    {
        return MstGaleri::where('JenisGaleri', 'gambar')
            ->paginate($perPage);
    }

    /**
     * Get all images
     */
    public function getAllImages()
    {
        return MstGaleri::where('JenisGaleri', 'gambar')->get();
    }

    /**
     * Get gallery by type
     */
    public function getByJenis($jenis)
    {
        return MstGaleri::where('JenisGaleri', $jenis)->get();
    }

    public function getGaleriHeader()
    {
        return MstKonten::getLandingGaleriHeader();
    }

    public function getGaleriFooter()
    {
        return MstKonten::getLandingGaleriFooter();
    }

    public function getGaleriTitle()
    {
        return MstKonten::getLandingGaleriTitle();
    }
}
