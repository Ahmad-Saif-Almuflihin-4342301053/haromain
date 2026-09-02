<?php

namespace App\Http\Controllers\Admin\Tagihan;

use App\Models\MstPaket;
use App\Models\MstCabang;
use App\Models\MstTravel;
use App\Models\ServerLog;
use App\Models\JamaahPaket;
use App\Models\TrKasTravel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TerimabayarController extends Controller
{
    protected $IDTravel;
    protected $MstTravel;
    protected $MstPaket;
    protected $MstCabang;
    protected $JamaahPaket;
    protected $TrKasTravel;
    protected $ServerLog;

    public function __construct(
        MstTravel $MstTravel,
        MstPaket $MstPaket,
        MstCabang $MstCabang,
        JamaahPaket $JamaahPaket,
        TrKasTravel $TrKasTravel,
        ServerLog $ServerLog
    ) {
        $this->IDTravel = Auth::user()->IDTravel ?? null;
        $this->MstTravel = $MstTravel;
        $this->MstPaket = $MstPaket;
        $this->MstCabang = $MstCabang;
        $this->JamaahPaket = $JamaahPaket;
        $this->TrKasTravel = $TrKasTravel;
        $this->ServerLog = $ServerLog;
    }

    /**
     * Display terima bayar page
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $IDTravel = $request->query('travel');
        $IDPaket = $request->query('paket');
        $IDCabang = $request->query('cabang');
        $NoInvoice = $request->query('inv');

        // Filter by NoInvoice atau by Travel-Paket-Cabang
        if (!empty($NoInvoice)) {
            $jamaahpaket = $this->JamaahPaket->getJoinAll(['NoInvoice' => $NoInvoice]);
        } elseif (!empty($IDPaket) && !empty($IDTravel) && !empty($IDCabang)) {
            $jamaahpaket = $this->JamaahPaket->getJoinAll([
                ['jamaahpaket.IDTravel', '=', $IDTravel],
                ['jamaahpaket.IDPaket', '=', $IDPaket],
                ['jamaahpaket.IDCabang', '=', $IDCabang],
                ['jamaahpaket.NoInvoice', '<>', null]
            ]);
        } else {
            $jamaahpaket = [];
        }

        // Ambil ID dari hasil query jika tidak ada dari request
        $IDTravel = $IDTravel ?: ($jamaahpaket[0]->IDTravel ?? null);
        $IDPaket = $IDPaket ?: ($jamaahpaket[0]->IDPaket ?? null);
        $IDCabang = $IDCabang ?: ($jamaahpaket[0]->IDCabang ?? null);

        // Filter berdasarkan level user
        if (!empty($this->IDTravel)) {
            $paket = MstPaket::where(['IDTravel' => $IDTravel, 'IsAktif' => 1])->get();
            $cabang = MstCabang::where(['IDTravel' => $IDTravel, 'IsAktif' => 1])->get();
            $travel = MstTravel::where(['IsShow' => 1, 'IDTravel' => $this->IDTravel])->get();
            $invoice = $this->TrKasTravel->getNoInvoice(['IDTravel' => $this->IDTravel]);
            $menu = 'Fee Haromain';
        } else {
            $paket = MstPaket::where(['IsAktif' => 1])->get();
            $cabang = MstCabang::where(['IsAktif' => 1])->get();
            $travel = MstTravel::where(['IsShow' => 1])->get();
            $invoice = $this->TrKasTravel->getNoInvoice([]);
            $menu = 'Terima Bayar Travel';
        }

        return view('admin-page.tagihan.terimabayar', [
            'menu' => $menu,
            'travel' => $travel,
            'invoice' => $invoice,
            'NamaTravel' => $jamaahpaket[0]->NamaTravel ?? null,
            'NamaPaket' => $jamaahpaket[0]->NamaPaket ?? null,
            'tarif' => $jamaahpaket[0]->InfoTagihan ?? null,
            'NoInvoice' => $jamaahpaket[0]->NoInvoice ?? null,
            'IsPaid' => $jamaahpaket[0]->IsPaid ?? null,
            'IDTravel' => $IDTravel,
            'IDPaket' => $IDPaket,
            'IDCabang' => $IDCabang,
            'jamaah' => $jamaahpaket,
            'paket' => $paket,
            'cabang' => $cabang,
            'LevelID' => Auth::user()->LevelID ?? null,
            'bank' => config('constants.BANK'),
        ]);
    }

    /**
     * Simpan pembayaran tagihan
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function simpan(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'NoInvoice' => 'required|string'
            ]);

            $NoInvoice = $validated['NoInvoice'];

            // Update TrKasTravel
            $transaksi = [
                'TanggalTransaksi' => date('Y-m-d'),
                'IDUserTransaksi' => Auth::user()->UserName ?? null,
                'NamaPersonTransaksi' => Auth::user()->NamaLengkap ?? Auth::user()->UserName ?? null,
                'IsVerified' => 1,
            ];

            TrKasTravel::where('NoInvoice', $NoInvoice)->update($transaksi);

            // Get NoTransaksi
            $no = TrKasTravel::where('NoInvoice', $NoInvoice)->first();
            
            if (!$no) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }

            $NoTransaksi = $no->NoTransaksi ?? null;

            // Update semua jamaah dengan NoInvoice tersebut
            $updateData = [
                'IsPaid' => 1,
                'VerifyPaidUser' => Auth::user()->UserName ?? null,
                'VerifyPaidDate' => date('Y-m-d H:i:s'),
                'NoTransaksi' => $NoTransaksi
            ];

            JamaahPaket::where('NoInvoice', $NoInvoice)->update($updateData);

            // Insert log
            $this->ServerLog->insertLog($NoInvoice, 'Bayar', 'Terima Bayar Tagihan Aplikasi');

            return response()->json([
                'status' => true, 
                'message' => 'Tagihan berhasil dibayar'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error simpan pembayaran: ' . $e->getMessage());
            
            return response()->json([
                'status' => false, 
                'message' => 'Terjadi kesalahan saat menyimpan pembayaran'
            ], 500);
        }
    }

    /**
     * Generate dan download invoice PDF
     * 
     * @param Request $request
     * @return BinaryFileResponse|View
     */
    public function invoice(Request $request)
    {
        try {
            $NoInvoice = $request->query('inv');

            if (empty($NoInvoice)) {
                abort(400, 'No Invoice tidak boleh kosong');
            }

            $one_jamaah_paket = JamaahPaket::where('NoInvoice', $NoInvoice)->first();

            if (!$one_jamaah_paket) {
                abort(404, 'Invoice tidak ditemukan');
            }

            $travel = MstTravel::where('IDTravel', $one_jamaah_paket->IDTravel)->first();
            $paket = MstPaket::where('IDPaket', $one_jamaah_paket->IDPaket)->first();
            $jamaah_paket = $this->JamaahPaket->getJoinPerson(['NoInvoice' => $NoInvoice]);

            $jumlah_jamaah = count($jamaah_paket);
            $total = $jumlah_jamaah * ($one_jamaah_paket->InfoTagihan ?? 0);

            $data = [
                'NamaTravel' => $travel->NamaTravel ?? '-',
                'NamaPaket' => $paket->NamaPaket ?? '-',
                'Alamat' => $travel->Alamat ?? '-',
                'InfoTagihan' => number_format($one_jamaah_paket->InfoTagihan ?? 0, 0, ',', '.'),
                'NoInvoice' => $NoInvoice,
                'JamaahPaket' => $jamaah_paket,
                'JumlahJamaah' => $jumlah_jamaah,
                'Total' => number_format($total, 0, ',', '.'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin-page.tagihan.cetak_invoice', $data)
                ->setPaper('A4', 'portrait');

            return $pdf->download($NoInvoice . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error generate invoice: ' . $e->getMessage());
            
            return back()->with('error', 'Terjadi kesalahan saat generate invoice');
        }
    }

    /**
     * Get paket berdasarkan IDTravel
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getpaket(Request $request): JsonResponse
    {
        try {
            $IDTravel = $request->query('id');

            if (empty($IDTravel)) {
                return response()->json([
                    'status' => false,
                    'message' => 'IDTravel tidak boleh kosong'
                ], 400);
            }

            $paket = MstPaket::where(['IDTravel' => $IDTravel, 'IsAktif' => 1])->get();

            return response()->json([
                'status' => true, 
                'data' => $paket
            ]);

        } catch (\Exception $e) {
            Log::error('Error get paket: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data paket'
            ], 500);
        }
    }

    /**
     * Get tagihan berdasarkan NoInvoice
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function gettagihan(Request $request): JsonResponse
    {
        try {
            $NoInvoice = $request->query('inv');

            if (empty($NoInvoice)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No Invoice tidak boleh kosong'
                ], 400);
            }

            $jamaahpaket = $this->JamaahPaket->getJoinAll(['NoInvoice' => $NoInvoice]);

            if (empty($jamaahpaket)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $data = [
                'NamaTravel' => $jamaahpaket[0]->NamaTravel ?? null,
                'NamaPaket' => $jamaahpaket[0]->NamaPaket ?? null,
                'tarif' => $jamaahpaket[0]->InfoTagihan ?? null,
                'NoInvoice' => $jamaahpaket[0]->NoInvoice ?? null,
                'IDTravel' => $jamaahpaket[0]->IDTravel ?? null,
                'IDPaket' => $jamaahpaket[0]->IDPaket ?? null,
                'IsPaid' => $jamaahpaket[0]->IsPaid ?? null,
                'jamaah' => $jamaahpaket,
            ];

            return response()->json([
                'status' => true, 
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error get tagihan: ' . $e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tagihan'
            ], 500);
        }
    }
}