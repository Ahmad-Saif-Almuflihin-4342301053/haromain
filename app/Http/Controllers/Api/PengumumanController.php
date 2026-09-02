<?php

namespace App\Http\Controllers\Api;

use App\Models\MstPerson;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Firebase\FirebaseNotificationService;

class PengumumanController extends Controller
{
    public function __construct(
        protected Pengumuman $Pengumuman,
        protected MstPerson $MstPerson,
    ) {}

    public function index(Request $request)
    {
        $IDPaket = $request->query('IDPaket');
        $IDTravel = $request->query('IDTravel');
        $IDPerson = $request->query('IDPerson');
        $IDProv = $request->query('IDProv');

        $cari = $request->query('cari');
        $page = (int)$request->query('page', 0);
        $limit = (int)$request->query('limit', 10);

        $result = $this->Pengumuman
            ->setIDPaket($IDPaket)
            ->setIDTravel($IDTravel)
            ->setIDPerson($IDPerson)
            ->setIDProv($IDProv)
            ->setPage($page)
            ->setLimit($limit)
            ->setCari($cari)
            ->findAll();

        if (count($result) > 0) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data pengumuman tidak ditemukan'
        ], 404);
    }

    public function count(Request $request)
    {
        $IDPaket = $request->query('IDPaket');
        $IDTravel = $request->query('IDTravel');
        $IDPerson = $request->query('IDPerson');
        $IDProv = $request->query('IDProv');

        $result = $this->Pengumuman
            ->setIDPaket($IDPaket)
            ->setIDTravel($IDTravel)
            ->setIDPerson($IDPerson)
            ->setIDProv($IDProv)
            ->countAll();

        if ($result !== null) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'data jml pengumuman tidak ditemukan'
        ], 404);
    }


    public function store(Request $request,FirebaseNotificationService $firebase)
    {
        $IDPaket = $request->input('IDPaket');
        $IDTravel = $request->input('IDTravel');
        $Isi = $request->input('Isi');
        $SomePerson = $request->input('SomePerson');
        $IDPerson = $request->input('IDPerson');

        $detailJamaah = $this->MstPerson->findJamaahPaket($IDTravel, $IDPaket, $IDPerson);

        if (!$detailJamaah) {
            return response()->json([
                'status' => false,
                'message' => 'gagal, user tidak dapat menambahkan data'
            ], 406);
        }

        if (strtolower($detailJamaah->StatusJamaah ?? '') != 'leader') {
            return response()->json([
                'status' => false,
                'message' => 'gagal, pengumuman hanya dapat ditambahkan oleh leader'
            ], 406);
        }

        try {

            if ($SomePerson != null) {

                $tokens = $this->MstPerson->findTokenPerson($SomePerson);
                foreach ($tokens as $token) {
                    $firebase->sendNotifPerson('pengumuman', $Isi, $token);
                }
            } else {

                $topic = $IDTravel . '-' . $IDPaket;
                $firebase->sendNotifBroadcast('pengumuman', $Isi, $topic);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
        }

        $inserted = $this->Pengumuman->savePengumuman($IDPaket, $IDTravel, $Isi, $SomePerson);

        if ($inserted) {
            return response()->json([
                'status' => true,
                'message' => 'pengumuman berhasil dikirim'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'pengumuman gagal di kirim',
        ], 406);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $IDPengumuman = $data['IDPengumuman'] ?? null;

        if (!$IDPengumuman) {
            return response()->json([
                'status' => false,
                'message' => 'IDPengumuman harus diisi'
            ], 400);
        }

        $updated = $this->Pengumuman->updatePengumuman($IDPengumuman, $data);

        if ($updated) {
            return response()->json([
                'status' => true,
                'message' => 'pengumuman berhasil diperbarui'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'pengumuman gagal di diperbarui',
        ], 406);
    }

    public function destroy($IDPengumuman)
    {
        if (!$IDPengumuman) {
            return response()->json([
                'status' => false,
                'message' => 'IDPengumuman harus diisi'
            ], 400);
        }

        $deleted = $this->Pengumuman->deletePengumuman($IDPengumuman);

        if ($deleted) {
            return response()->json([
                'status' => true,
                'message' => 'pengumuman berhasil dihapus'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'pengumuman gagal dihapus',
        ], 406);
    }
}
