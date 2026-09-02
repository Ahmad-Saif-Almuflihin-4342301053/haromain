<?php

namespace App\Models;

use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstPaket extends Model
{
    use HasFactory, HasDatatableParams, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstpaket';
    protected $primaryKey = 'IDPaket';
    public $incrementing = false;
    public $timestamps = false;

    protected static $IDTravel;
    protected static $KotaTujuan;
    protected static $TglBerangkat;
    protected static $KategoriPaket;

    protected $fillable = [
        'IDPaket',
        'IDTravel',
        'NamaPaket',
        'Maskapai',
        'JenisPenerbangan',
        'BerangkatDari',
        'LandingDi',
        'Keterangan',
        'Deskripsi',
        'NarasiPaket',
        'KategoriPaket',
        'KotaTujuan',
        'LamaHari',
        'KuotaPaket',
        'TglBerangkat',
        'TglPulang',
        'TglMaxBayar',
        'TglMaxBooking',
        'TglMaxDokumen',
        'Gambar1',
        'Gambar2',
        'Gambar3',
        'Gambar4',
        'Gambar5',
        'BiayaDouble',
        'BiayaTriple',
        'BiayaQuad',
        'IncludeFasilitas',
        'ExcludeFasilitas',
        'IsAktif',
    ];

    protected static array $columnOrder = [
        null,
        'NamaPaket',
        'Maskapai',
        'JenisPenerbangan',
        'LandingDi',
        'KotaTujuan',
        'TglBerangkat',
        'TglPulang',
    ];

    protected static array $columnSearch = [
        'NamaPaket',
        'KotaTujuan',
        'TglBerangkat',
        'TglPulang',
    ];

    protected static array $defaultOrder = [
        'mstpaket.IDPaket' => 'desc',
    ];

    public static function detail($IDTravel, $IDPaket)
    {
        return DB::table('mstpaket as pk')
            ->where('pk.IDPaket', $IDPaket)
            ->where('pk.IDTravel', $IDTravel)
            ->first();
    }

    public function detailInstance($IDTravel, $IDPaket)
    {
        return self::detail($IDTravel, $IDPaket);
    }

    public static function findKatalog()
    {
        return DB::table('mstpaket as pk')
            ->join('msttravel as tv', 'tv.IDTravel', '=', 'pk.IDTravel')
            ->whereRaw("tv.TglBerlakuPPIU >= NOW()")
            ->where('tv.IsVerified', 1)
            ->where('tv.IsShow', 1)
            ->where('tv.StatusAkun', 'Aktif')
            ->whereRaw("
                (
                    (pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE())
                    AND
                    (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())
                )
            ")
            ->where('pk.IsAktif', 1)
            ->get();
    }

    public static function findByAgen($IDPerson, bool $isKatalog = false)
    {

        $travels = DB::table('agentravel as agt')
            ->select('agt.IDTravel')
            ->where('agt.IDPerson', $IDPerson)
            ->whereRaw("
                (
                    IF(
                        agt.TglAkhirAgen IS NULL,
                        NOW() >= agt.TglMulaiAgen,
                        NOW() BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen
                    )
                )
            ")
            ->where('agt.IsAktif', 1)
            ->pluck('IDTravel')
            ->toArray();

        if (count($travels) === 0) {
            return null;
        }

        $query = DB::table('mstpaket as pk')
            ->whereIn('pk.IDTravel', $travels);

        if ($isKatalog) {
            $query->join('msttravel as tv', 'tv.IDTravel', '=', 'pk.IDTravel')
                ->whereRaw("tv.TglBerlakuPPIU >= NOW()")
                ->where('tv.IsVerified', 1)
                ->where('tv.IsShow', 1)
                ->where('tv.StatusAkun', 'Aktif')
                ->whereRaw("
                    (
                        (pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE())
                        AND
                        (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())
                    )
                ")
                ->where('pk.IsAktif', 1);
        }

        return $query->get();
    }

    public static function findAgenMonitoring($IDPerson)
    {
        $travels = DB::table('agentravel as at')
            ->select('at.IDTravel')
            ->where('at.IDPerson', $IDPerson)
            ->whereRaw("
            (
                IF(
                    at.TglAkhirAgen IS NULL,
                    NOW() >= at.TglMulaiAgen,
                    NOW() BETWEEN at.TglMulaiAgen AND at.TglAkhirAgen
                )
            )
            AND at.IsAktif = 1
        ")
            ->pluck('IDTravel')
            ->toArray();

        if (count($travels) === 0) {
            return null;
        }

        $query = self::baseFindAllQuery();

        $query->whereIn('pk.IDTravel', $travels)
            ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("(pk.TglBerangkat IS NOT NULL AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE()))");

        return $query->get();
    }

    public static function findAgenHistory($IDPerson)
    {
        $travels = DB::table('agentravel as at')
            ->select('at.IDTravel')
            ->where('at.IDPerson', $IDPerson)
            ->pluck('IDTravel')
            ->toArray();

        if (count($travels) === 0) {
            return null;
        }

        $query = self::baseFindAllQuery();

        $query->whereIn('pk.IDTravel', $travels)
            ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("
            (
                (pk.TglBerangkat IS NOT NULL AND DATE(pk.TglBerangkat) < CURDATE())
                AND
                (pk.TglPulang IS NOT NULL AND DATE(pk.TglPulang) <= CURDATE())
            )
        ");

        return $query->get();
    }

    public static function getSyaratPaket($IDTravel, $IDPaket)
    {
        return DB::table('syaratpaket')
            ->where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->orderBy('NoUrutSyarat', 'ASC')
            ->get()
            ->toArray();
    }

    public function getSyaratPaketInstance($IDTravel, $IDPaket)
    {
        return self::getSyaratPaket($IDTravel, $IDPaket);
    }

    // private function findAll()
    // {
    //     $query = DB::table('mstpaket as pk');

    //     if (self::getIDTravel()) {
    //         $query->where('pk.IDTravel', self::getIDTravel());
    //     }

    //     if (self::getKotaTujuan()) {
    //         $query->like('pk.KotaTujuan', self::getKotaTujuan());
    //     }

    //     if (self::getTglBerangkat()) {
    //         $query->whereRaw('DATE(pk.TglBerangkat) = ?', [self::getTglBerangkat()]);
    //     }

    //     if (self::getKategoriPaket()) {
    //         $query->where('pk.KategoriPaket', self::getKategoriPaket());
    //     }

    //     if ($this->getLimit() > 0) {
    //         $query->limit($this->getLimit())
    //             ->offset($this->getPage());
    //     }

    //     if ($this->getCari()) {
    //         $query->where(function ($q) {
    //             $cari = $this->getCari();
    //             $q->where('pk.Keterangan', 'LIKE', "%{$cari}%")
    //                 ->orWhere('pk.Deskripsi', 'LIKE', "%{$cari}%")
    //                 ->orWhere('pk.NarasiPaket', 'LIKE', "%{$cari}%")
    //                 ->orWhere('tv.NamaTravel', 'LIKE', "%{$cari}%");
    //         });
    //     }

    //     return $query->selectRaw("
    //         pk.*,
    //         tv.NamaTravel,
    //         IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook,
    //         (
    //             SELECT COUNT(*)
    //             FROM jamaahpaket jp
    //             WHERE jp.IDTravel = pk.IDTravel
    //               AND jp.IDPaket = pk.IDPaket
    //               AND jp.StatusJamaah IN ('" . config('constants.ST_JAMAAH_JAMAAH') . "', '" . config('constants.ST_JAMAAH_LEADER') . "')
    //         ) AS JumlahPeserta,
    //         bt.Value AS BiayaTrip,
    //         tv.TelpCP,
    //         tv.NamaCP
    //     ")
    //         ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
    //         ->leftJoin('transaksipromo as pr', function ($join) {
    //             $join->on('pr.IDTravel', '=', 'pk.IDTravel')
    //                 ->on('pr.IDPaket', '=', 'pk.IDPaket')
    //                 ->whereRaw('NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir');
    //         })
    //         ->leftJoin('sistemsetting as bt', function ($join) {
    //             $join->where('bt.KodeSetting', SistemSetting::KODE_BIAYA_TRIP);
    //         })
    //         ->orderBy('pk.TglBerangkat', 'ASC')
    //         ->get()
    //         ->toArray();
    // }

    public static function getIDTravel()
    {
        return self::$IDTravel;
    }

    public static function setIDTravel($IDTravel)
    {
        self::$IDTravel = $IDTravel;
        return new static;
    }

    public static function getKotaTujuan()
    {
        return self::$KotaTujuan;
    }

    public static function setKotaTujuan($KotaTujuan)
    {
        self::$KotaTujuan = $KotaTujuan;
        return new static;
    }

    public static function getTglBerangkat()
    {
        return self::$TglBerangkat;
    }

    public static function setTglBerangkat($TglBerangkat)
    {
        self::$TglBerangkat = $TglBerangkat;
        return new static;
    }

    // private static function _get_datatables_query()
    // {
    //     $query = DB::table('mstpaket')
    //         ->selectRaw('
    //         mstpaket.*,
    //         jamaahpaket.IDPerson as IDLeader,
    //         mstperson.NamaJamaah as NamaLeader
    //     ')
    //         ->leftJoin(
    //             'jamaahpaket',
    //             'jamaahpaket.IDPaket',
    //             '=',
    //             'mstpaket.IDPaket'
    //         )
    //         ->where('jamaahpaket.StatusJamaah', 'Leader')
    //         ->leftJoin(
    //             'mstperson',
    //             'jamaahpaket.IDPerson',
    //             '=',
    //             'mstperson.IDPerson'
    //         )
    //         ->where('mstpaket.IDTravel', Auth::user()->IDTravel);

    //     // SEARCH
    //     if (!empty(request('search.value'))) {
    //         $search = request('search.value');

    //         $query->where(function ($q) use ($search) {
    //             foreach (self::$columnSearch as $i => $item) {
    //                 if ($i === 0) {
    //                     $q->where($item, 'LIKE', "%{$search}%");
    //                 } else {
    //                     $q->orWhere($item, 'LIKE', "%{$search}%");
    //                 }
    //             }
    //         });
    //     }

    //     // ORDER
    //     if (request()->has('order')) {
    //         $col = request('order.0.column');
    //         $dir = request('order.0.dir');
    //         $query->orderBy(self::$columnOrder[$col], $dir);
    //     } elseif (!empty(self::$defaultOrder)) {
    //         $order = self::$defaultOrder;
    //         $query->orderBy(key($order), $order[key($order)]);
    //     }

    //     return $query;
    // }

    private function getDatatablesQuery($where = null)
    {
        if ($where === null) {
            return self::_get_datatables_query();
        }

        $query = DB::table('mstpaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'mstpaket.IDTravel')
            ->select('mstpaket.*', 'msttravel.NamaTravel')
            ->where($where);

        // SEARCH
        if (!empty(request('search.value'))) {
            $search = request('search.value');
            $query->where(function ($q) use ($search) {
                foreach ($this->column_search_monitor as $column) {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            });
        }

        // ORDER
        if (request()->has('order')) {
            $orderCol = $this->column_order_monitor[request('order.0.column')];
            $orderDir = request('order.0.dir');
            if ($orderCol) {
                $query->orderBy($orderCol, $orderDir);
            }
        } else {
            foreach ($this->order_monitor as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    private static function _get_datatables_query()
    {
        $query = DB::table('mstpaket')
            ->selectRaw('
            mstpaket.*,
            (SELECT GROUP_CONCAT(p.NamaJamaah SEPARATOR ", ") 
             FROM jamaahpaket jp 
             JOIN mstperson p ON jp.IDPerson = p.IDPerson 
             WHERE jp.IDPaket = mstpaket.IDPaket 
             AND jp.StatusJamaah = "Leader"
            ) as NamaLeader,
            (SELECT GROUP_CONCAT(jp.IDPerson SEPARATOR ",") 
             FROM jamaahpaket jp 
             WHERE jp.IDPaket = mstpaket.IDPaket 
             AND jp.StatusJamaah = "Leader"
            ) as IDLeader
        ')
            ->where('mstpaket.IDTravel', Auth::user()->IDTravel);

        // FILTER STATUS
        if (request()->has('status') && request('status') !== null && request('status') !== '') {
            $query->where('mstpaket.IsAktif', request('status'));
        }

        // SEARCH
        if (!empty(request('search.value'))) {
            $search = request('search.value');

            $query->where(function ($q) use ($search) {
                foreach (self::$columnSearch as $i => $item) {
                    if ($i === 0) {
                        $q->where($item, 'LIKE', "%{$search}%");
                    } else {
                        $q->orWhere($item, 'LIKE', "%{$search}%");
                    }
                }
            });
        }

        // ORDER
        if (request()->has('order')) {
            $col = request('order.0.column');
            $dir = request('order.0.dir');
            $query->orderBy(self::$columnOrder[$col], $dir);
        } elseif (!empty(self::$defaultOrder)) {
            $order = self::$defaultOrder;
            $query->orderBy(key($order), $order[key($order)]);
        }
        // Log::error(request()->all());

        return $query;
    }

    public static function count_filtered()
    {
        $query = self::_get_datatables_query();
        return $query->count();
    }

    public static function count_all()
    {
        return DB::table('mstpaket')->count();
    }

    public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'IDPaket',
            'prefix' => 'PKT-',
            'pad' => 7,
        ]);
    }

    public static function _getLatestPaket($limit, $where)
    {
        $query = DB::table('mstpaket')
            ->select(
                'mstpaket.Gambar1',
                'mstpaket.NamaPaket',
                'mstpaket.KategoriPaket',
                'mstpaket.TglBerangkat',
                'msttravel.NamaTravel',
                'mstkab.NamaKab',
                'mstpaket.IDPaket',
                'mstpaket.BiayaQuad'
            )
            ->join('msttravel', 'msttravel.IDTravel', '=', 'mstpaket.IDTravel')
            ->join('mstkab', 'mstkab.IDKab', '=', 'msttravel.IDKab')
            ->where('mstpaket.IsAktif', 1)
            ->whereRaw('DATE(mstpaket.TglBerangkat) > ?', [date('Y-m-d')])
            ->where($where)
            ->orderByRaw('DATE(TglBerangkat) ASC')
            ->limit($limit);

        return $query->get();
    }
    public static function _getLatestPaketPaging($limit, $offset, $where)
    {
        $query = DB::table('mstpaket')
            ->select(
                'mstpaket.Gambar1',
                'mstpaket.NamaPaket',
                'mstpaket.KategoriPaket',
                'mstpaket.TglBerangkat',
                'msttravel.NamaTravel',
                'mstkab.NamaKab',
                'mstpaket.IDPaket',
                'mstpaket.BiayaQuad'
            )
            ->join('msttravel', 'msttravel.IDTravel', '=', 'mstpaket.IDTravel')
            ->join('mstkab', 'mstkab.IDKab', '=', 'msttravel.IDKab')
            ->where('mstpaket.IsAktif', 1)
            ->whereRaw('DATE(mstpaket.TglBerangkat) > ?', [date('Y-m-d')])
            ->where($where)
            ->orderByRaw('DATE(TglBerangkat) ASC')
            ->offset($offset)
            ->limit($limit);

        return $query->get();
    }
    public static function _totalLatestPaket($where)
    {
        return DB::table('mstpaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'mstpaket.IDTravel')
            ->join('mstkab', 'mstkab.IDKab', '=', 'msttravel.IDKab')
            ->where('mstpaket.IsAktif', 1)
            ->whereRaw('DATE(mstpaket.TglBerangkat) > ?', [date('Y-m-d')])
            ->where($where)
            ->count();
    }


    public static function getLatestPaket($limit, $where)
    {
        $query = DB::table('mstpaket as pk')
            ->select('pk.*', 'tv.NamaTravel', 'mstkab.NamaKab')
            ->selectRaw('IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook')
            ->selectRaw('
            (SELECT COUNT(*)
             FROM jamaahpaket jp
             WHERE jp.IDTravel = pk.IDTravel
             AND jp.IDPaket = pk.IDPaket
             AND jp.StatusJamaah IN ("Jamaah", "Leader")
            ) AS JumlahPeserta
        ')
            ->selectRaw('bt.Value AS BiayaTrip')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->leftJoin('mstkab', 'mstkab.IDKab', '=', 'tv.IDKab')
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('(NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir)');
            })
            ->leftJoin('sistemsetting as bt', function ($join) {
                $join->where('bt.KodeSetting', '1');
            });

        $query->whereRaw('tv.TglBerlakuPPIU >= NOW()')
            ->where('tv.IsVerified', 1)
            ->where('tv.IsShow', 1)
            ->where('tv.StatusAkun', 'Aktif');

        $query->where(function ($q) {
            $q->where(function ($q2) {
                $q2->whereNull('pk.TglBerangkat')
                    ->orWhereRaw('DATE(pk.TglBerangkat) > CURDATE()');
            })->where(function ($q2) {
                $q2->whereNull('pk.TglPulang')
                    ->orWhereRaw('DATE(pk.TglPulang) > CURDATE()');
            });
        });

        $query->where('pk.IsAktif', 1);

        if (!empty($where)) {
            $query->where($where);
        }

        $query->orderBy('pr.IDTransaksi', 'asc')
            ->limit($limit);

        return $query->get();
    }

    public static function getLatestPaketPaging($limit, $offset, $where)
    {
        $query = DB::table('mstpaket as pk')
            ->select('pk.*', 'tv.NamaTravel', 'mstkab.NamaKab')
            ->selectRaw('IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook')
            ->selectRaw('
            (SELECT COUNT(*)
             FROM jamaahpaket jp
             WHERE jp.IDTravel = pk.IDTravel
             AND jp.IDPaket = pk.IDPaket
             AND jp.StatusJamaah IN ("Jamaah", "Leader")
            ) AS JumlahPeserta
        ')
            ->selectRaw('bt.Value AS BiayaTrip')

            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->leftJoin('mstkab', 'mstkab.IDKab', '=', 'tv.IDKab')
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('(NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir)');
            })
            ->leftJoin('sistemsetting as bt', function ($join) {
                $join->where('bt.KodeSetting', '1');
            });

        // WHERE utama
        $query->whereRaw('tv.TglBerlakuPPIU >= NOW()')
            ->where('tv.IsVerified', 1)
            ->where('tv.IsShow', 1)
            ->where('tv.StatusAkun', 'Aktif');

        // group_start() mirror
        $query->where(function ($q) {
            $q->where(function ($q2) {
                $q2->whereNull('pk.TglBerangkat')
                    ->orWhereRaw('DATE(pk.TglBerangkat) > CURDATE()');
            })
                ->where(function ($q2) {
                    $q2->whereNull('pk.TglPulang')
                        ->orWhereRaw('DATE(pk.TglPulang) > CURDATE()');
                });
        });

        $query->where('pk.IsAktif', 1);

        if (!empty($where)) {
            $query->where($where);
        }

        $query->orderBy('pr.IDTransaksi', 'asc')
            ->offset($offset)
            ->limit($limit);

        return $query->get();
    }


    public static function totalLatestPaket($where)
    {
        $query = DB::table('mstpaket as pk')
            ->select('pk.IDPaket')
            ->join('msttravel as tv', 'pk.IDTravel', '=', 'tv.IDTravel')
            ->leftJoin('mstkab', 'mstkab.IDKab', '=', 'tv.IDKab')
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('(NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir)');
            })
            ->leftJoin('sistemsetting as bt', function ($join) {
                $join->where('bt.KodeSetting', '1');
            });

        // WHERE utama
        $query->whereRaw('tv.TglBerlakuPPIU >= NOW()')
            ->where('tv.IsVerified', 1)
            ->where('tv.IsShow', 1)
            ->where('tv.StatusAkun', 'Aktif');

        // grouped where (mirror CI3)
        $query->where(function ($q) {
            $q->where(function ($q2) {
                $q2->whereNull('pk.TglBerangkat')
                    ->orWhereRaw('DATE(pk.TglBerangkat) > CURDATE()');
            })
                ->where(function ($q2) {
                    $q2->whereNull('pk.TglPulang')
                        ->orWhereRaw('DATE(pk.TglPulang) > CURDATE()');
                });
        });

        $query->where('pk.IsAktif', 1);

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->count();
    }

    public static function findJoinTravel($where)
    {
        return DB::table('mstpaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'mstpaket.IDTravel')
            ->where($where)
            ->first();
    }

    public static function findJoinPerson($where)
    {
        return DB::table('jamaahpaket')
            ->select('jamaahpaket.IDPerson', 'mstperson.NamaJamaah')
            ->join('mstperson', 'mstperson.IDPerson', '=', 'jamaahpaket.IDPerson')
            ->where($where)
            ->get();
    }

    public static function findJoinTravelLokasi($where)
    {
        return DB::table('mstpaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'mstpaket.IDTravel')
            ->leftJoin('mstkab', 'msttravel.IDKab', '=', 'mstkab.IDKab')
            ->leftJoin('mstmaskapai', 'mstpaket.Maskapai', '=', 'mstmaskapai.Maskapai')
            ->where($where)
            ->first();
    }

    public static function total($where)
    {
        $query = DB::table('mstpaket');

        foreach ($where as $key => $value) {

            if (is_numeric($key)) {
                continue;
            }

            if (str_starts_with($key, 'DATE(')) {
                preg_match('/DATE\((.*?)\)\s*(<=|>=|=|<|>)/', $key, $m);
                $query->whereDate($m[1], $m[2], $value);
            } elseif (str_starts_with($key, 'MONTH(')) {
                preg_match('/MONTH\((.*?)\)/', $key, $m);
                $query->whereMonth($m[1], $value);
            } elseif (str_starts_with($key, 'YEAR(')) {
                preg_match('/YEAR\((.*?)\)/', $key, $m);
                $query->whereYear($m[1], $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->count();
    }


    public function getKategoriPaket()
    {
        return $this->KategoriPaket;
    }

    public function setKategoriPaket($KategoriPaket)
    {
        $this->KategoriPaket = $KategoriPaket;
        return $this;
    }



    // Monitoring

    protected $column_order_monitor = [
        null,
        'mstpaket.NamaPaket',
        'mstpaket.KategoriPaket',
        'mstpaket.TglBerangkat',
        'mstpaket.TglPulang',
        'msttravel.NamaTravel',
    ];

    protected $column_search_monitor = [
        'mstpaket.NamaPaket',
        'mstpaket.KategoriPaket',
        'mstpaket.TglBerangkat',
        'mstpaket.TglPulang',
        'msttravel.NamaTravel',
    ];

    protected $order_monitor = [
        'mstpaket.TglBerangkat' => 'desc'
    ];


    public function travelMonitor()
    {
        return $this->belongsTo(MstTravel::class, 'IDTravel', 'IDTravel');
    }

    // Fungsi Datatables Query


    // Hitung data terfilter
    public function countFilteredMonitor($where)
    {
        Log::error($this->getDatatablesQuery($where)->count());
        return $this->getDatatablesQuery($where)->count();
    }

    // Hitung semua data
    public function countAllMonitor()
    {
        return $this->count();
    }

    // Hitung jumlah jamaah
    public function getJumlahJamaahMonitor($where, $jumlahTracking = false)
    {
        $query = DB::table('jamaahpaket')
            ->where($where);
        if ($jumlahTracking) {
            $query->whereNotNull('NoInvoice');
        }
        return $query->count();
    }

    /**
     * Get distinct kategori paket
     */
    public function getDistinctKategori()
    {
        return DB::table('mstpaket')
            ->distinct()
            ->pluck('KategoriPaket')
            ->toArray();
    }

    /**
     * Find paket by agen (simple list)
     */
    public function findByAgenPerson($IDPerson)
    {
        return self::findByAgen($IDPerson, false);
    }

    /**
     * Find katalog paket by agen with filters and pagination
     */
    public function findKatalogAgen(
        $IDPerson,
        $page = 0,
        $limit = 10,
        $cari = null,
        $IDTravel = null,
        $KotaTujuan = null,
        $TglBerangkat = null,
        $KategoriPaket = null
    ) {
        // Get travel IDs for agen
        $travels = DB::table('agentravel as agt')
            ->select('agt.IDTravel')
            ->where('agt.IDPerson', $IDPerson)
            ->whereRaw("
                (
                    IF(
                        agt.TglAkhirAgen IS NULL,
                        NOW() >= agt.TglMulaiAgen,
                        NOW() BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen
                    )
                )
            ")
            ->where('agt.IsAktif', 1)
            ->pluck('IDTravel')
            ->toArray();

        if (count($travels) === 0) {
            return null;
        }

        $query = DB::table('mstpaket as pk')
            ->join('msttravel as tv', 'tv.IDTravel', '=', 'pk.IDTravel')
            ->selectRaw("
                pk.*,
                tv.NamaTravel,
                IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook,
                (
                    SELECT COUNT(*)
                    FROM jamaahpaket jp
                    WHERE jp.IDTravel = pk.IDTravel
                      AND jp.IDPaket = pk.IDPaket
                      AND jp.StatusJamaah IN ('Jamaah', 'Leader')
                ) AS JumlahPeserta,
                bt.Value AS BiayaTrip,
                tv.TelpCP,
                tv.NamaCP
            ")
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir');
            })
            ->leftJoin('sistemsetting as bt', function ($join) {
                $join->where('bt.KodeSetting', SistemSetting::KODE_BIAYA_TRIP);
            })
            ->whereIn('pk.IDTravel', $travels)
            ->whereRaw("tv.TglBerlakuPPIU >= NOW()")
            ->where('tv.IsVerified', 1)
            ->where('tv.IsShow', 1)
            ->where('tv.StatusAkun', 'Aktif')
            ->whereRaw("
                (
                    (pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE())
                    AND
                    (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())
                )
            ")
            ->where('pk.IsAktif', 1);

        // Apply filters
        if ($IDTravel)
            $query->where('pk.IDTravel', $IDTravel);
        if ($KotaTujuan)
            $query->where('pk.KotaTujuan', 'like', "%{$KotaTujuan}%");
        if ($TglBerangkat)
            $query->whereDate('pk.TglBerangkat', $TglBerangkat);
        if ($KategoriPaket)
            $query->where('pk.KategoriPaket', $KategoriPaket);

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('pk.Keterangan', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.Deskripsi', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.NarasiPaket', 'LIKE', "%{$cari}%")
                    ->orWhere('tv.NamaTravel', 'LIKE', "%{$cari}%");
            });
        }

        $query->orderBy('pk.TglBerangkat', 'ASC');

        if ($limit > 0) {
            $query->limit($limit)->offset($page * $limit);
        }

        return $query->get()->toArray();
    }

    /**
     * Find monitoring paket by agen
     */
    public function findMonitoringAgen(
        $IDPerson,
        $page = 0,
        $limit = 10,
        $cari = null,
        $IDTravel = null,
        $KotaTujuan = null,
        $TglBerangkat = null,
        $KategoriPaket = null
    ) {
        $travels = DB::table('agentravel as at')
            ->select('at.IDTravel')
            ->where('at.IDPerson', $IDPerson)
            ->whereRaw("
            (
                IF(
                    at.TglAkhirAgen IS NULL,
                    NOW() >= at.TglMulaiAgen,
                    NOW() BETWEEN at.TglMulaiAgen AND at.TglAkhirAgen
                )
            )
            AND at.IsAktif = 1
        ")
            ->pluck('IDTravel')
            ->toArray();

        if (count($travels) === 0) {
            return null;
        }

        $query = DB::table('mstpaket as pk')
            ->join('msttravel as tv', 'tv.IDTravel', '=', 'pk.IDTravel')
            ->selectRaw("
                pk.*,
                tv.NamaTravel,
                IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook,
                (
                    SELECT COUNT(*)
                    FROM jamaahpaket jp
                    WHERE jp.IDTravel = pk.IDTravel
                      AND jp.IDPaket = pk.IDPaket
                      AND jp.StatusJamaah IN ('Jamaah', 'Leader')
                ) AS JumlahPeserta,
                bt.Value AS BiayaTrip,
                tv.TelpCP,
                tv.NamaCP
            ")
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir');
            })
            ->leftJoin('sistemsetting as bt', function ($join) {
                $join->where('bt.KodeSetting', SistemSetting::KODE_BIAYA_TRIP);
            })
            ->whereIn('pk.IDTravel', $travels)
            ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("(pk.TglBerangkat IS NOT NULL AND (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE()))");

        // Apply filters
        if ($IDTravel)
            $query->where('pk.IDTravel', $IDTravel);
        if ($KotaTujuan)
            $query->where('pk.KotaTujuan', 'like', "%{$KotaTujuan}%");
        if ($TglBerangkat)
            $query->whereDate('pk.TglBerangkat', $TglBerangkat);
        if ($KategoriPaket)
            $query->where('pk.KategoriPaket', $KategoriPaket);

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('pk.Keterangan', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.Deskripsi', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.NarasiPaket', 'LIKE', "%{$cari}%")
                    ->orWhere('tv.NamaTravel', 'LIKE', "%{$cari}%");
            });
        }

        $query->orderBy('pk.TglBerangkat', 'ASC');

        if ($limit > 0) {
            $query->limit($limit)->offset($page * $limit);
        }

        return $query->get()->toArray();
    }

    /**
     * Find history paket by agen
     */
    public function findHistoryAgen(
        $IDPerson,
        $page = 0,
        $limit = 10,
        $cari = null,
        $IDTravel = null,
        $KotaTujuan = null,
        $TglBerangkat = null,
        $KategoriPaket = null
    ) {
        $travels = DB::table('agentravel as at')
            ->select('at.IDTravel')
            ->where('at.IDPerson', $IDPerson)
            ->pluck('IDTravel')
            ->toArray();

        if (count($travels) === 0) {
            return null;
        }

        $query = DB::table('mstpaket as pk')
            ->join('msttravel as tv', 'tv.IDTravel', '=', 'pk.IDTravel')
            ->selectRaw("
                pk.*,
                tv.NamaTravel,
                IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook,
                (
                    SELECT COUNT(*)
                    FROM jamaahpaket jp
                    WHERE jp.IDTravel = pk.IDTravel
                      AND jp.IDPaket = pk.IDPaket
                      AND jp.StatusJamaah IN ('Jamaah', 'Leader')
                ) AS JumlahPeserta,
                bt.Value AS BiayaTrip,
                tv.TelpCP,
                tv.NamaCP
            ")
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir');
            })
            ->leftJoin('sistemsetting as bt', function ($join) {
                $join->where('bt.KodeSetting', SistemSetting::KODE_BIAYA_TRIP);
            })
            ->whereIn('pk.IDTravel', $travels)
            ->whereRaw("(tv.TglBerlakuPPIU >= NOW() AND tv.IsVerified = 1 AND tv.StatusAkun = 'Aktif')")
            ->whereRaw("
            (
                (pk.TglBerangkat IS NOT NULL AND DATE(pk.TglBerangkat) < CURDATE())
                AND
                (pk.TglPulang IS NOT NULL AND DATE(pk.TglPulang) <= CURDATE())
            )
        ");

        // Apply filters
        if ($IDTravel)
            $query->where('pk.IDTravel', $IDTravel);
        if ($KotaTujuan)
            $query->where('pk.KotaTujuan', 'like', "%{$KotaTujuan}%");
        if ($TglBerangkat)
            $query->whereDate('pk.TglBerangkat', $TglBerangkat);
        if ($KategoriPaket)
            $query->where('pk.KategoriPaket', $KategoriPaket);

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('pk.Keterangan', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.Deskripsi', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.NarasiPaket', 'LIKE', "%{$cari}%")
                    ->orWhere('tv.NamaTravel', 'LIKE', "%{$cari}%");
            });
        }

        $query->orderBy('pk.TglBerangkat', 'ASC');

        if ($limit > 0) {
            $query->limit($limit)->offset($page * $limit);
        }

        return $query->get()->toArray();
    }


    public function findKatalogPublic(
        $page = 0,
        $limit = 10,
        $cari = null,
        $IDTravel = null,
        $KotaTujuan = null,
        $TglBerangkat = null,
        $KategoriPaket = null
    ) {
        $query = DB::table('mstpaket as pk')
            ->join('msttravel as tv', 'tv.IDTravel', '=', 'pk.IDTravel')
            ->selectRaw("
                pk.*,
                tv.NamaTravel,
                IF(DATE(pk.TglMaxBooking) > CURDATE(), 1, 0) AS EnableBook,
                (
                    SELECT COUNT(*)
                    FROM jamaahpaket jp
                    WHERE jp.IDTravel = pk.IDTravel
                      AND jp.IDPaket = pk.IDPaket
                      AND jp.StatusJamaah IN ('Jamaah', 'Leader')
                ) AS JumlahPeserta,
                bt.Value AS BiayaTrip,
                tv.TelpCP,
                tv.NamaCP
            ")
            ->leftJoin('transaksipromo as pr', function ($join) {
                $join->on('pr.IDTravel', '=', 'pk.IDTravel')
                    ->on('pr.IDPaket', '=', 'pk.IDPaket')
                    ->whereRaw('NOW() BETWEEN pr.TglMulai AND pr.TglBerakhir');
            })
            ->leftJoin('sistemsetting as bt', function ($join) {
                $join->where('bt.KodeSetting', SistemSetting::KODE_BIAYA_TRIP);
            })
            ->whereRaw("tv.TglBerlakuPPIU >= NOW()")
            ->where('tv.IsVerified', 1)
            ->where('tv.IsShow', 1)
            ->where('tv.StatusAkun', 'Aktif')
            ->whereRaw("
                (
                    (pk.TglBerangkat IS NULL OR DATE(pk.TglBerangkat) > CURDATE())
                    AND
                    (pk.TglPulang IS NULL OR DATE(pk.TglPulang) > CURDATE())
                )
            ")
            ->where('pk.IsAktif', 1);

        // Apply filters
        if ($IDTravel)
            $query->where('pk.IDTravel', $IDTravel);
        if ($KotaTujuan)
            $query->where('pk.KotaTujuan', 'like', "%{$KotaTujuan}%");
        if ($TglBerangkat)
            $query->whereDate('pk.TglBerangkat', $TglBerangkat);
        if ($KategoriPaket)
            $query->where('pk.KategoriPaket', $KategoriPaket);

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('pk.Keterangan', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.Deskripsi', 'LIKE', "%{$cari}%")
                    ->orWhere('pk.NarasiPaket', 'LIKE', "%{$cari}%")
                    ->orWhere('tv.NamaTravel', 'LIKE', "%{$cari}%");
            });
        }

        $query->orderBy('pk.TglBerangkat', 'ASC');

        if ($limit > 0) {
            $query->limit($limit)->offset($page * $limit);
        }

        return $query->get()->toArray();
    }

    /**
     * Get detail paket with all related info
     */
    public function getDetailPaket($IDTravel, $IDPaket, $IDPerson = null)
    {
        $result = self::detail($IDTravel, $IDPaket);

        if (!$result) {
            return null;
        }

        $result = (array) $result;

        // Add contact info
        $result['NoHPLeader'] = $result['TelpCP'] ?? null;
        $result['NamaLeader'] = $result['NamaCP'] ?? '-';

        // Generate WhatsApp text
        $tglBerangkat = isset($result['TglBerangkat']) ? date('d-m-Y', strtotime($result['TglBerangkat'])) : '-';
        $result['TextWA'] = "Assalamualaikum kak 🙏\n\n" .
            "Saya ingin bertanya mengenai paket Umrah/Haji berikut:\n\n" .
            "📌 Nama Paket: " . ($result['NamaPaket'] ?? '-') . "\n" .
            "📌 Nama Travel: " . ($result['NamaTravel'] ?? '-') . "\n" .
            "📌 Tanggal Keberangkatan: " . $tglBerangkat . "\n\n" .
            "Mohon bantu informasinya ya kak 😊\n\n" .
            "Terima kasih...";

        // Get testimoni if IDPerson provided
        if ($IDPerson) {
            $testimoni = app(JamaahPaket::class)->getDetail($IDTravel, $IDPaket, $IDPerson);
            $result['RatingTestimoni'] = $testimoni['RatingTestimoni'] ?? 0;
            $result['NarasiTestimoni'] = $testimoni['NarasiTestimoni'] ?? '';
            $result['TanggalTestimoni'] = $testimoni['TanggalTestimoni'] ?? '';
        } else {
            $result['RatingTestimoni'] = 0;
            $result['NarasiTestimoni'] = '';
            $result['TanggalTestimoni'] = '';
        }

        // Get average rating and review count
        $avgTestimoni = app(JamaahPaket::class)->getAvgTestimoni($IDTravel);
        $result['AvgRating'] = round($avgTestimoni->avgRating ?? 0, 1);
        $result['JmlJamaah'] = (int) ($avgTestimoni->jmlJamaah ?? 0);
        $result['JmlReviewer'] = (int) ($avgTestimoni->jmlReviewer ?? 0);

        // Get syarat paket
        $result['syaratPaket'] = self::getSyaratPaket($IDTravel, $IDPaket);

        // Get travel detail
        $result['travel'] = app(MstTravel::class)->detail($IDTravel);

        return $result;
    }


    public static function getLandingKatalogList(
        string $namaPaket = null,
        array $kategoriPaket = [],
        int $bulanBerangkat = null,
        int $tahunBerangkat = null,
        array $hargaPaket = [],
        array $hargaPaketParam = [],
        string $kotaKebrangkatan = null,
        string $kotaTujuan = null,
        array $jarakHotel = [],
        array $jarakHotelParam = [],
        array $durasiPerjalanan = [],
        array $durasiPerjalananParam = [],
        array $jenisPenerbangan = [],
        string $maskapaiPenerbangan = null,
        string $orderBy,
        string $orderDirection,
        int $page,
        int $pagination
    ) {
        $data = MstPaket::leftJoin('msttravel', 'msttravel.IDTravel', '=', 'mstpaket.IDTravel')
            ->select('mstpaket.*', 'msttravel.NamaTravel')
            ->where('mstpaket.IsAktif', 1);
        // Apply filters
        if ($namaPaket) {
            $data = $data->where('mstpaket.NamaPaket', 'like', "%{$namaPaket}%");
        }
        if (count($kategoriPaket) > 0) {
            $data = $data->whereIn('mstpaket.KategoriPaket', $kategoriPaket);
        }
        if ($bulanBerangkat) {
            $data = $data->whereMonth('mstpaket.TglBerangkat', $bulanBerangkat);
        }
        if ($tahunBerangkat) {
            $data = $data->whereYear('mstpaket.TglBerangkat', $tahunBerangkat);
        }
        if ($hargaPaket) {
            if (count($hargaPaketParam) > 0) {
                foreach ($hargaPaketParam as $index => $param) {
                    if ($param == 'min') {
                        $data = $data->where('mstpaket.BiayaQuad', '>=', $hargaPaket[$index]);
                    } elseif ($param == 'max') {
                        $data = $data->where('mstpaket.BiayaQuad', '<=', $hargaPaket[$index]);
                    }
                }
            }
        }
        if ($kotaKebrangkatan) {
            $data = $data->where('mstpaket.BerangkatDari', '=', $kotaKebrangkatan);
        }
        if ($kotaTujuan) {
            $data = $data->where('mstpaket.LandingDi', '=', $kotaTujuan);
        }
        if ($jarakHotel) {
            if (count($jarakHotelParam) > 0) {
                foreach ($jarakHotelParam as $index => $param) {
                    if ($param == 'min') {
                        $data = $data->where('mstpaket.JarakHotel', '>=', $jarakHotel[$index]);
                    } elseif ($param == 'max') {
                        $data = $data->where('mstpaket.JarakHotel', '<=', $jarakHotel[$index]);
                    }
                }
            }
        }
        if ($durasiPerjalanan) {
            if (count($durasiPerjalananParam) > 0) {
                foreach ($durasiPerjalananParam as $index => $param) {
                    if ($param == 'min') {
                        $data = $data->whereRaw('DATEDIFF(mstpaket.TglPulang, mstpaket.TglBerangkat) >= ?', [$durasiPerjalanan[$index]]);
                    } elseif ($param == 'max') {
                        $data = $data->whereRaw('DATEDIFF(mstpaket.TglPulang, mstpaket.TglBerangkat) <= ?', [$durasiPerjalanan[$index]]);
                    }
                }
            }
        }
        if (count($jenisPenerbangan) > 0) {
            $data = $data->whereIn('mstpaket.JenisPenerbangan', $jenisPenerbangan);
        }
        if ($maskapaiPenerbangan) {
            $data = $data->whereRaw('LOWER(mstpaket.Maskapai) = ?', [$maskapaiPenerbangan]);
        }

        $data = $data
            ->whereDate('mstpaket.TglBerangkat', '>', date('Y-m-d H:i:s'));

        if ($orderBy != null && $orderDirection != null) {
            $data = $data->orderBy($orderBy, $orderDirection);
        } else {
            $data = $data->orderBy('mstpaket.TglBerangkat', 'ASC');
        }

        $data = $data->paginate($pagination, ['*'], 'page', $page);

        return $data;
    }

    public static function getLandingKategoriPaket()
    {
        return DB::table('mstpaket')
            ->distinct()
            ->pluck('KategoriPaket')
            ->toArray();
    }
    public static function getLandingKotaBerangkat()
    {
        return DB::table('mstpaket')
            ->distinct()
            ->pluck('BerangkatDari')
            ->toArray();
    }

    public static function getLandingKotaTujuan()
    {
        return DB::table('mstpaket')
            ->distinct()
            ->pluck('LandingDi')
            ->toArray();
    }
}
