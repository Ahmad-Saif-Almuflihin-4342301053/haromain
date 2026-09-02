<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JamaahPaket extends Model
{
    protected $table = 'jamaahpaket';
    protected $primaryKey = ['IDTravel', 'IDPaket', 'IDPerson'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'IDPaket',
        'IDPerson',
        'IDPersonAgen',
        'IDCabang',
        'TanggalBooking',
        'InfoTagihan',
        'InfoTerbayar',
        'InfoSisaTagihan',
        'NominalPaid',
        'StatusJamaah',
        'IsPaid',
        'VerifyPaidDate',
        'JumlahTrip',
        'NoInvoice',
        'NoTransaksi',
        'IsAlert',
        'TglAwalTrack',
        'TglAkhirTrack',
        'RatingTestimoni',
        'NarasiTestimoni',
        'TanggalTestimoni',
    ];

    // Constants
    public const STATUS_JAMAAH = 'Jamaah';
    public const STATUS_LEADER = 'Leader';

    /**
     * Base query dengan join
     */
    private static function baseQuery(?string $cari = null, ?int $limit = null, ?int $page = null)
    {
        $query = self::select(
            'jp.*',
            'pk.NamaPaket',
            'pk.KotaTujuan',
            'tv.NamaTravel',
            'pk.TglBerangkat',
            'pk.TglMaxBayar',
            'pk.TglMaxBooking',
            'pk.TglPulang',
            'pk.TglMaxDokumen',
            'pj.NamaJamaah',
            'pj.FotoProfil as FotoProfilJamaah',
            'pj.Alamat',
            'pj.NoHP as NoHPJamaah',
            'pa.NamaJamaah as NamaAgen',
            DB::raw("(SELECT IF(COUNT(sp.NoUrutSyarat) = COUNT(ds.NoUrutSyarat), 1, 0) FROM syaratpaket sp LEFT JOIN dokumensyarat ds ON ds.IDTravel=sp.IDTravel AND ds.IDPaket=sp.IDPaket AND ds.NoUrutSyarat=sp.NoUrutSyarat WHERE sp.IDTravel=jp.IDTravel AND sp.IDPaket=jp.IDPaket) AS IsSyaratLengkap"),
            DB::raw("NOW() AS ThisTime"),
            'pk.Gambar1',
            'pk.Gambar2',
            'pk.Gambar3',
            'pk.Gambar4',
            'pk.Gambar5',
            'prov.IDProv',
            'prov.NamaProv',
            'kab.IDKab',
            'kab.NamaKab',
            'kec.IDKec',
            'kec.NamaKec',
            'desa.IDDesa',
            'desa.NamaDesa'
        )
            ->from('jamaahpaket as jp')
            ->join('mstpaket as pk', function ($join) {
                $join->on('pk.IDTravel', '=', 'jp.IDTravel')
                    ->on('pk.IDPaket', '=', 'jp.IDPaket');
            })
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->join('mstperson as pj', 'pj.IDPerson', '=', 'jp.IDPerson')
            ->leftJoin('mstperson as pa', 'pa.IDPerson', '=', 'jp.IDPersonAgen')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'pj.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDProv', '=', 'pj.IDProv')
                    ->on('kab.IDKab', '=', 'pj.IDKab');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDProv', '=', 'pj.IDProv')
                    ->on('kec.IDKab', '=', 'pj.IDKab')
                    ->on('kec.IDKec', '=', 'pj.IDKec');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDProv', '=', 'pj.IDProv')
                    ->on('desa.IDKab', '=', 'pj.IDKab')
                    ->on('desa.IDKec', '=', 'pj.IDKec')
                    ->on('desa.IDDesa', '=', 'pj.IDDesa');
            });

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('jp.Keterangan', 'like', "%{$cari}%")
                    ->orWhere('pk.Keterangan', 'like', "%{$cari}%")
                    ->orWhere('pk.Deskripsi', 'like', "%{$cari}%")
                    ->orWhere('pk.NarasiPaket', 'like', "%{$cari}%")
                    ->orWhere('tv.NamaTravel', 'like', "%{$cari}%");
            });
        }

        if ($limit && $limit > 0) {
            $currentPage = max($page ?? 0, 0);
            $offset = $currentPage * $limit;

            $query->limit($limit)->offset($offset);
        }
        $query->where('tv.IsVerified', 1);
        $query->where('pk.IsAktif', 1);

        return $query;
    }

    /**
     * Get detail jamaah paket
     */
    public static function getDetail(string $IDTravel, string $IDPaket, string $IDPerson): ?array
    {
        return self::baseQuery()
            ->where('jp.IDTravel', $IDTravel)
            ->where('jp.IDPaket', $IDPaket)
            ->where('jp.IDPerson', $IDPerson)
            ->first()
            ?->toArray();
    }

    /**
     * Find all paid jamaah
     */
    public static function findAllPaidJamaah(string $IDTravel, string $IDPaket, ?int $limit = null): array
    {
        return self::baseQuery(null, $limit)
            ->whereRaw("(pk.TglPulang IS NULL OR DATE(pk.TglPulang) >= CURDATE())")
            ->where('jp.IsPaid', 1)
            ->where('jp.IDTravel', $IDTravel)
            ->where('jp.IDPaket', $IDPaket)
            ->get()
            ->toArray();
    }

    /**
     * Find paid jamaah by person
     */
    public static function findPaidJamaah(string $IDPerson): array
    {
        return self::baseQuery()
            ->whereRaw("(pk.TglPulang IS NULL OR DATE(pk.TglPulang) >= CURDATE())")
            ->where('jp.IsPaid', 1)
            ->where('jp.IDPerson', $IDPerson)
            ->orderByRaw("CASE WHEN CURDATE() BETWEEN pk.TglBerangkat AND pk.TglPulang THEN 0 ELSE 1 END")
            ->orderByRaw("ABS(DATEDIFF(pk.TglBerangkat, CURDATE())) ASC")
            ->get()
            ->toArray();
    }

    /**
     * Find by paket
     */
    public static function findByPaket(string $IDTravel, string $IDPaket, ?string $cari = null, ?int $limit = null, ?int $page = null): array
    {
        return self::baseQuery($cari, $limit, $page)
            ->where('jp.IDTravel', $IDTravel)
            ->where('jp.IDPaket', $IDPaket)
            ->get()
            ->toArray();
    }

    /**
     * Find by person
     */
    public static function findByPerson(string $IDPerson, ?string $cari = null, ?int $limit = null, ?int $page = null): array
    {
        return self::baseQuery($cari, $limit, $page)
            ->where('jp.IDPerson', $IDPerson)
            ->whereRaw("DATE(pk.TglPulang) >= ?", [date('Y-m-d')])
            ->get()
            ->toArray();
    }

    /**
     * Find history by person
     */
    public static function findHistoryPerson(string $IDPerson, ?string $cari = null, ?int $limit = null, ?int $page = null): array
    {
        return self::baseQuery($cari, $limit, $page)
            ->whereRaw("((pk.TglBerangkat IS NOT NULL AND DATE(pk.TglBerangkat) < CURDATE()) AND (pk.TglPulang IS NOT NULL AND DATE(pk.TglPulang) >= CURDATE()))")
            ->where('jp.IDPerson', $IDPerson)
            ->get()
            ->toArray();
    }

    /**
     * Find history by agen
     */
    public static function findHistoryAgen(string $IDPerson, ?string $cari = null, ?int $limit = null, ?int $page = null): array
    {
        return self::baseQuery($cari, $limit, $page)
            ->whereRaw("((pk.TglBerangkat IS NOT NULL AND DATE(pk.TglBerangkat) < CURDATE()) AND (pk.TglPulang IS NOT NULL AND DATE(pk.TglPulang) <= CURDATE()))")
            ->where('jp.IDPersonAgen', $IDPerson)
            ->get()
            ->toArray();
    }

    /**
     * Cek person terdaftar di paket
     */
    public static function cekPersonTerdaftar(string $IDTravel, string $IDPaket, string $IDPerson): array
    {
        return self::baseQuery()
            ->where('jp.IDTravel', $IDTravel)
            ->where('jp.IDPaket', $IDPaket)
            ->where('jp.IDPerson', $IDPerson)
            ->get()
            ->toArray();
    }

    /**
     * Find peserta paket
     */
    public static function findPeserta(string $IDTravel, string $IDPaket): array
    {
        return self::baseQuery()
            ->where('jp.IDTravel', $IDTravel)
            ->where('jp.IDPaket', $IDPaket)
            ->get()
            ->toArray();
    }

    /**
     * Get dokumen syarat
     */
    public static function getDokumenSyarat(string $IDTravel, string $IDPaket, string $IDPerson): array
    {
        return DB::select("
            SELECT sp.*, ds.Keterangan AS KeteranganUpload, ds.FileDokumen, ds.TglUpload, ds.IDPerson, p.NamaJamaah
            FROM syaratpaket sp
            LEFT JOIN dokumensyarat ds ON ds.IDTravel=sp.IDTravel AND ds.IDPaket=sp.IDPaket AND ds.NoUrutSyarat=sp.NoUrutSyarat
            LEFT JOIN mstperson p ON p.IDPerson=ds.IDPerson
            WHERE sp.IDTravel=? AND sp.IDPaket=? AND ds.IDPerson=?
        ", [$IDTravel, $IDPaket, $IDPerson]);
    }

    /**
     * Find dokumen
     */
    public static function findDokumen(string $IDTravel, string $IDPaket, string $IDPerson, int $NoUrutSyarat): ?object
    {
        return DB::table('dokumensyarat')
            ->where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->where('IDPerson', $IDPerson)
            ->where('NoUrutSyarat', $NoUrutSyarat)
            ->first();
    }

    /**
     * Ganti dokumen
     */
    public static function gantiDokumen(string $IDTravel, string $IDPaket, string $IDPerson, int $NoUrutSyarat, string $FileDokumen, ?string $Keterangan = null): bool
    {
        $updateData = ['FileDokumen' => $FileDokumen];
        if ($Keterangan !== null) {
            $updateData['Keterangan'] = $Keterangan;
        }

        return DB::table('dokumensyarat')
            ->where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->where('IDPerson', $IDPerson)
            ->where('NoUrutSyarat', $NoUrutSyarat)
            ->update($updateData) > 0;
    }

    /**
     * Update testimoni
     */
    public static function updateTestimoni(string $IDTravel, string $IDPaket, string $IDPerson, ?string $NarasiTestimoni, ?int $RatingTestimoni, ?string $TanggalTestimoni): bool
    {
        return self::where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->where('IDPerson', $IDPerson)
            ->update([
                'NarasiTestimoni' => $NarasiTestimoni,
                'RatingTestimoni' => $RatingTestimoni,
                'TanggalTestimoni' => $TanggalTestimoni,
            ]) > 0;
    }

    /**
     * Get average testimoni
     */
    public static function getAvgTestimoni(string $IDTravel): array
    {
        $result = DB::table('jamaahpaket')
            ->selectRaw('AVG(RatingTestimoni) as avgRating, COUNT(*) as jmlJamaah, COUNT(RatingTestimoni) as jmlReviewer')
            ->where('IDTravel', $IDTravel)
            ->first();

        return [
            'avgRating' => $result->avgRating ?? 0,
            'jmlJamaah' => $result->jmlJamaah ?? 0,
            'jmlReviewer' => $result->jmlReviewer ?? 0,
        ];
    }

    /**
     * List testimoni
     */
    public static function listTestimoni(array $where, int $limit = 20, int $offset = 0): array
    {
        $query = self::select(
            'jamaahpaket.*',
            'pj.NamaJamaah',
            'pj.FotoProfil'
        )
            ->from('jamaahpaket')
            ->join('mstperson as pj', 'pj.IDPerson', '=', 'jamaahpaket.IDPerson')
            ->whereNotNull('jamaahpaket.RatingTestimoni')
            ->where($where)
            ->limit($limit)
            ->offset($offset);

        return $query->get()->toArray();
    }

    /**
     * Find jamaah di paket (untuk cek leader)
     */
    public static function findJamaahPaket(string $IDTravel, string $IDPaket, string $IDPerson): ?array
    {
        return self::where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->where('IDPerson', $IDPerson)
            ->first()
            ?->toArray();
    }
}
