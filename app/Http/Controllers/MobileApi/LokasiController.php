<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Lokasi;
use Illuminate\Http\JsonResponse;

class LokasiController extends BaseApiController
{
    /**
     * GET /api/lokasi/provinsi/{IDProv?}
     * Get provinsi
     */
    public function provinsi(?string $IDProv = null): JsonResponse
    {
        $lokasi = new Lokasi();
        $result = $lokasi->setIDProv($IDProv)->findProvinsi();

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data provinsi tidak ditemukan');
    }

    /**
     * GET /api/lokasi/kabupaten/{IDProv}/{IDKab?}
     * Get kabupaten
     */
    public function kabupaten(?string $IDProv = null, ?string $IDKab = null): JsonResponse
    {
        $lokasi = new Lokasi();
        $result = $lokasi->setIDProv($IDProv)->setIDKab($IDKab)->findKabupaten();

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data kabupaten tidak ditemukan');
    }

    /**
     * GET /api/lokasi/kecamatan/{IDProv}/{IDKab}/{IDKec?}
     * Get kecamatan
     */
    public function kecamatan(?string $IDProv = null, ?string $IDKab = null, ?string $IDKec = null): JsonResponse
    {
        $lokasi = new Lokasi();
        $result = $lokasi->setIDProv($IDProv)->setIDKab($IDKab)->setIDKec($IDKec)->findKecamatan();

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data kecamatan tidak ditemukan');
    }

    /**
     * GET /api/lokasi/desa/{IDProv}/{IDKab}/{IDKec}/{IDDesa?}
     * Get desa
     */
    public function desa(?string $IDProv = null, ?string $IDKab = null, ?string $IDKec = null, ?string $IDDesa = null): JsonResponse
    {
        $lokasi = new Lokasi();
        $result = $lokasi->setIDProv($IDProv)->setIDKab($IDKab)->setIDKec($IDKec)->setIDDesa($IDDesa)->findDesa();

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data desa tidak ditemukan');
    }
}
