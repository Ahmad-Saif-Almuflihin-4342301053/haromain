<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgenTravel;


class AgenController extends Controller
{
    public function agentravel($IDPerson)
    {
        $result = (new AgenTravel)->findByAgen($IDPerson);

        if (count($result) <= 0){
            return response()->json([
                'status' => false,
                'message' => 'data lokasi tugas agen tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $result
        ], 200);
    }
}
