<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstKategoriKonten;

class KatKontenController extends Controller
{
    public function index(Request $request, $IDKatKonten = null)
    {
        $model = new MstKategoriKonten();
        $model->setIDKatKonten($IDKatKonten)
              ->setJenisKonten($request->query('jenis_konten'))
              ->setLimit($request->query('limit'))
              ->setPage($request->query('page'))
              ->setCari($request->query('cari'));

        $result = $model->findAll();
        $total = count($result);

        if ($total > 0) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'total' => $total
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data kategori konten tidak ditemukan'
        ], 404);
    }
}
