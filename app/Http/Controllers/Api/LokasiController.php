<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;

class LokasiController extends Controller
{
    public function __construct(protected Lokasi $Lokasi) {}


    public function provinsi($IDProv = null)
    {
        $result = $this->Lokasi
            ->setIDProv($IDProv)
            ->findProvinsi();

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data provinsi tidak ditemukan'
        ], 404);
    }

    public function kabupaten($IDProv = null, $IDKab = null)
    {
        $result = $this->Lokasi
            ->setIDProv($IDProv)
            ->setIDKab($IDKab)
            ->findKabupaten();

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data kabupaten tidak ditemukan'
        ], 404);
    }

    public function kecamatan($IDProv = null, $IDKab = null, $IDKec = null)
    {
        $result = $this->Lokasi
            ->setIDProv($IDProv)
            ->setIDKab($IDKab)
            ->setIDKec($IDKec)
            ->findKecamatan();

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data kecamatan tidak ditemukan'
        ], 404);
    }

    public function desa($IDProv = null, $IDKab = null, $IDKec = null, $IDDesa = null)
    {
        $result = $this->Lokasi
            ->setIDProv($IDProv)
            ->setIDKab($IDKab)
            ->setIDKec($IDKec)
            ->setIDDesa($IDDesa)
            ->findDesa();

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data desa tidak ditemukan'
        ], 404);
    }
}
