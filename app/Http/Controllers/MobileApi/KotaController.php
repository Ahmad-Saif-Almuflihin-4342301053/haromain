<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Kota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KotaController extends BaseApiController
{
    /**
     * GET /api/kota/{IDKota?}
     * Get kota
     */
    public function index(Request $request, ?string $IDKota = null): JsonResponse
    {
        $kota = new Kota();
        $result = $kota
            ->setIDKota($IDKota)
            ->setLimit((int) $request->query('limit', 10))
            ->setPage((int) $request->query('page', 0))
            ->setCari($this->sanitize($request->query('cari')))
            ->findAllKota();

        $total = $kota->countAllKota();

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'total' => $total,
            ]);
        }

        return $this->notFound('Data kota tidak ditemukan');
    }
}
