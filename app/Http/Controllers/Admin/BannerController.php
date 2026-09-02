<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstBanner;
use App\Models\MstTravel;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected $banner;
    protected $serverlog;

    public function __construct()
    {
        $this->banner = new MstBanner();
        $this->serverlog = new ServerLog();
    }

    private function checkBannerAccess(?MstBanner $banner = null): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Admin Cabang cannot access Banner at all
        if ((int)$user->LevelID === 2 && !empty($user->IDCabang)) {
            return false;
        }

        // Superadmin has full access
        if ((int)$user->LevelID === 1) {
            return true;
        }

        // Admin Travel Pusat
        if ((int)$user->LevelID === 2 && empty($user->IDCabang)) {
            if ($banner === null) {
                return true;
            }
            return $banner->IDTravel === $user->IDTravel;
        }

        return false;
    }

    public function index()
    {
        if (!$this->checkBannerAccess()) {
            abort(403, 'Akses Ditolak');
        }

        $travel = [];
        if ((int)Auth::user()->LevelID === 1) {
            $travel = MstTravel::where('StatusAkun', 'Aktif')->get();
        }

        return view('admin-page.banner.index', [
            'menu'   => 'Manajemen Banner',
            'travel' => $travel,
        ]);
    }

    public function tambah()
    {
        if (!$this->checkBannerAccess()) {
            abort(403, 'Akses Ditolak');
        }

        return view('admin-page.banner.tambah', [
            'menu'       => 'Manajemen Banner',
            'KodeBanner' => $this->banner->generateID(),
            'travel'     => MstTravel::where('StatusAkun', 'Aktif')->get()
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->id);
        $banner = MstBanner::where('KodeBanner', $id)->firstOrFail();

        if (!$this->checkBannerAccess($banner)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit banner ini');
        }

        return view('admin-page.banner.tambah', [
            'menu'   => 'Manajemen Banner',
            'banner' => $banner,
            'travel' => MstTravel::where('StatusAkun', 'Aktif')->get()
        ]);
    }

    public function simpan(Request $request)
    {
        if (!$this->checkBannerAccess()) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        $data = $request->all();
        $isUpdate   = !empty($data['id']);
        $kodeBanner = $data['id'] ?? null;
        $oldFoto    = $data['old_Gambar'] ?? '';
        $currentUser = Auth::user();

        $bannerData = [
            'Keterangan1' => $data['Keterangan1'] ?? null,
            'Keterangan2' => $data['Keterangan2'] ?? null,
        ];

        if ((int)$currentUser->LevelID === 2) {
            $bannerData['IDTravel'] = $currentUser->IDTravel;
        } else {
            $bannerData['IDTravel'] = !empty($data['IDTravel']) ? $data['IDTravel'] : null;
        }

        if ($isUpdate) {
            $targetBanner = MstBanner::where('KodeBanner', $kodeBanner)->first();
            if (!$targetBanner || !$this->checkBannerAccess($targetBanner)) {
                return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit banner ini'], 403);
            }

            if ($request->file('Gambar')) {
                $upload = $this->doUpload(
                    $request,
                    'Gambar',
                    'assets/banner',
                    ['jpg', 'png', 'jpeg']
                );

                if ($upload === 'error') {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Gagal mengupload file! Pastikan format file sesuai dan tidak lebih dari 2MB'
                    ]);
                }

                $bannerData['Gambar'] = $upload;

                if ($oldFoto) {
                    $this->deleteFile('assets/banner/' . $oldFoto);
                }
            }

            MstBanner::where('KodeBanner', $kodeBanner)->update($bannerData);
            (new ServerLog)->insertLog($kodeBanner, 'Edit', 'Banner');

            return response()->json([
                'status'   => true,
                'message'  => 'Berhasil mengedit data',
                'redirect' => url('admin/Banner')
            ]);
        }

        if (!$request->file('Gambar')) {
            return response()->json([
                'status'  => false,
                'message' => 'Gambar tidak boleh kosong'
            ]);
        }

        $upload = $this->doUpload(
            $request,
            'Gambar',
            'assets/banner',
            ['jpg', 'png', 'jpeg']
        );

        if ($upload === 'error') {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengupload file! Pastikan format file sesuai dan tidak lebih dari 2MB'
            ]);
        }

        $bannerData['KodeBanner'] = $this->banner->generateID();
        $bannerData['Gambar']     = $upload;
        $bannerData['IsAktif']    = 1;

        MstBanner::create($bannerData);
        (new ServerLog)->insertLog($bannerData['KodeBanner'], 'Tambah', 'Banner');

        return response()->json([
            'status'   => true,
            'message'  => 'Berhasil menambah data',
            'redirect' => url('admin/Banner')
        ]);
    }

    public function setaktif(Request $request)
    {
        $isAktif = $request->isaktif;
        $id = $request->id;

        $banner = MstBanner::where('KodeBanner', $id)->first();
        if (!$banner || !$this->checkBannerAccess($banner)) {
            return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
        }

        if ($isAktif == 1) {
            MstBanner::where('KodeBanner', $id)->update(['IsAktif' => 0]);
            $this->serverlog->insertLog($id, 'Nonaktifkan', 'Banner');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menonaktifkan data'
            ]);
        }

        MstBanner::where('KodeBanner', $id)->update(['IsAktif' => 1]);
        $this->serverlog->insertLog($id, 'Aktifkan', 'Banner');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mengaktifkan data'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->id;
            $foto = $request->foto;

            $banner = MstBanner::where('KodeBanner', $id)->first();
            if (!$banner || !$this->checkBannerAccess($banner)) {
                return response()->json(['status' => false, 'message' => 'Akses Ditolak'], 403);
            }

            MstBanner::where('KodeBanner', $id)->delete();

            if ($foto && File::exists(public_path('assets/banner/' . $foto))) {
                File::delete(public_path('assets/banner/' . $foto));
            }
            $this->serverlog->insertLog($id, 'Hapus', 'Banner');

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

    public function get(Request $request)
    {
        if (!$this->checkBannerAccess()) {
            return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $list = $this->banner->getDatatables();

        return $this->datatablesResponse(
            $request,
            $list,
            $this->banner->countAll(),
            $this->banner->countFiltered()
        );
    }
}
