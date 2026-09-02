<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\AgenTravel;
use Illuminate\Http\JsonResponse;

class AgenController extends BaseApiController
{
    /**
     * GET /api/agen/agentravel/{IDPerson}
     * Get agen travel by person
     */
    public function agenTravel(string $IDPerson): JsonResponse
    {
        $result = AgenTravel::findByAgen($IDPerson);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data lokasi tugas agen tidak ditemukan');
    }
}
