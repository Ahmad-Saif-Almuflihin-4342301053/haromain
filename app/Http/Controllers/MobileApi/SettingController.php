<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends BaseApiController
{
    /**
     * GET /api/setting/biayatrip
     * Get biaya trip setting
     */
    public function biayaTrip(): JsonResponse
    {
        $result = Setting::getBiayaTrip();

        if ($result) {
            return $this->success($result);
        }

        return $this->notFound('Data setting tidak ditemukan');
    }
}
