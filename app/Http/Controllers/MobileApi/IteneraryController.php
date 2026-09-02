<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\Itenerary;
use App\Models\MobileModels\Paket;
use App\Models\MobileModels\JamaahPaket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTime;

class IteneraryController extends BaseApiController
{
    /**
     * GET /api/itenerary
     * Get all itenerary
     */
    public function index(Request $request): JsonResponse
    {
        $cari = $this->sanitize($request->query('cari'));
        $page = (int) ($request->query('page') ?? 0);
        $limit = (int) ($request->query('limit') ?? 10);

        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $HariKe = $request->has('HariKe') ? (int) $request->query('HariKe') : null;

        if (!$IDTravel || !$IDPaket) {
            return $this->validationError('IDTravel dan IDPaket harus diisi');
        }

        $itenerary = new Itenerary();
        $result = $itenerary
            ->setIDTravel($IDTravel)
            ->setIDPaket($IDPaket)
            ->setHariKe($HariKe)
            ->setCari($cari)
            ->setPage($page)
            ->setLimit($limit)
            ->findAllItenerary();

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data itenerary tidak ditemukan');
    }

    /**
     * GET /api/itenerary/detail
     * Get itenerary detail
     */
    public function detail(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $NoUrut = (int) $request->query('NoUrut');

        if (!$IDTravel || !$IDPaket || !$NoUrut) {
            return $this->validationError('Parameter tidak lengkap');
        }

        $itenerary = new Itenerary();
        $result = $itenerary
            ->setIDTravel($IDTravel)
            ->setIDPaket($IDPaket)
            ->setNoUrut($NoUrut)
            ->setLimit(null)
            ->findAllItenerary();

        if (!empty($result)) {
            return $this->success($result[0]);
        }

        return $this->notFound('Data itenerary tidak ditemukan');
    }

    /**
     * GET /api/itenerary/current
     * Get current itenerary
     */
    public function current(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->query('IDTravel'));
        $IDPaket = $this->sanitize($request->query('IDPaket'));
        $IDPerson = $this->sanitize($request->query('IDPerson'));
        $CurrentTime = $request->has('CurrentTime') ? $request->query('CurrentTime') : null;

        if (!$IDTravel || !$IDPaket || !$IDPerson) {
            return $this->validationError('Parameter tidak lengkap');
        }

        $CurrentTimeUtc = null;
        if ($CurrentTime) {
            //$CurrentTimeUtc = Carbon::parse($CurrentTime)->utc()->format('Y-m-d H:i:s');
            $CurrentTimeUtc = Carbon::parse($CurrentTime)->format('Y-m-d H:i:s');
        }

        // Check jamaah exists
        $jamaah = JamaahPaket::findJamaahPaket($IDTravel, $IDPaket, $IDPerson);
        if (!$jamaah) {
            return $this->notFound('Gagal, data jamaah tidak ditemukan');
        }

        $itenerary = new Itenerary();
        $currentItenerary = $itenerary
            ->setIDTravel($IDTravel)
            ->setIDPaket($IDPaket)
            ->findCurrent($CurrentTimeUtc);

        if (!$currentItenerary) {
            return $this->notFound('Gagal, data itenerary tidak ditemukan');
        }

