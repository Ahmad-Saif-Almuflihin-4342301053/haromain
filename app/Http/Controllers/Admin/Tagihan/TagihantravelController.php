<?php

namespace App\Http\Controllers\Admin\Tagihan;

use App\Models\MstPaket;
use App\Models\MstCabang;
use App\Models\MstPerson;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\JamaahPaket;
use App\Models\TrKasTravel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TagihantravelController extends Controller
{
    protected $IDTravel;
    protected $MstTravel;
    protected $MstPaket;
    protected $MstCabang;
    protected $MstPerson;
    protected $JamaahPaket;
    protected $TrKasTravel;
    protected $ServerLog;

    public function __construct(
        MstTravel $MstTravel,
        MstPaket $MstPaket,
        MstCabang $MstCabang,
        MstPerson $MstPerson,
        JamaahPaket $JamaahPaket,
        TrKasTravel $TrKasTravel,
        ServerLog $ServerLog
    ) {
        $this->IDTravel = Auth::user()->IDTravel;
        $this->MstTravel = $MstTravel;
        $this->MstPaket = $MstPaket;
        $this->MstCabang = $MstCabang;
        $this->MstPerson = $MstPerson;
        $this->JamaahPaket = $JamaahPaket;
        $this->TrKasTravel = $TrKasTravel;
        $this->ServerLog = $ServerLog;
    }

    public function index()
    {
        $travel = MstTravel::where('IsShow', 1)->get();

        return view('admin-page.tagihan.pusat', [
            'menu' => 'Tagihan Travel',
            'IDTravel' => $this->IDTravel,
            'travel' => $travel,
            'paket' => [],
            'cabang' => []
        ]);
    }

    public function entryBayarJamaah()
    {
        $travel = MstTravel::where('IsShow', 1)->where('IDTravel', $this->IDTravel)->get();
        $paket = MstPaket::where('IsAktif', 1)->where('IDTravel', $this->IDTravel)->get();
        $cabang = MstCabang::where('IsAktif', 1)->where('IDTravel', $this->IDTravel)->get();

        return view('admin-page.tagihan.travel', [
            'menu' => 'Entry Pembayaran',
            'IDTravel' => $this->IDTravel,
            'travel' => $travel,
            'paket' => $paket,
            'cabang' => $cabang
        ]);
    }

    public function bayarjamaah(Request $request)
    {
        $IDPerson = base64_decode($request->query('id'));
        $IDPaket = base64_decode($request->query('pkt'));

        $jamaah = $this->MstPerson->findJoinJamaahPaket([
            'jamaahpaket.IDPerson' => $IDPerson,
            'jamaahpaket.IDPaket' => $IDPaket
        ]);

        $paket = MstPaket::where('IDPaket', $IDPaket)->first();

        return view('admin-page.tagihan.pembayaran-jamaah', [
            'menu' => 'Entry Pembayaran Jamaah',
            'cabang' => MstCabang::where('IDTravel', $this->IDTravel)->get(),
            'jamaah' => $jamaah,
            'paket' => $paket
        ]);
    }

    public function simpancetak(Request $request)
    {
        $data = $request->all();
        $NoInvoice = $this->TrKasTravel->generateNoInvoice();
        $NoTransaksi = $this->TrKasTravel->generateIDTrx();
        $total = str_replace(',', '', $data['Total'] ?? '0');
        $tarif = str_replace(',', '', $data['Tarif'] ?? '0');

        if (empty($data['CheckJamaah'] ?? null)) {
            return response()->json(['status' => false, 'message' => 'Anda belum memilih jamaah']);
        }

        $transaksi = [
            'IDTravel' => $data['IDTravel'] ?? $this->IDTravel,
            'NoTransaksi' => $NoTransaksi,
            'TanggalTransaksi' => null,
            'JenisTransaksi' => 'BAYAR APLIKASI',
            'Kredit' => $total,
            'IDUserTransaksi' => null,
            'NamaPersonTransaksi' => null,
            'IDCabang' => $data['IDCabang'] ?? null,
            'NoInvoice' => $NoInvoice,
            'IsVerified' => 0,
            'TanggalInvoice' => date('Y-m-d')
        ];

        TrKasTravel::create($transaksi);

        foreach ($data['IDPerson'] as $key => $value) {
            if (isset($data['CheckJamaah'][$key])) {
                JamaahPaket::where([
                    'IDPaket' => $data['IDPaket'],
                    'IDTravel' => $data['IDTravel'],
                    'IDPerson' => $value
                ])->update([
                    'NominalPaid' => $tarif,
                    'NoInvoice' => $NoInvoice,
                    'IsPaid' => 0
                ]);
            }
        }

        $this->ServerLog->insertLog($NoInvoice, 'Tambah', 'Invoice');

        $invoiceURL = url('admin/tagihan/Tagihantravel/invoice?inv=' . $NoInvoice);
        return response()->json(['status' => true, 'message' => 'Invoice berhasil disimpan', 'redirect' => $invoiceURL]);
    }

    private function formatCurrency($number)
    {
        if (empty($number)) return '';
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    public function simpanPembayaranJamaah(Request $request)
    {
        $data = $request->all();
        $NoTransaksi = $this->TrKasTravel->generateIDTrx();
        $nominalpembayaran = intval(str_replace(',', '', $data['Nominal'] ?? 0));

        $jamaah_paket = JamaahPaket::where(['IDPerson' => $data['IDPerson'], 'IDPaket' => $data['IDPaket']])->first();
        $infotagihan = $jamaah_paket->InfoTagihan ?? 0;
        $infoterbayar = $jamaah_paket->InfoTerbayar ?? 0;
        $sisatagihan = $infotagihan - $infoterbayar;

        if ($nominalpembayaran > $sisatagihan) {
            return response()->json(['status' => false, 'message' => 'Nominal pembayaran tidak boleh melebihi sisa tagihan']);
        }

        $transaksi = [
            'IDTravel' => $this->IDTravel,
            'NoTransaksi' => $NoTransaksi,
            'TanggalTransaksi' => date('Y-m-d H:i:s'),
            'JenisTransaksi' => 'PEMBAYARAN JAMAAH',
            'Debet' => $nominalpembayaran,
            'IDUserTransaksi' => Auth::user()->UserName,
            'NamaPersonTransaksi' => Auth::user()->NamaLengkap ?? Auth::user()->UserName,
            'IDCabang' => $jamaah_paket->IDCabang ?? null,
            'IsVerified' => 1,
            'UraianTransaksi' => $data['UraianTransaksi'] ?? null
        ];

        TrKasTravel::create($transaksi);

        $where = ['IDPaket' => $data['IDPaket'], 'IDPerson' => $data['IDPerson']];
        JamaahPaket::where($where)->update([
            'InfoTerbayar' => ($infoterbayar ?? 0) + $nominalpembayaran,
            'InfoSisaTagihan' => $sisatagihan - $nominalpembayaran,
            'NoTransaksi' => $NoTransaksi
        ]);

        $this->ServerLog->insertLog($NoTransaksi, 'Tambah', 'Pembayaran Jamaah');
        return response()->json([
            'status' => true,
            'message' => 'Pembayaran jamaah berhasil disimpan',
            'redirect' => url('admin/tagihan/Tagihantravel/entrybayarjamaah'),
            'print_url' => route('admin.tagihan.cetak_kwitansi', ['NoTransaksi' => $NoTransaksi])
        ]);
    }

    public function cetakKwitansi($NoTransaksi)
    {
        $transaksi = TrKasTravel::where('NoTransaksi', $NoTransaksi)->first();
        if (!$transaksi) abort(404);

        $jp = JamaahPaket::where('NoTransaksi', $NoTransaksi)->first();
        if (!$jp) abort(404, 'Data Jamaah Paket not found for this transaction');

        $data = $this->JamaahPaket->getDetail($jp->IDTravel, $jp->IDPaket, $jp->IDPerson);
        $travel = $this->MstTravel->detail($jp->IDTravel);

        return view('admin-page.tagihan.cetak-kwitansi', compact('data', 'travel', 'transaksi'));
    }

    public function invoice(Request $request)
    {
        $NoInvoice = $request->query('inv');

        $one_jamaah_paket = JamaahPaket::where('NoInvoice', $NoInvoice)->first();
        $travel = MstTravel::where('IDTravel', $one_jamaah_paket->IDTravel)->first();
        $paket = MstPaket::where('IDPaket', $one_jamaah_paket->IDPaket)->first();
        $jamaah_paket = $this->JamaahPaket->getJoinPerson(['NoInvoice' => $NoInvoice]);

        $jumlah_jamaah = count($jamaah_paket);

        $data = [
            'NamaTravel' => $travel->NamaTravel ?? null,
            'NamaPaket' => $paket->NamaPaket ?? null,
            'Alamat' => $travel->Alamat ?? null,
            'InfoTagihan' => $this->formatCurrency($one_jamaah_paket->InfoTagihan ?? 0),
            'NoInvoice' => $NoInvoice,
            'NominalPaid' => $this->formatCurrency($one_jamaah_paket->NominalPaid ?? 0),
            'JamaahPaket' => $jamaah_paket,
            'JumlahJamaah' => $jumlah_jamaah,
            'Total' => $this->formatCurrency($jumlah_jamaah * ($one_jamaah_paket->NominalPaid ?? 0)),
        ];

        $pdf = Pdf::loadView('admin-page.tagihan.cetak_invoice', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->download($NoInvoice . '.pdf');
    }

    public function getpaket(Request $request)
    {
        $IDTravel = $request->query('id');
        $paket = MstPaket::where(['IDTravel' => $IDTravel, 'IsAktif' => 1])->get();
        return response()->json(['status' => true, 'data' => $paket]);
    }

    public function getcabang(Request $request)
    {
        $IDTravel = $request->query('id');
        $cabang = MstCabang::where(['IDTravel' => $IDTravel, 'IsAktif' => 1])->get();
        return response()->json(['status' => true, 'data' => $cabang]);
    }

    public function gettagihan(Request $request)
    {
        $IDTravel = $request->query('travel');
        $IDPaket = $request->query('paket');
        $IDCabang = $request->query('cabang');

        $travel = MstTravel::where('IDTravel', $IDTravel)->first();
        $paket = MstPaket::where('IDPaket', $IDPaket)->first();
        $tarif = DB::table('sistemsetting')->where('Jenis', 'Biaya Trip')->first();
        $jamaahpaket = $this->JamaahPaket->getJoinPerson(['IDTravel' => $IDTravel, 'IDPaket' => $IDPaket, 'IDCabang' => $IDCabang]);

        $data = [
            'NamaTravel' => $travel->NamaTravel ?? null,
            'NamaPaket' => $paket->NamaPaket ?? null,
            'tarif' => $tarif->Value ?? null,
            'jamaah' => $jamaahpaket
        ];

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function gettagihanjamaah(Request $request)
    {
        $IDTravel = $request->query('travel');
        $IDPaket = $request->query('paket');
        $IDCabang = $request->query('cabang');

        $travel = MstTravel::where('IDTravel', $IDTravel)->first();
        $paket = MstPaket::where('IDPaket', $IDPaket)->first();
        $jamaahpaket = $this->JamaahPaket->getJoinPerson(['IDTravel' => $IDTravel, 'IDPaket' => $IDPaket, 'IDCabang' => $IDCabang]);

        $data = [
            'NamaTravel' => $travel->NamaTravel ?? null,
            'NamaPaket' => $paket->NamaPaket ?? null,
            'jamaah' => $jamaahpaket
        ];

        return response()->json(['status' => true, 'data' => $data]);
    }
}
