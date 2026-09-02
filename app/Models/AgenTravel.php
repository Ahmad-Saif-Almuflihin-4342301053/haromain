<?php

namespace App\Models;

use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;

class AgenTravel extends Model
{
    use HasDatatableParams, HasDatatablesPagination;

    protected $table = 'agentravel';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'LevelWilayah',
        'IDProv',
        'IDKab',
        'IDKec',
        'IDDesa',
        'IDPerson',
        'TglMulaiAgen',
        'TglAkhirAgen',
        'IsAktif',
        'DokMoU',
        'NoWAAgen'
    ];

    protected int $IsAktif = 1;
    protected bool $TugasAktif = true;

    protected array $column_order = [
        null,
        'NamaJamaah',
        'NoHP',
        'TglMulaiAgen',
        'TglAkhirAgen',
        'LevelWilayah'
    ];

    protected array $column_search = [
        'NamaJamaah',
        'NoHP',
        'TglMulaiAgen',
        'TglAkhirAgen',
        'LevelWilayah'
    ];

    protected array $order = [
        'IDPerson' => 'asc'
    ];



    public function findByAgen($IDPerson)
    {
        $this->setLimit(-1);

        return $this->findAll(function ($query) use ($IDPerson) {
            $query->where('agt.IDPerson', $IDPerson);
        });
    }

    private function findAll($callback = null)
    {
        $query = DB::table('agentravel as agt')
            ->select(
                'agt.*',
                'p.NamaJamaah AS NamaAgen',
                'prov.NamaProv',
                'kab.NamaKab',
                'kec.NamaKec',
                'desa.NamaDesa'
            )
            ->join('mstperson as p', 'p.IDPerson', '=', 'agt.IDPerson')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'agt.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDKab', '=', 'agt.IDKab')
                    ->on('kab.IDProv', '=', 'agt.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDKec', '=', 'agt.IDKec')
                    ->on('kec.IDKab', '=', 'agt.IDKab')
                    ->on('kec.IDProv', '=', 'agt.IDProv');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDDesa', '=', 'agt.IDDesa')
                    ->on('desa.IDKec', '=', 'agt.IDKec')
                    ->on('desa.IDKab', '=', 'agt.IDKab')
                    ->on('desa.IDProv', '=', 'agt.IDProv');
            });

        if ($this->getTugasAktif()) {
            $query->whereRaw("
                (
                    IF(
                        agt.TglMulaiAgen IS NOT NULL
                        AND agt.TglAkhirAgen IS NOT NULL,
                        (NOW() BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen),
                        NOW() >= agt.TglMulaiAgen
                    )
                )
            ");
        }



        if ($this->IsAktif !== null) {
            $query->where('agt.IsAktif', $this->IsAktif);
        }

        if ($callback) {
            $callback($query);
        }

        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())
                ->offset($this->getPage());
        }

        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->like('agt.LevelWilayah', $this->getCari())
                    ->orWhere('agt.NoWAAgen', 'like', '%' . $this->getCari() . '%')
                    ->orWhere('p.NamaJamaah', 'like', '%' . $this->getCari() . '%');
            });
        }

        return $query->get()->toArray();
    }


    public function getAgenTravel($IDTravel, $IDCabang = null)
    {
        $query = DB::table($this->table.' as agt')
            ->join('mstperson as p', 'p.IDPerson', '=', 'agt.IDPerson')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'agt.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDKab', '=', 'agt.IDKab')
                    ->on('kab.IDProv', '=', 'agt.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDKec', '=', 'agt.IDKec')
                    ->on('kec.IDKab', '=', 'agt.IDKab')
                    ->on('kec.IDProv', '=', 'agt.IDProv');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDDesa', '=', 'agt.IDDesa')
                    ->on('desa.IDKec', '=', 'agt.IDKec')
                    ->on('desa.IDKab', '=', 'agt.IDKab')
                    ->on('desa.IDProv', '=', 'agt.IDProv');
            })
            ->leftJoin('mstcabang as c', 'c.IDTravel', '=', 'agt.IDTravel')
            ->where('agt.IDTravel', $IDTravel)
            ->where('agt.IsAktif', 1)
            ->whereRaw("
            IF(
                agt.TglMulaiAgen IS NOT NULL
                AND agt.TglAkhirAgen IS NOT NULL,
                (NOW() BETWEEN agt.TglMulaiAgen AND agt.TglAkhirAgen),
                NOW() >= agt.TglMulaiAgen
            )
        ")
            ->select(
                'agt.*',
                'p.NamaJamaah as NamaAgen',
                'prov.NamaProv',
                'kab.NamaKab',
                'kec.NamaKec',
                'desa.NamaDesa'
            );

        if ($IDCabang !== null) {
            $query->where('c.IDCabang', $IDCabang)
                ->whereRaw("
                IF(
                    agt.LevelWilayah = 'Desa',
                    (c.IDProv, c.IDKab, c.IDKec, c.IDDesa) = (agt.IDProv, agt.IDKab, agt.IDKec, agt.IDDesa),
                    IF(
                        agt.LevelWilayah = 'Kecamatan',
                        (c.IDProv, c.IDKab, c.IDKec) = (agt.IDProv, agt.IDKab, agt.IDKec),
                        IF(
                            agt.LevelWilayah = 'Kabupaten',
                            (c.IDProv, c.IDKab) = (agt.IDProv, agt.IDKab),
                            IF(
                                agt.LevelWilayah = 'Provinsi',
                                c.IDProv = agt.IDProv,
                                TRUE
                            )
                        )
                    )
                )
              ");
        }

        return $query->get()->toArray();
    }





    public function getIsAktif()
    {
        return $this->IsAktif;
    }

    public function setIsAktif($IsAktif)
    {
        $this->IsAktif = $IsAktif;
        return $this;
    }

    public function getTugasAktif()
    {
        return $this->TugasAktif;
    }

    public function setTugasAktif($TugasAktif)
    {
        $this->TugasAktif = $TugasAktif;
        return $this;
    }

    public function total($where)
    {
        return DB::table($this->table)
            ->where($where)
            ->count();
    }

    public static function findJoinPerson($where)
    {
        $query = DB::table('agentravel')
            ->select(
                'agentravel.IDProv',
                'agentravel.IDKab',
                'agentravel.IDKec',
                'agentravel.IDDesa',
                'agentravel.IDTravel',
                'TglMulaiAgen',
                'TglAkhirAgen',
                'DokMoU',
                'agentravel.IDPerson',
                'mstperson.NamaJamaah',
                'mstperson.NIK',
                'mstperson.TempatLahir',
                'mstperson.TanggalLahir',
                'mstperson.Alamat',
                'mstperson.NoPasspor',
                'mstperson.UserName',
                'mstperson.JenisKelamin',
                'mstperson.FotoProfil',
                'mstperson.Email',
                'mstperson.NoHP',
                'mstperson.KodeReferral',
                'agentravel.IsAktif',
                'agentravel.LevelWilayah'
            )
            ->join('mstperson', 'mstperson.IDPerson', '=', 'agentravel.IDPerson');

        if (is_array($where)) {
            $formattedWhere = [];
            foreach ($where as $key => $value) {
                $qualifiedKey = strpos($key, '.') === false ? 'agentravel.' . $key : $key;
                $formattedWhere[$qualifiedKey] = $value;
            }
            $query->where($formattedWhere);
        } else {
            $query->where('agentravel.IDPerson', $where);
        }

        return $query->first();
    }

    public static function getJoinPerson($where)
    {
        $query = DB::table('agentravel')
            ->select(
                'agentravel.IDProv',
                'agentravel.IDKab',
                'agentravel.IDKec',
                'agentravel.IDDesa',
                'agentravel.IDTravel',
                'TglMulaiAgen',
                'TglAkhirAgen',
                'DokMoU',
                'agentravel.IDPerson',
                'mstperson.NamaJamaah',
                'mstperson.NIK',
                'mstperson.TempatLahir',
                'mstperson.TanggalLahir',
                'mstperson.Alamat',
                'mstperson.NoPasspor',
                'mstperson.UserName',
                'mstperson.JenisKelamin',
                'mstperson.FotoProfil',
                'mstperson.Email',
                'mstperson.NoHP',
                'mstperson.KodeReferral',
                'agentravel.IsAktif',
                'agentravel.LevelWilayah'
            )
            ->join('mstperson', 'mstperson.IDPerson', '=', 'agentravel.IDPerson');

        if (is_array($where)) {
            $formattedWhere = [];
            foreach ($where as $key => $value) {
                $qualifiedKey = strpos($key, '.') === false ? 'agentravel.' . $key : $key;
                $formattedWhere[$qualifiedKey] = $value;
            }
            $query->where($formattedWhere);
        } else {
            $query->where('agentravel.IDPerson', $where);
        }

        return $query->get();
    }

    public function getByLevelWilayah($IDTravel, $prov, $kab, $kec, $desa)
    {
        return DB::table('agentravel')
            ->select(
                'agentravel.IDProv',
                'agentravel.IDKab',
                'agentravel.IDKec',
                'agentravel.IDDesa',
                'TglMulaiAgen',
                'TglAkhirAgen',
                'DokMoU',
                'agentravel.IDPerson',
                'NamaJamaah',
                'NIK',
                'TempatLahir',
                'TanggalLahir',
                'Alamat',
                'NoPasspor',
                'UserName',
                'JenisKelamin',
                'FotoProfil',
                'Email',
                'NoHP',
                'agentravel.IsAktif'
            )
            ->join('mstperson', 'mstperson.IDPerson', '=', 'agentravel.IDPerson')
            ->where('agentravel.IDTravel', $IDTravel)
            ->where('agentravel.IsAktif', 1)
            ->where(function ($q) use ($prov) {
                $q->where('agentravel.IDProv', $prov)
                    ->orWhere('agentravel.LevelWilayah', 'NASIONAL');
            })
            ->where(function ($q) use ($kab) {
                $q->where('agentravel.IDKab', $kab)
                    ->orWhereNull('agentravel.IDKab');
            })
            ->where(function ($q) use ($kec) {
                $q->where('agentravel.IDKec', $kec)
                    ->orWhereNull('agentravel.IDKec');
            })
            ->where(function ($q) use ($desa) {
                $q->where('agentravel.IDDesa', $desa)
                    ->orWhereNull('agentravel.IDDesa');
            })
            ->get();
    }

    public function findAgentForAgenAndBooking($IDPersonAgen, $TanggalBooking)
    {
        return DB::table('agentravel as agt')
            ->distinct()
            ->select(
                'agt.IDTravel',
                'agt.LevelWilayah',
                'agt.IDPerson',
                'tv.NamaTravel',
                'tv.IDProv',
                'prov.NamaProv',
                'tv.IDKab',
                'kab.NamaKab',
                'tv.IDKec',
                'kec.NamaKec',
                'tv.IDDesa',
                'desa.NamaDesa'
            )
            ->leftJoin('jamaahpaket as jp', function ($join) {
                $join->on('jp.IDTravel', '=', 'agt.IDTravel')
                    ->on('jp.IDPersonAgen', '=', 'agt.IDPerson');
            })
            ->leftJoin('msttravel as tv', 'tv.IDTravel', '=', 'agt.IDTravel')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'tv.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDKab', '=', 'tv.IDKab')
                    ->on('kab.IDProv', '=', 'tv.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDKec', '=', 'tv.IDKec')
                    ->on('kec.IDKab', '=', 'tv.IDKab')
                    ->on('kec.IDProv', '=', 'tv.IDProv');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDDesa', '=', 'tv.IDDesa')
                    ->on('desa.IDKec', '=', 'tv.IDKec')
                    ->on('desa.IDKab', '=', 'tv.IDKab')
                    ->on('desa.IDProv', '=', 'tv.IDProv');
            })
            ->whereRaw('DATE(jp.TanggalBooking) >= agt.TglMulaiAgen')
            ->WhereRaw('DATE(jp.TanggalBooking) <= agt.TglAkhirAgen')

            ->where('agt.IDPerson', $IDPersonAgen)
            ->limit(1)
            ->first();
    }

    public function get_datatables(array $where = [])
    {
        $query = $this->getDatatablesQuery($where);

        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $query->limit($_POST['length'])->offset($_POST['start']);
        }

        return $query->get()->toArray();
    }

    private function getDatatablesQuery(array $where = [])
    {
        $query = DB::table('agentravel')
            ->select(
                'agentravel.IDProv',
                'agentravel.IDKab',
                'agentravel.IDKec',
                'agentravel.IDDesa',
                'agentravel.IDTravel',
                'agentravel.TglMulaiAgen',
                'agentravel.TglAkhirAgen',
                'agentravel.DokMoU',
                'agentravel.IDPerson',
                'mstperson.NamaJamaah',
                'mstperson.NIK',
                'mstperson.TempatLahir',
                'mstperson.TanggalLahir',
                'mstperson.Alamat',
                'mstperson.NoPasspor',
                'mstperson.UserName',
                'mstperson.JenisKelamin',
                'mstperson.FotoProfil',
                'mstperson.Email',
                'mstperson.NoHP',
                'mstperson.KodeReferral',
                'agentravel.IsAktif',
                'agentravel.LevelWilayah',
                'msttravel.NamaTravel'
            )
            ->join('mstperson', 'mstperson.IDPerson', '=', 'agentravel.IDPerson')
            ->leftJoin('msttravel', 'msttravel.IDTravel', '=', 'agentravel.IDTravel');

        if (!empty($where)) {
            $query->where($where);
        }

        if (!empty($_POST['search']['value'])) {
            $searchValue = $_POST['search']['value'];
            $query->where(function ($q) use ($searchValue) {
                foreach ($this->column_search as $item) {
                    $column = strpos($item, '.') === false ? 'mstperson.' . $item : $item;
                    $q->orWhere($column, 'like', '%' . $searchValue . '%');
                }
                $q->orWhere('msttravel.NamaTravel', 'like', '%' . $searchValue . '%');
            });
        }

        if (isset($_POST['order'])) {
            $columnIndex = $_POST['order'][0]['column'];
            $orderCol = $this->column_order[$columnIndex] ?? 'agentravel.IDPerson';
            if ($orderCol) {
                $column = strpos($orderCol, '.') === false ? 'mstperson.' . $orderCol : $orderCol;
                $query->orderBy(
                    $column,
                    $_POST['order'][0]['dir']
                );
            }
        } else {
            $order = $this->order;
            $query->orderBy('agentravel.IDPerson', 'desc');
        }

        return $query;
    }

    public function count_filtered(array $where = [])
    {
        return $this->getDatatablesQuery($where)->count();
    }

    public function count_all(array $where = [])
    {
        $query = DB::table($this->table);
        if (!empty($where)) {
            $query->where($where);
        }
        return $query->count();
    }
}
