<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransaksiPromo;
use App\Models\MstTravel;
use App\Models\MstPaket;
use App\Models\SistemSetting;
use App\Models\ServerLog;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;

class PromosiController extends Controller
{
    use DatatablesResponse;

    protected $HargaPromo;
    protected $Durasi;

    public function __construct(
        protected TransaksiPromo $TransaksiPromo,
        protected MstTravel $MstTravel,
        protected MstPaket $MstPaket,
        protected SistemSetting $SistemSetting,
        protected ServerLog $ServerLog
    ) {
        $hargaPromoSetting = $this->SistemSetting->where('Jenis', 'Biaya Promo')->first();
        $durasiSetting = $this->SistemSetting->where('Jenis', 'Durasi Promo')->first();

        $this->HargaPromo = $hargaPromoSetting->Value ?? 0;
        $this->Durasi = $durasiSetting->Value ?? 30;
    }

    public function index()
    {
        $IDTravel = Auth::user()->IDTravel ?? null;

        if ($IDTravel) {
            $paket = $this->MstPaket->where('IsAktif', 1)
                ->where('IDTravel', $IDTravel)
                ->get();
        } else {
            $paket = $this->MstPaket->where('IsAktif', 1)->get();
        }

        return view('admin-page.promosi.index', [
            'menu' => 'Paket Promo',
            'paket' => $paket,
        ]);
    }

    public function tambah()
    {
        $IDTravel = Auth::user()->IDTravel ?? null;

        if ($IDTravel) {
            $travel = $this->MstTravel->where('IsShow', 1)
                ->where('IDTravel', $IDTravel)
                ->get();
            $paket = $this->MstPaket->where('IsAktif', 1)
                ->where('IDTravel', $IDTravel)
                ->get();
        } else {
            $travel = $this->MstTravel->where('IsShow', 1)->get();
            $paket = [];
        }

        return view('admin-page.promosi.tambah', [
            'menu' => 'Paket Promo',
            'travel' => $travel,
            'paket' => $paket,
            'IDTravel' => $IDTravel ?? '',
            'IDPaket' => '',
            'HargaPromo' => $this->HargaPromo,
            'Durasi' => $this->Durasi,
        ]);
    }

    public function edit(Request $request)
    {
        $IDTransaksi = base64_decode($request->query('id'));
        $transaksipromo = $this->TransaksiPromo->getPromoDetail($IDTransaksi);

        if (!$transaksipromo) {
            return redirect()->route('admin.promosi.index')
                ->with('error', 'Data promosi tidak ditemukan');
        }

        $IDTravel = Auth::user()->IDTravel ?? null;

        $paket = $this->MstPaket->where('IsAktif', 1)
            ->where('IDTravel', $transaksipromo->IDTravel)
            ->get();

        if ($IDTravel) {
            $travel = $this->MstTravel->where('IsShow', 1)
                ->where('IDTravel', $IDTravel)
                ->get();
        } else {
            $travel = $this->MstTravel->where('IsShow', 1)->get();
        }

        return view('admin-page.promosi.tambah', [
            'menu' => 'Paket Promo',
            'travel' => $travel,
            'paket' => $paket,
            'IDTravel' => $transaksipromo->IDTravel,
            'IDPaket' => $transaksipromo->IDPaket,
            'transaksipromo' => $transaksipromo,
            'HargaPromo' => $transaksipromo->HargaPromo,
            'Durasi' => $this->Durasi,
        ]);
    }

    public function getpaket(Request $request)
    {
        $IDTravel = $request->query('id');

        $paket = $this->MstPaket->where('IDTravel', $IDTravel)
            ->where('IsAktif', 1)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $paket
        ], 200);
    }

    public function simpan(Request $request)
    {
        $data = $request->all();
        $IDTransaksi = $data['id'] ?? null;
        $isUpdate = $IDTransaksi ? true : false;

        $promosi = [
            'TglMulai' => $data['TglMulai'],
            'TglBerakhir' => $this->setTglBerakhir($data['TglMulai']),
            'IDPaket' => $data['IDPaket'],
            'IDTravel' => $data['IDTravel'],
        ];

        if ($isUpdate) {
            $this->TransaksiPromo->updatePromo($IDTransaksi, $promosi);
            $this->ServerLog->insertLog($IDTransaksi, 'Edit', 'Paket Promo');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengedit data',
                'redirect' => route('admin.promosi.index')
            ], 200);
        } else {
            $promosi['IDTransaksi'] = TransaksiPromo::generateID();
            $promosi['HargaPromo'] = $this->HargaPromo;
            $promosi['IsVerified'] = 0;

            $this->TransaksiPromo->createPromo($promosi);
            $this->ServerLog->insertLog($promosi['IDTransaksi'], 'Tambah', 'Paket Promo');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambah data',
                'redirect' => route('admin.promosi.index')
            ], 200);
        }
    }

    private function setTglBerakhir($inputDate)
    {
        $newDate = date('Y-m-d', strtotime($inputDate . ' +' . $this->Durasi . ' days'));
        return $newDate;
    }

    public function verifikasi(Request $request)
    {
        $id = $request->query('id');

        $this->TransaksiPromo->updatePromo($id, ['IsVerified' => 1]);
        $this->ServerLog->insertLog($id, 'Aktifkan', 'Paket Promo');

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Verifikasi data'
        ], 200);
    }

    public function hapus(Request $request)
    {
        try {
            $id = $request->query('id');

            $this->TransaksiPromo->deletePromo($id);
            $this->ServerLog->insertLog($id, 'Hapus', 'Paket Promo');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }

    public function get(Request $request)
    {
        $IDPaket = $request->query('paket');
        $IDTravel = Auth::user()->IDTravel ?? null;

        $where = [];

        if ($IDTravel) {
            $where['transaksipromo.IDTravel'] = $IDTravel;
        }

        if ($IDPaket) {
            $where['transaksipromo.IDPaket'] = $IDPaket;
        }

        $list = $this->TransaksiPromo->getDatatables($where);

         return $this->datatablesResponse(
            $request,
            $list,
            $this->TransaksiPromo->countAll($where),
            $this->TransaksiPromo->countFiltered($where)
        );
    }
}
