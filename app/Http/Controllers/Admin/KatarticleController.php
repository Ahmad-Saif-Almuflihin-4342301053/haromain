<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Models\MstKategoriKonten;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;

class KatarticleController extends Controller
{
    use UploadFileTrait, DatatablesResponse;
    protected string $jenisKonten = 'ARTIKEL';
    protected $katKonten;
    protected $serverLog;

    public function __construct(
        ServerLog $serverLog
    ) {
        $this->katKonten = new MstKategoriKonten();
        $this->serverLog = $serverLog;
    }

    public function index()
    {
        return view('admin-page.katartikel.index', [
            'menu' => 'Kategori Artikel',
            'jenisKonten' => 'ARTIKEL'
        ]);
    }

    public function tambah()
    {
        return view('admin-page.katartikel.tambah', [
            'menu' => 'Kategori Artikel'
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        $data = MstKategoriKonten::where('IDKatKonten', $id)->first();

        return view('admin-page.katartikel.tambah', [
            'menu' => 'Kategori Artikel',
            'data' => $data
        ]);
    }

    public function simpan(Request $request)
    {
        $id = $request->input('id');

        $data = [
            'NamaKategori' => $request->NamaKategori,
            'JenisKonten'  => $this->jenisKonten,
            'IsAktif'      => 1
        ];

        if ($request->hasFile('IconKategori')) {
            $upload = $this->doUpload(
                $request,
                'IconKategori',
                'assets/konten',
                [
                    'png',
                    'jpg',
                    'jpeg'
                ]
            );

            if ($upload === false) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal mengupload icon! Pastikan format file sesuai dan tidak lebih dari 2MB'
                ]);
            }

            $data['IconKategori'] = $upload;

            if ($id && $request->filled('old_IconKategori')) {
                $this->deleteFile('assets/konten/' . $request->old_IconKategori);
            }
        }

        if ($id) {
            MstKategoriKonten::where('IDKatKonten', $id)->update($data);

            $this->serverLog
                ->insertLog($id, 'Edit', 'Kategori Artikel');

            return response()->json([
                'status'   => true,
                'message'  => 'Berhasil mengedit kategori artikel',
                'redirect' => url('admin/katarticle')
            ]);
        }

        $newID = $this->katKonten->generateID();
        $data['IDKatKonten'] = $newID;

        MstKategoriKonten::create($data);

        $this->serverLog
            ->insertLog($newID, 'Tambah', 'Kategori Artikel');

        return response()->json([
            'status'   => true,
            'message'  => 'Berhasil menambah kategori artikel',
            'redirect' => url('admin/katarticle')
        ]);
    }

    public function setaktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $id      = $request->query('id');

        MstKategoriKonten::where('IDKatKonten', $id)->update([
            'IsAktif' => $isAktif == 1 ? 0 : 1
        ]);

        $this->serverLog
            ->insertLog(
                $id,
                $isAktif == 1 ? 'Nonaktifkan' : 'Aktifkan',
                'Kategori Artikel'
            );

        return response()->json([
            'status'  => true,
            'message' => $isAktif == 1
                ? 'Berhasil menonaktifkan kategori artikel'
                : 'Berhasil mengaktifkan kategori artikel'
        ]);
    }



    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');

            $katartikel = MstKategoriKonten::where('IDKatKonten', $id)->first();

            if (!$katartikel) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Kategori artikel tidak ditemukan'
                ]);
            }

            if (!empty($katartikel->IconKategori)) {
                $this->deleteFile('assets/konten/' . $katartikel->IconKategori);
            }

            MstKategoriKonten::where('IDKatKonten', $id)->delete();

            $this->serverLog
                ->insertLog($id, 'Hapus', 'Kategori Artikel');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menghapus kategori artikel'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus kategori artikel: ' . $th->getMessage()
            ], 409);
        }
    }

    public function get(Request $request)
    {

        $list = $this->katKonten
            ->setJenisKonten($this->jenisKonten)
            ->getDatatables();

        return $this->datatablesResponse(
            $request,
            $list,
            $this->katKonten->countAllDatatables(),
            $this->katKonten->countFiltered()
        );
    }
}
