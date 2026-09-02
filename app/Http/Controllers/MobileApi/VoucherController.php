<?php

namespace App\Http\Controllers\MobileApi;

use App\Models\MobileModels\VoucherPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class VoucherController extends BaseApiController
{
    /**
     * GET /api/voucher/list
     * Get list vouchers
     */
    public function list(Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);
        $offset = ($page - 1) * $limit;

        $IDPerson = $this->sanitize($request->query('IDPerson'));

        if (!$IDPerson) {
            return $this->validationError('IDPerson harus diisi');
        }

        $result = VoucherPerson::getList($IDPerson, $limit, $offset);

        if (!empty($result)) {
            return $this->success($result);
        }

        return $this->notFound('Data voucher tidak ditemukan');
    }

    /**
     * GET /api/voucher/detail/{IDVoucher}
     * Get voucher by ID
     */
    public function detail(int $IDVoucher): JsonResponse
    {
        $result = VoucherPerson::find($IDVoucher);

        if ($result) {
            return $this->success($result->toArray());
        }

        return $this->notFound('Data voucher tidak ditemukan');
    }

    /**
     * GET /api/voucher/person/{IDPerson}
     * Get vouchers by person
     */
    public function person(string $IDPerson, Request $request): JsonResponse
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);
        $offset = ($page - 1) * $limit;

        $where = ['IDPerson' => $IDPerson];

        // Default to unclaimed vouchers
        $where['IsKlaim'] = 0;

        $result = VoucherPerson::getAll($where, $limit, $offset);
        $total = VoucherPerson::countVouchers($where);

        if (!empty($result)) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit),
                ],
            ]);
        }

        return $this->notFound('Data voucher tidak ditemukan');
    }

    /**
     * GET /api/voucher/balance/{IDPerson}
     * Get balance for person
     */
    public function balance(string $IDPerson): JsonResponse
    {
        $balance = VoucherPerson::getBalance($IDPerson);

        return $this->success([
            'IDPerson' => $IDPerson,
            'Balance' => $balance,
        ]);
    }

    /**
     * POST /api/voucher/create
     * Create new voucher
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'IDPerson' => 'required',
            'NamaVoucher' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->first());
        }

        $data = [
            'IDPerson' => $this->sanitize($request->input('IDPerson')),
            'NamaVoucher' => $this->sanitize($request->input('NamaVoucher')),
            'Debet' => (int) $request->input('Debet', 0),
            'Kredit' => (int) $request->input('Kredit', 0),
            'Keterangan' => $this->sanitize($request->input('Keterangan')),
            'IDPaket' => $this->sanitize($request->input('IDPaket')),
            'IDTravel' => $this->sanitize($request->input('IDTravel')),
            'IsKlaim' => (int) $request->input('IsKlaim', 0),
        ];

        $created = VoucherPerson::createVoucher($data);

        if ($created) {
            return $this->success(null, 'Voucher berhasil dibuat', 201);
        }

        return $this->error('Voucher gagal dibuat', 500);
    }

    /**
     * PUT /api/voucher/claim/{IDVoucher}
     * Claim voucher
     */
    public function claim(int $IDVoucher): JsonResponse
    {
        $claimed = VoucherPerson::claimVoucher($IDVoucher);

        if ($claimed) {
            return $this->success(null, 'Voucher berhasil diklaim');
        }

        return $this->validationError('Voucher gagal diklaim');
    }
}
