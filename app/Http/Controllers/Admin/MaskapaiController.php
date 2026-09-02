<?php

namespace App\Http\Controllers\Admin;

use App\Models\MstMaskapai;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use App\Traits\UploadFileTrait;

class MaskapaiController extends Controller
{
    use UploadFileTrait, DatatablesResponse;

    public function __construct(
        protected MstMaskapai $MstMaskapai,
        protected ServerLog $ServerLog
    ) {}

    public function index()
    {
        return view('admin-page.maskapai.index', [
            'menu' => 'Data Maskapai'
        ]);
    }

    public function tambah()
    {
        return view('admin-page.maskapai.tambah', [
            'menu' => 'Data Maskapai'
        ]);
    }

    public function edit(Request $request)
    {
        $IDMaskapai = base64_decode($request->id);

        $dataMaskapai = $this->MstMaskapai->where('IDMaskapai', $IDMaskapai)->first();

        return view('admin-page.maskapai.tambah', [
            'menu' => 'Data Maskapai',
            'data' => $dataMaskapai
        ]);
    }



    public function setaktif(Request $request)
    {
        $isAktif = $request->isaktif;
        $id      = $request->id;

        if ($isAktif == 1) {
            $this->MstMaskapai->where('IDMaskapai', $id)->update([
                'IsAktif' => 0
            ]);

            $this->ServerLog->insertLog($id, 'Nonaktifkan', 'Data Maskapai');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menonaktifkan data Maskapai'
            ]);
        }

        $this->MstMaskapai->where('IDMaskapai', $id)->update([
            'IsAktif' => 1
        ]);

        $this->ServerLog->insertLog($id, 'Aktifkan', 'Data Maskapai');

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil mengaktifkan data maskapai'
        ]);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->id;

            $maskapai = $this->MstMaskapai->where('IDMaskapai', $id)->first();

            if (!$maskapai) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data kota tidak ditemukan'
                ]);
            }

            $this->MstMaskapai->where('IDMaskapai', $id)->delete();

            $this->ServerLog->insertLog($id, 'Hapus', 'Data maskapai');

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil menghapus data maskapai'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus data maskapai: ' . $th->getMessage()
            ]);
        }
    }

    public function get(Request $request)
    {
        $list = $this->MstMaskapai->getDatatables($request);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->MstMaskapai->countAllDatatables(),
            $this->MstMaskapai->countFiltered($request)
        );
    }



    public function simpan(Request $request)
    {
        $upload = null;

        try {
            $validated = $request->validate([
                'id'       => ['nullable', 'string'],
                'maskapai' => ['required', 'string', 'max:255'],
                'logo'     => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
                'old_logo' => ['nullable', 'string'],
            ]);

            $IDMaskapai = $validated['id'] ?? null;
            $maskapaiData = [
                'Maskapai' => $validated['maskapai'],
                'IsAktif'  => 1,
            ];

            if ($request->hasFile('logo')) {
                $upload = $this->doUpload(
                    $request,
                    'logo',
                    'assets/maskapai',
                    [
                        'png',
                        'jpg',
                        'jpeg'
                    ]
                );

                if ($upload === false || $upload === 'error') {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Gagal mengupload logo! Pastikan format file sesuai dan tidak lebih dari 2MB'
                    ], 422);
                }

                $maskapaiData['Logo'] = $upload;
            }

            if ($IDMaskapai) {
                $this->MstMaskapai->where('IDMaskapai', $IDMaskapai)->update($maskapaiData);

                if ($upload && !empty($validated['old_logo'])) {
                    $this->deleteFile('assets/maskapai/' . $validated['old_logo']);
                }

                $this->ServerLog->insertLog($IDMaskapai, 'Edit', 'Data Maskapai');

                return response()->json([
                    'status'   => true,
                    'message'  => 'Berhasil mengedit data maskapai',
                    'redirect' => url('admin/maskapai')
                ]);
            }

            $result = $this->MstMaskapai->create($maskapaiData);

            if ($result) {
                $this->ServerLog->insertLog($result->IDMaskapai, 'Tambah', 'Data Maskapai');

                return response()->json([
                    'status'   => true,
                    'message'  => 'Berhasil menambah data maskapai',
                    'redirect' => url('admin/maskapai')
                ]);
            }

            if ($upload) {
                $this->deleteFile('assets/maskapai/' . $upload);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menambah data maskapai'
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Data yang dikirim tidak valid'
            ], 422);
        } catch (\Throwable $th) {
            if (!empty($upload)) {
                $this->deleteFile('assets/maskapai/' . $upload);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan data maskapai: ' . $th->getMessage()
            ], 500);
        }
    }
}