        return $this->success($currentItenerary);
    }

    /**
     * POST /api/itenerary/batch
     * Batch insert itenerary
     */
    public function batch(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $IDPaket = $this->sanitize($request->input('IDPaket'));
        $IDPerson = $this->sanitize($request->input('IDPerson'));
        $insertData = $request->input('InsertData', []);

        if (!$IDTravel || !$IDPaket || !$IDPerson) {
            return $this->validationError('Parameter tidak lengkap');
        }

        // Validate paket
        $detailPaket = Paket::getDetail($IDTravel, $IDPaket);
        if (!$detailPaket) {
            return $this->validationError('Gagal, data paket travel tidak valid');
        }

        // Check if paket has expired
        $TglPulang = new DateTime($detailPaket['TglPulang']);
        $CurrentTime = new DateTime();
        if ($CurrentTime > $TglPulang) {
            return $this->validationError('Gagal, data paket travel telah kadaluarsa');
        }

        // Check if paket is active
        if ($detailPaket['IsAktif'] != 1) {
            return $this->validationError('Gagal, data paket travel tidak aktif');
        }

        // Check if user is leader
        $detailJamaah = JamaahPaket::findJamaahPaket($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah) {
            return $this->validationError('Gagal, user tidak dapat menambahkan data');
        }

        if (strtolower($detailJamaah['StatusJamaah']) !== 'leader') {
            return $this->validationError('Gagal, data itenerary hanya dapat ditambahkan oleh leader');
        }

        $itenerary = new Itenerary();
        $saved = $itenerary
            ->setIDTravel($IDTravel)
            ->setIDPaket($IDPaket)
            ->saveAll($IDPaket, $IDTravel, $insertData, false); // Use incremental mode

        
        if ($saved) {
            return $this->success(null, 'Data itenerary berhasil disimpan');
        }

        return $this->validationError('Data itenerary gagal disimpan');
    }

    /**
     * POST /api/itenerary
     * Create single itenerary
     */
    public function store(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $IDPaket = $this->sanitize($request->input('IDPaket'));
        $IDPerson = $this->sanitize($request->input('IDPerson'));

        if (!$IDTravel || !$IDPaket || !$IDPerson) {
            return $this->validationError('Parameter tidak lengkap');
        }

        // Validate paket
        $detailPaket = Paket::getDetail($IDTravel, $IDPaket);
        if (!$detailPaket) {
            return $this->validationError('Gagal, data paket travel tidak valid');
        }

        // Check if paket has expired
        $TglPulang = new DateTime($detailPaket['TglPulang']);
        $CurrentTime = new DateTime();
        if ($CurrentTime > $TglPulang) {
            return $this->validationError('Gagal, data paket travel telah kadaluarsa');
        }

        // Check if paket is active
        if ($detailPaket['IsAktif'] != 1) {
            return $this->validationError('Gagal, data paket travel tidak aktif');
        }

        // Check if user is leader
        $detailJamaah = JamaahPaket::findJamaahPaket($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah) {
            return $this->validationError('Gagal, user tidak dapat menambahkan data');
        }

        if (strtolower($detailJamaah['StatusJamaah']) !== 'leader') {
            return $this->validationError('Gagal, data itenerary hanya dapat ditambahkan oleh leader');
        }

        $iteneraryData = [
            'TglMulai' => $this->sanitize($request->input('TglMulai')),
            'TglSelesai' => $this->sanitize($request->input('TglSelesai')),
            'KoorLat' => $this->sanitize($request->input('KoorLat')),
            'KoorLong' => $this->sanitize($request->input('KoorLong')),
            'Lokasi' => $this->sanitize($request->input('Lokasi')),
            'Agenda' => $this->sanitize($request->input('Agenda')),
            'HariKe' => (int) $request->input('HariKe'),
        ];

        $itenerary = new Itenerary();
        $saved = $itenerary
            ->setIDTravel($IDTravel)
            ->setIDPaket($IDPaket)
            ->saveSingle($iteneraryData);

        if ($saved) {
            return $this->success(null, 'Data itenerary berhasil disimpan');
        }

        return $this->validationError('Data itenerary gagal disimpan');
    }

    /**
     * PUT /api/itenerary
     * Update itenerary
     */
    public function update(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $IDPaket = $this->sanitize($request->input('IDPaket'));
        $NoUrut = (int) $request->input('NoUrut');
        $IDPerson = $this->sanitize($request->input('IDPerson'));

        if (!$IDTravel || !$IDPaket || !$NoUrut || !$IDPerson) {
            return $this->validationError('Parameter tidak lengkap');
        }

        // Check if user is leader
        $detailJamaah = JamaahPaket::findJamaahPaket($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah || strtolower($detailJamaah['StatusJamaah']) !== 'leader') {
            return $this->validationError('Gagal, data itenerary hanya dapat diubah oleh leader');
        }

        $updateData = [];
        $fields = ['TglMulai', 'TglSelesai', 'KoorLat', 'KoorLong', 'Lokasi', 'Agenda', 'HariKe'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $field === 'HariKe'
                    ? (int) $request->input($field)
                    : $this->sanitize($request->input($field));
            }
        }

        if (empty($updateData)) {
            return $this->validationError('Tidak ada data yang diupdate');
        }

        $updated = Itenerary::updateItenerary($IDTravel, $IDPaket, $NoUrut, $updateData);

        if ($updated) {
            return $this->success(null, 'Data itenerary berhasil diperbarui');
        }

        return $this->validationError('Data itenerary gagal diperbarui');
    }

    /**
     * DELETE /api/itenerary
     * Delete itenerary
     */
    public function destroy(Request $request): JsonResponse
    {
        $IDTravel = $this->sanitize($request->input('IDTravel'));
        $IDPaket = $this->sanitize($request->input('IDPaket'));
        $NoUrut = (int) $request->input('NoUrut');
        $IDPerson = $this->sanitize($request->input('IDPerson'));

        if (!$IDTravel || !$IDPaket || !$NoUrut || !$IDPerson) {
            return $this->validationError('Parameter tidak lengkap');
        }

        // Check if user is leader
        $detailJamaah = JamaahPaket::findJamaahPaket($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah || strtolower($detailJamaah['StatusJamaah']) !== 'leader') {
            return $this->validationError('Gagal, data itenerary hanya dapat dihapus oleh leader');
        }

        $deleted = Itenerary::deleteItenerary($IDTravel, $IDPaket, $NoUrut);

        if ($deleted) {
            return $this->success(null, 'Data itenerary berhasil dihapus');
        }

        return $this->validationError('Data itenerary gagal dihapus');
    }
}
