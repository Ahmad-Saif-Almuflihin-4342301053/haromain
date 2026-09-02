<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstItenerary;
use App\Models\MstPaket;
use App\Models\JamaahPaket;
use App\Models\ServerLog;
use Carbon\Carbon;

class IteneraryController extends Controller
{
    public function __construct(
        protected MstItenerary $MstItenerary,
        protected MstPaket $MstPaket,
        protected JamaahPaket $JamaahPaket,
        protected ServerLog $ServerLog
    ) {}

    public function index(Request $request)
    {
        $cari = $request->query('cari');
        $page = (int) ($request->query('page', 0));
        $limit = (int) ($request->query('limit', 10));

        $IDTravel = $request->query('IDTravel');
        $IDPaket = $request->query('IDPaket');
        $HariKe = $request->query('HariKe');

        $it = $this->MstItenerary
            ->setIDTravel($IDTravel)
            ->setIDPaket($IDPaket)
            ->setHariKe($HariKe)
            ->setCari($cari)
            ->setPage($page)
            ->setLimit($limit)
            ->findAll();

        if ($it && count($it) > 0) {
            return response()->json(['status' => true, 'data' => $it]);
        }

        return response()->json([
            'status' => false,
            'message' => 'data itenerary tidak ditemukan'
        ], 404);
    }

    public function detail(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $IDPaket = $request->query('IDPaket');
        $NoUrut = $request->query('NoUrut');

        $result = $this->MstItenerary
            ->setIDTravel($IDTravel)
            ->setIDPaket($IDPaket)
            ->setNoUrut($NoUrut)
            ->setLimit(null)
            ->findAll();

        if ($result && count($result) > 0) {
            return response()->json(['status' => true, 'data' => $result[0]]);
        }

        return response()->json([
            'status' => false,
            'message' => 'data itenerary tidak ditemukan'
        ], 404);
    }

