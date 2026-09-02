<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Models\MstKategoriKonten;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;

class KatlokasiController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected string $jenisKonten = 'LOKASI';
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
        return view('admin-page.katlokasi.index', [
            'menu' => 'Kategori Lokasi'
        ]);
    }

    public function tambah()
    {
        return view('admin-page.katlokasi.tambah', [
            'menu' => 'Kategori Lokasi'
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        $data = MstKategoriKonten::where('IDKatKonten', $id)->first();

        return view('admin-page.katlokasi.tambah', [
            'menu' => 'Kategori Lokasi',
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
                ->insertLog($id, 'Edit', 'Kategori Lokasi');

            return response()->json([
                'status'   => true,
                'message'  => 'Berhasil mengedit kategori lokasi',
                'redirect' => url('admin/katlokasi')
            ]);
        }

        $newID = $this->katKonten->generateID();
        $data['IDKatKonten'] = $newID;

        MstKategoriKonten::create($data);

        $this->serverLog
            ->insertLog($newID, 'Tambah', 'Kategori Lokasi');

        return response()->json([
            'status'   => true,
            'message'  => 'Berhasil menambah kategori lokasi',
            'redirect' => url('admin/katlokasi')
        ]);
    }

    public function setaktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $id      = $request->query('id');

        MstKategoriKonten::where('IDKatKonten', $id)->update([
            'IsAktif' => $isAktif == 1 ? 0 : 1
        ]);

        $this->serverLog->insertLog(
            $id,
            $isAktif == 1 ? 'Nonaktifkan' : 'Aktifkan',
            'Kategori Lokasi'
        );

        return response()->json([
            'status'  => true,
            'message' => $isAktif == 1
                ? 'Berhasil menonaktifkan kategori lokasi'
                : 'Berhasil mengaktifkan kategori lokasi'
        ]);
    }



    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');

            $katlokasi = MstKategoriKonten::where('IDKatKonten', $id)->first();

            if (!$katlokasi) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Kategori lokasi tidak ditemukan'
                ]);
            }

            if (!empty($katlokasi->IconKategori)) {
                $this->deleteFile('assets/konten/' . $katlokasi->IconKategori);
            }

            MstKategoriKonten::where('IDKatKonten', $id)->delete();

            $this->serverLog
                ->insertLog($id, 'Hapus', 'Kategori Lokasi');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menghapus kategori lokasi'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus kategori lokasi: ' . $th->getMessage()
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
