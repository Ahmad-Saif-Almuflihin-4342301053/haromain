<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MstCabang;
use App\Models\TrKasTravel;
use App\Models\ServerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluarankasController extends Controller
{
    protected $IDTravel;
    protected $MstCabang;
    protected $TrKasTravel;
    protected $ServerLog;

    public function __construct(MstCabang $MstCabang, TrKasTravel $TrKasTravel, ServerLog $ServerLog)
    {
        $this->IDTravel = Auth::user()->IDTravel;
        $this->MstCabang = $MstCabang;
        $this->TrKasTravel = $TrKasTravel;
        $this->ServerLog = $ServerLog;
    }

    public function index()
    {
        $cabang = $this->MstCabang->where('IDTravel', $this->IDTravel)->get();

        return view('admin-page.pengeluarankas.index', [
            'menu' => 'Pengeluaran Operasional',
            'cabang' => $cabang
        ]);
    }

    public function simpan(Request $request)
    {
        $Kredit = str_replace(',', '', $request->Kredit ?? '0');
        $NoTransaksi = $this->TrKasTravel->generateIDTrx();

        $transaksi = [
            'IDTravel' => $this->IDTravel,
            'NoTransaksi' => $NoTransaksi,
            'TanggalTransaksi' => date('Y-m-d'),
            'JenisTransaksi' => 'BAYAR OPERASIONAL',
            'NamaPersonTransaksi' => Auth::user()->NamaLengkap ?? Auth::user()->UserName,
            'UraianTransaksi' => $request->UraianTransaksi,
            'Kredit' => $Kredit,
            'IDUserTransaksi' => Auth::user()->UserName,
            'IDCabang' => $request->IDCabang,
            'IsVerified' => 1
        ];

        TrKasTravel::create($transaksi);

        $this->ServerLog->insertLog($this->IDTravel, 'Tambah', 'Pengeluaran Operasional');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambah data',
            'redirect' => route('admin.pengeluarankas.index'),
            'print_url' => route('admin.pengeluarankas.cetak_nota', ['NoTransaksi' => base64_encode($NoTransaksi)])
        ]);
    }

    public function cetakNota($NoTransaksi)
    {
        $NoTransaksi = base64_decode($NoTransaksi);
        $data = $this->TrKasTravel->where('NoTransaksi', $NoTransaksi)->first();
        
        if (!$data) {
            abort(404, 'Data tidak ditemukan');
        }

        $travel = \App\Models\MstTravel::find($this->IDTravel);
        $cabang = $this->MstCabang->find($data->IDCabang);
        $namaCabang = $cabang ? $cabang->NamaCabang : '-';

        return view('admin-page.pengeluarankas.cetak-nota', compact('data', 'travel', 'namaCabang'));
    }
}
