<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\KatKonten;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KatKontenController extends BaseApiController
{
    /**
     * GET /api/katkonten/{IDKatKonten?}
     * Get kategori konten
     */
    public function index(Request $request, ?string $IDKatKonten = null): JsonResponse
    {
        $katKonten = new KatKonten();
        $result = $katKonten
            ->setIDKatKonten($IDKatKonten)
            ->setJenisKonten($this->sanitize($request->query('jenis_konten')))
            ->setLimit((int) $request->query('limit', 10))
            ->setPage((int) $request->query('page', 0))
            ->setCari($this->sanitize($request->query('cari')))
            ->findAllKatKonten();

        $total = $katKonten->countAllKatKonten();

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'total' => $total,
            ]);
        }

        return $this->notFound('Data kategori konten tidak ditemukan');
    }
}
