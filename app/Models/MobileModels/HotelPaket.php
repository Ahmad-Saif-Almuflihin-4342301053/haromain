<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HotelPaket extends Model
{
    protected $table = 'hotelpaket';
    protected $primaryKey = ['IDTravel', 'IDPaket', 'NoUrut'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'IDPaket',
        'NoUrut',
        'NamaHotel',
        'Alamat',
        'Bintang',
        'Gambar',
        'KoorLat',
        'KoorLong',
    ];

    /**
     * Find by paket
     */
    public static function findByPaket(string $IDTravel, string $IDPaket): array
    {
        return self::where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->orderBy('TglMasuk', 'ASC')
            ->get()
            ->toArray();
    }
}
