<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Konten;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KontenController extends BaseApiController
{
    /**
     * GET /api/konten/artikel
     * Get artikel
     */
    public function artikel(Request $request): JsonResponse
    {
        $konten = new Konten();
        $konten->setJenisKonten(Konten::JENIS_ARTIKEL)
            ->setLimit((int) $request->query('limit', 10))
            ->setPage((int) $request->query('page', 0))
            ->setCari($this->sanitize($request->query('cari')));

        if ($request->query('IDKatKonten')) {
            $konten->setIDKatKonten($this->sanitize($request->query('IDKatKonten')));
        }

        $result = $konten->findAllKonten();
        $total = $konten->countAllKonten();

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'total' => $total,
            ]);
        }

        return $this->notFound('Data artikel tidak ditemukan');
    }

    /**
     * GET /api/konten/lokasi
     * Get lokasi
     */
    public function lokasi(Request $request): JsonResponse
    {
        $konten = new Konten();
        $konten->setJenisKonten(Konten::JENIS_LOKASI)
            ->setLimit((int) $request->query('limit', 10))
            ->setPage((int) $request->query('page', 0))
            ->setCari($this->sanitize($request->query('cari')));

        if ($request->query('IDKatKonten')) {
            $konten->setIDKatKonten($this->sanitize($request->query('IDKatKonten')));
        }

        if ($request->query('IDKota')) {
            $konten->setIDKota($this->sanitize($request->query('IDKota')));
        }

        if ($request->query('lat')) {
            $konten->setLat((float) $request->query('lat'));
        }

        if ($request->query('lng')) {
            $konten->setLng((float) $request->query('lng'));
        }

        $result = $konten->findAllKonten();
        $total = $konten->countAllKonten();

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'total' => $total,
            ]);
        }

        return $this->notFound('Data lokasi tidak ditemukan');
    }

    /**
     * GET /api/konten/slider
     * Get slider
     */
    public function slider(Request $request): JsonResponse
    {
        $konten = new Konten();
        $konten->setJenisKonten(Konten::JENIS_SLIDER);

        if ($request->query('IDKatKonten')) {
            $konten->setIDKatKonten($this->sanitize($request->query('IDKatKonten')));
        }

        $result = $konten->findAllKonten();
        $total = $konten->countAllKonten();

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'total' => $total,
            ]);
        }

        return $this->notFound('Data slider tidak ditemukan');
    }
}
