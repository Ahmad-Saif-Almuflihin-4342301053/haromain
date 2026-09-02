<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Travel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TravelController extends BaseApiController
{
    /**
     * GET /api/travel
     * Get all travel
     */
    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 0);
        $cari = $this->sanitize($request->query('cari'));
        $limit = (int) $request->query('limit', 10);

        $result = Travel::findAllTravel($cari, $limit > 0 ? $limit : null, $page);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data travel tidak ditemukan');
    }

    /**
     * GET /api/travel/agen
     * Get travel by agen
     */
    public function agen(Request $request): JsonResponse
    {
        $IDPerson = $this->sanitize($request->query('IDPerson'));
        $page = (int) $request->query('page', 0);
        $cari = $this->sanitize($request->query('cari'));

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        $result = Travel::findByAgenTravel($IDPerson, $cari, 10, $page);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data travel tidak ditemukan');
    }

    /**
     * GET /api/travel/agenlist
     * Get all travel by agen (no limit)
     */
    public function agenList(Request $request): JsonResponse
    {
        $IDPerson = $this->sanitize($request->query('IDPerson'));

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        $result = Travel::findByAgenTravel($IDPerson);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data travel tidak ditemukan');
    }

    /**
     * GET /api/travel/{IDTravel}
     * Get travel detail
     */
    public function detail(string $IDTravel): JsonResponse
    {
        $result = Travel::getDetail($IDTravel);

        if ($result) {
            return $this->success($result);
        }

        return $this->notFound('Data travel tidak ditemukan');
    }
}
