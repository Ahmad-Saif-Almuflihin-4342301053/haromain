<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MstPaket;
use App\Models\MstTravel;
use App\Models\HotelPaket;
use App\Models\JamaahPaket;

class PaketController extends Controller
{
    public function __construct(
        protected MstPaket $MstPaket,
        protected MstTravel $MstTravel,
        protected HotelPaket $HotelPaket,
        protected JamaahPaket $JamaahPaket
    ) {}

    public function jenis()
    {
        $result = $this->MstPaket->getDistinctKategori();

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data kategori paket tidak ditemukan'
        ], 404);
    }

    public function agen($IDPerson)
    {
        $result = $this->MstPaket->findByAgenPerson($IDPerson);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data paket travel tidak ditemukan'
        ], 404);
    }

    public function katalogagen(Request $request, $IDPerson)
    {
        $page = (int)$request->query('page', 0);
        $cari = $request->query('cari');
        $dashboard = (bool)$request->query('dashboard', false);

        $IDTravel = $request->query('IDTravel');
        $KotaTujuan = $request->query('KotaTujuan');
        $TglBerangkat = $request->query('TglBerangkat');
        $KategoriPaket = $request->query('KategoriPaket');

        $limit = $dashboard ? 7 : 10;
        $result = $this->MstPaket->findKatalogAgen(
            $IDPerson,
            $page,
            $limit,
            $cari,
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket
        );

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'page' => $page,
                'count' => count($result)
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data paket travel tidak ditemukan'
        ], 404);
    }

    public function monitoringagen(Request $request, $IDPerson)
    {
        $page = $request->query('page', 0);
        $cari = $request->query('cari');

        $IDTravel = $request->query('IDTravel');
        $KotaTujuan = $request->query('KotaTujuan');
        $TglBerangkat = $request->query('TglBerangkat');
        $KategoriPaket = $request->query('KategoriPaket');

        $result = $this->MstPaket->findMonitoringAgen(
            $IDPerson,
            $page,
            10,
            $cari,
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket
        );

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data paket travel tidak ditemukan'
        ], 404);
    }

    public function historyagen(Request $request, $IDPerson)
    {
        $page = $request->query('page', 0);
        $cari = $request->query('cari');

        $IDTravel = $request->query('IDTravel');
        $KotaTujuan = $request->query('KotaTujuan');
        $TglBerangkat = $request->query('TglBerangkat');
        $KategoriPaket = $request->query('KategoriPaket');

        $result = $this->MstPaket->findHistoryAgen(
            $IDPerson,
            $page,
            10,
            $cari,
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket
        );

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data paket travel tidak ditemukan'
        ], 404);
    }

    public function katalog(Request $request)
    {
        $page = $request->query('page', 0);
        $cari = $request->query('cari');
        $dashboard = (bool)$request->query('dashboard', false);

        $IDTravel = $request->query('IDTravel');
        $KotaTujuan = $request->query('KotaTujuan');
        $TglBerangkat = $request->query('TglBerangkat');
        $KategoriPaket = $request->query('KategoriPaket');

        $limit = $dashboard ? 7 : 10;

        $result = $this->MstPaket->findKatalogPublic(
            $page,
            $limit,
            $cari,
            $IDTravel,
            $KotaTujuan,
            $TglBerangkat,
            $KategoriPaket
        );

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data paket travel tidak ditemukan'
        ], 404);
    }

    public function detail(Request $request, $IDTravel, $IDPaket)
    {
        $IDPerson = $request->query('IDPerson');

        $result = $this->MstPaket->getDetailPaket($IDTravel, $IDPaket, $IDPerson);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data paket travel tidak ditemukan'
        ], 404);
    }

    public function hotel(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $IDPaket = $request->query('IDPaket');

        if (!$IDTravel || !$IDPaket) {
            return response()->json([
                'status' => false,
                'message' => 'IDTravel dan IDPaket harus diisi'
            ], 400);
        }

        $result = $this->HotelPaket->findByPaket($IDTravel, $IDPaket);

        if ($result) {
            $resultValue = [];
            foreach ($result as $value) {
                $value = (array)$value;
                $value['TimeStamp'] = time();
                $resultValue[] = $value;
            }

            return response()->json([
                'status' => true,
                'data' => $resultValue
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data hotel tidak ditemukan'
        ], 404);
    }

    public function testimoni(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $page = (int)$request->query('page', 1);

        if (!$IDTravel) {
            return response()->json([
                'status' => false,
                'message' => 'IDTravel harus diisi'
            ], 400);
        }

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $result = $this->JamaahPaket->listTestimoni([
            'jamaahpaket.IDTravel' => $IDTravel
        ], $limit, $offset);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data testimoni tidak ditemukan'
        ], 404);
    }
}
