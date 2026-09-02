<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Cabang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CabangController extends BaseApiController
{
    /**
     * GET /api/cabang/travel/{IDTravel}
     * Get cabang by travel
     */
    public function travel(string $IDTravel): JsonResponse
    {
        $result = Cabang::findByTravel($IDTravel);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data cabang tidak ditemukan');
    }

    /**
     * GET /api/cabang/agentravel
     * Get agen travel by cabang
     */
    public function agenTravel(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDCabang = $this->sanitize($request->query('IDCabang'));

        if (!$IDTravel) {
            return $this->validationError('IDTravel harus diisi');
        }

        $result = Cabang::findAgenTravel($IDTravel, $IDCabang);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data agen tidak ditemukan');
    }
}
