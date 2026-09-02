<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lokasi;
use App\Models\MstPaket;
use App\Models\MstCabang;
use App\Models\MstPerson;
use App\Models\MstTravel;
use App\Models\JamaahPaket;
use App\Models\SyaratPaket;
use App\Models\TrKasTravel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\DatatablesResponse;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    use DatatablesResponse;

    protected $IDTravel;

    public function __construct(
        Request $request,
        protected MstPaket $MstPaket,
        protected JamaahPaket $JamaahPaket,
        protected MstTravel $MstTravel,
        protected TrKasTravel $TrKasTravel,
        protected SyaratPaket $SyaratPaket,
        protected MstPerson $MstPerson,
    ) {
        $this->IDTravel = Auth::user()->IDTravel ?? null;
    }

    public function paketperjalanan()
    {
        if ($this->IDTravel) {
            $travel = MstTravel::where('IsShow', 1)
                ->where('IDTravel', $this->IDTravel)
                ->get();
        } else {
            $travel = MstTravel::where('IsShow', 1)->get();
        }

        return view('admin-page.laporan.paket-perjalanan', [
            'menu' => 'Laporan Paket Perjalanan',
            'IDTravel' => $this->IDTravel,
            'travel' => $travel,
        ]);
    }

    public function pembayaran()
    {
        if ($this->IDTravel) {
            $travel = MstTravel::where('IsShow', 1)
                ->where('IDTravel', $this->IDTravel)
                ->get();
            $paket = MstPaket::where('IsAktif', 1)
                ->where('IDTravel', $this->IDTravel)
                ->get();
        } else {
            $travel = MstTravel::where('IsShow', 1)->get();
            $paket = collect();
        }

        return view('admin-page.laporan.pembayaran', [
            'menu' => 'Laporan Pembayaran',
            'IDTravel' => $this->IDTravel,
            'travel' => $travel,
            'paket' => $paket,
        ]);
    }

    public function pengeluarankas()
    {
        $cabang = MstCabang::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();

        return view('admin-page.laporan.pengeluaran-kas', [
            'menu' => 'Laporan Pengeluaran Kas',
            'cabang' => $cabang,
        ]);
    }

    public function aruskas()
    {
        $cabang = MstCabang::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();

        return view('admin-page.laporan.arus-kas', [
            'menu' => 'Laporan Arus Kas',
            'cabang' => $cabang,
        ]);
    }

    public function laporanhistoryjamaah()
    {
        $travel = $this->IDTravel
            ? MstTravel::where('IsShow', 1)->where('IDTravel', $this->IDTravel)->get()
            : MstTravel::where('IsShow', 1)->get();

        $prov = (new Lokasi())->setIDProv(null)->findProvinsi();
        $agen = MstPerson::where('StatusPerson', 'Agen')->where('IsAktif', 1)->get();

        return view('admin-page.laporan.history_jamaah', [
            'menu' => 'History Perjalanan Jamaah',
            'prov' => $prov,
            'agen' => $agen,
            'travel' => $travel,
            'IDTravel' => $this->IDTravel,
        ]);
    }

    public function laporanjamaah()
    {
        $cabang = MstCabang::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();

        $prov = (new Lokasi())->setIDProv(null)->findProvinsi();
        $paket = MstPaket::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();
        $agen = MstPerson::where('StatusPerson', 'Agen')->where('IsAktif', 1)->get();

        return view('admin-page.laporan.jamaah', [
            'menu' => 'Laporan Jamaah',
            'prov' => $prov,
            'cabang' => $cabang,
            'paket' => $paket,
            'agen' => $agen,
        ]);
    }

    public function terimabayar()
    {
        $cabang = MstCabang::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();

        $prov = (new Lokasi())->setIDProv(null)->findProvinsi();
        $agen = MstPerson::where('StatusPerson', 'Agen')->where('IsAktif', 1)->get();
        $paket = MstPaket::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();
        $jamaah = MstPerson::where('StatusPerson', 'Jamaah')->where('IsAktif', 1)->get();

        return view('admin-page.laporan.terima-bayar', compact(
            'cabang',
            'prov',
            'agen',
            'paket',
            'jamaah'
        ))->with('menu', 'Laporan Penerimaan Pembayaran Jamaah');
    }

    public function tagihanjamaah()
    {
        $travel = $this->IDTravel
            ? MstTravel::where('IsShow', 1)->where('IDTravel', $this->IDTravel)->get()
            : MstTravel::where('IsShow', 1)->get();

        $prov = (new Lokasi())->setIDProv(null)->findProvinsi();
        $agen = MstPerson::where('StatusPerson', 'Agen')->where('IsAktif', 1)->get();
        $jamaah = MstPerson::where('StatusPerson', 'Jamaah')->where('IsAktif', 1)->get();

        return view('admin-page.laporan.tagihan', compact(
            'prov',
            'agen',
            'jamaah',
            'travel'
        ))->with('menu', 'Laporan Tagihan Jamaah')
            ->with('IDTravel', $this->IDTravel);
    }

    public function dokumen()
    {
        $cabang = MstCabang::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();

        $prov = (new Lokasi())->setIDProv(null)->findProvinsi();
        $agen = MstPerson::where('StatusPerson', 'Agen')->where('IsAktif', 1)->get();
        $paket = MstPaket::where('IsAktif', 1)
            ->when($this->IDTravel, fn($q) => $q->where('IDTravel', $this->IDTravel))
            ->get();
        $jamaah = MstPerson::where('StatusPerson', 'Jamaah')->where('IsAktif', 1)->get();

        return view('admin-page.laporan.dokumen', compact(
            'cabang',
            'prov',
            'agen',
            'paket',
            'jamaah'
        ))->with('menu', 'Laporan Dokumen Jamaah');
    }

    public function get_paketperjalanan(Request $request)
    {
        $IDTravel = $this->IDTravel ?: $request->post('trv');
        $TglBerangkat = $request->post('startDate');
        $TglPulang = $request->post('endDate');

        $where = [];

        if ($IDTravel) {
            $where[] = ['mstpaket.IDTravel', $IDTravel];
        }
        if ($TglBerangkat) {
            $where[] = ['mstpaket.TglBerangkat', '>=', $TglBerangkat];
        }

        if ($TglPulang) {
            $where[] = ['mstpaket.TglPulang', '<=', $TglPulang];
        }

        $list = $this->MstPaket->getDatatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->MstPaket->countAllMonitor(),
            $this->MstPaket->countFilteredMonitor($where),
            function ($row) {
                $row->JumlahJamaah = $this->MstPaket->getJumlahJamaahMonitor([
                    'IDPaket'  => $row->IDPaket,
                    'IDTravel' => $row->IDTravel,
                ]);

                $row->JumlahTracking = $this->MstPaket->getJumlahJamaahMonitor([
                    'IDPaket'  => $row->IDPaket,
                    'IDTravel' => $row->IDTravel,
                ], true);
            }
        );
    }

    public function get_pembayaran(Request $request)
    {
        $IDTravel = $this->IDTravel ?: $request->post('trv');
        $IDPaket = $request->post('paket');
        $TglAwal = $request->post('startDate');
        $TglAkhir = $request->post('endDate');

        $where = [
            ['trkastravel.JenisTransaksi', '=', 'BAYAR APLIKASI'],
            ['trkastravel.IsVerified', '=', 1],
        ];

        // filter Level admin travel
        if ($IDTravel) {
            $where[] = ['trkastravel.IDTravel', '=', $IDTravel];
        }

        if ($IDPaket) {
            $where[] = ['jamaahpaket.IDPaket', '=', $IDPaket];
        }

        // filter tanggal
        if ($TglAwal) {
            $where[] = ['trkastravel.TanggalTransaksi', '>=', $TglAwal];
        }

        if ($TglAkhir) {
            $where[] = ['trkastravel.TanggalTransaksi', '<=', $TglAkhir];
        }

        $list = $this->TrKasTravel->getDatatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->TrKasTravel->countAll($where),
            $this->TrKasTravel->countFiltered($where),
            function ($row) {
                $row->JumlahTracking = $this->MstPaket->getJumlahJamaahMonitor([
                    'NoTransaksi' => $row->NoTransaksi,
                    'IsPaid' => 1
                ]);
            }
        );
    }

    public function get_pengeluarankas(Request $request)
    {
        $TglAwal = $request->post('startDate');
        $TglAkhir = $request->post('enddate');
        $IDCabang = $request->post('cb');

        $where = [];

        $where[] = ['Kredit', '>', 0];

        $where[] = ['trkastravel.IsVerified', '=', 1];

        if ($this->IDTravel) {
            $where[] = ['trkastravel.IDTravel', '=', $this->IDTravel];
        }

        if ($TglAwal) {
            $where[] = ['trkastravel.TanggalTransaksi', '>=', $TglAwal];
        }

        if ($TglAkhir) {
            $where[] = ['trkastravel.TanggalTransaksi', '<=', $TglAkhir];
        }

        if ($IDCabang) {
            $where[] = ['trkastravel.IDCabang', '=', $IDCabang];
        }

        $list = $this->TrKasTravel->getDatatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->TrKasTravel->countAll($where),
            $this->TrKasTravel->countFiltered($where)
        );
    }

    public function get_aruskas(Request $request)
    {
        $TglAwal = $request->post('startDate');
        $TglAkhir = $request->post('endDate');
        $IDCabang = $request->post('cb');

        $where[] = ['trkastravel.IsVerified', '=', 1];

        if ($this->IDTravel) {
            $where[] = ['trkastravel.IDTravel', '=', $this->IDTravel];
        }

        if ($TglAwal) {
            $where[] = ['trkastravel.TanggalTransaksi', '>=', $TglAwal];
        }

        if ($TglAkhir) {
            $where[] = ['trkastravel.TanggalTransaksi', '<=', $TglAkhir];
        }

        if ($IDCabang) {
            $where[] = ['trkastravel.IDCabang', '=', $IDCabang];
        }


        $list = $this->TrKasTravel->getDatatables($where);
        $arrSaldoTransaksi = $this->get_saldo_transaksi($IDCabang);

        return $this->datatablesResponse(
            $request,
            $list,
            $list->count(),
            $list->count(),
            function ($row) use ($arrSaldoTransaksi) {
                $row->Saldo = $this->find_saldo($row->NoTransaksi, $arrSaldoTransaksi);
            }
        );
    }

    private function get_saldo_transaksi($IDCabang)
    {
        $transaksiKas = TrkasTravel::where('IDCabang', $IDCabang)
            ->where('IsVerified', 1)
            ->orderBy('NoTransaksi', 'asc')
            ->get();

        $saldoTransaksi = [];
        $saldo = 0;
        foreach ($transaksiKas as $key => $row) {
            if (!empty($row->Debet)) {
                $saldo += $row->Debet;
            } elseif (!empty($row->Kredit)) {
                $saldo -= $row->Kredit;
            }
            $saldoTransaksi[$key]['NoTransaksi'] = $row->NoTransaksi;
            $saldoTransaksi[$key]['Saldo'] = $saldo;
        }
        return $saldoTransaksi;
    }

    private function find_saldo($NoTransaksi, $arrSaldo)
    {
        foreach ($arrSaldo as $val) {
            if ($val['NoTransaksi'] === $NoTransaksi) {
                return $val['Saldo'];
            }
        }
        return 0;
    }

    public function get_history_jamaah(Request $request)
    {
        $IDProv = $request->query('prov');
        $IDKab = $request->query('kab');
        $IDCabang = $request->query('cb');
        $IDAgen = $request->query('agen');
        $IDPaket = $request->query('paket');
        $IDTravel = $request->query('trv');

        $where = [
            ['mstperson.IsAktif', '=', 1],
        ];

        if (!empty($this->IDTravel)) {
            $where[] = ['jamaahpaket.IDTravel', '=', $this->IDTravel];
        } elseif (!empty($IDTravel)) {
            $where[] = ['jamaahpaket.IDTravel', '=', $IDTravel];
        }

        if (!empty($IDProv)) {
            $where[] = ['mstperson.IDProv', '=', $IDProv];
        }

        if (!empty($IDKab)) {
            $where[] = ['mstperson.IDKab', '=', $IDKab];
        }

        if (!empty($IDCabang)) {
            $where[] = ['jamaahpaket.IDCabang', '=', $IDCabang];
        }

        if (!empty($IDAgen)) {
            $where[] = ['jamaahpaket.IDPersonAgen', '=', $IDAgen];
        }

        if (!empty($IDPaket)) {
            $where[] = ['jamaahpaket.IDPaket', '=', $IDPaket];
        }

        $list = $this->JamaahPaket->get_datatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->JamaahPaket->count_all($where),
            $this->JamaahPaket->count_filtered($where)
        );
    }

    public function get_jamaah(Request $request)
    {
        $IDTravel = $this->IDTravel;
        $where = [
            'mstperson.IsAktif' => 1,
            'jamaahpaket.IDTravel' => $IDTravel
        ];

        $list = $this->MstPerson->getDatatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->MstPerson->countAll(),
            $this->MstPerson->countFiltered()
        );
    }

    public function get_terimabayar(Request $request)
    {
        $IDTravel = $this->IDTravel;
        $IDPerson = $request->post('jm');
        $IDCabang = $request->post('cb');
        $IDPaket = $request->post('paket');
        $IDAgen = $request->post('agen');
        $TglAwal = $request->post('min');
        $TglAkhir = $request->post('max');


        $where = [
            ['mstperson.IsAktif', '=', 1],
            ['jamaahpaket.IDTravel', '=', $IDTravel],
            ['jamaahpaket.InfoTerbayar', '>', 0]
        ];

        if ($TglAwal) {
            $where[] = ['trkastravel.TanggalTransaksi', '>=', $TglAwal];
        }
        if ($TglAkhir) {
            $where[] = ['trkastravel.TanggalTransaksi', '<=', $TglAkhir];
        }

        if ($IDCabang) {
            $where[] = ['jamaahpaket.IDCabang', '=', $IDCabang];
        }
        if ($IDAgen) {
            $where[] = ['jamaahpaket.IDPersonAgen', '=', $IDAgen];
        }
        if ($IDPaket) {
            $where[] = ['jamaahpaket.IDPaket', '=', $IDPaket];
        }
        if ($IDPerson) {
            $where[] = ['jamaahpaket.IDPerson', '=', $IDPerson];
        }

        $list = $this->JamaahPaket->get_datatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->JamaahPaket->count_all(),
            $this->JamaahPaket->count_filtered($where)
        );
    }

    public function get_tagihan(Request $request)
    {
        $IDTravel = $request->post('trv');
        $IDCabang = $request->post('cb');
        $IDPaket = $request->post('paket');
        $IDAgen = $request->post('agen');
        $TglAwal = $request->post('min');
        $TglAkhir = $request->post('max');

        $where[] = ['mstperson.IsAktif', '=', 1];
        $where[] = ['jamaahpaket.InfoSisaTagihan', '<>', 0];

        if ($this->IDTravel) {
            $where[] = ['jamaahpaket.IDTravel', '=', $this->IDTravel];
        } elseif ($IDTravel) {
            $where[] = ['jamaahpaket.IDTravel', '=', $IDTravel];
        }

        if ($TglAwal) {
            $where[] = ['mstpaket.TglBerangkat', '>=', $TglAwal];
        }

        if ($TglAkhir) {
            $where[] = ['mstpaket.TglBerangkat', '<=', $TglAkhir];
        }

        if ($IDCabang) {
            $where[] = ['jamaahpaket.IDCabang', '=', $IDCabang];
        }

        if ($IDAgen) {
            $where[] = ['jamaahpaket.IDPersonAgen', '=', $IDAgen];
        }

        if ($IDPaket) {
            $where[] = ['jamaahpaket.IDPaket', '=', $IDPaket];
        }

        $list = $this->JamaahPaket->get_datatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->JamaahPaket->count_all($where),
            $this->JamaahPaket->count_filtered($where),
            null,
            [
                "total" => $this->JamaahPaket->totalTagihan($where)
            ]
        );
    }
    public function get_dokumen(Request $request)
    {
        $IDTravel = $this->IDTravel;
        $IDCabang = $request->get('cb');
        $IDPaket = $request->get('paket');
        $IDAgen = $request->get('agen');
        $TglAwal = $request->get('min');
        $TglAkhir = $request->get('max');

        $where = [
            ['mstperson.IsAktif', '=', 1],
            ['jamaahpaket.IDTravel', '=', $IDTravel],
        ];

        if ($TglAwal != '') {
            $where[] = ['mstpaket.TglBerangkat', '>=', $TglAwal];
        }

        if ($TglAkhir != '') {
            $where[] = ['mstpaket.TglBerangkat', '<=', $TglAkhir];
        }

        if ($IDCabang != '') {
            $where[] = ['jamaahpaket.IDCabang', '=', $IDCabang];
        }

        if ($IDAgen != '') {
            $where[] = ['jamaahpaket.IDPersonAgen', '=', $IDAgen];
        }

        if ($IDPaket != '') {
            $where[] = ['jamaahpaket.IDPaket', '=', $IDPaket];
        }

        $list = $this->JamaahPaket->get_datatables($where);

        return $this->datatablesResponse(
            $request,
            $list,
            $this->JamaahPaket->count_all(),
            $this->JamaahPaket->count_filtered($where),
            function ($row) {
                $row->JmlSyarat = $this->SyaratPaket->totalUploadMonitor([
                    ['IDTravel', $row->IDTravel],
                    ['IDPaket', $row->IDPaket]
                ]);

                $row->JmlUpload = $this->SyaratPaket->totalUploadMonitor([
                    ['IDTravel', $row->IDTravel],
                    ['IDPaket', $row->IDPaket],
                    ['IDPerson', $row->IDPerson]
                ]);
            }
        );
    }
}
