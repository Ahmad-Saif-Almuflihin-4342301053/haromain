<?php

namespace App\Models;

use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengumuman extends Model
{
    use HasFactory, HasDatatableParams, HasDatatablesPagination;

    protected $table = 'pengumuman';
    protected $primaryKey = 'IDPengumuman';
    public $timestamps = false;

    protected $fillable = [
        'IDPaket',
        'IDPerson',
        'IDTravel',
        'Isi',
        'Tanggal',
        'DiBaca',
        'IDProv',
    ];

    protected $IDPaket;
    protected $IDTravel;
    protected $IDPerson;
    protected $IDProv;
    protected $IDProvSet = false;

    public function findAll()
    {
        $query = DB::table('pengumuman as pm')
            ->selectRaw("
                pm.IDPengumuman,
                pm.Tanggal,
                IF(
                    pm.IDProv IS NULL,
                    IF(pm.IDPerson IS NULL, CONCAT('TRAVEL : ', pm.Isi), CONCAT('TL : ', pm.Isi)),
                    CONCAT('HAROMAIN : ', pm.Isi)
                ) AS Isi,
                pm.IDPaket,
                pm.IDPerson,
                pm.IDTravel,
                pm.DiBaca,
                pm.IDProv,
                p.NamaJamaah
            ")
            ->leftJoin('mstperson as p', 'p.IDPerson', '=', 'pm.IDPerson')
            ->orderBy('pm.Tanggal', 'DESC');

        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->where('pm.Isi', 'like', "%{$this->getCari()}%")
                    ->orWhere('p.NamaJamaah', 'like', "%{$this->getCari()}%");
            });
        }

        $query->where(function ($q) {
            if ($this->IDPaket || $this->IDTravel || $this->IDPerson) {
                $q->where(function ($q2) {
                    if ($this->IDPaket) {
                        $q2->where('pm.IDPaket', $this->IDPaket);
                    }
                    if ($this->IDTravel) {
                        $q2->where('pm.IDTravel', $this->IDTravel);
                    }
                    if ($this->IDPerson) {
                        $q2->where(function ($q3) {
                            $q3->where('pm.IDPerson', $this->IDPerson)
                                ->orWhereNull('pm.IDPerson');
                        });
                    }
                });
            }

            if ($this->IDProvSet && $this->IDProv !== null) {
                $q->orWhere(function ($q3) {
                    $q3->where('pm.IDProv', $this->IDProv)
                        ->whereNull('pm.IDPaket')
                        ->whereNull('pm.IDTravel')
                        ->whereNull('pm.IDPerson');
                });
            }
        });

        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())->offset($this->getPage());
        }

        return $query->get();
    }

    public function count_all($where = [])
    {
        return DB::table($this->table)->whereRaw($where)->count();
    }

    public function countAll()
    {
        $query = DB::table('pengumuman as pm')
            ->leftJoin('mstperson as p', 'p.IDPerson', '=', 'pm.IDPerson')
            ->where('pm.DiBaca', '!=', '1');

        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->where('pm.Isi', 'like', "%{$this->getCari()}%")
                    ->orWhere('p.NamaJamaah', 'like', "%{$this->getCari()}%");
            });
        }

        $query->where(function ($q) {
            if ($this->IDPaket || $this->IDTravel || $this->IDPerson) {
                $q->where(function ($q2) {
                    if ($this->IDPaket) {
                        $q2->where('pm.IDPaket', $this->IDPaket);
                    }
                    if ($this->IDTravel) {
                        $q2->where('pm.IDTravel', $this->IDTravel);
                    }
                    if ($this->IDPerson) {
                        $q2->where(function ($q3) {
                            $q3->where('pm.IDPerson', $this->IDPerson)
                                ->orWhereNull('pm.IDPerson');
                        });
                    }
                });
            }

            if ($this->IDProvSet && $this->IDProv !== null) {
                $q->orWhere(function ($q3) {
                    $q3->where('pm.IDProv', $this->IDProv)
                        ->whereNull('pm.IDPaket')
                        ->whereNull('pm.IDTravel')
                        ->whereNull('pm.IDPerson');
                });
            }
        });

        return $query->count();
    }

    private function getDatatablesQuery($where)
    {
        $query = DB::table('pengumuman')
            ->selectRaw('
                pengumuman.Isi,
                pengumuman.Tanggal,
                pengumuman.IDPengumuman,
                mstprov.NamaProv,
                pengumuman.IDProv
            ')
            ->leftJoin('mstprov', 'mstprov.IDProv', '=', 'pengumuman.IDProv')
            ->whereRaw($where)
            ->groupBy([
                'pengumuman.Isi',
                'pengumuman.Tanggal',
                'pengumuman.IDPengumuman',
                'mstprov.NamaProv',
                'pengumuman.IDProv'
            ]);

        if (request('search.value')) {
            $search = request('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('pengumuman.Isi', 'like', "%$search%")
                    ->orWhere('pengumuman.Tanggal', 'like', "%$search%")
                    ->orWhere('mstprov.NamaProv', 'like', "%$search%");
            });
        }

        return $query;
    }

    public function count_filtered($where)
    {
        return $this->getDatatablesQuery($where)->count();
    }

    public function setIDPerson($val)
    {
        $this->IDPerson = $val;
        return $this;
    }
    public function setIDTravel($val)
    {
        $this->IDTravel = $val;
        return $this;
    }
    public function setIDPaket($val)
    {
        $this->IDPaket  = $val;
        return $this;
    }
    public function setIDProv($val)
    {
        $this->IDProv = $val;
        $this->IDProvSet = true;
        return $this;
    }

    public function getIdLast()
    {
        return DB::table('pengumuman')
            ->orderByDesc('IDPengumuman')
            ->value('IDPengumuman');
    }

    public function savePengumuman($IDPaket,$IDTravel,$Isi,  $SomePerson = null)
    {
        if (!$IDTravel || !$IDPaket) {
            return false;
        }

        if ($SomePerson !== null && is_array($SomePerson)) {

            $insertData = [];
            foreach ($SomePerson as $IDPerson) {
                $insertData[] = [
                    'IDTravel' => $IDTravel,
                    'IDPaket'  => $IDPaket,
                    'IDPerson' => $IDPerson,
                    'Isi'      => $Isi,
                    'DiBaca'   => 0,
                ];
            }

            $inserted = DB::table($this->table)->insert($insertData);

            return $inserted && count($insertData) === count($SomePerson);
        }

        $inserted = DB::table($this->table)->insert([
            'IDTravel' => $IDTravel,
            'IDPaket'  => $IDPaket,
            'Isi'      => $Isi,
            'DiBaca'   => 0,
        ]);

        return $inserted;
    }
}
