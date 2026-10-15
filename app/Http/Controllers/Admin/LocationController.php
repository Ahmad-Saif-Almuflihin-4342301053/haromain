<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstKota;
use App\Models\MstKonten;
use App\Models\MstTravel;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Models\MstKategoriKonten;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    private string $JenisKonten = 'LOKASI';

    protected MstKonten $konten;
    protected MstKategoriKonten $katkonten;
    protected MstKota $kota;
    protected $serverLog;

    public function __construct(
        MstKonten $konten,
        MstKategoriKonten $katkonten,
        MstKota $kota,
        ServerLog $serverLog
    ) {
        $this->konten = $konten;
        $this->katkonten = $katkonten;
        $this->kota = $kota;
        $this->serverLog = $serverLog;
    }

    private function checkLocationAccess(?MstKonten $lokasi = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Admin Cabang cannot access Lokasi
        if ((int)$user->LevelID === 2 && !empty($user->IDCabang)) {
            return false;
        }

        // Superadmin has full access
        if ((int)$user->LevelID === 1) {
            return true;
        }

        // Admin Travel Pusat
        if ((int)$user->LevelID === 2 && empty($user->IDCabang)) {
            return true;
        }

        return false;
    }

    private function getKotaForCurrentUser()
    {
        return MstKota::where('IsAktif', 1)->get();
    }

    public function getKotaByTravel($idTravel)
    {
        $kota = MstKota::where('IsAktif', 1)->get();
        return response()->json($kota);
    }

    public function index()
    {
        if (!$this->checkLocationAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.location.index', [
            'menu' => 'Manajemen Lokasi',
            'kategori_konten' => $this->katkonten->setJenisKonten($this->JenisKonten)->get(),
            'kota' => $this->getKotaForCurrentUser()
        ]);
    }

    public function tambah()
    {
        if (!$this->checkLocationAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.location.tambah', [
            'menu' => 'Manajemen Lokasi',
            'kategori_konten' => $this->katkonten->setJenisKonten($this->JenisKonten)->get(),
            'kota' => $this->getKotaForCurrentUser(),
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));
        $dataKonten = $this->konten->where('IDKonten', $id)->firstOrFail();

        if (!$this->checkLocationAccess($dataKonten)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit lokasi ini');
        }

        return view('admin-page.location.tambah', [
            'menu' => 'Manajemen Lokasi',
            'data' => $dataKonten,
            'kategori_konten' => $this->katkonten->setJenisKonten($this->JenisKonten)->get(),
            'kota' => $this->getKotaForCurrentUser(),
        ]);
    }

    public function simpan(Request $request)
    {
        if (!$this->checkLocationAccess()) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $IDKonten = $request->input('id');
        $currentUser = Auth::user();

        $konten = [
            'JudulKonten' => $request->JudulKonten,
            'IsiKonten' => $request->IsiKonten,
            'Author' => $request->Author ?? (Auth::user()->NamaLengkap ?? Auth::user()->name ?? 'Admin'),
            'JenisKonten' => $this->JenisKonten,
            'TanggalKonten' => now()->toDateString(),
            'IsAktif' => 1,
            'IDKatKonten' => $request->IDKatKonten,
            'IDKota' => $request->IDKota,
            'Lat' => $request->Lat,
            'Lng' => $request->Lng
        ];

        if ($IDKonten) {
            $target = $this->konten->where('IDKonten', $IDKonten)->first();
            if (!$target || !$this->checkLocationAccess($target)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit lokasi ini'], 403);
            }

            if ($request->hasFile('GambarKonten')) {
                $upload = $this->doUpload(
                    $request,
                    'GambarKonten',
                    'assets/konten',
                    ['jpg', 'jpeg', 'png', 'gif']
                );

                $konten['GambarKonten'] = $upload;

                if ($request->filled('old_GambarKonten')) {
                    $this->deleteFile('assets/konten/' . $request->old_GambarKonten);
                }
            }

            $this->konten->where('IDKonten', $IDKonten)->update($konten);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit lokasi',
                'redirect' => route('admin.location')
            ]);
        }

        if ($request->hasFile('GambarKonten')) {
            $konten['GambarKonten'] = $this->doUpload(
                $request,
                'GambarKonten',
                'assets/konten',
                ['jpg', 'jpeg', 'png', 'gif']
            );
        }

        $konten['IDKonten'] = $this->konten->generateID();
        $konten['IsRekomendasi'] = 0;
        $result = $this->konten->create($konten);

        return response()->json([
            'status' => (bool) $result,
            'message' => $result ? 'Berhasil menambah lokasi' : 'Gagal menambah lokasi',
            'redirect' => $result ? route('admin.location') : null
        ]);
    }

    public function setrekomendasi(Request $request)
    {
        $isRekomendasi = $request->query('isrekomendasi');
        $id = $request->query('id');

        $lokasi = $this->konten->where('IDKonten', $id)->first();
        if (!$lokasi || !$this->checkLocationAccess($lokasi)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        if ((int) $isRekomendasi === 1) {
            $this->konten->where('IDKonten', $id)->update([
                'IsRekomendasi' => 0
            ]);

            $this->serverLog->insertLog($id, 'Nonaktifkan Rekomendasi', 'Lokasi');

            return response()->json([
                'status' => true,
                'message' => 'Lokasi tidak lagi direkomendasikan'
            ]);
        }

        $this->konten->where('IDKonten', $id)->update([
            'IsRekomendasi' => 1
        ]);

        $this->serverLog->insertLog($id, 'Aktifkan Rekomendasi', 'Lokasi');

        return response()->json([
            'status' => true,
            'message' => 'Lokasi berhasil direkomendasikan'
        ]);
    }

    public function setaktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $id = $request->query('id');

        $lokasi = $this->konten->where('IDKonten', $id)->first();
        if (!$lokasi || !$this->checkLocationAccess($lokasi)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        if ((int) $isAktif === 1) {
            $this->konten->where('IDKonten', $id)->update([
                'IsAktif' => 0
            ]);

            $this->serverLog->insertLog($id, 'Nonaktifkan', 'Lokasi');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menonaktifkan Lokasi'
            ]);
        }

        $this->konten->where('IDKonten', $id)->update([
            'IsAktif' => 1
        ]);

        $this->serverLog->insertLog($id, 'Aktifkan', 'Lokasi');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengaktifkan Lokasi'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');

            $lokasi = $this->konten->where('IDKonten', $id)->first();

            if (!$lokasi || !$this->checkLocationAccess($lokasi)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lokasi tidak ditemukan atau Anda tidak memiliki akses'
                ], 403);
            }

            $this->konten->where('IDKonten', $id)->delete();

            if (!empty($lokasi->GambarKonten)) {
                $this->deleteFile('assets/konten/' . $lokasi->GambarKonten);
            }

            $this->serverLog->insertLog($id, 'Hapus', 'Lokasi');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus Lokasi'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus Lokasi'
            ]);
        }
    }

    public function get(Request $request)
    {
        if (!$this->checkLocationAccess()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        if ($request->filled('IDKatKonten')) {
            $this->konten->setIDKatKonten($request->IDKatKonten);
        }

        if ($request->filled('IDKota')) {
            $this->konten->setIDKota($request->IDKota);
        }

        $list = $this->konten
            ->setJenisKonten($this->JenisKonten)
            ->get_datatables($request);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->konten->count_all_datatables(),
            $this->konten->count_filtered()
        );
    }
}