    public function current(Request $request)
    {
        $IDTravel = $request->query('IDTravel');
        $IDPaket = $request->query('IDPaket');
        $IDPerson = $request->query('IDPerson');
        $CurrentTime = $request->query('CurrentTime', Carbon::now('Asia/Jakarta')->toDateTimeString());

        $jamaah = $this->JamaahPaket->cekPersonTerdaftar($IDTravel, $IDPaket, $IDPerson);
        if (!$jamaah) {
            return response()->json([
                'status' => false,
                'message' => 'gagal, data jamaah tidak ditemukan'
            ], 404);
        }

        $current = $this->MstItenerary->setIDTravel($IDTravel)->setIDPaket($IDPaket)->findCurrent($CurrentTime);
        if (!$current) {
            return response()->json(['
            status' => false, 'message' => 'gagal, data itenerary tidak ditemukan'], 404);
        }

        return response()->json(['status' => true, 'data' => $current]);
    }

    public function batch(Request $request)
    {
        $IDTravel = $request->input('IDTravel');
        $IDPaket = $request->input('IDPaket');
        $IDPerson = $request->input('IDPerson');
        $insertData = $request->input('InsertData', []);

        $detailPaket = $this->MstPaket->detail($IDTravel, $IDPaket);
        if (!$detailPaket) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel tidak valid'
        ], 406);

        $TglPulang = Carbon::parse($detailPaket->TglPulang);
        if (Carbon::now('Asia/Jakarta')->gt($TglPulang)) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel telah kadaluarsa'
        ], 406);
        if ((int) $detailPaket->IsAktif != 1) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel tidak aktif'
        ], 406);

        $detailJamaah = $this->JamaahPaket->cekPersonTerdaftar($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah) return response()->json(
            [
                'status' => false,
                'message' => 'gagal,
            user tidak dapat menambahkan data'
            ],
            406
        );
        $statusJamaah = is_array($detailJamaah) ? ($detailJamaah['StatusJamaah'] ?? null) : ($detailJamaah->StatusJamaah ?? null);
        if (strtolower($statusJamaah ?? '') != 'leader') return response()->json([
            'status' => false,
            'message' => 'gagal, data itenerary hanya dapat ditambahkan oleh leader'
        ], 406);

        $saved = $this->MstItenerary->saveAll($IDPaket, $IDTravel, $insertData);
        if ($saved) return response()->json([
            'status' => true,
            'message' => 'data itenerary berhasil disimpan'
        ]);

        return response()->json([
            '
        status' => false,
            'message' => 'data itenerary gagal disimpan'
        ], 406);
    }

    public function store(Request $request)
    {
        $IDTravel = $request->input('IDTravel');
        $IDPaket = $request->input('IDPaket');
        $TglMulai = $request->input('TglMulai');
        $TglSelesai = $request->input('TglSelesai');
        $KoorLat = $request->input('KoorLat');
        $KoorLong = $request->input('KoorLong');
        $Lokasi = $request->input('Lokasi');
        $Agenda = $request->input('Agenda');
        $IDPerson = $request->input('IDPerson');
        $HariKe = $request->input('HariKe');

        $detailPaket = $this->MstPaket->detail($IDTravel, $IDPaket);
        if (!$detailPaket) return response()->json(
            [
                'status' => false,
                'message' => 'gagal, data paket travel tidak valid'
            ],
            406
        );

        $TglPulang = Carbon::parse($detailPaket->TglPulang);
        if (Carbon::now('Asia/Jakarta')->gt($TglPulang)) return response()->json(
            ['status' => false, 'message' => 'gagal, data paket travel telah kadaluarsa'],
            406
        );
        if ((int) $detailPaket->IsAktif != 1) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel tidak aktif'
        ], 406);

        $detailJamaah = $this->JamaahPaket->cekPersonTerdaftar($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah) return response()->json([
            'status' => false,
            'message' => 'gagal, user tidak dapat menambahkan data'
        ], 406);
        $statusJamaah = is_array($detailJamaah) ? ($detailJamaah['StatusJamaah'] ?? null) : ($detailJamaah->StatusJamaah ?? null);
        if (strtolower($statusJamaah ?? '') != 'leader') return response()->json([
            'status' => false,
            'message' => 'gagal, data itenerary hanya dapat ditambahkan oleh leader'
        ], 406);

        $this->MstItenerary->setIDTravel($IDTravel)->setIDPaket($IDPaket);
        $saved = $this->MstItenerary->saveItinerary($TglMulai, $TglSelesai, $KoorLat, $KoorLong, $Lokasi, $Agenda, $HariKe);
        if ($saved) return response()->json([
            'status' => true,
            'message' => 'data itenerary berhasil disimpan'
        ]);

        return response()->json(['status' => false, 'message' => 'data itenerary gagal disimpan'], 406);
    }

    public function update(Request $request)
    {
        $IDTravel = $request->input('IDTravel');
        $IDPaket = $request->input('IDPaket');
        $NoUrut = $request->input('NoUrut');

        $TglMulai = $request->input('TglMulai');
        $TglSelesai = $request->input('TglSelesai');
        $KoorLat = $request->input('KoorLat');
        $KoorLong = $request->input('KoorLong');
        $Lokasi = $request->input('Lokasi');
        $Agenda = $request->input('Agenda');
        $IDPerson = $request->input('IDPerson');

        $detailPaket = $this->MstPaket->detail($IDTravel, $IDPaket);
        if (!$detailPaket) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel tidak valid'
        ], 406);

        $TglPulang = Carbon::parse($detailPaket->TglPulang);
        if (Carbon::now('Asia/Jakarta')->gt($TglPulang)) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel telah kadaluarsa'
        ], 406);
        if ((int) $detailPaket->IsAktif != 1) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel tidak aktif'
        ], 406);

        $detailJamaah = $this->JamaahPaket->cekPersonTerdaftar($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah) return response()->json([
            'status' => false,
            'message' => 'gagal, user tidak dapat merubah data'
        ], 406);
        $statusJamaah = is_array($detailJamaah) ? ($detailJamaah['StatusJamaah'] ?? null) : ($detailJamaah->StatusJamaah ?? null);
        if (strtolower($statusJamaah ?? '') != 'leader') return response()->json([
            'status' => false,
            'message' => 'gagal, perubahan data itenerary hanya dapat ditambahkan oleh leader'
        ], 406);

        $this->MstItenerary->setIDTravel($IDTravel)->setIDPaket($IDPaket)->setNoUrut($NoUrut);
        $updated = $this->MstItenerary->updateItinerary($TglMulai, $TglSelesai, $KoorLat, $KoorLong, $Lokasi, $Agenda);

        if ($updated) return response()->json([
            'status' => true,
            'message' => 'perubahan data itenerary berhasil disimpan'
        ]);
        return response()->json(['
        status' => false, 'message' => 'perubahan data itenerary gagal disimpan'], 406);
    }

    public function destroy(Request $request)
    {
        $IDTravel = $request->input('IDTravel');
        $IDPaket = $request->input('IDPaket');
        $NoUrut = $request->input('NoUrut');
        $IDPerson = $request->input('IDPerson');

        $detailPaket = $this->MstPaket->detail($IDTravel, $IDPaket);
        if (!$detailPaket) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel tidak valid'
        ], 406);

        $TglPulang = Carbon::parse($detailPaket->TglPulang);
        if (Carbon::now('Asia/Jakarta')->gt($TglPulang)) return response()->json([
            'status' => false,
            'message' => 'gagal, data paket travel telah kadaluarsa'
        ], 406);
        if ((int) $detailPaket->IsAktif != 1) return response()->json(['status' => false, 'message' => 'gagal, data paket travel tidak aktif'], 406);

        $detailJamaah = $this->JamaahPaket->cekPersonTerdaftar($IDTravel, $IDPaket, $IDPerson);
        if (!$detailJamaah) return response()->json([
            'status' => false,
            'message' => 'gagal, user tidak dapat merubah data'
        ], 406);
        $statusJamaah = is_array($detailJamaah) ? ($detailJamaah['StatusJamaah'] ?? null) : ($detailJamaah->StatusJamaah ?? null);
        if (strtolower($statusJamaah ?? '') != 'leader') return response()->json([
            'status' => false,
            'message' => 'gagal, perubahan data itenerary hanya dapat ditambahkan oleh leader'
        ], 406);

        $this->MstItenerary->setIDTravel($IDTravel)->setIDPaket($IDPaket)->setNoUrut($NoUrut);
        $deleted = $this->MstItenerary->deleteItinerary();
        if ($deleted) return response()->json([
            'status' => true,
            'message' => 'hapus data itenerary berhasil'
        ]);
        return response()->json([
            'status' => false,
            'message' => 'hapus data itenerary gagal'
        ], 406);
    }
}
