<?php

namespace App\Models;

use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JamaahPaket extends Model
{
    use HasFactory, HasDatatableParams, HasDatatablesPagination;

    protected $table = 'jamaahpaket';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'TanggalBooking',
        'IDPaket',
        'IDPerson',
        'IDTravel',
        'IDCabang',
        'Keterangan',
        'IsPaid',
        'NoInvoice',
        'TglAwalTrack',
        'TglAkhirTrack',
        'VerifyPaidDate',
        'VerifyPaidUser',
        'IDPersonAgen',
        'StatusJamaah',
        'NarasiTestimoni',
        'InfoTagihan',
        'InfoTerbayar',
        'InfoSisaTagihan',
        'NominalPaid',
        'NoTransaksi',
        'JumlahTrip',
        'PotonganVoucher',
        'RatingTestimoni',
        'TypeKamar',
        'TanggalInvoice',
        'TanggalTestimoni'
    ];

    protected array $column_order = [null, 'mstperson.NamaJamaah'];
    protected array $column_search = ['mstperson.NamaJamaah'];
    protected array $order = ['IDPerson' => 'desc'];

    /**
     * Get detail jamaah paket
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @param string $IDPerson
     * @return object|null
     */
    public function getDetail($IDTravel, $IDPaket, $IDPerson)
    {
        $result = $this->findAll(function ($query) use ($IDTravel, $IDPaket, $IDPerson) {
            $query->where([
                'jp.IDTravel' => $IDTravel,
                'jp.IDPaket' => $IDPaket,
                'jp.IDPerson' => $IDPerson
            ]);
        });

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Find all paid jamaah by paket
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @return array
     */
    public function findAllPaidJamaah($IDTravel, $IDPaket)
    {
        return $this->findAll(function ($query) use ($IDTravel, $IDPaket) {
            $query->whereRaw("(pk.TglPulang IS NULL OR DATE(pk.TglPulang) >= CURDATE())")
                ->where([
                    'jp.IsPaid'   => 1,
                    'jp.IDTravel' => $IDTravel,
                    'jp.IDPaket'  => $IDPaket,
                ]);
        });
    }

    /**
     * Find paid jamaah by person
     * 
     * @param string $IDPerson
     * @return array
     */
    public function findPaidJamaah($IDPerson)
    {
        return $this->findAll(function ($query) use ($IDPerson) {
            $query->whereRaw("(pk.TglPulang IS NULL OR DATE(pk.TglPulang) >= CURDATE())")
                ->where([
                    'jp.IsPaid'   => 1,
                    'jp.IDPerson' => $IDPerson,
                ])
                ->orderByRaw("
                    CASE
                        WHEN CURDATE() BETWEEN pk.TglBerangkat AND pk.TglPulang THEN 0
                        ELSE 1
                    END,
                    ABS(DATEDIFF(pk.TglBerangkat, CURDATE())) ASC
                ");
        });
    }

    /**
     * Find by paket
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @return array
     */
    public function findByPaket($IDTravel, $IDPaket)
    {
        return $this->findAll(function ($query) use ($IDTravel, $IDPaket) {
            $query->where([
                'jp.IDTravel' => $IDTravel,
                'jp.IDPaket'  => $IDPaket
            ]);
        });
    }

    /**
     * Find by agen
     * 
     * @param string $IDPerson
     * @return array
     */
    public function findByAgen($IDPerson)
    {
        return $this->findAll(function ($query) use ($IDPerson) {
            $query->whereRaw(
                "DATE(pk.TglBerangkat) < CURDATE() 
                 OR (pk.TglPulang IS NULL OR DATE(pk.TglPulang) < CURDATE())"
            )
                ->where(['jp.IDPersonAgen' => $IDPerson]);
        });
    }

    /**
     * Find by person
     * 
     * @param string $IDPerson
     * @return array
     */
    public function findByPerson($IDPerson)
    {
        return $this->findAll(function ($query) use ($IDPerson) {
            $query->where(['jp.IDPerson' => $IDPerson]);
        });
    }

    /**
     * Find history person
     * 
     * @param string $IDPerson
     * @return array
     */
    public function findHistoryPerson($IDPerson)
    {
        return $this->findAll(function ($query) use ($IDPerson) {
            $query->whereRaw(
                "((pk.TglBerangkat IS NOT NULL AND DATE(pk.TglBerangkat) < CURDATE()) 
                  AND (pk.TglPulang IS NOT NULL AND DATE(pk.TglPulang) <= CURDATE()))"
            )
                ->where(['jp.IDPerson' => $IDPerson]);
        });
    }

    /**
     * Find history agen
     * 
     * @param string $IDPerson
     * @return array
     */
    public function findHistoryAgen($IDPerson)
    {
        return $this->findAll(function ($query) use ($IDPerson) {
            $query->whereRaw(
                "((pk.TglBerangkat IS NOT NULL AND DATE(pk.TglBerangkat) < CURDATE()) 
                  AND (pk.TglPulang IS NOT NULL AND DATE(pk.TglPulang) <= CURDATE()))"
            )
                ->where(['jp.IDPersonAgen' => $IDPerson]);
        });
    }

    /**
     * Cek person terdaftar
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @param string $IDPerson
     * @return array
     */
    public function cekPersonTerdaftar($IDTravel, $IDPaket, $IDPerson)
    {
        return $this->findAll(function ($query) use ($IDTravel, $IDPaket, $IDPerson) {
            $query->where([
                'jp.IDTravel' => $IDTravel,
                'jp.IDPaket'  => $IDPaket,
                'jp.IDPerson' => $IDPerson
            ]);
        });
    }

    /**
     * Find peserta
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @return array
     */
    public function findPeserta($IDTravel, $IDPaket)
    {
        return $this->findAll(function ($query) use ($IDTravel, $IDPaket) {
            $query->where([
                'jp.IDTravel' => $IDTravel,
                'jp.IDPaket'  => $IDPaket
            ]);
        });
    }

    /**
     * Get dokumen syarat
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @param string $IDPerson
     * @return array
     */
    public function getDokumenSyarat($IDTravel, $IDPaket, $IDPerson)
    {
        return DB::table('syaratpaket as sp')
            ->leftJoin('dokumensyarat as ds', function ($join) {
                $join->on('ds.IDTravel', '=', 'sp.IDTravel')
                    ->on('ds.IDPaket', '=', 'sp.IDPaket')
                    ->on('ds.NoUrutSyarat', '=', 'sp.NoUrutSyarat');
            })
            ->leftJoin('mstperson as p', 'p.IDPerson', '=', 'ds.IDPerson')
            ->where('sp.IDTravel', $IDTravel)
            ->where('sp.IDPaket', $IDPaket)
            ->where('ds.IDPerson', $IDPerson)
            ->select(
                'sp.*',
                'ds.Keterangan as KeteranganUpload',
                'ds.FileDokumen',
                'ds.TglUpload',
                'ds.IDPerson',
                'p.NamaJamaah'
            )
            ->get()
            ->toArray();
    }

    /**
     * Base findAll dengan joins lengkap
     * 
     * @param callable|null $callback
     * @return array
     */
    private function findAll($callback = null)
    {
        $query = DB::table('jamaahpaket as jp')
            ->selectRaw("
                jp.*,
                pk.NamaPaket,
                pk.KotaTujuan,
                tv.NamaTravel,
                pk.TglBerangkat,
                pk.TglMaxBayar,
                pk.TglMaxBooking,
                pk.TglPulang,
                pk.TglMaxDokumen,
                pj.NamaJamaah,
                pj.FotoProfil AS FotoProfilJamaah,
                pj.Alamat,
                pj.NoHP AS NoHPJamaah,
                pa.NamaJamaah AS NamaAgen,
                (
                    SELECT IF(COUNT(sp.NoUrutSyarat) = COUNT(ds.NoUrutSyarat), 1, 0)
                    FROM syaratpaket sp
                    LEFT JOIN dokumensyarat ds
                        ON ds.IDTravel=sp.IDTravel
                       AND ds.IDPaket=sp.IDPaket
                       AND ds.NoUrutSyarat=sp.NoUrutSyarat
                    WHERE sp.IDTravel=jp.IDTravel
                      AND sp.IDPaket=jp.IDPaket
                ) AS IsSyaratLengkap,
                NOW() AS ThisTime,
                pk.Gambar1,
                pk.Gambar2,
                pk.Gambar3,
                pk.Gambar4,
                pk.Gambar5,
                prov.IDProv,
                prov.NamaProv,
                kab.IDKab,
                kab.NamaKab,
                kec.IDKec,
                kec.NamaKec,
                desa.IDDesa,
                desa.NamaDesa
            ")
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

        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())->offset($this->getPage());
        }

        if ($this->getCari()) {
            $searchTerm = '%' . $this->getCari() . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('jp.Keterangan', 'like', $searchTerm)
                    ->orWhere('pk.Keterangan', 'like', $searchTerm)
                    ->orWhere('pk.Deskripsi', 'like', $searchTerm)
                    ->orWhere('pk.NarasiPaket', 'like', $searchTerm)
                    ->orWhere('tv.NamaTravel', 'like', $searchTerm);
            });
        }

        if ($callback) {
            $callback($query);
        }

        return $query->get()->toArray();
    }

    /**
     * Find dokumen
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @param string $IDPerson
     * @param int $NoUrutSyarat
     * @return object|null
     */
    public function findDokumen($IDTravel, $IDPaket, $IDPerson, $NoUrutSyarat)
    {
        return DB::table('dokumensyarat')
            ->where([
                'IDTravel'      => $IDTravel,
                'IDPaket'       => $IDPaket,
                'IDPerson'      => $IDPerson,
                'NoUrutSyarat'  => $NoUrutSyarat
            ])
            ->first();
    }

    /**
     * Ganti dokumen
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @param string $IDPerson
     * @param int $NoUrutSyarat
     * @param string $FileDokumen
     * @param string|null $Keterangan
     * @return int
     */
    public function gantiDokumen($IDTravel, $IDPaket, $IDPerson, $NoUrutSyarat, $FileDokumen, $Keterangan = null)
    {
        $data = ['FileDokumen' => $FileDokumen];
        
        if ($Keterangan !== null) {
            $data['Keterangan'] = $Keterangan;
        }

        return DB::table('dokumensyarat')
            ->where([
                'IDTravel'     => $IDTravel,
                'IDPaket'      => $IDPaket,
                'IDPerson'     => $IDPerson,
                'NoUrutSyarat' => $NoUrutSyarat
            ])
            ->update($data);
    }

    /**
     * Get join person
     * 
     * @param array $where
     * @return array
     */
    public function getJoinPerson($where)
    {
        return DB::table('jamaahpaket')
            ->select(
                'jamaahpaket.IDPaket',
                'jamaahpaket.IDPerson',
                'mstperson.NoHP',
                'jamaahpaket.IDTravel',
                'mstperson.NamaJamaah',
                'jamaahpaket.NoInvoice',
                'jamaahpaket.InfoTagihan',
                'jamaahpaket.InfoTerbayar',
                'jamaahpaket.IsPaid',
                'jamaahpaket.NominalPaid'
            )
            ->join('mstperson', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->where($where)
            ->get()
            ->toArray();
    }

    /**
     * Get join person with pagination
     * 
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getJoinPersonPaging($where, $limit, $offset)
    {
        return DB::table('jamaahpaket')
            ->select(
                'jamaahpaket.IDPaket',
                'jamaahpaket.IDPerson',
                'jamaahpaket.IDTravel',
                'mstperson.NamaJamaah',
                'jamaahpaket.NoInvoice',
                'jamaahpaket.InfoTagihan',
                'jamaahpaket.IsPaid'
            )
            ->join('mstperson', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->where($where)
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }

    /**
     * Get join all tables
     * 
     * @param array $where
     * @return array
     */
    public function getJoinAll($where)
    {
        $query = DB::table('jamaahpaket')
            ->select(
                'jamaahpaket.IDPaket',
                'jamaahpaket.IDPerson',
                'jamaahpaket.IDTravel',
                'mstperson.NamaJamaah',
                'jamaahpaket.NoInvoice',
                'jamaahpaket.InfoTagihan',
                'jamaahpaket.IsPaid',
                'mstpaket.NamaPaket',
                'msttravel.NamaTravel',
                'jamaahpaket.IDCabang',
                'jamaahpaket.NominalPaid'
            )
            ->join('mstperson', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->join('mstpaket', 'jamaahpaket.IDPaket', '=', 'mstpaket.IDPaket')
            ->join('msttravel', 'jamaahpaket.IDTravel', '=', 'msttravel.IDTravel');

        // Handle different where formats
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                // Format: [['column', 'operator', 'value']]
                if (count($value) === 3) {
                    $query->where($value[0], $value[1], $value[2]);
                } elseif (count($value) === 2) {
                    $query->where($value[0], $value[1]);
                }
            } else {
                // Format: ['column' => 'value']
                $query->where($key, $value);
            }
        }

        return $query->get()->toArray();
    }

    /**
     * Get tracking data
     * 
     * @param array $where
     * @return array
     */
    public function getTracking($where)
    {
        return DB::table('jamaahpaket')
            ->select(
                'jamaahpaket.TglAwalTrack',
                'jamaahpaket.TglAkhirTrack',
                'mstperson.NamaJamaah',
                'mstperson.NIK',
                'mstperson.KoorLat',
                'mstperson.KoorLong',
                'mstperson.FotoProfil',
                'mstperson.LastModified',
                'jamaahpaket.IDPerson'
            )
            ->join('mstperson', 'mstperson.IDPerson', '=', 'jamaahpaket.IDPerson')
            ->where($where)
            ->get()
            ->toArray();
    }

    /**
     * Total count
     * 
     * @param array $where
     * @return int
     */
    public function total($where)
    {
        return DB::table($this->table)
            ->where($where)
            ->distinct()
            ->count(DB::raw('CONCAT(IDTravel, IDPerson)'));
    }

    /**
     * Total booking dengan kondisi dinamis
     * 
     * @param array $conditions
     * @return int
     */
    public function totalBooking($conditions = [])
    {
        $query = DB::table('jamaahpaket');

        foreach ($conditions as $condition) {
            if (is_array($condition) && count($condition) >= 2) {
                $column = $condition[0];
                $operator = $condition[1];
                $value = $condition[2] ?? null;

                // Handle special MONTH/YEAR functions
                if (stripos($column, 'MONTH(') !== false) {
                    $column = str_replace(['MONTH(', ')'], '', $column);
                    $query->whereMonth($column, $operator);
                } elseif (stripos($column, 'YEAR(') !== false) {
                    $column = str_replace(['YEAR(', ')'], '', $column);
                    $query->whereYear($column, $operator);
                } else {
                    if ($value !== null) {
                        $query->where($column, $operator, $value);
                    } else {
                        $query->where($column, $operator);
                    }
                }
            }
        }

        return $query->distinct()->count('IDPerson');
    }

    /**
     * Total tracking dengan kondisi dinamis
     * 
     * @param array $conditions
     * @return int
     */
    public function totalTracking($conditions = [])
    {
        $query = DB::table('jamaahpaket')
            ->leftJoin('trkastravel', 'trkastravel.NoInvoice', '=', 'jamaahpaket.NoInvoice');

        foreach ($conditions as $condition) {
            if (is_array($condition) && count($condition) >= 2) {
                $column = $condition[0];
                $operator = $condition[1];
                $value = $condition[2] ?? null;

                // Handle special MONTH/YEAR functions
                if (stripos($column, 'MONTH(') !== false) {
                    $column = str_replace(['MONTH(', ')'], '', $column);
                    $query->whereMonth($column, $operator);
                } elseif (stripos($column, 'YEAR(') !== false) {
                    $column = str_replace(['YEAR(', ')'], '', $column);
                    $query->whereYear($column, $operator);
                } else {
                    if ($value !== null) {
                        $query->where($column, $operator, $value);
                    } else {
                        $query->where($column, $operator);
                    }
                }
            }
        }

        return $query->distinct()->count('jamaahpaket.IDPerson');
    }

    /**
     * Get datatables query
     * 
     * @param array $where
     * @return \Illuminate\Database\Query\Builder
     */
    private function getDatatablesQuery($where)
    {
        $whereDate = null;
        if (isset($where['whereDate'])) {
            $whereDate = $where['whereDate'];
            unset($where['whereDate']);
        }

        $query = DB::table('jamaahpaket')
            ->select(
                'jamaahpaket.IDPerson',
                'jamaahpaket.IDTravel',
                'jamaahpaket.IDPaket',
                'mstperson.NamaJamaah',
                'mstperson.Alamat',
                'mstpaket.NamaPaket',
                'mstcabang.NamaCabang',
                'jamaahpaket.IDPersonAgen',
                DB::raw('agen.NamaJamaah AS NamaAgen'),
                'mstpaket.TglBerangkat',
                'mstprov.NamaProv',
                'mstkab.NamaKab',
                'jamaahpaket.InfoTerbayar',
                'jamaahpaket.VerifyPaidDate',
                'jamaahpaket.IsPaid',
                'mstpaket.TglMaxBayar',
                'jamaahpaket.InfoTagihan',
                'jamaahpaket.InfoSisaTagihan',
                'mstpaket.TglMaxDokumen',
                'mstperson.UserName',
                'mstperson.NoHP',
                'mstperson.IsAktif',
                'trkastravel.TanggalTransaksi',
                'mstperson.FotoProfil',
                'mstperson.Tgl_Register'
            )
            ->join('mstperson', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->join('mstpaket', 'mstpaket.IDPaket', '=', 'jamaahpaket.IDPaket')
            ->leftJoin('mstcabang', 'mstcabang.IDCabang', '=', 'jamaahpaket.IDCabang')
            ->leftJoin('mstperson as agen', 'agen.IDPerson', '=', 'jamaahpaket.IDPersonAgen')
            ->leftJoin('mstprov', 'mstprov.IDProv', '=', 'mstperson.IDProv')
            ->leftJoin('mstkab', 'mstkab.IDKab', '=', 'mstperson.IDKab')
            ->leftJoin('trkastravel', 'trkastravel.NoTransaksi', '=', 'jamaahpaket.NoTransaksi')
            ->where($where);

        if ($whereDate && count($whereDate) === 3) {
            $query->whereDate($whereDate[0], $whereDate[1], $whereDate[2]);
        }

        // Search functionality
        if (!empty($_POST['search']['value'])) {
            $searchValue = $_POST['search']['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('mstperson.NamaJamaah', 'like', '%' . $searchValue . '%');
            });
        }

        // Ordering
        if (isset($_POST['order']) && !empty($_POST['order'])) {
            $columnIndex = $_POST['order'][0]['column'];
            $direction = $_POST['order'][0]['dir'];
            
            if (isset($this->column_order[$columnIndex]) && $this->column_order[$columnIndex] !== null) {
                $query->orderBy($this->column_order[$columnIndex], $direction);
            }
        } else {
            // Default ordering
            foreach ($this->order as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    /**
     * Get datatables data
     * 
     * @param array $where
     * @return array
     */
    public function get_datatables($where)
    {
        $query = $this->getDatatablesQuery($where);

        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $query->limit($_POST['length'])->offset($_POST['start']);
        }

        return $query->get()->toArray();
    }

    /**
     * Count filtered results
     * 
     * @param array $where
     * @return int
     */
    public function count_filtered($where)
    {
        return $this->getDatatablesQuery($where)->count();
    }

    /**
     * Count all results
     * 
     * @return int
     */
    public function count_all()
    {
        return DB::table($this->table)->count();
    }

    /**
     * Total tagihan
     * 
     * @param array $where
     * @return object
     */
    public function totalTagihan($where)
    {
        return DB::table($this->table)
            ->join('mstperson', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->join('mstpaket', 'mstpaket.IDPaket', '=', 'jamaahpaket.IDPaket')
            ->where($where)
            ->selectRaw('SUM(InfoSisaTagihan) AS jml')
            ->first();
    }

    /**
     * List testimoni
     * 
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listTestimoni($where, $limit, $offset)
    {
        return DB::table($this->table)
            ->select(
                'jamaahpaket.TanggalTestimoni',
                'jamaahpaket.NarasiTestimoni',
                'jamaahpaket.RatingTestimoni',
                'mstperson.NamaJamaah',
                'mstpaket.NamaPaket'
            )
            ->join('mstperson', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->join('mstpaket', 'mstpaket.IDPaket', '=', 'jamaahpaket.IDPaket')
            ->where($where)
            ->whereNotNull('RatingTestimoni')
            ->orderBy('TanggalTestimoni', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }

    /**
     * Update testimoni
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @param string $IDPerson
     * @param string|null $NarasiTestimoni
     * @param int|null $RatingTestimoni
     * @param string|null $TanggalTestimoni
     * @return int
     */
    public function updateTestimoni($IDTravel, $IDPaket, $IDPerson, $NarasiTestimoni = null, $RatingTestimoni = null, $TanggalTestimoni = null)
    {
        $data = ['TanggalTestimoni' => $TanggalTestimoni ?? date('Y-m-d')];
        
        if ($NarasiTestimoni !== null) {
            $data['NarasiTestimoni'] = $NarasiTestimoni;
        }
        
        if ($RatingTestimoni !== null) {
            $data['RatingTestimoni'] = $RatingTestimoni;
        }

        return DB::table($this->table)
            ->where([
                'IDTravel' => $IDTravel,
                'IDPaket'  => $IDPaket,
                'IDPerson' => $IDPerson
            ])
            ->update($data);
    }

    /**
     * Register jamaah dengan transaksi
     * 
     * @param array $person
     * @param array $jamaahPaket
     * @param array $dokumenSyarat
     * @param bool $isPersonTerdaftar
     * @return bool
     */
    public function registerJamaah(array $person, array $jamaahPaket, array $dokumenSyarat = [], bool $isPersonTerdaftar = false)
    {
        try {
            DB::beginTransaction();

            // Insert person jika belum terdaftar
            if (!$isPersonTerdaftar) {
                DB::table('mstperson')->insert($person);
            }

            // Insert jamaah paket
            DB::table($this->table)->insert($jamaahPaket);

            // Insert dokumen syarat jika ada
            if (!empty($dokumenSyarat)) {
                DB::table('dokumensyarat')->insert($dokumenSyarat);
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error registerJamaah: " . $th->getMessage());
            return false;
        }
    }

    /**
     * Toggle alert status
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @param string $IDPerson
     * @return bool
     */
    public function toggleAlert($IDTravel, $IDPaket, $IDPerson)
    {
        $updated = DB::table($this->table)
            ->where([
                'IDTravel' => $IDTravel,
                'IDPaket'  => $IDPaket,
                'IDPerson' => $IDPerson,
            ])
            ->update(['IsAlert' => DB::raw("IF(IsAlert='1','0','1')")]);

        return (bool) $updated;
    }

    /**
     * Get average testimoni
     * 
     * @param string $IDTravel
     * @return object
     */
    public function getAvgTestimoni($IDTravel)
    {
        return DB::table($this->table)
            ->selectRaw('
                AVG(RatingTestimoni) AS avgRating, 
                COUNT(IDTravel) AS jmlJamaah, 
                SUM(IF(RatingTestimoni IS NULL, 0, 1)) AS jmlReviewer
            ')
            ->where('IDTravel', $IDTravel)
            ->first();
    }

    /**
     * Get leader information
     * 
     * @param string $IDTravel
     * @param string $IDPaket
     * @return object|null
     */
    public function getLeader($IDTravel, $IDPaket)
    {
        return DB::table($this->table)
            ->select(
                'mstperson.NoHP',
                DB::raw('mstperson.NamaJamaah AS NamaLeader')
            )
            ->join('mstperson', 'jamaahpaket.IDPerson', '=', 'mstperson.IDPerson')
            ->where([
                'jamaahpaket.IDTravel' => $IDTravel,
                'jamaahpaket.IDPaket'  => $IDPaket,
                'StatusJamaah'         => 'Leader'
            ])
            ->first();
    }
}