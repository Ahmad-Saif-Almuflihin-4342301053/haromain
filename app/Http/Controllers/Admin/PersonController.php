<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lokasi;
use App\Models\MstPaket;
use App\Models\MstCabang;
use App\Models\MstPerson;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\Agentravel;
use App\Models\JamaahPaket;
use App\Models\DokumenSyarat;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory as Factory;

class PersonController extends Controller
{
    use UploadFileTrait, DatatablesResponse;
    protected $IDTravel;
    protected $MstPerson;
    protected $serverLog;
    protected $lokasi;

    public function __construct(ServerLog $serverLog, MstPerson $MstPerson, Lokasi $lokasi)
    {
        $this->IDTravel = Auth::user()->IDTravel ?? null;
        $this->MstPerson = $MstPerson;
        $this->serverLog = $serverLog;
        $this->lokasi = $lokasi;
    }

    private function checkPersonAccess(?MstPerson $person = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Superadmin has full access
        if ((int)$user->LevelID === 1) {
            return true;
        }

        // Admin Travel & Admin Cabang
        if ((int)$user->LevelID === 2) {
            if ($person === null) {
                return true;
            }

            $query = DB::table('jamaahpaket')
                ->where('IDPerson', $person->IDPerson)
                ->where('IDTravel', $user->IDTravel);

            if (!empty($user->IDCabang)) {
                $query->where('IDCabang', $user->IDCabang);
            }

            return $query->exists();
        }

        return false;
    }

    public function index()
    {
        if (!$this->checkPersonAccess()) {
            abort(403, 'Akses Ditolak');
        }

        $user = Auth::user();
        if ((int)$user->LevelID === 1) {
            $travel = MstTravel::where('IsShow', 1)->orWhere('StatusAkun', 'Aktif')->get();
        } else {
            $travel = MstTravel::where('IDTravel', $user->IDTravel)->get();
        }

        return view('admin-page.person.index', [
            'menu' => 'Manajemen Jamaah',
            'travel' => $travel,
            'IDTravel' => $user->IDTravel ?? null
        ]);
    }

    public function tambah()
    {
        if (!$this->checkPersonAccess()) {
            abort(403, 'Akses Ditolak');
        }

        $user = Auth::user();
        $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        $cabang = collect();
        $paket = collect();

        if ((int)$user->LevelID === 2) {
            if (!empty($user->IDCabang)) {
                $cabang = MstCabang::where('IDCabang', $user->IDCabang)->where('IsAktif', 1)->get();
            } else {
                $cabang = MstCabang::where('IDTravel', $user->IDTravel)->where('IsAktif', 1)->get();
            }
            $paket = MstPaket::where('IDTravel', $user->IDTravel)->where('IsAktif', 1)->get();
        }

        $prov = array_map(fn($item) => collect($item)->toArray(), $this->lokasi->findProvinsi());

        return view('admin-page.person.tambah', [
            'menu' => 'Manajemen Jamaah',
            'paket' => $paket,
            'cabang' => $cabang,
            'travel' => $travel,
            'prov' => $prov,
            'readonly_form' => ''
        ]);
    }

