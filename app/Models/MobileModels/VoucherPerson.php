<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\VoucherPerson as VoucherPersonModel;

class VoucherPerson extends Model
{
    protected $table = 'voucherperson';
    protected $primaryKey = 'IDVoucher';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDVoucher',
        'IDPerson',
        'IDTravel',
        'IDPaket',
        'Debet',
        'Kredit',
        'NamaVoucher',
        'Keterangan',
        'IsKlaim',
        // 'TglVoucher',
    ];

    /**
     * Get list vouchers
     */
    public static function getList(string $IDPerson, int $limit = 10, int $offset = 0): array
    {
        return self::where('IDPerson', $IDPerson)
            // ->orderBy('TglVoucher', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }

    /**
     * Get all vouchers with filter
     */
    public static function getAll(array $where, int $limit = 10, int $offset = 0): array
    {
        return self::where($where)
            // ->orderBy('TglVoucher', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }

    /**
     * Count vouchers
     */
    public static function countVouchers(array $where): int
    {
        return self::where($where)->count();
    }

    /**
     * Get balance
     */
    public static function getBalance(string $IDPerson): int
    {
        $result = self::selectRaw('COALESCE(SUM(Debet), 0) - COALESCE(SUM(Kredit), 0) as balance')
            ->where('IDPerson', $IDPerson)
            ->where('IsKlaim', 0)
            ->first();

        return (int) ($result->balance ?? 0);
    }

    /**
     * Create voucher
     */
    public static function createVoucher(array $data): bool
    {
        // $data['TglVoucher'] = now();
        // return self::insert($data);
        return (new VoucherPersonModel)->createVoucher($data);
    }

    /**
     * Claim voucher
     */
    public static function claimVoucher(int $IDVoucher): bool
    {
        return self::where('IDVoucher', $IDVoucher)
            ->update(['IsKlaim' => 1]) > 0;
    }
}
