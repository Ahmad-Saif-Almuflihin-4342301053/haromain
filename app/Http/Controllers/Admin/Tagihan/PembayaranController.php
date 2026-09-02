<?php

namespace App\Http\Controllers\Admin\Tagihan;

use DateTime;
use DateTimeZone;
use GuzzleHttp\Client;
use App\Models\TrKasTravel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    protected $IDTravel;

    public function __construct()
    {

        $this->IDTravel = Auth::user()->IDTravel;
    }

    public function index(Request $request)
    {
        $invoice = base64_decode($request->query('inv'));

        $transaksitravel = (new TrKasTravel)->findJoinTravel([
            'NoInvoice' => $invoice,
            'trkastravel.IsVerified' => 0,
        ]);

        return view('admin-page.tagihan.generate_va', [
            'menu' => 'Tagihan Biaya',
            'transaksitravel' => $transaksitravel,
            'NamaBank' => $this->getNamaBank(optional($transaksitravel)->Bank),
        ]);
    }

    public function generateVirtualAccount(Request $request)
    {
        $invoice = $request->query('inv');
        $bank = $request->query('bank');

        $transaksitravel = (new TrKasTravel)->findJoinTravel([
            'NoInvoice' => $invoice,
            'trkastravel.IsVerified' => 0,
        ]);

        $va = $this->createFVA(
            $transaksitravel->NamaTravel,
            $bank,
            $transaksitravel->Kredit
        );

        if (isset($va['id'])) {
            $expired = new DateTime($va['expiration_date']);
            $expired = $expired->format('Y-m-d H:i:s');

            TrKasTravel::where('NoInvoice', $invoice)->update([
                'IDVA' => $va['id'],
                'ExpiredVA' => $expired,
                'Bank' => $va['bank_code'],
                'NoVa' => $va['account_number'],
            ]);

            return response()->json([
                'status' => 'success',
                'redirect' => url('admin/tagihan/Pembayaran?inv=' . base64_encode($invoice)),
                'data' => $va,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mendapatkan data',
        ]);
    }

    private function createFVA($nama, $bank, $nominalbayar)
    {
        date_default_timezone_set('Asia/Jakarta');

        $expired_date = new DateTime();
        $expired_date->modify('+1 day');
        $expired_date->setTimezone(new DateTimeZone('UTC'));

        $payload = [
            'external_id' => 'VA_fixed-' . time(),
            'bank_code' => $bank,
            'name' => $nama,
            'is_closed' => true,
            'expected_amount' => (int) $nominalbayar,
            'expiration_date' => $expired_date->format(DateTime::ISO8601),
            'is_single_use' => true,
        ];

        $client = new Client([
            'base_uri' => 'https://api.xendit.co/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => config('constants.AUTH_XENDIT'),
            ],
        ]);

        $response = $client->post('callback_virtual_accounts', [
            'json' => $payload,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    private function getNamaBank($kodeBank)
    {
        foreach (config('constants.BANK') as $val) {
            if ($val['code'] === $kodeBank) {
                return $val['name'];
            }
        }
    }
}
