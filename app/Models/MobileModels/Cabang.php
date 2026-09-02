<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cabang extends Model
{
    protected $table = 'mstcabang';
    protected $primaryKey = 'IDCabang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDCabang',
        'IDTravel',
        'NamaCabang',
        'Alamat',
        'IDProv',
        'IDKab',
        'IDKec',
        'IDDesa',
        'IsAktif',
    ];

    /**
     * Find by Travel
     */
    public static function findByTravel(string $IDTravel): array
    {
        return DB::select("
            SELECT c.*, prov.NamaProv, kab.NamaKab, kec.NamaKec, desa.NamaDesa
            FROM mstcabang c
            LEFT JOIN mstprov prov ON prov.IDProv=c.IDProv
            LEFT JOIN mstkab kab ON (kab.IDProv, kab.IDKab)=(c.IDProv, c.IDKab)
            LEFT JOIN mstkec kec ON (kec.IDProv, kec.IDKab, kec.IDKec)=(c.IDProv, c.IDKab, c.IDKec)
            LEFT JOIN mstdesa desa ON (desa.IDProv, desa.IDKab, desa.IDKec, desa.IDDesa)=(c.IDProv, c.IDKab, c.IDKec, c.IDDesa)
            WHERE c.IsAktif=1 AND c.IDTravel LIKE ?
        ", [$IDTravel]);
    }

    /**
     * Find agen travel by cabang
     */
    public static function findAgenTravel(string $IDTravel, ?string $IDCabang = null): array
    {
        $params = [$IDTravel];

        $sql = "SELECT agt.*, p.NamaJamaah AS NamaAgen, prov.NamaProv, kab.NamaKab, kec.NamaKec, desa.NamaDesa
            FROM agentravel agt
            INNER JOIN mstperson p ON p.IDPerson=agt.IDPerson
            LEFT JOIN mstprov prov ON prov.IDProv=agt.IDProv
            LEFT JOIN mstkab kab ON kab.IDKab=agt.IDKab AND kab.IDProv=agt.IDProv
            LEFT JOIN mstkec kec ON kec.IDKec=agt.IDKec AND kec.IDKab=agt.IDKab AND kec.IDProv=agt.IDProv
            LEFT JOIN mstdesa desa ON desa.IDDesa=agt.IDDesa AND desa.IDKec=agt.IDKec AND desa.IDKab=agt.IDKab AND desa.IDProv=agt.IDProv
            LEFT JOIN mstcabang c ON c.IDTravel=agt.IDTravel
            WHERE agt.IDTravel = ? AND (IF(agt.TglMulaiAgen IS NOT NULL AND agt.TglAkhirAgen IS NOT NULL, (NOW() BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen), NOW() >= agt.TglMulaiAgen)) AND agt.IsAktif = 1";

        if ($IDCabang !== null) {
            $sql = "SELECT agt.*, p.NamaJamaah AS NamaAgen, prov.NamaProv, kab.NamaKab, kec.NamaKec, desa.NamaDesa
                FROM agentravel agt
                INNER JOIN mstperson p ON p.IDPerson=agt.IDPerson
                LEFT JOIN mstprov prov ON prov.IDProv=agt.IDProv
                LEFT JOIN mstkab kab ON kab.IDKab=agt.IDKab AND kab.IDProv=agt.IDProv
                LEFT JOIN mstkec kec ON kec.IDKec=agt.IDKec AND kec.IDKab=agt.IDKab AND kec.IDProv=agt.IDProv
                LEFT JOIN mstdesa desa ON desa.IDDesa=agt.IDDesa AND desa.IDKec=agt.IDKec AND desa.IDKab=agt.IDKab AND desa.IDProv=agt.IDProv
                LEFT JOIN mstcabang c ON c.IDTravel=agt.IDTravel
                WHERE agt.IDTravel = ? AND (IF(agt.TglMulaiAgen IS NOT NULL AND agt.TglAkhirAgen IS NOT NULL, (NOW() BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen), NOW() >= agt.TglMulaiAgen)) AND agt.IsAktif = 1 AND c.IDCabang=? AND IF(agt.LevelWilayah='Desa', ((c.IDProv, c.IDKab, c.IDKec, c.IDDesa)=(agt.IDProv, agt.IDKab, agt.IDKec, agt.IDDesa)),
                    IF(agt.LevelWilayah='Kecamatan', ((c.IDProv, c.IDKab, c.IDKec)=(agt.IDProv, agt.IDKab, agt.IDKec)),
                        IF(agt.LevelWilayah='Kabupaten', ((c.IDProv, c.IDKab)=(agt.IDProv, agt.IDKab)),
                            IF(agt.LevelWilayah='Provinsi', ((c.IDProv)=(agt.IDProv)),
                                TRUE))))";
            $params = [$IDTravel, $IDCabang];
        }

        return DB::select($sql, $params);
    }
}
