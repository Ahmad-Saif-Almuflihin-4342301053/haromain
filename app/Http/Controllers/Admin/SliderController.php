<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstBanner;
use App\Models\MstKonten;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\UploadFileTrait;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;

class SliderController extends Controller
{

    use UploadFileTrait, DatatablesResponse;

    private $JenisKonten;
    protected $banner;
    protected $konten;
    public function __construct()
    {
        $this->JenisKonten = 'SLIDER';
        $this->banner = new MstBanner();
        $this->konten = new MstKonten();
    }

    public function index()
    {
        return view('admin-page.slider.index', [
            'menu' => 'Manajemen Slider',
        ]);
    }

    public function tambah()
    {
        return view('admin-page.slider.tambah', [
            'menu' => 'Manajemen Slider',
            'KodeBanner' => $this->banner->generateID(),
        ]);
    }

    public function edit(Request $request)
    {
        $IDKonten = base64_decode($request->query('id'));
        $konten = MstKonten::find($IDKonten);

        return view('admin-page.slider.tambah', [
            'menu' => 'Manajemen Slider',
            'banner' => $konten,
        ]);
    }

    public function simpan(Request $request)
    {
        $isUpdate = !empty($request->id);
        $IDKonten = $isUpdate ? base64_decode($request->id) : null;

        $insertData = [
            'Author' => Auth::user()->NamaUser,
            'JenisKonten' => $this->JenisKonten
        ];

        if ($request->hasFile('slider')) {

            $upload = $this->doUpload(
                $request,
                'slider',
                'assets/konten',
                ['jpg', 'jpeg', 'png']
            );

            if ($upload === 'error') {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengupload file! Pastikan format file sesuai dan tidak lebih dari 2MB'
                ]);
            }

            $insertData['GambarKonten'] = $upload;

            if ($isUpdate) {
                $konten = MstKonten::find($IDKonten);
                if ($konten && $konten->GambarKonten) {
                    $this->deleteFile('assets/konten/' . $konten->GambarKonten);
                }
            }
        } else {
            if (!$isUpdate) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gambar tidak boleh kosong',
                ]);
            }
        }

        if ($isUpdate) {
            MstKonten::where('IDKonten', $IDKonten)->update($insertData);

            (new ServerLog)->insertLog($IDKonten, 'Edit', 'Slider');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit data',
                'redirect' => url('admin/Slider')
            ]);
        } else {
            $insertData['IDKonten'] = $this->konten->generateID();
            $insertData['IsAktif'] = 1;
            $insertData['TanggalKonten'] = now();

            MstKonten::create($insertData);

            (new ServerLog)->insertLog($insertData['IDKonten'], 'Tambah', 'Slider');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambah data',
                'redirect' => url('admin/Slider')
            ]);
        }
    }


    public function setaktif(Request $request)
    {
        $isAktif = $request->query('isaktif');
        $id = $request->query('id');

        $banner = MstBanner::where('KodeBanner', $id)->first();
        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Banner tidak ditemukan'
            ]);
        }

        $banner->IsAktif = $isAktif == 1 ? 0 : 1;
        $banner->save();

        (new ServerLog)->insertLog($id, $isAktif == 1 ? 'Nonaktifkan' : 'Aktifkan', 'Banner');

        return response()->json([
            'status' => true,
            'message' => $isAktif == 1 ? 'Berhasil menonaktifkan data' : 'Berhasil mengaktifkan data'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = base64_decode($request->query('id'));

            $konten = MstKonten::find($id);

            if (!$konten) {
                return response()->json([
                    'status' => false,
                    'message' => 'Slider tidak ditemukan'
                ]);
            }

            if ($konten && $konten->GambarKonten) {
                $this->deleteFile('assets/konten/' . $konten->GambarKonten);
            }

            $konten->delete();
            (new ServerLog)->insertLog($id, 'Hapus', 'Slider');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus slider'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus slider: ' . $th->getMessage()
            ]);
        }
    }

    public function get(Request $request)
    {
        $IDKatKonten = $request->post('IDKatKonten', null);

        if ($IDKatKonten) {
            $this->konten->setIDKatKonten($IDKatKonten);
        }

        $list = $this->konten
            ->setJenisKonten($this->JenisKonten)
            ->get_datatables();

         return $this->datatablesResponse(
            $request,
            $list,
            $this->konten->count_all_datatables(),
            $this->konten->count_filtered()
        );
    }
}
