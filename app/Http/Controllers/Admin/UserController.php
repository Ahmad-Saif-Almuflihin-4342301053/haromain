<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstCabang;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use DatatablesResponse;

    public function __construct(protected UserLogin $UserLogin) {}

    public function index()
    {
        return view('admin-page.user.index', [
            'menu' => 'Manajemen User'
        ]);
    }

    public function getCabangByTravel($idTravel)
    {
        $cabang = MstCabang::where('IDTravel', $idTravel)
            ->where('IsAktif', 1)
            ->select('IDCabang', 'NamaCabang')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $cabang
        ]);
    }

    private function checkUserAccess(UserLogin $targetUser): bool
    {
        $currentUser = Auth::user();
        if (!$currentUser) return false;

        // Superadmin (Level 1) has full access
        if ((int)$currentUser->LevelID === 1) {
            return true;
        }

        // Level 2 (Travel / Cabang)
        if ((int)$currentUser->LevelID === 2) {
            // Cannot manage Superadmin
            if ((int)$targetUser->LevelID === 1) {
                return false;
            }

            // Must belong to the same Travel
            if ($targetUser->IDTravel !== $currentUser->IDTravel) {
                return false;
            }

            // If current user is Admin Cabang, can ONLY manage users of their own branch
            if (!empty($currentUser->IDCabang)) {
                if ($targetUser->IDCabang !== $currentUser->IDCabang) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function tambah()
    {
        $currentUser = Auth::user();
        $cabang = collect();
        $namaCabangUser = null;

        if ((int)$currentUser->LevelID === 2 && $currentUser->IDTravel) {
            if (!empty($currentUser->IDCabang)) {
                $namaCabangUser = MstCabang::where('IDCabang', $currentUser->IDCabang)->value('NamaCabang');
            } else {
                $cabang = MstCabang::where('IDTravel', $currentUser->IDTravel)->where('IsAktif', 1)->get();
            }
        }

        return view('admin-page.user.tambah', [
            'menu'           => 'Manajemen User',
            'travel'         => MstTravel::where('StatusAkun', 'Aktif')->get(),
            'cabang'         => $cabang,
            'namaCabangUser' => $namaCabangUser
        ]);
    }

    public function edit(Request $request)
    {
        $username = base64_decode($request->query('u'));

        $userlogin = $this->UserLogin->where('UserName', $username)->firstOrFail();

        if (!$this->checkUserAccess($userlogin)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit user ini');
        }

        $currentUser = Auth::user();
        $cabang = collect();
        $namaCabangUser = null;

        if ((int)$currentUser->LevelID === 2 && !empty($currentUser->IDCabang)) {
            $namaCabangUser = MstCabang::where('IDCabang', $currentUser->IDCabang)->value('NamaCabang');
        } elseif ($userlogin->IDTravel) {
            $cabang = MstCabang::where('IDTravel', $userlogin->IDTravel)->where('IsAktif', 1)->get();
        }

        return view('admin-page.user.tambah', [
            'menu'           => 'Manajemen User',
            'travel'         => MstTravel::where('StatusAkun', 'Aktif')->get(),
            'userlogin'      => $userlogin,
            'cabang'         => $cabang,
            'namaCabangUser' => $namaCabangUser
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->all();
        $isUpdate = !empty($data['id']);
        $oldFoto = $data['old_Foto'] ?? null;
        $currentUser = Auth::user();

        unset($data['id'], $data['old_Foto'], $data['_token']);

        if ($isUpdate) {
            $targetUser = $this->UserLogin->where('UserName', $data['UserName'])->first();
            if (!$targetUser) {
                return response()->json(['status' => false, 'message' => 'User tidak ditemukan'], 404);
            }

            if (!$this->checkUserAccess($targetUser)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit user ini'], 403);
            }

            if ($request->hasFile('Foto')) {
                $file = $request->file('Foto');
                if (!$file->isValid()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Gagal mengupload file'
                    ]);
                }

                $filename = uniqid() . '.' . $file->extension();
                $file->move(public_path('assets/foto-profil'), $filename);
                $data['Foto'] = $filename;

                if ($oldFoto && File::exists(public_path('assets/foto-profil/' . $oldFoto))) {
                    File::delete(public_path('assets/foto-profil/' . $oldFoto));
                }
            } else {
                unset($data['Foto']);
            }

            if ((int)$currentUser->LevelID === 2) {
                $data['LevelID'] = 2;
                $data['IDTravel'] = $currentUser->IDTravel;
                if (!empty($currentUser->IDCabang)) {
                    $data['IDCabang'] = $currentUser->IDCabang;
                } else {
                    $data['IDCabang'] = !empty($data['IDCabang']) ? $data['IDCabang'] : null;
                }
            } else {
                if (!empty($data['IDTravel'])) {
                    $data['LevelID'] = 2;
                    $data['IDCabang'] = !empty($data['IDCabang']) ? $data['IDCabang'] : null;
                } else {
                    $data['LevelID'] = 1;
                    $data['IDTravel'] = null;
                    $data['IDCabang'] = null;
                }
            }

            if (!empty($data['UserPsw'])) {
                $data['UserPsw'] = Hash::make($data['UserPsw']);
            } else {
                unset($data['UserPsw']);
            }

            $this->UserLogin->where('UserName', $data['UserName'])->update($data);
            (new ServerLog)->insertLog($data['UserName'], 'Edit', 'User Login');
            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit data',
                'redirect' => url('admin/User')
            ]);
        }

        // INSERT
        if ($this->UserLogin->where('UserName', $data['UserName'])->exists()) {
            return response()->json(['status' => false, 'message' => 'Username sudah digunakan']);
        }

        if ($this->UserLogin->where('Email', $data['Email'])->exists()) {
            return response()->json(['status' => false, 'message' => 'Email sudah digunakan']);
        }

        if ($this->UserLogin->where('HPNo', $data['HPNo'])->exists()) {
            return response()->json(['status' => false, 'message' => 'No HP sudah digunakan']);
        }

        if ($request->hasFile('Foto')) {
            $file = $request->file('Foto');
            if (!$file->isValid()) {
                return response()->json(['status' => false, 'message' => 'Gagal mengupload file']);
            }

            $filename = uniqid() . '.' . $file->extension();
            $file->move(public_path('assets/foto-profil'), $filename);
            $data['Foto'] = $filename;
        }

        if ((int)$currentUser->LevelID === 2) {
            $data['LevelID'] = 2;
            $data['IDTravel'] = $currentUser->IDTravel;
            if (!empty($currentUser->IDCabang)) {
                $data['IDCabang'] = $currentUser->IDCabang;
            } else {
                $data['IDCabang'] = !empty($data['IDCabang']) ? $data['IDCabang'] : null;
            }
        } else {
            if (!empty($data['IDTravel'])) {
                $data['LevelID'] = 2;
                $data['IDCabang'] = !empty($data['IDCabang']) ? $data['IDCabang'] : null;
            } else {
                $data['LevelID'] = 1;
                $data['IDTravel'] = null;
                $data['IDCabang'] = null;
            }
        }

        $data['UserStatus'] = 1;
        $data['IsAktif'] = 1;
        $data['UserPsw'] = Hash::make($data['UserPsw']);

        $this->UserLogin->create($data);
        (new ServerLog)->insertLog($data['UserName'], 'Tambah', 'User Login');
        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah data',
            'redirect' => url('admin/User')
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:5'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->UserPsw)) {
            return response()->json([
                'status' => false,
                'message' => 'Password lama salah'
            ]);
        }

        $this->UserLogin->where('UserName', $user->UserName)->update([
            'UserPsw' => Hash::make($request->new_password)
        ]);

        (new ServerLog)->insertLog($user->UserName, 'Ganti Password', 'User Login');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengubah password'
        ]);
    }

    public function setaktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $username = $request->query('username');

        $targetUser = $this->UserLogin->where('UserName', $username)->first();
        if (!$targetUser) {
            return response()->json(['status' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        if (!$this->checkUserAccess($targetUser)) {
            return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengubah status user ini'], 403);
        }

        if ($isAktif == 1) {
            $this->UserLogin->where('UserName', $username)->update(['IsAktif' => 0]);
            (new ServerLog)->insertLog($username, 'Nonaktifkan', 'User Login');
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menonaktifkan ' . $username
            ]);
        }

        $this->UserLogin->where('UserName', $username)->update(['IsAktif' => 1]);
        (new ServerLog)->insertLog($username, 'Aktifkan', 'User Login');
        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengaktifkan ' . $username
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $username = $request->query('username');
            $foto = $request->query('foto');

            $targetUser = $this->UserLogin->where('UserName', $username)->first();
            if (!$targetUser) {
                return response()->json(['status' => false, 'message' => 'User tidak ditemukan'], 404);
            }

            if (!$this->checkUserAccess($targetUser)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk menghapus user ini'], 403);
            }

            $this->UserLogin->where('UserName', $username)->delete();

            if ($foto && File::exists(public_path('assets/foto-profil/' . $foto))) {
                File::delete(public_path('assets/foto-profil/' . $foto));
            }

            (new ServerLog)->insertLog($username, 'Hapus', 'User Login');
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus ' . $username
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
        $list = $this->UserLogin->getDatatables();

         return $this->datatablesResponse(
            $request,
            $list,
            $this->UserLogin->countAll(),
            $this->UserLogin->countFiltered()
        );
    }
}
