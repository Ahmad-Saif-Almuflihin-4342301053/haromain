<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKota;

class KotaController extends Controller
{
    public function index(Request $request, $IDKota = null)
    {
        $limit = $request->query('limit', -1); // default -1 = tanpa limit
        $page = $request->query('page', 0);
        $cari = $request->query('cari', null);

        $kotaModel = (new MstKota())
            ->setIDKota($IDKota)
            ->setLimit($limit)
            ->setPage($page)
            ->setCari($cari);

        $result = $kotaModel->findAll();
        $total = $kotaModel->countAll();

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'total' => $total
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data kota tidak ditemukan'
        ], 404);
    }
}
