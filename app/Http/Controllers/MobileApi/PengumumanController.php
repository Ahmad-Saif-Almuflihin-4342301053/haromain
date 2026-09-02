<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Pengumuman;
use App\Models\MobileModels\JamaahPaket;
use App\Models\MobileModels\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengumumanController extends BaseApiController
{
    /**
     * GET /api/pengumuman
     * Get all pengumuman
     */
    public function index(Request $request): JsonResponse
    {
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPerson = $request->has('IDPerson') ? $this->sanitize($request->query('IDPerson')) : null;
        $IDProv = $request->has('IDProv') ? $this->sanitize($request->query('IDProv')) : null;

        $cari = $this->sanitize($request->query('cari'));
        $page = (int) ($request->query('page') ?? 0);
        $limit = (int) ($request->query('limit') ?? 10);

        $pengumuman = new Pengumuman();
        $result = $pengumuman
            ->setIDPaket($IDPaket)
            ->setIDTravel($IDTravel)
            ->setIDPerson($IDPerson)
            ->setIDProv($IDProv)
            ->setPage($page)
            ->setLimit($limit)
            ->setCari($cari)
            ->findAllPengumuman();

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data pengumuman tidak ditemukan');
    }

    /**
     * GET /api/pengumuman/count
     * Get count pengumuman
     */
    public function count(Request $request): JsonResponse
    {
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPerson = $request->has('IDPerson') ? $this->sanitize($request->query('IDPerson')) : null;
        $IDProv = $request->has('IDProv') ? $this->sanitize($request->query('IDProv')) : null;

        $pengumuman = new Pengumuman();
        $result = $pengumuman
            ->setIDPaket($IDPaket)
            ->setIDTravel($IDTravel)
            ->setIDPerson($IDPerson)
            ->setIDProv($IDProv)
            ->countAllPengumuman();

        return $this->success($result);
    }

    /**
     * POST /api/pengumuman
     * Create pengumuman
     */
    public function store(Request $request): JsonResponse
    {
        $IDPaket = $this->sanitize($request->input('IDPaket'));
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $Isi = $this->sanitize($request->input('Isi'));
        $SomePerson = $request->input('SomePerson');
        $IDPerson = $this->sanitize($request->input('IDPerson'));
        $Tanggal = $request->input('Tanggal');

        if (!$IDTravel || !$IDPaket || !$IDPerson || !$Isi) {
            return $this->validationError('Parameter tidak lengkap');
        }

        $TanggalUtc = null;
        if ($Tanggal) {
            $TanggalUtc = Carbon::parse($Tanggal)->utc()->format('Y-m-d H:i:s');
        }

        // Check if user is leader
        $detailJamaah = JamaahPaket::findJamaahPaket($IDTravel, $IDPaket, $IDPerson);

        if (!$detailJamaah) {
            return $this->validationError('Gagal, user tidak dapat menambahkan data');
        }

        if (strtolower($detailJamaah['StatusJamaah']) !== 'leader') {
            return $this->validationError('Gagal, pengumuman hanya dapat ditambahkan oleh leader');
        }

        try {
            // Send notifications
            /* if (!empty($SomePerson) && is_array($SomePerson)) {
                $tokens = Person::findTokenPerson($SomePerson);
                foreach ($tokens as $token) {
                    $this->sendNotifPerson('Pengumuman', $Isi, $token);
                }
            } else { */
                // Broadcast notification
                $topic = $IDTravel . '-' . $IDPaket;
                $this->sendNotifBroadcast('Pengumuman', $Isi, $topic);
            //}
        } finally {
            $pengumuman = new Pengumuman();
            $inserted = $pengumuman
                ->setIDPaket($IDPaket)
                ->setIDTravel($IDTravel)
                ->savePengumuman($Isi, $SomePerson, $TanggalUtc);

            if ($inserted) {
                return $this->success(null, 'Pengumuman berhasil dikirim');
            }

            return $this->validationError('Pengumuman gagal dikirim');
        }
    }

    /**
     * PUT /api/pengumuman
     * Update pengumuman
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->all();
        $IDPengumuman = $data['IDPengumuman'] ?? null;

        if (!$IDPengumuman) {
            return $this->validationError('IDPengumuman harus diisi');
        }

        unset($data['IDPengumuman']);

        $updated = Pengumuman::updatePengumuman($IDPengumuman, $data);

        if ($updated) {
            return $this->success(null, 'Pengumuman berhasil diperbarui');
        }

        return $this->validationError('Pengumuman gagal diperbarui');
    }

    /**
     * DELETE /api/pengumuman/{IDPengumuman}
     * Delete pengumuman
     */
    public function destroy(int $IDPengumuman): JsonResponse
    {
        $deleted = Pengumuman::deletePengumuman($IDPengumuman);

        if ($deleted) {
            return $this->success(null, 'Pengumuman berhasil dihapus');
        }

        return $this->validationError('Pengumuman gagal dihapus');
    }
}
