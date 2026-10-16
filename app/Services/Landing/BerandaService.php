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

    public function getMitraTravelHeader()
    {
        return MstKonten::where('JenisKonten', 'LANDING_MITRA_TRAVEL')
            ->where('IsAktif', 1)
            ->first();
    }

    public function getMitraTravelList()
    {
        return MstKonten::getLandingMitraTravelList();
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

    public function getGlobalSetting()
    {
        $setting = MstKonten::where('JenisKonten', 'LANDING_GLOBAL_SETTING')
            ->where('IsAktif', 1)
            ->first();

        $default = [
            'WhatsApp' => '6287777235337',
            'WhatsAppText' => '+62 877 7723 5337',
            'Email' => 'haromainindonesia0@gmail.com',
            'Instagram' => 'https://www.instagram.com/haromain.indonesia',
            'Facebook' => 'https://web.facebook.com/profile.php?id=61584806686309',
            'TikTok' => 'https://www.tiktok.com/@haromain.indonesi?_r=1&_t=ZS-92LGAA16RhB/',
            'DeskripsiFooter' => 'Haromain adalah platform pendampingan jamaah haji dan umroh yang dikembangkan dari pengalaman serta kondisi nyata di lapangan.',
            'AppStore' => 'https://apps.apple.com/id/app/haromain/id6736605994',
            'PlayStore' => 'https://play.google.com/store/apps/details?id=com.tiap.haromain.haromain'
        ];

        if (!$setting || empty($setting->IsiKonten)) {
            return $default;
        }

        try {
            $decoded = json_decode($setting->IsiKonten, true);
            if (is_array($decoded)) {
                $mapped = [
                    'WhatsApp'        => $decoded['whatsapp'] ?? $decoded['WhatsApp'] ?? $default['WhatsApp'],
                    'WhatsAppText'    => $decoded['whatsapp_text'] ?? $decoded['WhatsAppText'] ?? ('+' . ($decoded['whatsapp'] ?? $default['WhatsApp'])),
                    'Email'           => $decoded['email'] ?? $decoded['Email'] ?? $default['Email'],
                    'Instagram'       => $decoded['instagram'] ?? $decoded['Instagram'] ?? $default['Instagram'],
                    'Facebook'        => $decoded['facebook'] ?? $decoded['Facebook'] ?? $default['Facebook'],
                    'TikTok'          => $decoded['tiktok'] ?? $decoded['TikTok'] ?? $default['TikTok'],
                    'DeskripsiFooter' => $decoded['description'] ?? $decoded['DeskripsiFooter'] ?? $default['DeskripsiFooter'],
                    'AppStore'        => $decoded['appstore'] ?? $decoded['AppStore'] ?? $default['AppStore'],
                    'PlayStore'       => $decoded['playstore'] ?? $decoded['PlayStore'] ?? $default['PlayStore'],
                ];
                return array_merge($default, $decoded, $mapped);
            }
        } catch (\Throwable $e) {
            // fallback gracefully
        }

        return $default;
    }
}
