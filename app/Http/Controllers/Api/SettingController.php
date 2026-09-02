<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SistemSetting;

class SettingController extends Controller
{
    public function biayatrip()
    {
        $result = (new SistemSetting)->getBiayaTrip();

        if (!$result) {
            return response()->json([
                'status' => false,
                'message' => 'data setting tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $result
        ], 200);
    }
}
