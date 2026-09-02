<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Models\MstKonten as Konten;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected string $jenisKonten = 'LANDING_TESTIMONIAL_LIST';
    protected Konten $konten;
    protected ServerLog $serverlog;

    public function __construct(Konten $konten, ServerLog $serverlog)
    {
        $this->konten = $konten;
        $this->serverlog = $serverlog;
    }

    public function index()
    {
        return view('admin-page.testimonial.index', [
            'menu' => 'Manajemen Testimonial',
        ]);
    }

    public function tambah()
    {
        return view('admin-page.testimonial.tambah', [
            'menu' => 'Manajemen Testimonial',
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        return view('admin-page.testimonial.tambah', [
            'menu' => 'Manajemen Testimonial',
            'data' => $this->konten->find($id),
        ]);
    }

    public function simpan(Request $request)
    {
        $id = $request->input('id');

        $data = [
            'JudulKonten'   => $request->JudulKonten,
            'IsiKonten'     => $request->IsiKonten,
            'Author'        => Auth::user()->NamaUser ?? 'Admin',
            'JenisKonten'   => $this->jenisKonten,
            'TanggalKonten' => now(),
            'IsAktif'       => 1,
            'IsRekomendasi' => 0,
        ];

        if ($id) {
            $this->konten->where('IDKonten', $id)->update($data);
            $this->serverlog->insertLog($id, 'Edit', 'Testimonial');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit Testimonial',
                'redirect' => route('admin.testimonial.index')
            ]);
        }

        $data['IDKonten'] = $this->konten->generateID();
        $this->konten->create($data);
        $this->serverlog->insertLog($data['IDKonten'], 'Tambah', 'Testimonial');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah Testimonial',
            'redirect' => route('admin.testimonial.index')
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');
            $testimonial = $this->konten->where('IDKonten', $id)->first();

            if (!$testimonial) {
                return response()->json([
                    'status' => false,
                    'message' => 'Testimonial tidak ditemukan'
                ]);
            }

            $testimonial->delete();
            $this->serverlog->insertLog($id, 'Hapus', 'Testimonial');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus Testimonial'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus Testimonial: ' . $e->getMessage()
            ], 409);
        }
    }

    public function get(Request $request)
    {
        $list = $this->konten
            ->setJenisKonten($this->jenisKonten)
            ->get_datatables();

        return $this->datatablesResponse(
            $request,
            $list,
            $this->konten->count_all_datatables(),
            $this->konten->count_filtered()
        );
    }
}
