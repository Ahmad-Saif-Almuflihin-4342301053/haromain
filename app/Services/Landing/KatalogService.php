<?php

namespace App\Services\Landing;
use App\Models\MstKonten;
use App\Models\MstPaket;
use App\Models\MstMaskapai;
use Stevebauman\Purify\Facades\Purify;

class KatalogService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getKatalogHeader()
    {
        return MstKonten::getLandingKatalogHeader();
    }

    public function getKatalogList($parameters)
    {
        $parameters = Purify::clean($parameters);
        // olah filter parameeter disini
        $namaPaket = $parameters['NamaPaket'] ?? null;
        $kategoriPaket = $parameters['KategoriPaket'] ?? null;
        $bulanTahunBerangkat = $parameters['BulanTahunBerangkat'] ?? null;
        $xBulanTahunBerangkat = explode('-', $bulanTahunBerangkat);
        $bulanBerangkat = null;
        $tahunBerangkat = null;

        if (count($xBulanTahunBerangkat) == 2) {
            $bulanBerangkat = (int) $xBulanTahunBerangkat[1];
            $tahunBerangkat = (int) $xBulanTahunBerangkat[0];
        }

        $hargaPaketP = $parameters['HargaPaket'] ?? null;
        $hargaPaket = [];
        $hargaPaketParam = [];
        if (is_array($hargaPaketP) && count($hargaPaketP) > 0) {
            if ($hargaPaketP['param'] == 'min-max') {
                $xHarga = explode('-', $hargaPaketP['value']);
                $hargaPaket[] = (int) $xHarga[0];
                $hargaPaket[] = (int) $xHarga[1];
                $hargaPaketParam[] = 'min';
                $hargaPaketParam[] = 'max';
            } elseif ($hargaPaketP['param'] == 'min') {
                $hargaPaket[] = (int) $hargaPaketP['value'];
                $hargaPaketParam[] = 'min';
            } elseif ($hargaPaketP['param'] == 'max') {
                $hargaPaket[] = (int) $hargaPaketP['value'];
                $hargaPaketParam[] = 'max';
            }
        }

        $kotaKebrangkatan = strtolower($parameters['KotaKebarangkatan'] ?? '');
        $kotaTujuan = strtolower($parameters['KotaTujuan'] ?? '');

        $jarakHotelP = $parameters['JarakHotel'] ?? null;
        $jarakHotel = [];
        $jarakHotelParam = [];

        if (is_array($jarakHotelP) && count($jarakHotelP) > 0) {
            if ($jarakHotelP['param'] == 'min-max') {
                $xJarak = explode('-', $jarakHotelP['value']);
                $jarakHotel[] = (int) $xJarak[0];
                $jarakHotel[] = (int) $xJarak[1];
                $jarakHotelParam[] = 'min';
                $jarakHotelParam[] = 'max';
            } elseif ($jarakHotelP['param'] == 'min') {
                $jarakHotel[] = (int) $jarakHotelP['value'];
                $jarakHotelParam[] = 'min';
            } elseif ($jarakHotelP['param'] == 'max') {
                $jarakHotel[] = (int) $jarakHotelP['value'];
                $jarakHotelParam[] = 'max';
            }
        }

        $durasiPerjalananP = $parameters['DurasiPerjalanan'] ?? null;
        $durasiPerjalanan = [];
        $durasiPerjalananParam = [];

        if (is_array($durasiPerjalananP) && count($durasiPerjalananP) > 0) {
            if ($durasiPerjalananP['param'] == 'min-max') {
                $xDurasi = explode('-', $durasiPerjalananP['value']);
                $durasiPerjalanan[] = (int) $xDurasi[0];
                $durasiPerjalanan[] = (int) $xDurasi[1];
                $durasiPerjalananParam[] = 'min';
                $durasiPerjalananParam[] = 'max';
            } elseif ($durasiPerjalananP['param'] == 'min') {
                $durasiPerjalanan[] = (int) $durasiPerjalananP['value'];
                $durasiPerjalananParam[] = 'min';
            } elseif ($durasiPerjalananP['param'] == 'max') {
                $durasiPerjalanan[] = (int) $durasiPerjalananP['value'];
                $durasiPerjalananParam[] = 'max';
            }
        }
        $jenisPenerbangan = $parameters['JenisPenerbangan'] ?? [];
        $maskapaiPenerbangan = strtolower($parameters['MaskapaiPenerbangan'] ?? '');

        $orderByData = $parameters['OrderBy'] ?? null;
        $orderBy = '';
        $orderDirection = '';

        if ($orderByData != null && $orderByData != '') {
            $xOrderBy = explode('_', $orderByData);
            if (count($xOrderBy) == 2) {
                if (in_array($xOrderBy[0], ['date', 'harga'])) {
                    $orderBy = $xOrderBy[0] == 'date' ? 'TglBerangkat' : 'BiayaQuad';
                }
                if (in_array($xOrderBy[1], ['asc', 'desc'])) {
                    $orderDirection = strtoupper($xOrderBy[1]);
                }
            }
        }


        $page = (int) ($parameters['Page'] ?? 1);
        $pagination = 6;


        // return data paket dari model
        $data = MstPaket::getLandingKatalogList(
            namaPaket: $namaPaket,
            kategoriPaket: $kategoriPaket,
            bulanBerangkat: $bulanBerangkat,
            tahunBerangkat: $tahunBerangkat,
            hargaPaket: $hargaPaket,
            hargaPaketParam: $hargaPaketParam,
            kotaKebrangkatan: $kotaKebrangkatan,
            kotaTujuan: $kotaTujuan,
            jarakHotel: $jarakHotel,
            jarakHotelParam: $jarakHotelParam,
            durasiPerjalanan: $durasiPerjalanan,
            durasiPerjalananParam: $durasiPerjalananParam,
            jenisPenerbangan: $jenisPenerbangan,
            maskapaiPenerbangan: $maskapaiPenerbangan,
            orderBy: $orderBy,
            orderDirection: $orderDirection,
            page: $page,
            pagination: $pagination
        );


        return $data;
    }

    public function getKategoriPaket()
    {
        return MstPaket::getLandingKategoriPaket();
    }

    public function getKotaBerangkat()
    {
        $kotaBerangkat = MstPaket::getLandingKotaBerangkat();
        // Capitalize each word and filter out empty values
        return array_values(
            array_filter(
                array_map(fn($kota) => ucwords(strtolower($kota)), $kotaBerangkat),
                fn($kota) => !empty($kota)
            )
        );
    }

    public function getKotaTujuan()
    {
        $kotaTujuan = MstPaket::getLandingKotaTujuan();
        // Capitalize each word
        return array_values(
            array_filter(
                array_map(fn($kota) => ucwords(strtolower($kota)), $kotaTujuan),
                fn($kota) => !empty($kota)
            )
        );
    }

    public function getMaskapaiList(): array
    {
        return MstMaskapai::getLandingMaskapaiList();
    }

    public function getKatalogFooter()
    {
        return MstKonten::getLandingKatalogFooter();
    }
}
