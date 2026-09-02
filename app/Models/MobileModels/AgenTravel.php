<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AgenTravel extends Model
{
    protected $table = 'agentravel';
    protected $primaryKey = ['IDTravel', 'IDPerson'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'IDPerson',
        'TglMulaiAgen',
        'TglAkhirAgen',
        'LevelWilayah',
        'IDProv',
        'IDKab',
        'IDKec',
        'IDDesa',
        'IsAktif',
    ];

    /**
     * Find by Agen
     */
    public static function findByAgen(string $IDPerson): array
    {
        return self::select(
            'agentravel.*',
            'tv.NamaTravel',
            'prov.NamaProv',
            'kab.NamaKab',
            'kec.NamaKec',
            'desa.NamaDesa'
        )
            ->from('agentravel')
            ->join('msttravel as tv', 'tv.IDTravel', '=', 'agentravel.IDTravel')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'agentravel.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDKab', '=', 'agentravel.IDKab')
                    ->on('kab.IDProv', '=', 'agentravel.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDKec', '=', 'agentravel.IDKec')
                    ->on('kec.IDKab', '=', 'agentravel.IDKab')
                    ->on('kec.IDProv', '=', 'agentravel.IDProv');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDDesa', '=', 'agentravel.IDDesa')
                    ->on('desa.IDKec', '=', 'agentravel.IDKec')
                    ->on('desa.IDKab', '=', 'agentravel.IDKab')
                    ->on('desa.IDProv', '=', 'agentravel.IDProv');
            })
            ->where('agentravel.IDPerson', $IDPerson)
            ->whereRaw('(IF(agentravel.TglAkhirAgen IS NULL, NOW() >= agentravel.TglMulaiAgen, NOW() BETWEEN agentravel.TglMulaiAgen AND agentravel.TglAkhirAgen))')
            ->where('agentravel.IsAktif', 1)
            ->get()
            ->toArray();
    }
}
