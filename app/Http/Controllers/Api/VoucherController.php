<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\VoucherPerson;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function __construct(
        protected VoucherPerson $VoucherPerson
    ) {}

    public function list(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $IDPerson = $request->query('IDPerson');

        $result = $this->VoucherPerson->getList($IDPerson, $limit, $page);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data voucher tidak ditemukan'
        ], 404);
    }

    public function detail($IDVoucher)
    {
        if (empty($IDVoucher)) {
            return response()->json([
                'status' => false,
                'message' => 'IDVoucher harus diisi'
            ], 400);
        }

        $result = $this->VoucherPerson->getDetail($IDVoucher);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data voucher tidak ditemukan'
        ], 404);
    }

    public function person(Request $request, $IDPerson)
    {
        if (empty($IDPerson)) {
            return response()->json([
                'status' => false,
                'message' => 'IDPerson harus diisi'
            ], 400);
        }

        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $where = ['IDPerson' => $IDPerson];
        $IsKlaim = 0; // Default to unclaimed
        if ($request->has('IsKlaim')) {
            $IsKlaim = $request->query('IsKlaim');
        }
        $where['IsKlaim'] = $IsKlaim;

        $result = $this->VoucherPerson->getAllVouchers($where, $limit, $page);
        $total = $this->VoucherPerson->countVouchers($where);

        if ($result) {
            return response()->json([
                'status' => true,
                'data' => $result,
                'pagination' => [
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data voucher tidak ditemukan'
        ], 404);
    }

    public function balance($IDPerson)
    {
        if (empty($IDPerson)) {
            return response()->json([
                'status' => false,
                'message' => 'IDPerson harus diisi'
            ], 400);
        }

        $balance = $this->VoucherPerson->getBalance($IDPerson);

        return response()->json([
            'status' => true,
            'data' => [
                'IDPerson' => $IDPerson,
                'Balance' => (int)$balance
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'IDPerson' => 'required',
            'NamaVoucher' => 'required',
        ], [
            'IDPerson.required' => 'Field IDPerson harus diisi',
            'NamaVoucher.required' => 'Field NamaVoucher harus diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $voucherData = [
            'IDPerson' => $data['IDPerson'],
            'NamaVoucher' => $data['NamaVoucher'],
            'Debet' => $data['Debet'] ?? 0,
            'Kredit' => $data['Kredit'] ?? 0,
            'Keterangan' => $data['Keterangan'] ?? null,
            'IDPaket' => $data['IDPaket'] ?? null,
            'IDTravel' => $data['IDTravel'] ?? null,
            'IsKlaim' => $data['IsKlaim'] ?? 0,
        ];

        try {
            $voucher = $this->VoucherPerson->createVoucher($voucherData);

            if ($voucher) {
                return response()->json([
                    'status' => true,
                    'message' => 'Voucher berhasil dibuat',
                    'data' => [
                        'IDVoucher' => $voucher->IDVoucher
                    ]
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Error creating voucher: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat voucher: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => false,
            'message' => 'Gagal membuat voucher'
        ], 500);
    }


    public function update(Request $request, $IDVoucher)
    {
        if (empty($IDVoucher)) {
            return response()->json([
                'status' => false,
                'message' => 'IDVoucher harus diisi'
            ], 400);
        }

        $data = $request->all();

        if (empty($data)) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak valid'
            ], 400);
        }

        $existing = $this->VoucherPerson->getDetail($IDVoucher);
        if (!$existing) {
            return response()->json([
                'status' => false,
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }

        $updateData = [];
        if (isset($data['IDPerson'])) $updateData['IDPerson'] = $data['IDPerson'];
        if (isset($data['Debet'])) $updateData['Debet'] = $data['Debet'];
        if (isset($data['Kredit'])) $updateData['Kredit'] = $data['Kredit'];
        if (isset($data['NamaVoucher'])) $updateData['NamaVoucher'] = $data['NamaVoucher'];
        if (isset($data['Keterangan'])) $updateData['Keterangan'] = $data['Keterangan'];
        if (isset($data['IDPaket'])) $updateData['IDPaket'] = $data['IDPaket'];
        if (isset($data['IDTravel'])) $updateData['IDTravel'] = $data['IDTravel'];
        if (isset($data['IsKlaim'])) $updateData['IsKlaim'] = $data['IsKlaim'];

        try {
            $result = $this->VoucherPerson->updateVoucher($IDVoucher, $updateData);

            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Voucher berhasil diupdate'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengupdate voucher: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => false,
            'message' => 'Gagal mengupdate voucher'
        ], 500);
    }

    public function destroy($IDVoucher)
    {
        if (empty($IDVoucher)) {
            return response()->json([
                'status' => false,
                'message' => 'IDVoucher harus diisi'
            ], 400);
        }

        $existing = $this->VoucherPerson->getDetail($IDVoucher);
        if (!$existing) {
            return response()->json([
                'status' => false,
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }

        try {
            $result = $this->VoucherPerson->deleteVoucher($IDVoucher);

            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Voucher berhasil dihapus'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus voucher: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => false,
            'message' => 'Gagal menghapus voucher'
        ], 500);
    }

    public function klaim($IDVoucher)
    {
        if (empty($IDVoucher)) {
            return response()->json([
                'status' => false,
                'message' => 'IDVoucher harus diisi'
            ], 400);
        }

        $existing = $this->VoucherPerson->getDetail($IDVoucher);
        if (!$existing) {
            return response()->json([
                'status' => false,
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }

        if ($existing->IsKlaim == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Voucher sudah diklaim'
            ], 400);
        }

        try {
            $result = $this->VoucherPerson->klaimVoucher($IDVoucher);

            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Voucher berhasil diklaim'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengklaim voucher: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => false,
            'message' => 'Gagal mengklaim voucher'
        ], 500);
    }

    public function summary(Request $request)
    {
        $where = [];

        $IDPerson = $request->query('IDPerson');
        if ($IDPerson !== null) $where['IDPerson'] = $IDPerson;

        $IDTravel = $request->query('IDTravel');
        if ($IDTravel !== null) $where['IDTravel'] = $IDTravel;

        $IDPaket = $request->query('IDPaket');
        if ($IDPaket !== null) $where['IDPaket'] = $IDPaket;

        $totalVoucher = $this->VoucherPerson->countVouchers($where);
        $totalDebet = $this->VoucherPerson->getTotalDebet($where);
        $totalKredit = $this->VoucherPerson->getTotalKredit($where);
        $balance = $totalDebet - $totalKredit;

        // Count claimed and unclaimed
        $whereClaimed = array_merge($where, ['IsKlaim' => 1]);
        $whereUnclaimed = array_merge($where, ['IsKlaim' => 0]);

        $totalClaimed = $this->VoucherPerson->countVouchers($whereClaimed);
        $totalUnclaimed = $this->VoucherPerson->countVouchers($whereUnclaimed);

        return response()->json([
            'status' => true,
            'data' => [
                'TotalVoucher' => $totalVoucher,
                'TotalDebet' => (float)$totalDebet,
                'TotalKredit' => (float)$totalKredit,
                'Balance' => (float)$balance,
                'TotalClaimed' => $totalClaimed,
                'TotalUnclaimed' => $totalUnclaimed
            ]
        ], 200);
    }
}
