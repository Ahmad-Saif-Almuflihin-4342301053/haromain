<?php

namespace App\Services\Landing;
use App\Models\MstKonten;
use App\Models\MstBanner;
use App\Models\MstTravel;

class BerandaService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    public function getSlider()
    {
        // return MstKonten::getLandingSlider();
        return MstBanner::getLandingBanner();
    }

    public function getMemahamiTentang()
    {
        return MstKonten::getLandingMemahamiTentang();
    }

    public function getPeranHaromain()
    {
        return MstKonten::getLandingPeranHaromain();
    }

    public function getPeranHaromainList()
    {
        return MstKonten::getLandingPeranHaromainList();
    }

    public function getPeranHaromainFooter()
    {
        return MstKonten::getLandingPeranHaromainFooter();
    }

    public function getKenalLebih()
    {
        return MstKonten::getLandingKenalLebih();
    }

    public function getHaromainBersama()
    {
        return MstKonten::getLandingHaromainBersama();
    }

    public function getFiturUnggulan()
    {
        return MstKonten::getLandingFiturUnggulan();
    }

    public function getFiturUnggulanList()
    {
        return MstKonten::getLandingFiturUnggulanList();
    }

    public function getSiapaYang()
    {
        return MstKonten::getLandingSiapaYang();
    }

    public function siapaYangList()
    {
        return MstKonten::getLandingSiapaYangList();
    }

    public function getKondisiPengguna()
    {
        return MstKonten::getLandingKondisiPengguna();
    }

    public function getMitraTravelList()
    {
        return MstTravel::where('IsShow', 1)
            ->where('IsVerified', 1)
            ->where('StatusAkun', 'Aktif')
            ->orderBy('NamaTravel', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'JudulKonten' => $item->NamaTravel,
                    'IsiKonten' => $item->NamaCP . ' - ' . $item->TelpCP,
                    'GambarKonten' => $item->LogoKantor,
                ];
            })
            ->toArray();
    }

    public function getTestimonial()
    {
        return MstKonten::getLandingTestimonial();
    }

    public function getTestimonialList()
    {
        $data = MstKonten::getLandingTestimonialList();
        $grouped = array_chunk($data, 3);
        return $grouped;
    }

    public function getPertanyaan()
    {
        return MstKonten::getLandingPertanyaan();
    }

    public function getPertanyaanList()
    {
        return MstKonten::getLandingPertanyaanList();
    }

    public function getActionButton()
    {
        return MstKonten::getLandingActionButton();
    }

    public function getCaraMemulai()
    {
        return MstKonten::getLandingCaraMemulai();
    }
}
