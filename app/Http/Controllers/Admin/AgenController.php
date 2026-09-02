<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lokasi;
use App\Models\MstPaket;
use App\Models\MstPerson;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\AgenTravel;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Exceptions\UserException;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgenController extends Controller
{
    use UploadFileTrait, DatatablesResponse;
    protected $IDTravel;

    public function __construct(
        protected AgenTravel $AgenTravel,
        protected MstPaket $MstPaket,
        protected MstPerson $MstPerson,
        protected Lokasi $Lokasi,
        protected ServerLog $ServerLog
    )
    {
        $this->IDTravel = Auth::user()->IDTravel ?? null;
    }

    private function checkAgenAccess(?string $idTravel = null): bool
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            return false;
        }

        $levelId = (int)$currentUser->LevelID;

        // Superadmin full access
        if ($levelId === 1) {
            return true;
        }

        // Admin Travel & Cabang
        if ($levelId === 2) {
            if ($idTravel !== null && $currentUser->IDTravel !== $idTravel) {
                return false;
            }
            return true;
        }

        return false;
    }

    public function index()
    {
        if (!$this->checkAgenAccess()) {
            abort(403, 'Akses tidak diizinkan');
        }

        $travel = [];
        if (Auth::user()->LevelID == 1) {
            $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        }

        return view('admin-page.agen.index', [
            'menu' => 'Manajemen Agen',
            'travel' => $travel,
        ]);
    }

    public function tambah()
    {
        if (!$this->checkAgenAccess()) {
            abort(403, 'Akses tidak diizinkan');
        }

        $currentUser = Auth::user();
        $travel = [];
        $jamaah = [];

        if ($currentUser->LevelID == 1) {
            $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
            $jamaah = $this->MstPerson->where('IsAktif', 1)->where('StatusPerson', 'Jamaah')->get();
        } else {
            $jamaah = $this->MstPerson->where('IsAktif', 1)
                ->where('StatusPerson', 'Jamaah')
                ->where('IDTravel', $currentUser->IDTravel)
                ->get();
        }

        return view('admin-page.agen.tambah', [
            'menu'   => 'Manajemen Agen',
            'jamaah' => $jamaah,
            'travel' => $travel,
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        $agen = AgenTravel::findJoinPerson($id);
        if (!$agen) {
            abort(404, 'Data agen tidak ditemukan');
        }

        if (!$this->checkAgenAccess($agen->IDTravel)) {
            abort(403, 'Anda tidak memiliki akses ke agen travel ini');
        }

        $travel = [];
        if (Auth::user()->LevelID == 1) {
            $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        }

        return view('admin-page.agen.tambah', [
            'menu'   => 'Manajemen Agen',
            'agen'   => $agen,
            'travel' => $travel,
            'prov'   => $this->Lokasi->findProvinsi(),
            'kab'    => $this->Lokasi->setIDProv($agen->IDProv)->findKabupaten(),
            'kec'    => $this->Lokasi->setIDProv($agen->IDProv)
                ->setIDKab($agen->IDKab)
                ->findKecamatan(),
            'desa'   => $this->Lokasi->setIDProv($agen->IDProv)
                ->setIDKab($agen->IDKab)
                ->setIDKec($agen->IDKec)
                ->findDesa(),
        ]);
    }

    public function addFromJamaah(Request $request)
    {
        $id = base64_decode($request->query('id'));

        $agen = $this->MstPerson->where('IDPerson', $id)->firstOrFail();

        if (!$this->checkAgenAccess($agen->IDTravel)) {
            abort(403, 'Anda tidak memiliki akses ke data jamaah ini');
        }

        $travel = [];
        if (Auth::user()->LevelID == 1) {
            $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        }

        return view('admin-page.agen.tambah', [
            'menu'   => 'Manajemen Agen',
            'agen'   => $agen,
            'mutasi' => true,
            'travel' => $travel,
            'prov'   => $this->Lokasi->findProvinsi(),
            'kab'    => $this->Lokasi->setIDProv($agen->IDProv)->findKabupaten(),
            'kec'    => $this->Lokasi->setIDProv($agen->IDProv)
                ->setIDKab($agen->IDKab)
                ->findKecamatan(),
            'desa'   => $this->Lokasi->setIDProv($agen->IDProv)
                ->setIDKab($agen->IDKab)
                ->setIDKec($agen->IDKec)
                ->findDesa(),
        ]);
    }

    public function getJamaahByTravel(Request $request)
    {
        $idTravel = $request->query('id_travel');
        if (empty($idTravel)) {
            return response()->json(['data' => []]);
        }

        if (!$this->checkAgenAccess($idTravel)) {
            return response()->json(['data' => []]);
        }

        $jamaah = $this->MstPerson->where('IsAktif', 1)
            ->where('StatusPerson', 'Jamaah')
            ->where('IDTravel', $idTravel)
            ->select('IDPerson', 'NamaJamaah')
            ->get();

        return response()->json(['data' => $jamaah]);
    }

    public function simpan(Request $request)
    {
        DB::beginTransaction();

        try {
            $IDPerson = $request->id;
            $IsMutasi = $request->mutasi;
            $currentUser = Auth::user();

            if ($currentUser->LevelID == 1) {
                $IDTravel = $request->IDTravel;
                if (empty($IDTravel)) {
                    throw new UserException('Perusahaan Travel wajib dipilih');
                }
            } else {
                $IDTravel = $currentUser->IDTravel;
            }

            if (!$this->checkAgenAccess($IDTravel)) {
                throw new UserException('Anda tidak memiliki akses ke travel ini');
            }

            $aksi = null;

            $LevelWilayah = 'Provinsi';
            if ($request->IDKab) $LevelWilayah = 'Kabupaten';
            if ($request->IDKec) $LevelWilayah = 'Kecamatan';
            if ($request->IDDesa) $LevelWilayah = 'Desa';

            $jamaah = [
                'NamaJamaah'    => $request->NamaJamaah,
                'NIK'           => $request->NIK,
                'TempatLahir'   => $request->TempatLahir,
                'TanggalLahir'  => $request->TanggalLahir,
                'NoHP'          => $request->NoHP,
                'NoPasspor'     => $request->NoPasspor,
                'UserName'      => $request->NoHP,
                'JenisKelamin'  => $request->JenisKelamin,
                'Email'         => $request->Email,
                'IDTravel'      => $IDTravel,
                'IDProv'        => $request->IDProv,
                'IDKab'         => $request->IDKab ?: null,
                'IDKec'         => $request->IDKec ?: null,
                'IDDesa'        => $request->IDDesa ?: null,
                'LastModified'  => now(),
            ];

            $agentravel = [
                'IDTravel'     => $IDTravel,
                'LevelWilayah' => $LevelWilayah,
                'IDProv'       => $request->IDProv,
                'IDKab'        => $request->IDKab ?: null,
                'IDKec'        => $request->IDKec ?: null,
                'IDDesa'       => $request->IDDesa ?: null,
                'NoWAAgen'     => $request->NoHP,
            ];

            if ($IDPerson && $IsMutasi) {
                $existingPerson = $this->MstPerson->where('IDPerson', $IDPerson)->first();
                if (!$existingPerson || !$this->checkAgenAccess($existingPerson->IDTravel)) {
                    throw new UserException('Akses ke data jamaah tidak diizinkan');
                }

                unset(
                    $jamaah['IDProv'],
                    $jamaah['IDKab'],
                    $jamaah['IDKec'],
                    $jamaah['IDDesa']
                );

                $cekAgen = AgenTravel::where('IDPerson', $IDPerson)
                    ->where('IDTravel', $IDTravel)
                    ->first();

                if ($cekAgen) {
                    throw new UserException('Jamaah telah terdaftar sebagai agen travel');
                }

                if ($request->hasFile('FotoProfil')) {
                    $jamaah['FotoProfil'] = $this->doUpload(
                        $request,
                        'FotoProfil',
                        'assets/foto-profil',
                        ['jpg', 'jpeg', 'png']
                    );

                    $this->deleteFile('assets/foto-profil/' . $request->old_FotoProfil);
                }

                if ($request->hasFile('DokMoU')) {
                    $agentravel['DokMoU'] = $this->doUpload(
                        $request,
                        'DokMoU',
                        'assets/dokumen-mou',
                        ['pdf']
                    );
                }

                $jamaah['StatusPerson'] = 'Agen';
                $jamaah['IDTravel'] = $IDTravel;
                if (empty($existingPerson->KodeReferral)) {
                    $jamaah['KodeReferral'] = $this->MstPerson->generateKodeReferral();
                }

                $this->MstPerson->where('IDPerson', $IDPerson)->update($jamaah);

                $agentravel['IDPerson'] = $IDPerson;
                $agentravel['IsAktif'] = 1;
                $agentravel['TglMulaiAgen'] = now()->toDateString();

                AgenTravel::create($agentravel);
                $this->ServerLog->insertLog($IDPerson, 'Tambah', 'Agen Travel');

                $aksi = 'mutasi';
            } elseif ($IDPerson) {
                $existingAgen = AgenTravel::where('IDPerson', $IDPerson)
                    ->where('IDTravel', $IDTravel)
                    ->first();
                if (!$existingAgen) {
                    throw new UserException('Data agen tidak ditemukan');
                }

                if ($request->hasFile('FotoProfil')) {
                    $jamaah['FotoProfil'] = $this->doUpload(
                        $request,
                        'FotoProfil',
                        'assets/foto-profil',
                        ['jpg', 'jpeg', 'png']
                    );

                    $this->deleteFile('assets/foto-profil/' . $request->old_FotoProfil);
                }

                if ($request->hasFile('DokMoU')) {
                    $agentravel['DokMoU'] = $this->doUpload(
                        $request,
                        'DokMoU',
                        'assets/dokumen-mou',
                        ['pdf']
                    );

                    $this->deleteFile('assets/dokumen-mou/' . $request->old_DokMoU);
                }

                if ($request->Password) {
                    $jamaah['Password'] = Hash::make($request->Password);
                }

                $this->MstPerson->where('IDPerson', $IDPerson)->update($jamaah);

                AgenTravel::where('IDPerson', $IDPerson)
                    ->where('IDTravel', $IDTravel)
                    ->update($agentravel);

                $this->ServerLog->insertLog($IDPerson, 'Edit', 'Agen Travel');

                $aksi = 'edit';
            } else {
                $cekEmail = $this->MstPerson->where('Email', $request->Email)->exists();
                if ($cekEmail) {
                    throw new UserException('Email sudah digunakan');
                }

                $cekNoHP = $this->MstPerson->where('NoHP', $request->NoHP)->exists();
                if ($cekNoHP) {
                    throw new UserException('No HP sudah digunakan');
                }

                if ($request->hasFile('FotoProfil')) {
                    $jamaah['FotoProfil'] = $this->doUpload(
                        $request,
                        'FotoProfil',
                        'assets/foto-profil',
                        ['jpg', 'jpeg', 'png']
                    );
                }

                if ($request->hasFile('DokMoU')) {
                    $agentravel['DokMoU'] = $this->doUpload(
                        $request,
                        'DokMoU',
                        'assets/dokumen-mou',
                        ['pdf']
                    );
                }

                $jamaah['IDPerson']     = $this->MstPerson->generateID();
                $jamaah['Password']     = Hash::make($request->Password);
                $jamaah['IsAktif']      = 1;
                $jamaah['IsVerified']   = 1;
                $jamaah['Tgl_Register'] = now();
                $jamaah['StatusPerson'] = 'Agen';
                $jamaah['KodeReferral'] = $this->MstPerson->generateKodeReferral();

                $agentravel['IDPerson']      = $jamaah['IDPerson'];
                $agentravel['IsAktif']       = 1;
                $agentravel['TglMulaiAgen']  = now()->toDateString();

                $this->MstPerson->create($jamaah);
                AgenTravel::create($agentravel);

                $this->ServerLog->insertLog($jamaah['IDPerson'], 'Tambah', 'Agen Travel');

                $aksi = 'create';
            }
            DB::commit();

            return match ($aksi) {
                'mutasi' => response()->json([
                    'status' => true,
                    'message' => 'Berhasil mutasi jamaah menjadi agen',
                    'redirect' => route('admin.agen.index'),
                ]),
                'edit' => response()->json([
                    'status' => true,
                    'message' => 'Berhasil mengedit data',
                    'redirect' => route('admin.agen.index'),
                ]),
                default => response()->json([
                    'status' => true,
                    'message' => 'Berhasil menambah data',
                    'redirect' => route('admin.agen.index'),
                ]),
            };
        } catch (UserException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan data'
            ]);
        }
    }

    public function setAktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $id      = $request->query('id');

        $agen = AgenTravel::findJoinPerson($id);
        if (!$agen || !$this->checkAgenAccess($agen->IDTravel)) {
            return response()->json(['status' => false, 'message' => 'Akses ditolak']);
        }

        $idTravel = $agen->IDTravel;

        if ($isAktif == 1) {
            AgenTravel::where('IDPerson', $id)
                ->where('IDTravel', $idTravel)
                ->update([
                    'IsAktif'      => 0,
                    'TglAkhirAgen' => now()->toDateString()
                ]);

            $this->ServerLog->insertLog($id, 'Nonaktifkan', 'Agen Travel');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menonaktifkan data'
            ]);
        } else {
            AgenTravel::where('IDPerson', $id)
                ->where('IDTravel', $idTravel)
                ->update([
                    'IsAktif'      => 1,
                    'TglAkhirAgen' => null
                ]);

            $this->ServerLog->insertLog($id, 'Aktifkan', 'Agen Travel');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengaktifkan data'
            ]);
        }
    }

    public function hapus(Request $request)
    {
        try {
            $id  = $request->query('id');
            $dok = $request->query('dok');

            $agen = AgenTravel::findJoinPerson($id);
            if (!$agen || !$this->checkAgenAccess($agen->IDTravel)) {
                return response()->json(['status' => false, 'message' => 'Akses ditolak']);
            }

            $idTravel = $agen->IDTravel;

            AgenTravel::where('IDPerson', $id)
                ->where('IDTravel', $idTravel)
                ->delete();

            if ($dok) {
                $this->deleteFile('assets/dokumen-mou/' . $dok);
            }

            $this->ServerLog->insertLog($id, 'Hapus', 'Agen');

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
        if (!$this->checkAgenAccess()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $currentUser = Auth::user();
        $where = [];

        if ((int)$currentUser->LevelID === 2) {
            $where['agentravel.IDTravel'] = $currentUser->IDTravel;
        } elseif ($request->IDTravel) {
            $where['agentravel.IDTravel'] = $request->IDTravel;
        }

        $list = $this->AgenTravel->get_datatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->AgenTravel->count_all($where),
            $this->AgenTravel->count_filtered($where)
        );
    }
}
