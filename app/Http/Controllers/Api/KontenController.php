<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MstKonten;

class KontenController extends Controller
{
    public function __construct(
        protected MstKonten $MstKonten
    ) {}

    public function artikel(Request $request)
    {
        $page = $request->query('page', 0);
        $limit = $request->query('limit', 0);
        $cari = $request->query('cari');
        $IDKatKonten = $request->query('IDKatKonten');

        $result = $this->MstKonten->findArtikel($page, $limit, $cari, $IDKatKonten);

        if ($result['data']) {
            return response()->json([
                'status' => true,
                'data' => $result['data'],
                'total' => $result['total']
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data artikel tidak ditemukan'
        ], 404);
    }

    public function lokasi(Request $request)
    {
        $page = $request->query('page', 0);
        $limit = $request->query('limit', 10);
        $cari = $request->query('cari');
        $IDKatKonten = $request->query('IDKatKonten');
        $IDKota = $request->query('IDKota');
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $result = $this->MstKonten->findLokasi(
            $page,
            $limit,
            $cari,
            $IDKatKonten,
            $IDKota,
            $lat,
            $lng
        );

        if ($result['data']) {
            return response()->json([
                'status' => true,
                'data' => $result['data'],
                'total' => $result['total']
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data lokasi tidak ditemukan'
        ], 404);
    }

    public function slider(Request $request)
    {
        $IDKatKonten = $request->query('IDKatKonten');

        $result = $this->MstKonten->findSlider($IDKatKonten);

        if ($result['data']) {
            return response()->json([
                'status' => true,
                'data' => $result['data'],
                'total' => $result['total']
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data slider tidak ditemukan'
        ], 404);
    }
}
