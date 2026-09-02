<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MstCabang;
use App\Models\AgenTravel;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function travel($IDTravel)
    {
        $result = (new MstCabang)->getByTravel($IDTravel);

        if (count($result) <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'data cabang tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $result
        ], 200);
    }

    public function agentravel(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $IDCabang = $request->query('IDCabang');

        $result = (new AgenTravel)->getAgenTravel($IDTravel, $IDCabang);

        if (count($result) <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'data agen tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $result
        ], 200);
    }
}