    public function getAgenByLevel(Request $request)
    {
        $idTravel = Auth::user()->IDTravel ?? $request->IDTravel;
        $agen = Agentravel::getByLevelWilayah(
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

    public function detail(Request $request, $id = null)
    {
        $idPerson = $id ?: base64_decode($request->id);

        $jamaah = MstPerson::select('mstperson.*', 'mstprov.NamaProv', 'mstkab.NamaKab', 'mstkec.NamaKec', 'mstdesa.NamaDesa')
            ->leftJoin('mstprov', 'mstprov.IDProv', '=', 'mstperson.IDProv')
            ->leftJoin('mstkab', 'mstkab.IDKab', '=', 'mstperson.IDKab')
            ->leftJoin('mstkec', 'mstkec.IDKec', '=', 'mstperson.IDKec')
            ->leftJoin('mstdesa', 'mstdesa.IDDesa', '=', 'mstperson.IDDesa')
            ->where('mstperson.IDPerson', $idPerson)
            ->firstOrFail();

        if (!$this->checkPersonAccess($jamaah)) {
            abort(403, 'Akses Ditolak');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'data' => $jamaah
            ]);
        }

        $listPaket = JamaahPaket::select('jamaahpaket.*', 'mstpaket.NamaPaket', 'mstpaket.TglBerangkat', 'mstpaket.TglPulang', 'msttravel.NamaTravel')
            ->join('mstpaket', 'mstpaket.IDPaket', '=', 'jamaahpaket.IDPaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'jamaahpaket.IDTravel')
            ->where('jamaahpaket.IDPerson', $idPerson)
            ->get();

        return view('admin-page.person.detail', [
            'menu' => 'Manajemen Jamaah',
            'jamaah' => $jamaah,
            'listPaket' => $listPaket,
            'readonly_form' => 'disabled',
        ]);
    }

    public function edit(Request $request, $id = null)
    {
        $idPerson = $id ?: base64_decode($request->id);

        $jamaah = MstPerson::select('mstperson.*', 'mstprov.NamaProv', 'mstkab.NamaKab', 'mstkec.NamaKec', 'mstdesa.NamaDesa')
            ->leftJoin('mstprov', 'mstprov.IDProv', '=', 'mstperson.IDProv')
            ->leftJoin('mstkab', 'mstkab.IDKab', '=', 'mstperson.IDKab')
            ->leftJoin('mstkec', 'mstkec.IDKec', '=', 'mstperson.IDKec')
            ->leftJoin('mstdesa', 'mstdesa.IDDesa', '=', 'mstperson.IDDesa')
            ->where('mstperson.IDPerson', $idPerson)
            ->firstOrFail();

        if (!$this->checkPersonAccess($jamaah)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit data jamaah ini');
        }

        $user = Auth::user();
        $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        $cabang = collect();
        if ((int)$user->LevelID === 2) {
            if (!empty($user->IDCabang)) {
                $cabang = MstCabang::where('IDCabang', $user->IDCabang)->where('IsAktif', 1)->get();
            } else {
                $cabang = MstCabang::where('IDTravel', $user->IDTravel)->where('IsAktif', 1)->get();
            }
        } elseif (!empty($jamaah->IDTravel)) {
            $cabang = MstCabang::where('IDTravel', $jamaah->IDTravel)->where('IsAktif', 1)->get();
        }

        $prov = array_map(fn($item) => collect($item)->toArray(), $this->lokasi->findProvinsi());
        $kab = $jamaah?->IDProv
            ? array_map(fn($item) => collect($item)->toArray(), $this->lokasi->setIDProv($jamaah->IDProv)->findKabupaten())
            : null;

        $kec = $jamaah?->IDKab
            ? array_map(fn($item) => collect($item)->toArray(), $this->lokasi->setIDProv($jamaah->IDProv)->setIDKab($jamaah->IDKab)->findKecamatan())
            : null;

        $desa = $jamaah?->IDKec
            ? array_map(fn($item) => collect($item)->toArray(), $this->lokasi->setIDProv($jamaah->IDProv)->setIDKab($jamaah->IDKab)->setIDKec($jamaah->IDKec)->findDesa())
            : null;

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'data' => $jamaah
            ]);
        }

        $listPaket = JamaahPaket::select('jamaahpaket.*', 'mstpaket.NamaPaket', 'mstpaket.TglBerangkat', 'mstpaket.TglPulang', 'msttravel.NamaTravel')
            ->join('mstpaket', 'mstpaket.IDPaket', '=', 'jamaahpaket.IDPaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'jamaahpaket.IDTravel')
            ->where('jamaahpaket.IDPerson', $idPerson)
            ->get();

        return view('admin-page.person.tambah', [
            'menu' => 'Manajemen Jamaah',
            'jamaah' => $jamaah,
            'listPaket' => $listPaket,
            'prov' => $prov,
            'kab' => $kab,
            'kec' => $kec,
            'desa' => $desa,
            'travel' => $travel,
            'cabang' => $cabang,
        ]);
    }

    public function simpan(Request $request)
    {
        if (!$this->checkPersonAccess()) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $IDPerson = $request->id;
        $currentUser = Auth::user();

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

        if ($IDPerson) {
            $target = $this->MstPerson->where('IDPerson', $IDPerson)->first();
            if (!$target || !$this->checkPersonAccess($target)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit data ini'], 403);
            }

            if ($request->hasFile('FotoProfil')) {
                $upload = $this->doUpload(
                    $request,
                    'FotoProfil',
                    'assets/foto-profil',
                    ['jpg', 'jpeg', 'png']
                );

                if (!$upload) {
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

            $this->MstPerson->where('IDPerson', $IDPerson)->update($jamaah);
            $this->serverLog->insertLog($IDPerson, 'Edit', 'Jamaah');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit data',
                'redirect' => url('admin/Person')
            ]);
        }

        if ($request->Email && $this->MstPerson->where('Email', $request->Email)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Email sudah digunakan'
            ]);
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

            if (!$upload) {
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

        $this->MstPerson->create($jamaah);
        $this->serverLog->insertLog($jamaah['IDPerson'], 'Tambah', 'Jamaah');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah data',
            'redirect' => url('admin/Person')
        ]);
    }

    public function setaktif(Request $request)
    {
        $id = $request->id;
        $isAktif = $request->isaktif;

        $target = $this->MstPerson->where('IDPerson', $id)->first();
        if (!$target || !$this->checkPersonAccess($target)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $this->MstPerson->where('IDPerson', $id)->update([
            'IsAktif' => $isAktif == 1 ? 0 : 1
        ]);
        $this->serverLog->insertLog(
            $id,
            $isAktif == 1
                ? 'Nonaktifkan'
                : 'Aktifkan',
            'Jamaah'
        );

        return response()->json([
            'status' => true,
            'message' => $isAktif == 1
                ? 'Berhasil menonaktifkan data'
                : 'Berhasil mengaktifkan data'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->id;
            $paket = $request->paket;
            $travel = $request->travel;

            $target = $this->MstPerson->where('IDPerson', $id)->first();
            if (!$target || !$this->checkPersonAccess($target)) {
                return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
            }

            $dokumen = DokumenSyarat::where('IDPerson', $id)
                ->where('IDPaket', $paket)
                ->where('IDTravel', $travel)
                ->get();

            foreach ($dokumen as $dok) {
                if ($dok->FileDokumen) {
                    $this->deleteFile('assets/dokumen_syarat/' . $dok->FileDokumen);
                }
            }

            DokumenSyarat::where('IDPerson', $id)
                ->where('IDPaket', $paket)
                ->where('IDTravel', $travel)
                ->delete();

            JamaahPaket::where('IDPerson', $id)
                ->where('IDPaket', $paket)
                ->where('IDTravel', $travel)
                ->delete();

            $this->serverLog->insertLog($id, 'Hapus', 'Jamaah paket');
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        $id = $request->id;
        try {
            $target = $this->MstPerson->where('IDPerson', $id)->first();
            if (!$target || !$this->checkPersonAccess($target)) {
                return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
            }

            $this->MstPerson->where('IDPerson', $id)->update([
                'Password' => md5('123456')
            ]);

            $this->serverLog->insertLog($id, 'Reset Password', 'Jamaah');

            return response()->json([
                'status' => true,
                'message' => 'Password berhasil direset'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mereset password'
            ]);
        }
    }

    public function get(Request $request)
    {
        if (!$this->checkPersonAccess()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $where = [];
        $currentUser = Auth::user();

        if ((int)$currentUser->LevelID === 2) {
            if (!empty($currentUser->IDCabang)) {
                $where['jamaahpaket.IDCabang'] = $currentUser->IDCabang;
                $where['jamaahpaket.IDTravel'] = $currentUser->IDTravel;
            } else {
                $where['jamaahpaket.IDTravel'] = $currentUser->IDTravel;
            }
        } else {
            $IDTravel = $request->IDTravel ?? $request->trv;
            if ($IDTravel) {
                $where['jamaahpaket.IDTravel'] = $IDTravel;
            }
        }

        $data = $this->MstPerson->getDatatables($where);

        return $this->datatablesResponse(
            $request,
            $data,
            $this->MstPerson->countAll($where),
            $this->MstPerson->countFiltered($where)
        );
    }

    public function forceLogout(Request $request)
    {
        try {
            $idPerson = $request->id;

            if (!$idPerson) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID Person tidak valid'
                ]);
            }

            $target = $this->MstPerson->where('IDPerson', $idPerson)->first();
            if (!$target || !$this->checkPersonAccess($target)) {
                return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
            }

            $serviceAccountPath = storage_path('app/firebase/haromain-afindo-firebase-adminsdk-qzlal-87b11d086c.json');

            if (!file_exists($serviceAccountPath)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Firebase service account tidak ditemukan'
                ]);
            }

            $factory = (new Factory)
                ->withServiceAccount($serviceAccountPath)
                ->withDatabaseUri('https://haromain-afindo-default-rtdb.asia-southeast1.firebasedatabase.app');

            $database = $factory->createDatabase();

            $users = $database->getReference('users')->getValue();

            $userKey = null;

            if (is_array($users)) {
                foreach ($users as $key => $user) {
                    if (($user['person'] ?? null) === $idPerson) {
                        $userKey = $key;
                        break;
                    }
                }
            }

            if (!$userKey) {
                return response()->json([
                    'status' => false,
                    'message' => 'User tidak ditemukan di Firebase'
                ]);
            }

            $database->getReference('users/' . $userKey)->remove();

            $this->serverLog->insertLog($idPerson, 'Force Logout', 'Person');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil paksa logout user'
            ]);
        } catch (\Throwable $th) {
            Log::error('Force Logout Error: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $th->getMessage()
            ]);
        }
    }

    public function updateNama(Request $request)
    {
        try {
            $id   = $request->id;
            $nama = trim($request->NamaJamaah);

            if (!$id || !$nama) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak valid'
                ]);
            }

            $target = $this->MstPerson->where('IDPerson', $id)->first();
            if (!$target || !$this->checkPersonAccess($target)) {
                return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
            }

            $this->MstPerson->where('IDPerson', $id)->update([
                'NamaJamaah'   => $nama,
                'LastModified' => now(),
            ]);

            $this->serverLog->insertLog($id, 'Edit Nama', 'Jamaah');

            return response()->json([
                'status'  => true,
                'message' => 'Nama jamaah berhasil diperbarui'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui nama jamaah'
            ]);
        }
    }
}
