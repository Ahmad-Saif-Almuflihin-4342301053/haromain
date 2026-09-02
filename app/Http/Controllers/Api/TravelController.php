<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstTravel;

class TravelController extends Controller
{
    public function index(Request $request)
    {
        $model = new MstTravel();
        $model->setCari($request->query('cari'))
              ->setPage($request->query('page'))
              ->setLimit($request->query('limit'));

        $result = $model->findAllTravel();

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data travel tidak ditemukan'
        ], 404);
    }

    public function agen(Request $request)
    {
        $IDPerson = $request->query('IDPerson');

        $model = new MstTravel();
        $model->setCari($request->query('cari'))
              ->setPage($request->query('page'))
              ->setLimit(10);

        $result = $model->findByAgenTravel($IDPerson);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data travel tidak ditemukan'
        ], 404);
    }

    public function agenList(Request $request)
    {
        $IDPerson = $request->query('IDPerson');

        $model = new MstTravel();
        $model->setLimit(-1);

        $result = $model->findByAgenTravel($IDPerson);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data travel tidak ditemukan'
        ], 404);
    }

    public function detail($IDTravel)
    {
        $model = new MstTravel();
        $result = $model->detail($IDTravel);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data travel tidak ditemukan'
        ], 404);
    }
}
