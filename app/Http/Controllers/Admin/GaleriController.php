<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServerLog;
use App\Models\MstKonten;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Models\MstGaleri;
use App\Http\Controllers\Controller;

class GaleriController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected MstGaleri $galeri;
    protected ServerLog $serverlog;

    public function __construct(MstGaleri $galeri, ServerLog $serverlog)
    {
        $this->galeri = $galeri;
        $this->serverlog = $serverlog;
    }

    public function index()
    {
        return view('admin-page.galeri.index', [
            'menu' => 'Manajemen Galeri',
        ]);
    }

    public function tambah()
    {
        return view('admin-page.galeri.tambah', [
            'menu' => 'Manajemen Galeri',
        ]);
    }

    public function edit(Request $request)
    {
        $id = $request->query('id');

        return view('admin-page.galeri.tambah', [
            'menu' => 'Manajemen Galeri',
            'data' => $this->galeri->find($id),
        ]);
    }

    public function simpan(Request $request)
    {
        $id = $request->input('id');
        $jenis = $request->JenisGaleri;
        $prefix = $jenis == 'video' ? 'VID-' : 'IMG-';

        $data = [
            'NamaGaleri' => $request->NamaGaleri,
            'JenisGaleri' => $jenis,
            'KategoriGaleri' => $request->KategoriGaleri,
        ];

        // Handling File Upload or Link
        if ($request->hasFile('KontenGaleri')) {
            $upload = $this->doUpload(
                $request,
                'KontenGaleri',
                'assets/galeri',
                ['jpg', 'jpeg', 'png', 'gif', 'mp4']
            );

            if ($upload === 'error') {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupload file!'
                ]);
            }
            $data['KontenGaleri'] = $upload;

            if ($id && $request->filled('old_KontenGaleri')) {
                if (!filter_var($request->old_KontenGaleri, FILTER_VALIDATE_URL)) {
                    $this->deleteFile('assets/galeri/' . $request->old_KontenGaleri);
                }
            }
        } elseif ($request->filled('LinkVideo')) {
            $data['KontenGaleri'] = $request->LinkVideo;
        }

        if ($id) {
            $this->galeri->where('IDGaleri', $id)->update($data);
            $this->serverlog->insertLog($id, 'Edit', 'Galeri');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit Galeri',
                'redirect' => route('admin.galeri.index')
            ]);
        }

        $data['IDGaleri'] = $this->galeri->generateID($prefix);
        $this->galeri->create($data);
        $this->serverlog->insertLog($data['IDGaleri'], 'Tambah', 'Galeri');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah Galeri',
            'redirect' => route('admin.galeri.index')
        ]);
    }


    public function simpanSettingHalaman(Request $request)
    {

        $dataHeader = [
            'NamaGaleri' => $request->judul_header,
            'JenisGaleri' => $request->deskripsi_header,
        ];
        
        $dataFooter = [
            'NamaGaleri' => $request->judul_footer,
            'JenisGaleri' => $request->deskrpsi_footer
        ];

        // Handling File Upload or Link
        if ($request->hasFile('gambar_header')) {
            $upload = $this->doUpload(
                $request,
                'gambar_header',
                'assets/konten',
                ['jpg', 'jpeg', 'png', 'gif', 'mp4']
            );

            if ($upload === 'error') {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupload file!'
                ]);
            }
            $dataHeader['GambarKontennetnoKrabmad'] = $upload;
            
            if ($request->filled('old_gambar_header')) {
                if (!filter_var($request->old_gambar_header, FILTER_VALIDATE_URL)) {
                    $this->deleteFile('assets/konten/' . $request->old_gambar_header);
                }
            }
        }

        $jenisKontenHeader = 'LANDING_GALERI_HEADER';
        $jenisKontenFooter = 'LANDING_GALERI_FOOTER';
        //HEADER
        MstKonten::where('JenisKonten', $jenisKontenHeader)->updateOrCreate(
            ['JenisKonten' => $jenisKontenHeader],
            $dataHeader
        );
        //FOOTER
        MstKonten::where('JenisKonten', $jenisKontenFooter)->updateOrCreate(
            ['JenisKonten' => $jenisKontenFooter],
            $dataFooter
        );
        
        $this->serverlog->insertLog($jenisKontenHeader, 'Tambah', 'Galeri');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah Galeri',
            'redirect' => route('admin.galeri.index')
        ]);
    }


    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');
            $galeri = $this->galeri->where('IDGaleri', $id)->first();

            if (!$galeri) {
                return response()->json([
                    'status' => false,
                    'message' => 'Galeri tidak ditemukan'
                ]);
            }

            if ($galeri->KontenGaleri && !filter_var($galeri->KontenGaleri, FILTER_VALIDATE_URL)) {
                $this->deleteFile('assets/galeri/' . $galeri->KontenGaleri);
            }

            $galeri->delete();
            $this->serverlog->insertLog($id, 'Hapus', 'Galeri');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus Galeri'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus Galeri: ' . $e->getMessage()
            ], 409);
        }
    }

    public function get(Request $request)
    {
        $list = $this->galeri
            ->setJenisGaleri($request->JenisGaleri)
            ->setKategoriGaleri($request->KategoriGaleri)
            ->get_datatables();

        return $this->datatablesResponse(
            $request,
            $list,
            $this->galeri->count_all_datatables(),
            $this->galeri->count_filtered()
        );
    }
}
