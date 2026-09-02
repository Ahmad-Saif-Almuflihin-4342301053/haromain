<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lokasi;

class LokasiController extends Controller
{
    protected $lokasi;

    public function __construct(Lokasi $lokasi)
    {
        $this->lokasi = $lokasi;
    }

    public function getProv($IDProv = null)
    {
        $result = $this->lokasi->setIDProv($IDProv)->findProvinsi();
        if (count($result) > 0) {
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => $result
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data provinsi tidak ditemukan',
            'data' => []
        ]);
    }

    public function getKab($IDProv = null, $IDKab = null)
    {
        $result = $this->lokasi->setIDProv($IDProv)->setIDKab($IDKab)->findKabupaten();
        if (count($result) > 0) {
            return response()->json(['status' => true, 'message' => '', 'data' => $result]);
        }

        return response()->json(['status' => false, 'message' => 'Data kabupaten tidak ditemukan', 'data' => []]);
    }

    public function getKec($IDProv = null, $IDKab = null, $IDKec = null)
    {
        $result = $this->lokasi->setIDProv($IDProv)->setIDKab($IDKab)->setIDKec($IDKec)->findKecamatan();
        if (count($result) > 0) {
            return response()->json(['status' => true, 'message' => '', 'data' => $result]);
        }

        return response()->json(['status' => false, 'message' => 'Data kecamatan tidak ditemukan', 'data' => []]);
    }

    public function getDesa($IDProv = null, $IDKab = null, $IDKec = null, $IDDesa = null)
    {
        $result = $this->lokasi->setIDProv($IDProv)
            ->setIDKab($IDKab)
            ->setIDKec($IDKec)
            ->setIDDesa($IDDesa)
            ->findDesa();

        if (count($result) > 0) {
            return response()->json(['status' => true, 'message' => '', 'data' => $result]);
        }

        return response()->json(['status' => false, 'message' => 'Data desa tidak ditemukan', 'data' => []]);
    }
}
