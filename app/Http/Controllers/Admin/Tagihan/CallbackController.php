<?php

namespace App\Http\Controllers\Admin\Tagihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trkastravel;
use App\Models\Jamaahpaket;

class CallbackController extends Controller
{
    public function terbayar(Request $request)
    {
        $headers = array_change_key_case($request->headers->all(), CASE_LOWER);

        if (($headers['x-callback-token'][0] ?? null) !== env('X_CALLBACK_TOKEN')) {
            return response('Access Denied', 403);
        }

        $data = json_decode($request->getContent());

        if ($data) {
            Trkastravel::where('IDVA', $data->id)->update([
                'IsVerified' => 1,
                'TanggalTransaksi' => now(),
            ]);

            $transaksi = Trkastravel::where('IDVA', $data->id)->first();

            if ($transaksi) {
                $jamaah = Jamaahpaket::where('NoInvoice', $transaksi->NoInvoice)->get();

                foreach ($jamaah as $value) {
                    Jamaahpaket::where([
                        'NoInvoice' => $transaksi->NoInvoice,
                        'IDPerson'  => $value->IDPerson,
                    ])->update([
                        'IsPaid'         => 1,
                        'VerifyPaidDate' => now(),
                        'NoTransaksi'    => $transaksi->NoTransaksi,
                    ]);
                }

                return response()->json($transaksi);
            }
        }
    }

    public function update(Request $request)
    {
        $headers = array_change_key_case($request->headers->all(), CASE_LOWER);

        if (($headers['x-callback-token'][0] ?? null) !== env('X_CALLBACK_TOKEN')) {
            return response('Access Denied', 403);
        }

        $data = json_decode($request->getContent());

        if ($data) {
            switch ($data->status) {
                case 'PENDING':
                    return response('pending');
                case 'ACTIVE':
                    return response('active');
                case 'INACTIVE':
                    $result = Trkastravel::where('IDVA', $data->id)->update([
                        'IDVA'      => null,
                        'ExpiredVA' => null,
                        'Bank'      => null,
                        'NoVa'      => null,
                    ]);
                    return response()->json($result);
            }
        }
    }
}
