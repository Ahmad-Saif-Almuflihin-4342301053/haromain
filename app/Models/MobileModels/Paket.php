<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Paket extends Model
{
    protected $table = 'mstpaket';
    protected $primaryKey = ['IDTravel', 'IDPaket'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'IDPaket',
        'TglBerangkat',
        'LamaHari',
        'BiayaQuad',
        'TglMaxBayar',
        'TglMaxBooking',
        'Keterangan',
        'KuotaPaket',
        'Deskripsi',
        'Gambar1',
        'Gambar2',
        'Gambar3',
        'Gambar4',
        'Gambar5',
        'NamaPaket',
        'KotaTujuan',
        'NarasiPaket',
        'BiayaTriple',
        'BiayaDouble',
        'TglPulang',
        'KategoriPaket',
        'TglMaxDokumen',
        'IncludeFasilitas',
        'ExcludeFasilitas',
        'IsAktif',
        'BerangkatDari',
        'LandingDi',
        'Maskapai',
        'JenisPenerbangan',
        'Kategori'
    ];

    // Konstanta untuk Setting
    public const KODE_BIAYA_TRIP = 'BIAYA_TRIP';

    /**
     * Base query builder
     */
    private static function baseQuery(
        ?string $IDTravel = null,
        ?string $KotaTujuan = null,
        ?string $TglBerangkat = null,
        ?string $KategoriPaket = null,
        ?string $cari = null,
        ?int $limit = null,
        ?int $page = null
    ) {
        $query = self::select(
            'pk.*',
            'tv.NamaTravel',
            DB::raw("IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook"),
            DB::raw("(SELECT COUNT(*) FROM jamaahpaket jp WHERE jp.IDTravel=pk.IDTravel AND jp.IDPaket=pk.IDPaket AND jp.StatusJamaah IN ('Jamaah', 'Leader')) AS JumlahPeserta"),
            'bt.Value as BiayaTrip',
            'tv.TelpCP',
            'tv.NamaCP'
        )
            ->from('mstpaket as pk')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('(NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir)');
            })
            ->leftJoin('sistemsetting as bt', 'bt.KodeSetting', '=', DB::raw("'" . self::KODE_BIAYA_TRIP . "'"));

        if ($IDTravel) {
            $query->where('pk.IDTravel', $IDTravel);
        }

        if ($KotaTujuan) {
            $query->where('pk.KotaTujuan', 'like', "%{$KotaTujuan}%");
        }

        if ($TglBerangkat) {
            $query->whereDate('pk.TglBerangkat', $TglBerangkat);
        }

        if ($KategoriPaket) {
            $query->where('pk.KategoriPaket', $KategoriPaket);
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('pk.Keterangan', 'like', "%{$cari}%")
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

        return $query->orderBy('pk.TglBerangkat', 'ASC');
    }

    /**
     * Get paket detail
     */
    public static function getDetail(string $IDTravel, string $IDPaket): ?array
    {
        return self::baseQuery()
            ->where('pk.IDPaket', $IDPaket)
            ->where('pk.IDTravel', $IDTravel)
            ->first()
            ?->toArray();
    }

    /**
     * Find katalog paket
     */
    public static function findKatalog(
        ?string $IDTravel = null,
        ?string $KotaTujuan = null,
        ?string $TglBerangkat = null,
        ?string $KategoriPaket = null,
        ?string $cari = null,
        ?int $limit = null,
        ?int $page = null
    ): array {
        return self::baseQuery($IDTravel, $KotaTujuan, $TglBerangkat, $KategoriPaket, $cari, $limit, $page)
            ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.IsShow = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("((pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE()) AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())) AND pk.IsAktif = 1")
            ->get()
            ->toArray();
    }

    /**
     * Find by Agen
     */
    public static function findByAgen(
        string $IDPerson,
        bool $isKatalog = false,
        ?string $IDTravel = null,
        ?string $KotaTujuan = null,
        ?string $TglBerangkat = null,
        ?string $KategoriPaket = null,
        ?string $cari = null,
        ?int $limit = null,
        ?int $page = null
    ): ?array {
        // Get agen travel IDs
        $agenTravel = DB::table('agentravel as agt')
            ->select('agt.IDTravel')
            ->whereRaw('(IF(agt.TglAkhirAgen IS NULL, NOW() >= agt.TglMulaiAgen, NOW() BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen))')
            ->where('agt.IsAktif', 1)
            ->where('agt.IDPerson', $IDPerson)
            ->pluck('IDTravel')
            ->toArray();

        if (empty($agenTravel)) {
            return null;
        }

        $query = self::baseQuery($IDTravel, $KotaTujuan, $TglBerangkat, $KategoriPaket, $cari, $limit, $page)
            ->whereIn('pk.IDTravel', $agenTravel);

        if ($isKatalog) {
            $query->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.IsShow = 1 AND tv.StatusAkun = 'Aktif')")
                ->whereRaw("((pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE()) AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())) AND pk.IsAktif = 1");
        }

        return $query->get()->toArray();
    }

    /**
     * Find Agen Monitoring
     */
    public static function findAgenMonitoring(
        string $IDPerson,
        ?string $IDTravel = null,
        ?string $KotaTujuan = null,
        ?string $TglBerangkat = null,
        ?string $KategoriPaket = null,
        ?string $cari = null,
        ?int $limit = null,
        ?int $page = null
    ): ?array {
        $agenTravel = DB::table('agentravel as at')
            ->select('at.IDTravel')
            ->whereRaw('(IF(at.TglAkhirAgen IS NULL, NOW() >= at.TglMulaiAgen, NOW() BETWEEN at.TglMulaiAgen AND at.TglAkhirAgen))')
            ->where('at.IsAktif', 1)
            ->where('at.IDPerson', $IDPerson)
            ->pluck('IDTravel')
            ->toArray();

        if (empty($agenTravel)) {
            return null;
        }

        return self::baseQuery($IDTravel, $KotaTujuan, $TglBerangkat, $KategoriPaket, $cari, $limit, $page)
            ->whereIn('pk.IDTravel', $agenTravel)
            ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("(pk.TglBerangkat IS NOT NULL AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE()))")
            ->get()
            ->toArray();
    }

    /**
     * Find Agen History
     */
    public static function findAgenHistory(
        string $IDPerson,
        ?string $IDTravel = null,
        ?string $KotaTujuan = null,
        ?string $TglBerangkat = null,
        ?string $KategoriPaket = null,
        ?string $cari = null,
        ?int $limit = null,
        ?int $page = null
    ): ?array {
        $agenTravel = DB::table('agentravel as at')
            ->select('at.IDTravel')
            ->where('at.IDPerson', $IDPerson)
            ->pluck('IDTravel')
            ->toArray();

        if (empty($agenTravel)) {
            return null;
        }

        return self::baseQuery($IDTravel, $KotaTujuan, $TglBerangkat, $KategoriPaket, $cari, $limit, $page)
            ->whereIn('pk.IDTravel', $agenTravel)
            ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("((pk.TglBerangkat IS NOT NULL AND DATE(pk.TglBerangkat) < CURDATE()) AND (pk.TglPulang IS NOT NULL AND DATE(pk.TglPulang) <= CURDATE()))")
            ->get()
            ->toArray();
    }

    /**
     * Get Jenis/Kategori Paket
     */
    public static function getJenisPaket(): array
    {
        return self::select('KategoriPaket')
            ->distinct()
            ->pluck('KategoriPaket')
            ->toArray();
    }

    /**
     * Get Syarat Paket
     */
    public static function getSyaratPaket(string $IDTravel, string $IDPaket): array
    {
        return DB::table('syaratpaket')
            ->where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->orderBy('NoUrutSyarat', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Generate ID
     */
    public static function generateID(): string
    {
        $lastRow = self::selectRaw('MAX(IDPaket) as IDPaket')->first();
        $lastID = $lastRow?->IDPaket;

        if (!$lastID) {
            return 'PKT-0000001';
        }

        $lastDigit = substr($lastID, -7);
        $newLastDigit = str_pad((int) $lastDigit + 1, 7, '0', STR_PAD_LEFT);
        return substr_replace($lastID, $newLastDigit, -7);
    }

    /**
     * Get Filter Options for Paket
     * Returns data for dropdowns and checkboxes
     */
    public static function getFilterOptions(): array
    {
        // Get unique Bandara Keberangkatan (BerangkatDari)
        $bandaraKeberangkatan = self::select(DB::raw('LOWER(pk.BerangkatDari) as BerangkatDari'))
            ->from('mstpaket as pk')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->whereNotNull('pk.BerangkatDari')
            ->where('pk.BerangkatDari', '!=', '')
            /* ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.IsShow = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("((pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE()) AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())) AND pk.IsAktif = 1") */
            ->distinct()
            ->orderBy('BerangkatDari', 'ASC')
            ->pluck('BerangkatDari')
            ->toArray();

        // Get unique Bandara Tujuan (LandingDi)
        $bandaraTujuan = self::select(DB::raw('LOWER(pk.LandingDi) as LandingDi'))
            ->from('mstpaket as pk')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->whereNotNull('pk.LandingDi')
            ->where('pk.LandingDi', '!=', '')
            /* ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.IsShow = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("((pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE()) AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())) AND pk.IsAktif = 1") */
            ->distinct()
            ->orderBy('LandingDi', 'ASC')
            ->pluck('LandingDi')
            ->toArray();

        // Get unique Kategori
        $kategori = self::select(DB::raw('LOWER(pk.Kategori) as Kategori'))
            ->from('mstpaket as pk')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->whereNotNull('pk.Kategori')
            ->where('pk.Kategori', '!=', '')
            /* ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.IsShow = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("((pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE()) AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())) AND pk.IsAktif = 1") */
            ->distinct()
            ->orderBy('Kategori', 'ASC')
            ->pluck('Kategori')
            ->toArray();

        // Get available months from TglBerangkat
        $bulanKeberangkatan = self::select(
            DB::raw("DATE_FORMAT(pk.TglBerangkat, '%Y-%m') as bulan"),
            DB::raw("DATE_FORMAT(pk.TglBerangkat, '%M %Y') as bulan_label")
        )
            ->from('mstpaket as pk')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->whereNotNull('pk.TglBerangkat')
            /* ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.IsShow = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("((pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE()) AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())) AND pk.IsAktif = 1") */
            ->distinct()
            ->orderBy('bulan', 'ASC')
            ->get()
            ->toArray();

        // Harga options (static range)
        $hargaOptions = [
            ['value' => 'lte30', 'label' => '≤ 30 Juta'],
            ['value' => '30to40', 'label' => '30 - 40 Juta'],
            ['value' => 'gte40', 'label' => '≥ 40 Juta'],
        ];

        return [
            'bandara_keberangkatan' => array_map('ucwords', $bandaraKeberangkatan),
            'bandara_tujuan' => array_map('ucwords', $bandaraTujuan),
            'kategori' => array_map('ucwords', $kategori),
            'bulan_keberangkatan' => $bulanKeberangkatan,
            'harga_options' => $hargaOptions,
        ];
    }

    /**
     * Find katalog paket with advanced filter
     */
    public static function findKatalogWithFilter(
        ?string $IDTravel = null,
        ?string $namaPaket = null,
        ?array $kategori = null,
        ?string $bulanKeberangkatan = null,
        ?string $hargaRange = null,
        ?string $bandaraKeberangkatan = null,
        ?string $bandaraTujuan = null,
        ?int $durasiMin = null,
        ?int $durasiMax = null,
        ?string $cari = null,
        ?int $limit = null,
        ?int $page = null
    ): array {
        $query = self::select(
            'pk.*',
            'tv.NamaTravel',
            DB::raw("IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook"),
            DB::raw("(SELECT COUNT(*) FROM jamaahpaket jp WHERE jp.IDTravel=pk.IDTravel AND jp.IDPaket=pk.IDPaket AND jp.StatusJamaah IN ('Jamaah', 'Leader')) AS JumlahPeserta"),
            DB::raw("DATEDIFF(pk.TglPulang, pk.TglBerangkat) + 1 AS DurasiHari"),
            'bt.Value as BiayaTrip',
            'tv.TelpCP',
            'tv.NamaCP'
        )
            ->from('mstpaket as pk')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('(NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir)');
            })
            ->leftJoin('sistemsetting as bt', 'bt.KodeSetting', '=', DB::raw("'" . self::KODE_BIAYA_TRIP . "'"));

        // Base filter for active paket
        $query->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.IsShow = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("((pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE()) AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())) AND pk.IsAktif = 1");

        // Filter by IDTravel
        if ($IDTravel) {
            $query->where('pk.IDTravel', $IDTravel);
        }

        // Filter by Nama Paket
        if ($namaPaket) {
            $query->where('pk.NamaPaket', 'like', "%{$namaPaket}%");
        }

        // Filter by Kategori (array of kategori) - case insensitive
        if ($kategori && count($kategori) > 0) {
            $kategoriLower = array_map('strtolower', $kategori);
            $query->whereRaw('LOWER(pk.Kategori) IN (' . implode(',', array_fill(0, count($kategoriLower), '?')) . ')', $kategoriLower);
        }

        // Filter by Bulan Keberangkatan (format: YYYY-MM)
        if ($bulanKeberangkatan) {
            $query->whereRaw("DATE_FORMAT(pk.TglBerangkat, '%Y-%m') = ?", [$bulanKeberangkatan]);
        }

        // Filter by Harga Range (BiayaDouble)
        if ($hargaRange) {
            switch ($hargaRange) {
                case 'lte30':
                    $query->where('pk.BiayaDouble', '<=', 30000000);
                    break;
                case '30to40':
                    $query->where('pk.BiayaDouble', '>', 30000000)
                        ->where('pk.BiayaDouble', '<=', 40000000);
                    break;
                case 'gte40':
                    $query->where('pk.BiayaDouble', '>', 40000000);
                    break;
            }
        }

        // Filter by Bandara Keberangkatan - case insensitive
        if ($bandaraKeberangkatan) {
            $query->whereRaw('LOWER(pk.BerangkatDari) = ?', [strtolower($bandaraKeberangkatan)]);
        }

        // Filter by Bandara Tujuan - case insensitive
        if ($bandaraTujuan) {
            $query->whereRaw('LOWER(pk.LandingDi) = ?', [strtolower($bandaraTujuan)]);
        }

        // Filter by Durasi Perjalanan (min days)
        if ($durasiMin !== null) {
            $query->whereRaw("DATEDIFF(pk.TglPulang, pk.TglBerangkat) + 1 >= ?", [$durasiMin]);
        }

        // Filter by Durasi Perjalanan (max days)
        if ($durasiMax !== null) {
            $query->whereRaw("DATEDIFF(pk.TglPulang, pk.TglBerangkat) + 1 <= ?", [$durasiMax]);
        }

        // General search
        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('pk.Keterangan', 'like', "%{$cari}%")
                    ->orWhere('pk.Deskripsi', 'like', "%{$cari}%")
                    ->orWhere('pk.NarasiPaket', 'like', "%{$cari}%")
                    ->orWhere('pk.NamaPaket', 'like', "%{$cari}%")
                    ->orWhere('tv.NamaTravel', 'like', "%{$cari}%");
            });
        }

        // Order by TglBerangkat
        $query->orderBy('pk.TglBerangkat', 'ASC');

        // Pagination
        if ($limit && $limit > 0) {
            $currentPage = max($page ?? 0, 0);
            $offset = $currentPage * $limit;

            $query->limit($limit)->offset($offset);
        }

        return $query->get()->toArray();
    }
}
