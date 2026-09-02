<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Paket;
use App\Models\MobileModels\Travel;
use App\Models\MobileModels\JamaahPaket;
use App\Models\MobileModels\HotelPaket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaketController extends BaseApiController
{
    /**
     * GET /api/paket/jenis
     * Get jenis/kategori paket
     */
    public function jenis(): JsonResponse
    {
        $result = Paket::getJenisPaket();

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data kategori paket tidak ditemukan');
    }

    /**
     * GET /api/paket/agen/{IDPerson}
     * Get paket by agen
     */
    public function agen(string $IDPerson): JsonResponse
    {
        $result = Paket::findByAgen($IDPerson);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data paket travel tidak ditemukan');
    }

    /**
     * GET /api/paket/katalogagen/{IDPerson}
     * Get katalog paket untuk agen
     */
    public function katalogAgen(string $IDPerson, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));
        $dashboard = (bool) $request->query('dashboard', false);

        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $KotaTujuan = $this->sanitize($request->query('KotaTujuan'));
        $TglBerangkat = $this->sanitize($request->query('TglBerangkat'));
        $KategoriPaket = $this->sanitize($request->query('KategoriPaket'));

        $limit = $dashboard ? 7 : 10;

        $result = Paket::findByAgen(
            $IDPerson,
            true,
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket,
            $cari,
            $limit,
            $page
        );

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'page' => $page,
                'count' => count($result),
            ]);
        }

        return $this->notFound('Data paket travel tidak ditemukan');
    }

    /**
     * GET /api/paket/monitoringagen/{IDPerson}
     * Get monitoring paket untuk agen
     */
    public function monitoringAgen(string $IDPerson, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));

        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $KotaTujuan = $this->sanitize($request->query('KotaTujuan'));
        $TglBerangkat = $this->sanitize($request->query('TglBerangkat'));
        $KategoriPaket = $this->sanitize($request->query('KategoriPaket'));

        $result = Paket::findAgenMonitoring(
            $IDPerson,
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket,
            $cari,
            10,
            $page
        );

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data paket travel tidak ditemukan');
    }

    /**
     * GET /api/paket/historyagen/{IDPerson}
     * Get history paket untuk agen
     */
    public function historyAgen(string $IDPerson, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));

        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $KotaTujuan = $this->sanitize($request->query('KotaTujuan'));
        $TglBerangkat = $this->sanitize($request->query('TglBerangkat'));
        $KategoriPaket = $this->sanitize($request->query('KategoriPaket'));

        $result = Paket::findAgenHistory(
            $IDPerson,
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket,
            $cari,
            10,
            $page
        );

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data paket travel tidak ditemukan');
    }

    /**
     * GET /api/paket/katalog
     * Get katalog paket
     */
    public function katalog(Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $cari = $this->sanitize($request->query('cari'));
        $dashboard = (bool) $request->query('dashboard', false);

        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $KotaTujuan = $this->sanitize($request->query('KotaTujuan'));
        $TglBerangkat = $this->sanitize($request->query('TglBerangkat'));
        $KategoriPaket = $this->sanitize($request->query('KategoriPaket'));

        $limit = $dashboard ? 7 : 10;

        $result = Paket::findKatalog(
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket,
            $cari,
            $limit,
            $page
        );

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data paket travel tidak ditemukan');
    }

    /**
     * GET /api/paket/{IDTravel}/{IDPaket}
     * Get detail paket
     */
    public function detail(string $IDTravel, string $IDPaket, Request $request): JsonResponse
    {
        $IDPerson = $this->sanitize($request->query('IDPerson'));

        $result = Paket::getDetail($IDTravel, $IDPaket);

        if (!$result) {
            return $this->notFound('Data paket travel tidak ditemukan');
        }

        // Add NoHP and NamaLeader
        $result['NoHPLeader'] = $result['TelpCP'] ?? null;
        $result['NamaLeader'] = $result['NamaCP'] ?? '-';

        // Generate WhatsApp text
        $result['TextWA'] = "Assalamualaikum kak 🙏

Saya ingin bertanya mengenai paket Umrah/Haji berikut:

📌 Nama Paket: " . $result['NamaPaket'] . "
📌 Nama Travel: " . $result['NamaTravel'] . "
📌 Tanggal Keberangkatan: " . date('d-m-Y', strtotime($result['TglBerangkat'])) . "

Mohon bantu informasinya ya kak 😊

Terima kasih...";

        // Get testimoni if IDPerson provided
        if ($IDPerson) {
            $testimoni = JamaahPaket::getDetail($IDTravel, $IDPaket, $IDPerson);
            $result['RatingTestimoni'] = $testimoni['RatingTestimoni'] ?? 0;
            $result['NarasiTestimoni'] = $testimoni['NarasiTestimoni'] ?? '';
            $result['TanggalTestimoni'] = $testimoni['TanggalTestimoni'] ?? '';
        } else {
            $result['RatingTestimoni'] = 0;
            $result['NarasiTestimoni'] = '';
            $result['TanggalTestimoni'] = '';
        }

        // Get average testimoni
        $avgTestimoni = JamaahPaket::getAvgTestimoni($IDTravel);
        $result['AvgRating'] = round($avgTestimoni['avgRating'], 1);
        $result['JmlJamaah'] = (int) $avgTestimoni['jmlJamaah'];
        $result['JmlReviewer'] = (int) $avgTestimoni['jmlReviewer'];

        // Get syarat paket
        $result['syaratPaket'] = Paket::getSyaratPaket($IDTravel, $IDPaket);

        // Get travel detail
        $result['travel'] = Travel::getDetail($IDTravel);

        return $this->success($result);
    }

    /**
     * GET /api/paket/hotel
     * Get hotel by paket
     */
    public function hotel(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $CurrentDateTime = $this->sanitize($request->query('CurrentDateTime'));

        if (!$IDTravel || !$IDPaket) {
            return $this->validationError('IDTravel dan IDPaket harus diisi');
        }

        $result = HotelPaket::findByPaket($IDTravel, $IDPaket);

        if (!empty($result)) {
            $timestamp = $CurrentDateTime ? strtotime($CurrentDateTime) : time();

            // Add timestamp to each hotel
            $resultValue = array_map(function ($hotel) use ($timestamp) {
                $hotel['TimeStamp'] = $timestamp;
                return $hotel;
            }, $result);

            // Sort: Current active hotel first, then by TglMasuk
            usort($resultValue, function ($a, $b) use ($timestamp) {
                $startA = strtotime($a['TglMasuk'] . ' 00:00:00');
                $endA = strtotime($a['TglKeluar'] . ' 23:59:59');
                $isCurrentA = ($timestamp >= $startA && $timestamp <= $endA);

                $startB = strtotime($b['TglMasuk'] . ' 00:00:00');
                $endB = strtotime($b['TglKeluar'] . ' 23:59:59');
                $isCurrentB = ($timestamp >= $startB && $timestamp <= $endB);

                // Priority to current hotel
                if ($isCurrentA && !$isCurrentB) return -1;
                if (!$isCurrentA && $isCurrentB) return 1;

                // Secondary sort by start date
                return $startA <=> $startB;
            });

            return $this->success($resultValue);
        }

        return $this->notFound('Data hotel tidak ditemukan');
    }

    /**
     * GET /api/paket/testimoni
     * Get testimoni by travel
     */
    public function testimoni(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $page = (int) ($request->query('page') ?? 1);

        if (!$IDTravel) {
            return $this->validationError('IDTravel harus diisi');
        }

        $result = JamaahPaket::listTestimoni(
            ['jamaahpaket.IDTravel' => $IDTravel],
            20,
            ($page - 1) * 20
        );

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data testimoni tidak ditemukan');
    }

    /**
     * GET /api/paket/filter-options
     * Get all filter options for paket (dropdown & checkbox data)
     */
    public function filterOptions(): JsonResponse
    {
        $result = Paket::getFilterOptions();

        return $this->success($result);
    }

    /**
     * GET /api/paket/filter
     * Get filtered paket data
     */
    public function filter(Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 0);
        $limit = (int) ($request->query('limit') ?? 10);

        // Get filter parameters
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $namaPaket = $this->sanitize($request->query('nama_paket'));
        $cari = $this->sanitize($request->query('cari'));

        // Kategori - bisa multiple (comma-separated)
        $kategoriParam = $this->sanitize($request->query('kategori'));
        $kategori = $kategoriParam ? explode(',', $kategoriParam) : null;

        // Bulan keberangkatan (format: YYYY-MM)
        $bulanKeberangkatan = $this->sanitize($request->query('bulan_keberangkatan'));

        // Harga range (lte30, 30to40, gte40)
        $hargaRange = $this->sanitize($request->query('harga_range'));

        // Bandara
        $bandaraKeberangkatan = $this->sanitize($request->query('bandara_keberangkatan'));
        $bandaraTujuan = $this->sanitize($request->query('bandara_tujuan'));

        // Durasi perjalanan
        $durasiMin = $request->query('durasi_min') !== null ? (int) $request->query('durasi_min') : null;
        $durasiMax = $request->query('durasi_max') !== null ? (int) $request->query('durasi_max') : null;

        $result = Paket::findKatalogWithFilter(
            $IDTravel,
            $namaPaket,
            $kategori,
            $bulanKeberangkatan,
            $hargaRange,
            $bandaraKeberangkatan,
            $bandaraTujuan,
            $durasiMin,
            $durasiMax,
            $cari,
            $limit,
            $page
        );

        return response()->json([
            'status' => true,
            'data' => $result,
            'page' => $page,
            'count' => count($result),
        ]);
    }
}
