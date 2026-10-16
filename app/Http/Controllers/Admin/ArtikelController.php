<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstTravel;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Models\MstKategoriKonten;
use App\Traits\DatatablesResponse;
use App\Models\MstKonten as Konten;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ArtikelController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected string $jenisKonten = 'ARTIKEL';

    protected Konten $konten;
    protected MstKategoriKonten $katkonten;
    protected ServerLog $serverlog;

    public function __construct(Konten $konten, MstKategoriKonten $katkonten, ServerLog $serverlog)
    {
        $this->konten = $konten;
        $this->katkonten = $katkonten;
        $this->serverlog = $serverlog;
    }

    private function checkArtikelAccess(?Konten $artikel = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Admin Cabang cannot access Artikel
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

    public function index()
    {
        if (!$this->checkArtikelAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.artikel.index', [
            'menu' => 'Manajemen Artikel',
            'kategori_konten' => $this->katkonten
                ->where('JenisKonten', $this->jenisKonten)
                ->get()
        ]);
    }

    public function tambah()
    {
        if (!$this->checkArtikelAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.artikel.tambah', [
            'menu' => 'Manajemen Artikel',
            'kategori_konten' => $this->katkonten
                ->where('JenisKonten', $this->jenisKonten)
                ->get()
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        $artikel = $this->konten->where('IDKonten', $id)->firstOrFail();

        if (!$this->checkArtikelAccess($artikel)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit artikel ini');
        }

        return view('admin-page.artikel.tambah', [
            'menu' => 'Manajemen Artikel',
            'data' => $artikel,
            'kategori_konten' => $this->katkonten
                ->where('JenisKonten', $this->jenisKonten)
                ->get()
        ]);
    }

    public function simpan(Request $request)
    {
        if (!$this->checkArtikelAccess()) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $id = $request->input('id');
        $currentUser = Auth::user();

        $data = [
            'JudulKonten'   => $request->JudulKonten,
            'IsiKonten'     => $request->IsiKonten,
            'Author'        => $request->Author ?? (Auth::user()->NamaLengkap ?? Auth::user()->name ?? 'Admin'),
            'JenisKonten'   => $this->jenisKonten,
            'TanggalKonten' => $request->TanggalKonten,
            'IsAktif'       => 1,
            'IDKatKonten'   => $request->IDKatKonten,
            'Lat'           => $request->Lat,
            'Lng'           => $request->Lng,
            'NoUrut'        => $request->NoUrut ?? null,
        ];

        if ($request->hasFile('GambarKonten')) {
            $upload = $this->doUpload(
                $request,
                'GambarKonten',
                'assets/konten',
                ['jpg', 'jpeg', 'png', 'gif']
            );

            if ($upload === 'error') {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupload gambar! Maksimal 2MB'
                ]);
            }

            $data['GambarKonten'] = $upload;

            if ($id && $request->filled('old_GambarKonten')) {
                $this->deleteFile('assets/konten/' . $request->old_GambarKonten);
            }
        }

        if ($request->is_remove == 1) {
            if ($request->filled('old_GambarKonten')) {
                $this->deleteFile('assets/konten/' . $request->old_GambarKonten);
            }

            $data['GambarKonten'] = null;
        }

        if ($id) {
            $target = $this->konten->where('IDKonten', $id)->first();
            if (!$target || !$this->checkArtikelAccess($target)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit artikel ini'], 403);
            }

            $this->konten->where('IDKonten', $id)->update($data);
            $this->serverlog->insertLog($id, 'Edit', 'Artikel');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit artikel',
                'redirect' => route('admin.artikel.index')
            ]);
        }

        $data['IDKonten'] = $this->konten->generateID();
        $data['IsRekomendasi'] = 0;

        $this->konten->create($data);
        $this->serverlog->insertLog($data['IDKonten'], 'Tambah', 'Artikel');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah artikel',
            'redirect' => route('admin.artikel.index')
        ]);
    }

    public function setRekomendasi(Request $request)
    {
        $id = $request->query('id');
        $isRekomendasi = $request->query('isrekomendasi');

        $artikel = $this->konten->where('IDKonten', $id)->first();
        if (!$artikel || !$this->checkArtikelAccess($artikel)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $this->konten
            ->where('IDKonten', $id)
            ->update([
                'IsRekomendasi' => $isRekomendasi == 1 ? 0 : 1
            ]);

        $status = $isRekomendasi == 1 ? 'Nonaktifkan Rekomendasi' : 'Aktifkan Rekomendasi';
        $this->serverlog->insertLog($id, $status, 'Artikel');

        return response()->json([
            'status' => true,
            'message' => $isRekomendasi == 1
                ? 'Artikel tidak lagi direkomendasikan'
                : 'Artikel berhasil direkomendasikan'
        ]);
    }

    public function setAktif(Request $request)
    {
        $id = $request->query('id');
        $isAktif = $request->query('isaktif');

        $artikel = $this->konten->where('IDKonten', $id)->first();
        if (!$artikel || !$this->checkArtikelAccess($artikel)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $this->konten
            ->where('IDKonten', $id)
            ->update([
                'IsAktif' => $isAktif == 1 ? 0 : 1
            ]);

        $status = $isAktif == 1 ? 'Nonaktifkan' : 'Aktifkan';
        $this->serverlog->insertLog($id, $status, 'Artikel');
        return response()->json([
            'status' => true,
            'message' => $isAktif == 1
                ? 'Berhasil menonaktifkan artikel'
                : 'Berhasil mengaktifkan artikel'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');

            $artikel = $this->konten->where('IDKonten', $id)->first();

            if (!$artikel || !$this->checkArtikelAccess($artikel)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Artikel tidak ditemukan atau Anda tidak memiliki akses'
                ], 403);
            }

            if (!empty($artikel->GambarKonten)) {
                $this->deleteFile('assets/konten/' . $artikel->GambarKonten);
            }

            $artikel->delete();
            $this->serverlog->insertLog($id, 'Hapus', 'Artikel');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus artikel'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus artikel: ' . $e->getMessage()
            ], 409);
        }
    }

    public function get(Request $request)
    {
        if (!$this->checkArtikelAccess()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $IDKatKonten = $request->input('IDKatKonten', '');

        if (!empty($IDKatKonten)) {
            $this->konten->setIDKatKonten($IDKatKonten);
        }

        $list = $this->konten
            ->setJenisKonten($this->jenisKonten)
            ->get_datatables();

        return $this->datatablesResponse(
            $request,
            $list,
            $this->konten->count_all_datatables(),
            $this->konten->count_filtered()
        );
    }
}
