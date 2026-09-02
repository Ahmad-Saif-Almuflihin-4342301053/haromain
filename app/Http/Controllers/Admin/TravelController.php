<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lokasi;
use App\Models\MstCabang;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\AgenTravel;
use Illuminate\Http\Request;
use App\Models\DokumenTravel;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\File;

class TravelController extends Controller
{
    use DatatablesResponse;

    protected $travel;
    protected $dokumentravel;
    protected $lokasi;
    protected $cabang;
    protected $agentravel;

    public function __construct(
        MstTravel $travel,
        DokumenTravel $dokumentravel,
        Lokasi $lokasi,
        MstCabang $cabang,
        AgenTravel $agentravel
    ) {
        $this->travel = $travel;
        $this->dokumentravel = $dokumentravel;
        $this->lokasi = $lokasi;
        $this->cabang = $cabang;
        $this->agentravel = $agentravel;
    }

    public function index()
    {
        return view('admin-page.travel.index', [
            'menu' => 'Manajemen Travel',
        ]);
    }

    public function tambah()
    {
        return view('admin-page.travel.tambah', [
            'menu' => 'Manajemen Travel',
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));
        $travel = $this->travel->where('IDTravel', $id)->first();

        $prov = $this->lokasi->setIDProv(null)->findProvinsi();
        $kab = $this->lokasi->setIDProv($travel->IDProv)->setIDKab(null)->findKabupaten();
        $kec = $this->lokasi->setIDProv($travel->IDProv)->setIDKab($travel->IDKab)->setIDKec(null)->findKecamatan();
        $desa = $this->lokasi->setIDProv($travel->IDProv)
            ->setIDKab($travel->IDKab)
            ->setIDKec($travel->IDKec)
            ->setIDDesa(null)
            ->findDesa();

        $dokumentravel = $this->dokumentravel->where('IDTravel', $id)->get();

        return view('admin-page.travel.tambah', [
            'menu' => 'Manajemen Travel',
            'travel' => $travel,
            'dokumentravel' => $dokumentravel,
            'prov' => $prov,
            'kab' => $kab,
            'kec' => $kec,
            'desa' => $desa,
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->all();
        $idtravel = $data['id'] ?? null;
        $isUpdate = $idtravel ? true : false;
        $oldLogoKantor = $data['old_LogoKantor'] ?? '';

        $travelData = [
            'NamaTravel' => $data['NamaTravel'],
            'NOPPIU' => $data['NoPPIU'],
            'TglBerlakuPPIU' => $data['TglBerlakuPPIU'],
            'Akreditasi' => $data['Akreditasi'],
            'NamaPemilik' => $data['NamaPemilik'],
            'NoTelpPemilik' => $data['NoTelpPemilik'],
            'IDDesa' => $data['IDDesa'] ?: null,
            'IDKec' => $data['IDKec'] ?: null,
            'IDKab' => $data['IDKab'] ?: null,
            'IDProv' => $data['IDProv'],
            'NamaCP' => $data['NamaCP'],
            'TelpCP' => $data['TelpCP'],
            'Alamat' => $data['Alamat']
        ];

        if ($isUpdate) {
            if ($request->hasFile('LogoKantor')) {
                $file = $request->file('LogoKantor');
                $uploadPath = public_path('assets/logo-kantor');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($uploadPath, $fileName);
                $travelData['LogoKantor'] = $fileName;

                if ($oldLogoKantor && file_exists(public_path("assets/logo-kantor/$oldLogoKantor"))) {
                    unlink(public_path("assets/logo-kantor/$oldLogoKantor"));
                }
            }

            MstTravel::where('IDTravel', $idtravel)->update($travelData);

            if (isset($data['NamaDokumen'])) {
                foreach ($data['NamaDokumen'] as $key => $namaDokumen) {
                    $dokumenData = [
                        'NamaDokumen' => $namaDokumen,
                        'MasaAktifSampai' => $data['MasaAktifSampai'][$key],
                        'TglDokumen' => $data['TglDokumen'][$key]
                    ];

                    if ($request->hasFile("FileDokumen.$key")) {
                        $file = $request->file("FileDokumen.$key");
                        $uploadPath = public_path('assets/dokumen-travel');
                        $fileName = time() . '_' . $file->getClientOriginalExtension();
                        $file->move($uploadPath, $fileName);
                        $dokumenData['FileDokumen'] = $fileName;

                        $oldFile = $data['old_FileDokumen'][$key] ?? null;
                        if ($oldFile && file_exists(public_path("assets/dokumen-travel/$oldFile"))) {
                            unlink(public_path("assets/dokumen-travel/$oldFile"));
                        }
                    }

                    if (isset($data['IDDokumen'][$key])) {
                        DokumenTravel::where('IDDokumen', $data['IDDokumen'][$key])->update($dokumenData);
                    } else {

                        $dokumenData['IDDokumen'] = $this->dokumentravel->generateID();
                        $dokumenData['IDTravel'] = $idtravel;
                        DokumenTravel::create($dokumenData);
                    }
                }
            }

            if (isset($data['delete_Dokumen'])) {
                foreach ($data['delete_Dokumen'] as $iddok) {
                    $dokumen = DokumenTravel::where('IDDokumen', $iddok)->first();
                    if ($dokumen && $dokumen->FileDokumen && file_exists(public_path("assets/dokumen-travel/{$dokumen->FileDokumen}"))) {
                        unlink(public_path("assets/dokumen-travel/{$dokumen->FileDokumen}"));
                    }
                    DokumenTravel::where('IDDokumen', $iddok)->delete();
                }
            }

            (new ServerLog)->insertLog($idtravel, 'Edit', 'Travel');
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit data',
                'redirect' => route('admin.travel.index')
            ]);
        }

        else {
            $travelData['IsShow'] = 1;
            $travelData['StatusAkun'] = 'Aktif';
            $travelData['IsVerified'] = 1;
            $travelData['TglRegister'] = now()->format('Y-m-d');
            $travelData['IDTravel'] = $this->travel->generateID();
            $idtravel = $travelData['IDTravel'];

            if ($request->hasFile('LogoKantor')) {
                $file = $request->file('LogoKantor');
                $fileName = time() . '_' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/logo-kantor'), $fileName);
                $travelData['LogoKantor'] = $fileName;
            }

            if (isset($data['NamaDokumen'])) {
                foreach ($data['NamaDokumen'] as $key => $namaDokumen) {
                    $dokumenData = [
                        'IDDokumen' => $this->dokumentravel->generateID(),
                        'IDTravel' => $idtravel,
                        'NamaDokumen' => $namaDokumen,
                        'MasaAktifSampai' => $data['MasaAktifSampai'][$key],
                        'TglDokumen' => $data['TglDokumen'][$key],
                    ];

                    if ($request->hasFile("FileDokumen.$key")) {
                        $file = $request->file("FileDokumen.$key");
                        $fileName = time() . '_' . $file->getClientOriginalExtension();
                        $file->move(public_path('assets/dokumen-travel'), $fileName);
                        $dokumenData['FileDokumen'] = $fileName;
                    }

                    DokumenTravel::create($dokumenData);
                }
            }

            MstTravel::create($travelData);

            $levelWilayah = 'Provinsi';
            if (!empty($travelData['IDKab'])) $levelWilayah = 'Kabupaten';
            if (!empty($travelData['IDKec'])) $levelWilayah = 'Kecamatan';
            if (!empty($travelData['IDDesa'])) $levelWilayah = 'Desa';

            $cabang = [
                'IDCabang' => $this->cabang->generateID(),
                'IsAktif' => 1,
                'NamaCabang' => 'Kantor Pusat',
                'NamaPimpinanCabang' => $travelData['NamaPemilik'],
                'IDTravel' => $idtravel,
                'NoHPPimp' => $travelData['TelpCP'] ?? null,
                'TglAwalBerdiri' => null,
                'IDDesa' => $travelData['IDDesa'],
                'IDKec' => $travelData['IDKec'],
                'IDKab' => $travelData['IDKab'],
                'IDProv' => $travelData['IDProv'],
                'NamaCP' => $travelData['NamaCP'],
                'TelpCP' => $travelData['TelpCP'],
                'LevelWilayah' => $levelWilayah,
            ];

            MstCabang::create($cabang);
            (new ServerLog)->insertLog($cabang['IDCabang'], 'Tambah', 'Cabang');

            $agentravel = [
                'IDTravel' => $idtravel,
                'LevelWilayah' => 'NASIONAL',
                'IDProv' => null,
                'IDKab' => null,
                'IDKec' => null,
                'IDDesa' => null,
                'TglMulaiAgen' => now()->format('Y-m-d'),
                'TglAkhirAgen' => null,
                'DokMou' => null,
                'IsAktif' => 1,
                'NoWAAgen' => '081234567890',
                'IDPerson' => 'PRS-2023-0000000000'
            ];

            AgenTravel::create($agentravel);

            (new ServerLog)->insertLog('PRS-2023-0000000000', 'Tambah', 'Agen Travel');
            (new ServerLog)->insertLog($idtravel, 'Tambah', 'Travel');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambah data',
                'redirect' => route('admin.travel.index')
            ]);
        }
    }

    public function setaktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $idtravel = $request->query('id');

        $travel = MstTravel::where('IDTravel', $idtravel)->firstOrFail();

        if ($isAktif == 1) {
            // Nonaktifkan
            $travel->update(['IsShow' => 0]);
            (new ServerLog)->insertLog($idtravel, 'Nonaktifkan', 'Travel');
            return response()->json(['status' => true, 'message' => 'Berhasil menonaktifkan travel']);
        } else {
            // Aktifkan
            $travel->update(['IsShow' => 1]);
            (new ServerLog)->insertLog($idtravel, 'Aktifkan', 'Travel');
            return response()->json(['status' => true, 'message' => 'Berhasil mengaktifkan travel']);
        }
    }

    public function hapus(Request $request)
    {
        $id = $request->query('id');
        $logo = $request->query('logo');

        try {
            // Hapus dokumen travel
            $dokumentravel = DokumenTravel::where('IDTravel', $id)->get();
            foreach ($dokumentravel as $dok) {
                if (File::exists(public_path("assets/dokumen-travel/{$dok->FileDokumen}"))) {
                    File::delete(public_path("assets/dokumen-travel/{$dok->FileDokumen}"));
                }
                $dok->delete();
            }

            // Hapus travel
            $travel = MstTravel::where('IDTravel', $id)->firstOrFail();
            $travel->delete();

            // Hapus logo kantor
            if ($logo && File::exists(public_path("assets/logo-kantor/{$logo}"))) {
                File::delete(public_path("assets/logo-kantor/{$logo}"));
            }

            (new ServerLog)->insertLog($id, 'Hapus', 'Travel');

            return response()->json(['status' => true, 'message' => 'Berhasil menghapus data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Gagal menghapus data'],409);
        }
    }

    public function get(Request $request)
    {
        $list = $this->travel->get_datatables($request);

         return $this->datatablesResponse(
            $request,
            $list,
            $this->travel->count_all(),
            $this->travel->count_filtered($request)
        );
    }
}
