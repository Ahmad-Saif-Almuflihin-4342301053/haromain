<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Lokasi;
use App\Models\MstCabang;
use App\Models\MstTravel;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CabangController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    public function __construct(
        protected Lokasi $Lokasi,
        protected MstCabang $MstCabang,
        protected ServerLog $ServerLog
    ) {}

    public function index()
    {
        return view('admin-page.cabang.index', [
            'menu' => 'Manajemen Cabang'
        ]);
    }

    public function tambah()
    {
        $travel = collect();
        if (Auth::user()->LevelID == 1) {
            $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        }

        return view('admin-page.cabang.tambah', [
            'menu'   => 'Manajemen Cabang',
            'travel' => $travel
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        $cabang = MstCabang::where('IDCabang', $id)->firstOrFail();

        $travel = collect();
        if (Auth::user()->LevelID == 1) {
            $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        }

        return view('admin-page.cabang.tambah', [
            'menu'   => 'Manajemen Cabang',
            'cabang' => $cabang,
            'travel' => $travel,
            'prov'   => $this->Lokasi->findProvinsi(),
            'kab'    => $this->Lokasi->setIDProv($cabang->IDProv)->findKabupaten(),
            'kec'    => $this->Lokasi->setIDProv($cabang->IDProv)
                ->setIDKab($cabang->IDKab)
                ->findKecamatan(),
            'desa'   => $this->Lokasi->setIDProv($cabang->IDProv)
                ->setIDKab($cabang->IDKab)
                ->setIDKec($cabang->IDKec)
                ->findDesa(),
        ]);
    }

    public function simpan(Request $request)
    {
        $IDCabang = $request->id;
        $IDTravel = Auth::user()->IDTravel ?: $request->IDTravel;

        if (empty($IDTravel)) {
            return response()->json([
                'status'  => false,
                'message' => 'Travel harus dipilih!'
            ]);
        }

        $LevelWilayah = 'Provinsi';
        if ($request->filled('IDKab'))  $LevelWilayah = 'Kabupaten';
        if ($request->filled('IDKec'))  $LevelWilayah = 'Kecamatan';
        if ($request->filled('IDDesa')) $LevelWilayah = 'Desa';

        $cabangData = [
            'NamaCabang'           => $request->NamaCabang,
            'NamaPimpinanCabang'   => $request->NamaPimpinanCabang,
            'IDTravel'             => $IDTravel,
            'NoHPPimp'             => $request->NoHPPimp,
            'TglAwalBerdiri'       => $request->TglAwalBerdiri,
            'IDDesa'               => $request->IDDesa ?: null,
            'IDKec'                => $request->IDKec ?: null,
            'IDKab'                => $request->IDKab ?: null,
            'IDProv'               => $request->IDProv,
            'NamaCP'               => $request->NamaCP,
            'TelpCP'               => $request->TelpCP,
            'LevelWilayah'         => $LevelWilayah,
        ];

        if ($IDCabang) {

            if ($request->hasFile('DokPembukaanCabang')) {
                $upload = $this->doUpload($request, 'DokPembukaanCabang', 'assets/dokumen-cabang', ['pdf']);
                if ($upload == 'error') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal mengupload file! Pastikan format file sesuai dan tidak lebih dari 2MB'
                    ]);
                } else {
                    $cabang['DokPembukaanCabang'] = $upload;
                    $this->deleteFile('assets/dokumen-cabang/' . $request->old_DokPembukaanCabang);
                }
            }
            MstCabang::where('IDCabang', $IDCabang)->update($cabangData);
            $this->ServerLog->insertLog($IDCabang, 'Edit', 'Cabang');
            return response()->json([
                'status'  => true,
                'message' => 'Berhasil mengedit data',
                'redirect' => route('admin.cabang.index')
            ]);
        } else {

            if ($request->hasFile('DokPembukaanCabang')) {
                $upload = $this->doUpload($request, 'DokPembukaanCabang', 'assets/dokumen-cabang', ['pdf']);
                if ($upload == 'error') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal mengupload file! Pastikan format file sesuai dan tidak lebih dari 2MB'
                    ]);
                } else {
                    $cabang['DokPembukaanCabang'] = $upload;
                }
            }

            $cabangData['IDCabang'] = $this->MstCabang->generateID();
            $cabangData['IsAktif']  = 1;

            MstCabang::create($cabangData);

            $this->ServerLog->insertLog($cabangData['IDCabang'], 'Tambah', 'Cabang');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menambah data',
                'redirect' => route('admin.cabang.index')
            ]);
        }
    }

    public function setAktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $id      = $request->query('id');

        if ($isAktif == 1) {
            // NONAKTIFKAN
            MstCabang::where('IDCabang', $id)->update([
                'IsAktif'         => 0,
                'TglAkhirCabang'  => Carbon::now()->format('Y-m-d')
            ]);

            $this->ServerLog->insertLog($id, 'Nonaktifkan', 'Cabang');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menonaktifkan data'
            ]);
        }

        // AKTIFKAN
        MstCabang::where('IDCabang', $id)->update([
            'IsAktif'        => 1,
            'TglAkhirCabang' => null
        ]);

        $this->ServerLog->insertLog($id, 'Aktifkan', 'Cabang');

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil mengaktifkan data'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id  = $request->query('id');
            $dok = $request->query('dok');

            MstCabang::where('IDCabang', $id)->delete();

            // hapus file dokumen
            if ($dok) {
                $this->deleteFile('assets/dokumen-cabang/' . $dok);
            }

            $this->ServerLog->insertLog($id, 'Hapus', 'Cabang');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }

    public function get(Request $request)
    {
        $list = $this->MstCabang->getDatatables();

         return $this->datatablesResponse(
            $request,
            $list,
            $this->MstCabang->countAll(),
            $this->MstCabang->countFiltered()
        );
    }
}
