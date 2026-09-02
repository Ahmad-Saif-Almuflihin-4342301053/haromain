<?php

namespace App\Http\Controllers\Admin\Tagihan;

use App\Models\MstPaket;
use App\Models\TrKasTravel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;

class BiayaAplikasiController extends Controller
{
    use DatatablesResponse;

    protected TrKasTravel $trkastravel;
    protected MstPaket $paket;
    protected  $IDTravel;

    public function __construct(
        TrKasTravel $trkastravel,
        MstPaket $paket
    ) {
        $this->trkastravel = $trkastravel;
        $this->paket = $paket;

        $this->IDTravel = Auth::user()->IDTravel ?? null;
    }

    public function index()
    {
        return view('admin-page.tagihan.biaya-aplikasi', [
            'menu'  => 'Tagihan Biaya Aplikasi',
            'paket' => $this->paket->where(
                'IDTravel',
                $this->IDTravel
            )->where('IsAktif', 1)->get(),
            'bank'  => config('constants.BANK'),
        ]);
    }

    public function get(Request $request)
    {
        $where = [
            'JenisTransaksi'           => 'BAYAR APLIKASI',
            'trkastravel.IsVerified'   => 0,
        ];

        $list = $this->trkastravel->getDatatables($where);

         return $this->datatablesResponse(
            $request,
            $list,
            $this->trkastravel->countAll($where),
            $this->trkastravel->countFiltered($where)
        );
    }
}
