<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Travel extends Model
{
    protected $table = 'msttravel';
    protected $primaryKey = 'IDTravel';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'NamaTravel',
        'NamaPemilik',
        'Alamat',
        'NoTelpPemilik',
        'NoPPIU',
        'TglBerlakuPPIU',
        'TglRegister',
        'IsVerified',
        'StatusAkun',
        'IsShow',
        'IDProv',
        'IDKab',
        'IDKec',
        'IDDesa',
        'Logo',
        'TelpCP',
        'NamaCP',
    ];

    /**
     * Query Builder untuk pencarian travel
     */
    private static function baseQuery(?string $cari = null, ?int $limit = null, ?int $page = null)
    {
        $query = self::select(
            'tv.*',
            'desa.NamaDesa',
            'kec.NamaKec',
            'kab.NamaKab',
            'prov.NamaProv'
        )
            ->from('msttravel as tv')
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDDesa', '=', 'tv.IDDesa')
                    ->on('desa.IDKec', '=', 'tv.IDKec')
                    ->on('desa.IDKab', '=', 'tv.IDKab')
                    ->on('desa.IDProv', '=', 'tv.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDKec', '=', 'tv.IDKec')
                    ->on('kec.IDKab', '=', 'tv.IDKab')
                    ->on('kec.IDProv', '=', 'tv.IDProv');
            })
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDKab', '=', 'tv.IDKab')
                    ->on('kab.IDProv', '=', 'tv.IDProv');
            })
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'tv.IDProv');

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('tv.NamaTravel', 'like', "%{$cari}%")
                    ->orWhere('tv.NamaPemilik', 'like', "%{$cari}%")
                    ->orWhere('tv.Alamat', 'like', "%{$cari}%")
                    ->orWhere('desa.NamaDesa', 'like', "%{$cari}%")
                    ->orWhere('kec.NamaKec', 'like', "%{$cari}%")
                    ->orWhere('kab.NamaKab', 'like', "%{$cari}%")
                    ->orWhere('prov.NamaProv', 'like', "%{$cari}%");
            });
        }

        if ($limit && $limit > 0) {
            $currentPage = max($page ?? 0, 0);
            $offset = $currentPage * $limit;

            $query->limit($limit)->offset($offset);
        }

        return $query;
    }

    /**
     * Get detail travel
     */
    public static function getDetail(string $IDTravel): ?array
    {
        return self::baseQuery()
            ->where('tv.IDTravel', $IDTravel)
            ->groupBy('tv.IDTravel')
            ->first()
                ?->toArray();
    }

    /**
     * Find all travel aktif
     */
    public static function findAllTravel(?string $cari = null, ?int $limit = null, ?int $page = null): array
    {
        return self::baseQuery($cari, $limit, $page)
            ->addSelect('at.LevelWilayah', 'at.IDProv as IDProvAgen', 'at.IDKab as IDKabAgen', 'at.IDKec as IDKecAgen', 'at.IDDesa as IDDesaAgen')
            ->join('agentravel as at', function ($join) {
                $join->on('at.IDTravel', '=', 'tv.IDTravel')
                    ->whereRaw('(NOW() BETWEEN at.TglMulaiAgen AND IFNULL(at.TglAkhirAgen, NOW()))')
                    ->where('at.IsAktif', 1);
            })
            ->whereRaw('DATE(tv.TglBerlakuPPIU) >= NOW()')
            ->where('tv.IsVerified', 1)
            ->where('tv.StatusAkun', 'Aktif')
            ->where('tv.IsShow', 1)
            ->groupBy('tv.IDTravel')
            ->get()
            ->toArray();
    }

    /**
     * Find by Agen Travel
     */
    public static function findByAgenTravel(string $IDPerson, ?string $cari = null, ?int $limit = null, ?int $page = null): array
    {
        return self::baseQuery($cari, $limit, $page)
            ->addSelect('at.LevelWilayah', 'at.IDProv as IDProvAgen', 'at.IDKab as IDKabAgen', 'at.IDKec as IDKecAgen', 'at.IDDesa as IDDesaAgen')
            ->join('agentravel as at', function ($join) use ($IDPerson) {
                $join->on('at.IDTravel', '=', 'tv.IDTravel')
                    ->whereRaw('(NOW() BETWEEN at.TglMulaiAgen AND IFNULL(at.TglAkhirAgen, NOW()))')
                    ->where('at.IsAktif', 1)
                    ->where('at.IDPerson', $IDPerson);
            })
            ->whereRaw('DATE(tv.TglBerlakuPPIU) >= NOW()')
            ->where('tv.IsVerified', 1)
            ->where('tv.StatusAkun', 'Aktif')
            ->where('tv.IsShow', 1)
            ->groupBy('tv.IDTravel')
            ->get()
            ->toArray();
    }

    /**
     * Generate new ID
     */
    public static function generateID(): string
    {
        $lastRow = self::selectRaw('MAX(IDTravel) as IDTravel')->first();
        $lastID = $lastRow?->IDTravel;

        if (!$lastID) {
            return 'TRV-0000001';
        }

        $lastDigit = substr($lastID, -7);
        $newLastDigit = str_pad((int) $lastDigit + 1, 7, '0', STR_PAD_LEFT);
        return substr_replace($lastID, $newLastDigit, -7);
    }
}
