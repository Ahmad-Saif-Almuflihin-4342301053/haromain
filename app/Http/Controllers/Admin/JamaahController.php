<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lokasi;
use App\Models\MstPaket;
use App\Models\MstCabang;
use App\Models\MstPerson;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\AgenTravel;
use App\Models\JamaahPaket;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class JamaahController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected $lokasi;
    protected $jamaahPaket;
    protected $agenTravel;
    protected $MstPerson;
    protected $serverLog;

    public function __construct(Lokasi $lokasi, JamaahPaket $jamaahPaket, AgenTravel $agenTravel, MstPerson $MstPerson, ServerLog $serverLog)
    {
        $this->lokasi = $lokasi;
        $this->jamaahPaket = $jamaahPaket;
        $this->agenTravel = $agenTravel;
        $this->MstPerson = $MstPerson;
        $this->serverLog = $serverLog;
    }

    private function checkJamaahAccess(?string $idTravel = null, ?string $idCabang = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Superadmin has full access
        if ((int)$user->LevelID === 1) {
            return true;
        }

        // Admin Travel & Admin Cabang
        if ((int)$user->LevelID === 2) {
            if ($idTravel === null && $idCabang === null) {
                return true;
            }

            // Admin Cabang
            if (!empty($user->IDCabang)) {
                return $idCabang === $user->IDCabang && $idTravel === $user->IDTravel;
            }

            // Admin Travel Pusat
            return $idTravel === $user->IDTravel;
        }

        return false;
    }

    public function index()
    {
        if (!$this->checkJamaahAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.jamaah.index', [
            'menu' => 'Daftarkan Jamaah'
        ]);
    }

    public function tambah()
    {
        if (!$this->checkJamaahAccess()) {
            abort(403, 'Akses Ditolak');
        }

        $user = Auth::user();
        $idTravel = $user->IDTravel;
        $levelId = (int)$user->LevelID;

        $paketQuery = MstPaket::where('IsAktif', 1);
        $cabangQuery = MstCabang::where('IsAktif', 1);

        if ($levelId === 2) {
            $paketQuery->where('IDTravel', $idTravel);
            if (!empty($user->IDCabang)) {
                $cabangQuery->where('IDCabang', $user->IDCabang);
            } else {
                $cabangQuery->where('IDTravel', $idTravel);
            }
        }

        $paket = $paketQuery->get();
        $cabang = $cabangQuery->get();
        $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        $prov = $this->lokasi->findProvinsi();

        return view('admin-page.jamaah.tambah', [
            'menu' => 'Daftarkan Jamaah',
            'paket' => $paket,
            'cabang' => $cabang,
            'travel' => $travel,
            'prov' => $prov,
            'readonly_form' => '',
            'jamaah' => null
        ]);
    }

    public function getAgenByLevel(Request $request)
    {
        $idTravel = Auth::user()->IDTravel ?? $request->IDTravel;
        $agen = $this->agenTravel->getByLevelWilayah(
            $idTravel,
            $request->prov,
            $request->kab,
            $request->kec,
            $request->desa
        );

        return response()->json([
            'status' => true,
            'data' => $agen
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->id);
        $paketId = base64_decode($request->pkt);
        $user = Auth::user();
        $idTravel = $user->IDTravel;
        $levelId = (int)$user->LevelID;

        $jamaah = $this->MstPerson->findJoinJamaahpaket([
            'mstperson.IDPerson' => $id,
            'jamaahpaket.IDPaket' => $paketId
        ]);

        if (!$jamaah) {
            abort(404, 'Data Jamaah Paket tidak ditemukan');
        }

        if (!$this->checkJamaahAccess($jamaah->IDTravel, $jamaah->IDCabang)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit data jamaah ini');
        }

        $agenQuery = [
            'agentravel.IsAktif' => 1
        ];

        $cabangQuery = MstCabang::where('IsAktif', 1);
        $paketQuery = MstPaket::where('IsAktif', 1);

        if ($levelId === 2) {
            $agenQuery['IDTravel'] = $idTravel;
            if (!empty($user->IDCabang)) {
                $cabangQuery->where('IDCabang', $user->IDCabang);
            } else {
                $cabangQuery->where('IDTravel', $idTravel);
            }
            $paketQuery->where('IDTravel', $idTravel);
        } elseif (!empty($jamaah->IDTravel)) {
            $agenQuery['IDTravel'] = $jamaah->IDTravel;
            $cabangQuery->where('IDTravel', $jamaah->IDTravel);
            $paketQuery->where('IDTravel', $jamaah->IDTravel);
        }

        $agentravel = $this->agenTravel->getJoinPerson($agenQuery);
        $cabang = $cabangQuery->get();
        $paket = $paketQuery->get();
        $travel = MstTravel::where('StatusAkun', 'Aktif')->get();

        $prov = $this->lokasi->findProvinsi();
        $kab = $jamaah?->IDProv
            ? $this->lokasi->setIDProv($jamaah->IDProv)->findKabupaten()
            : null;

        $kec = $jamaah?->IDKab
            ? $this->lokasi->setIDProv($jamaah->IDProv)
            ->setIDKab($jamaah->IDKab)
            ->findKecamatan()
            : null;

        $desa = $jamaah?->IDKec
            ? $this->lokasi->setIDProv($jamaah->IDProv)
            ->setIDKab($jamaah->IDKab)
            ->setIDKec($jamaah->IDKec)
            ->findDesa()
            : null;

        return view('admin-page.jamaah.tambah', [
            'menu' => 'Daftarkan Jamaah',
            'jamaah' => $jamaah,
            'paket' => $paket,
            'cabang' => $cabang,
            'travel' => $travel,
            'agentravel' => $agentravel,
            'prov' => $prov,
            'kab' => $kab,
            'kec' => $kec,
            'desa' => $desa,
            'readonly_form' => 'readonly'
        ]);
    }

    public function simpan(Request $request)
    {
        if (!$this->checkJamaahAccess()) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $IDPerson = $request->id;
        $old_IDPaket = $request->old_IDPaket;
        $currentUser = Auth::user();
        $levelId = (int)$currentUser->LevelID;

        $jamaah = [
            'NamaJamaah' => $request->NamaJamaah,
            'NIK' => $request->NIK,
            'TempatLahir' => $request->TempatLahir,
            'TanggalLahir' => $request->TanggalLahir,
            'Alamat' => $request->Alamat,
            'NoHP' => $request->NoHP,
            'Keterangan' => '',
            'NoPasspor' => $request->NoPasspor,
            'UserName' => $request->NoHP,
            'JenisKelamin' => $request->JenisKelamin,
            'IDDesa' => $request->IDDesa ?: null,
            'IDKec' => $request->IDKec ?: null,
            'IDKab' => $request->IDKab ?: null,
            'IDProv' => $request->IDProv,
            'Email' => $request->Email,
        ];

        // Scoping IDTravel & IDCabang for mstperson
        if ($levelId === 2) {
            $jamaah['IDTravel'] = $currentUser->IDTravel;
            $jamaah['IDCabang'] = !empty($currentUser->IDCabang) ? $currentUser->IDCabang : null;
        } else {
            // Level 1 Superadmin
            $paket = MstPaket::where('IDPaket', $request->IDPaket)->first();
            $jamaah['IDTravel'] = $request->IDTravel ?: ($paket->IDTravel ?? null);
            $jamaah['IDCabang'] = !empty($request->IDCabang) ? $request->IDCabang : null;
        }

        $currentTravelId = $jamaah['IDTravel'];
        $currentCabangId = !empty($currentUser->IDCabang) ? $currentUser->IDCabang : ($request->IDCabang ?: $jamaah['IDCabang']);

        if ($IDPerson) {
            $target = $this->MstPerson->where('IDPerson', $IDPerson)->first();
            if (!$target || !$this->checkJamaahAccess($target->IDTravel, $target->IDCabang)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit data jamaah ini'], 403);
            }

            if ($request->hasFile('FotoProfil')) {
                $upload = $this->doUpload(
                    $request,
                    'FotoProfil',
                    'assets/foto-profil',
                    ['jpg', 'jpeg', 'png']
                );

                if ($upload === false) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal mengupload file'
                    ]);
                }

                $jamaah['FotoProfil'] = $upload;
                if ($request->old_FotoProfil) {
                    $this->deleteFile('assets/foto-profil/' . $request->old_FotoProfil);
                }
            }

            $jamaah['LastModified'] = now();

            if ($request->Password) {
                $jamaah['Password'] = md5($request->Password);
            }

            $jamaah_paket = [
                'IDPaket' => $request->IDPaket,
                'IDPerson' => $IDPerson,
                'IDTravel' => $currentTravelId,
                'IDCabang' => $currentCabangId,
                'IDPersonAgen' => $request->IDPersonAgen,
                'StatusJamaah' => 'Jamaah',
                'JumlahTrip' => $request->JumlahTrip,
                'InfoTagihan' => $this->getInfoTagihan($request->IDPaket, $request->JumlahTrip),
                'InfoSisaTagihan' => $this->getInfoTagihan($request->IDPaket, $request->JumlahTrip)
            ];

            $this->MstPerson->where('IDPerson', $IDPerson)->update($jamaah);
            $this->serverLog->insertLog($IDPerson, 'Edit', 'Jamaah');
            
            $updateWhere = [
                'IDPerson' => $IDPerson,
                'IDPaket' => $old_IDPaket
            ];
            
            if ($levelId === 2) {
                $updateWhere['IDTravel'] = $currentTravelId;
            }

            JamaahPaket::where($updateWhere)->update($jamaah_paket);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit data',
                'redirect' => route('admin.jamaah.index')
            ]);
        }

        if ($request->Email) {
            if ($this->MstPerson->where('Email', $request->Email)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email sudah digunakan'
                ]);
            }
        }

        if ($this->MstPerson->where('NoHP', $request->NoHP)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'No HP sudah digunakan'
            ]);
        }

        if ($request->hasFile('FotoProfil')) {
            $upload = $this->doUpload(
                $request,
                'FotoProfil',
                'assets/foto-profil',
                ['jpg', 'jpeg', 'png']
            );

            if ($upload === false) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupload file'
                ]);
            }

            $jamaah['FotoProfil'] = $upload;
        }

        $jamaah['IDPerson'] = $this->MstPerson->generateID();
        $jamaah['IsAktif'] = 1;
        $jamaah['IsVerified'] = 1;
        $jamaah['Tgl_Register'] = now();
        $jamaah['StatusPerson'] = 'Jamaah';
        $jamaah['KodeReferral'] = $this->MstPerson->generateKodeReferral();
        $jamaah['Password'] = md5($request->Password);

        $jamaah_paket = [
            'IDPaket' => $request->IDPaket,
            'IDPerson' => $jamaah['IDPerson'],
            'IDTravel' => $currentTravelId,
            'IDCabang' => $currentCabangId,
            'IsPaid' => 0,
            'IsAlert' => 0,
            'IDPersonAgen' => $request->IDPersonAgen,
            'StatusJamaah' => 'Jamaah',
            'TanggalBooking' => now(),
            'JumlahTrip' => $request->JumlahTrip,
            'InfoTagihan' => $this->getInfoTagihan($request->IDPaket, $request->JumlahTrip)
        ];

        $this->MstPerson->create($jamaah);
        JamaahPaket::create($jamaah_paket);
        $this->serverLog->insertLog($jamaah['IDPerson'], 'Tambah', 'Jamaah');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah data',
            'redirect' => route('admin.jamaah.index')
        ]);
    }

    private function getInfoTagihan($IDPaket, $JumlahTrip)
    {
        $column = match ($JumlahTrip) {
            '2' => 'BiayaDouble',
            '3' => 'BiayaTriple',
            '4' => 'BiayaQuad',
            default => null
        };

        if (!$column) {
            return 0;
        }

        $paket = MstPaket::where('IDPaket', $IDPaket)->first();

        if (!$paket || empty($paket->$column)) {
            return 0;
        }

        return $paket->$column;
    }

    public function setaktif(Request $request)
    {
        $id = $request->id;
        $isAktif = $request->isaktif;

        $target = $this->MstPerson->where('IDPerson', $id)->first();
        if (!$target || !$this->checkJamaahAccess($target->IDTravel, $target->IDCabang)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        if ($isAktif == 1) {
            $this->MstPerson->where('IDPerson', $id)->update([
                'IsAktif' => 0
            ]);

            $this->serverLog->insertLog($id, 'Nonaktifkan', 'Jamaah');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menonaktifkan data'
            ]);
        }

        $this->MstPerson->where('IDPerson', $id)->update([
            'IsAktif' => 1
        ]);

        $this->serverLog->insertLog($id, 'Aktifkan', 'Jamaah');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengaktifkan data'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->id;
            $paket = $request->paket;

            $target = JamaahPaket::where('IDPerson', $id)->where('IDPaket', $paket)->first();
            if (!$target || !$this->checkJamaahAccess($target->IDTravel, $target->IDCabang)) {
                return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
            }

            JamaahPaket::where('IDPerson', $id)
                ->where('IDPaket', $paket)
                ->delete();

            $this->serverLog->insertLog($id, 'Hapus', 'Jamaah');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function get(Request $request)
    {
        if (!$this->checkJamaahAccess()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $currentUser = Auth::user();
        $idTravel = $currentUser->IDTravel;
        $levelId = (int)$currentUser->LevelID;

        $where = [];

        if ($levelId === 2) {
            $where['jamaahpaket.IDTravel'] = $idTravel;
            if (!empty($currentUser->IDCabang)) {
                $where['jamaahpaket.IDCabang'] = $currentUser->IDCabang;
            }
        } elseif ($request->IDTravel) {
            $where['jamaahpaket.IDTravel'] = $request->IDTravel;
        }

        $list = $this->jamaahPaket->get_datatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->jamaahPaket->count_all(),
            $this->jamaahPaket->count_filtered($where)
        );
    }
}
