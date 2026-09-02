<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstKota;
use App\Models\MstTravel;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;

class KotaController extends Controller
{
    use DatatablesResponse;

    public function __construct(
        protected MstKota $MstKota,
        protected ServerLog $ServerLog
    ) {}

    private function checkKotaAccess(?MstKota $kota = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Admin Cabang cannot access Master Kota
        if ((int)$user->LevelID === 2 && !empty($user->IDCabang)) {
            return false;
        }

        // Superadmin has full access
        if ((int)$user->LevelID === 1) {
            return true;
        }

        // Admin Travel Pusat
        if ((int)$user->LevelID === 2 && empty($user->IDCabang)) {
            if ($kota === null) {
                return true;
            }
            return $kota->IDTravel === $user->IDTravel;
        }

        return false;
    }

    public function index()
    {
        if (!$this->checkKotaAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.kota.index', [
            'menu' => 'Data Kota'
        ]);
    }

    public function tambah()
    {
        if (!$this->checkKotaAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.kota.tambah', [
            'menu'   => 'Data Kota',
            'travel' => MstTravel::where('StatusAkun', 'Aktif')->get()
        ]);
    }

    public function edit(Request $request)
    {
        $IDKota = base64_decode($request->id);

        $dataKota = $this->MstKota->where('IDKota', $IDKota)->firstOrFail();

        if (!$this->checkKotaAccess($dataKota)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit data kota ini');
        }

        return view('admin-page.kota.tambah', [
            'menu'   => 'Data Kota',
            'data'   => $dataKota,
            'travel' => MstTravel::where('StatusAkun', 'Aktif')->get()
        ]);
    }

    public function simpan(Request $request)
    {
        if (!$this->checkKotaAccess()) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $IDKota = $request->id;
        $currentUser = Auth::user();

        $kotaData = [
            'NamaKota' => $request->NamaKota,
            'IsAktif'  => 1
        ];

        if ((int)$currentUser->LevelID === 2) {
            $kotaData['IDTravel'] = $currentUser->IDTravel;
        } else {
            $kotaData['IDTravel'] = !empty($request->IDTravel) ? $request->IDTravel : null;
        }

        if ($IDKota) {
            $targetKota = $this->MstKota->where('IDKota', $IDKota)->first();
            if (!$targetKota || !$this->checkKotaAccess($targetKota)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit kota ini'], 403);
            }

            $this->MstKota->where('IDKota', $IDKota)->update($kotaData);

            $this->ServerLog->insertLog($IDKota, 'Edit', 'Data Kota');

            return response()->json([
                'status'   => true,
                'message'  => 'Berhasil mengedit data kota',
                'redirect' => url('admin/kota')
            ]);
        }

        $newID = $this->MstKota->generateID();
        $kotaData['IDKota'] = $newID;

        $result = $this->MstKota->create($kotaData);

        if ($result) {
            $this->ServerLog->insertLog($newID, 'Tambah', 'Data Kota');

            return response()->json([
                'status'   => true,
                'message'  => 'Berhasil menambah data kota',
                'redirect' => url('admin/kota')
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Gagal menambah data kota'
        ]);
    }

    public function setaktif(Request $request)
    {
        $isAktif = $request->isaktif;
        $id      = $request->id;

        $kota = $this->MstKota->where('IDKota', $id)->first();
        if (!$kota || !$this->checkKotaAccess($kota)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        if ($isAktif == 1) {
            $this->MstKota->where('IDKota', $id)->update([
                'IsAktif' => 0
            ]);

            $this->ServerLog->insertLog($id, 'Nonaktifkan', 'Data Kota');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menonaktifkan data kota'
            ]);
        }

        $this->MstKota->where('IDKota', $id)->update([
            'IsAktif' => 1
        ]);

        $this->ServerLog->insertLog($id, 'Aktifkan', 'Data Kota');

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil mengaktifkan data kota'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->id;

            $kota = $this->MstKota->where('IDKota', $id)->first();

            if (!$kota || !$this->checkKotaAccess($kota)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data kota tidak ditemukan atau Anda tidak memiliki akses'
                ], 403);
            }

            $this->MstKota->where('IDKota', $id)->delete();

            $this->ServerLog->insertLog($id, 'Hapus', 'Data Kota');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menghapus data kota'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus data kota: ' . $th->getMessage()
            ]);
        }
    }

    public function get(Request $request)
    {
        if (!$this->checkKotaAccess()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $list = $this->MstKota->getDatatables($request);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->MstKota->countAllDatatables(),
            $this->MstKota->countFiltered($request)
        );
    }
}
