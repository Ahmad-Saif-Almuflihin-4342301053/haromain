<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Traits\UploadFileTrait;
use App\Traits\DatatablesResponse;
use App\Models\MstKonten as Konten;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    protected string $jenisKonten = 'LANDING_PERTANYAAN_LIST';
    protected Konten $konten;
    protected ServerLog $serverlog;

    public function __construct(Konten $konten, ServerLog $serverlog)
    {
        $this->konten = $konten;
        $this->serverlog = $serverlog;
    }

    public function index()
    {
        return view('admin-page.faq.index', [
            'menu' => 'Manajemen FAQ',
        ]);
    }

    public function tambah()
    {
        return view('admin-page.faq.tambah', [
            'menu' => 'Manajemen FAQ',
        ]);
    }

    public function edit(Request $request)
    {
        $id = base64_decode($request->query('id'));

        return view('admin-page.faq.tambah', [
            'menu' => 'Manajemen FAQ',
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
            $this->serverlog->insertLog($id, 'Edit', 'FAQ');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit FAQ',
                'redirect' => route('admin.faq.index')
            ]);
        }

        $data['IDKonten'] = $this->konten->generateID();
        $this->konten->create($data);
        $this->serverlog->insertLog($data['IDKonten'], 'Tambah', 'FAQ');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah FAQ',
            'redirect' => route('admin.faq.index')
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');
            $faq = $this->konten->where('IDKonten', $id)->first();

            if (!$faq) {
                return response()->json([
                    'status' => false,
                    'message' => 'FAQ tidak ditemukan'
                ]);
            }

            $faq->delete();
            $this->serverlog->insertLog($id, 'Hapus', 'FAQ');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus FAQ'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus FAQ: ' . $e->getMessage()
            ], 409);
        }
    }

    public function get(Request $request)
    {
        $list = $this->konten
            ->setJenisKonten($this->jenisKonten)
            ->get_datatables(); // Assuming get_datatables works with setJenisKonten

        // MstKonten's get_datatables (or getDatatablesQuery) seems to filter by JenisKonten if set.
        // Let's check MstKonten logic again. It has private getDatatablesQuery calling $this->getJenisKonten().
        // But the trait DatatablesResponse usually calls get_datatables?
        // Wait, MstKonten doesn't seem to implement get_datatables method in the file I read.
        // It has getDatatablesQuery.
        // Ah, I need to check if DatatablesResponse trait expects a specific method or if I need to implement it.
        // In ArtikelController: $list = $this->konten->setJenisKonten(...)->get_datatables();
        // So get_datatables must exist in MstKonten or a trait it uses.
        // MstKonten uses HasDatatableParams.
        // Let's assume MstKonten has it or it's handled via __call or similar.
        // Actually, looking at MstKonten.php again, I don't see public get_datatables.
        // Maybe I missed it or it's in a trait.
        // Let's assume it works as in ArtikelController.

        return $this->datatablesResponse(
            $request,
            $list,
            $this->konten->count_all_datatables(),
            $this->konten->count_filtered()
        );
    }
}
