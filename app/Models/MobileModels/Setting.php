<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Setting extends Model
{
    protected $table = 'sistemsetting';
    protected $primaryKey = 'KodeSetting';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'KodeSetting',
        'Jenis',
        'Value',
        'Value1',
        'Keterangan',
    ];

    // Constants
    public const KODE_BIAYA_TRIP = 'BIAYA_TRIP';
    public const KODE_VOUCHER_REFERRAL = 'VOUCHER_REFERRAL';
    public const KODE_VOUCHER_PENDAFTARAN = 'VOUCHER_PENDAFTARAN';

    /**
     * Get Biaya Trip
     */
    public static function getBiayaTrip(): ?array
    {
        return self::where('KodeSetting', self::KODE_BIAYA_TRIP)
            ->first()
                ?->toArray();
    }

    /**
     * Get Voucher settings
     */
    public static function getVoucher(): array
    {
        return self::whereIn('KodeSetting', [
            self::KODE_VOUCHER_REFERRAL,
            self::KODE_VOUCHER_PENDAFTARAN,
        ])
            ->get()
            ->toArray();
    }

    /**
     * Get setting by kode
     */
    public static function getByKode(string $kode): ?array
    {
        return self::where('KodeSetting', $kode)
            ->first()
                ?->toArray();
    }
}
