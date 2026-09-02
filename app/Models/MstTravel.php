<?php

namespace App\Models;

use Illuminate\Http\Request;
use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstTravel extends Model
{
    use HasFactory, HasDatatableParams ,HasDatatablesPagination, HasSequentialId;

    protected $table = 'msttravel';
    protected $primaryKey = 'IDTravel';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'NamaTravel',
        'NoPPIU',
        'TglBerlakuPPIU',
        'Akreditasi',
        'NamaPemilik',
        'NoTelpPemilik',
        'TglRegister',
        'IsShow',
        'LogoKantor',
        'NamaCP',
        'TelpCP',
        'IDDesa',
        'IDKec',
        'IDKab',
        'IDProv',
        'IsVerified',
        'StatusAkun',
    ];

    protected array $column_order = [
        null,
        'TglRegister',
        'NamaTravel',
        'NamaPemilik',
        'Alamat',
        'NoTelpPemilik',
        'NoPPIU'
    ];

    protected array $column_search = [
        'TglRegister',
        'NamaTravel',
        'NamaPemilik',
        'Alamat',
        'NoTelpPemilik',
        'NoPPIU'
    ];

    protected array $order = ['TglRegister' => 'desc'];

    public function detail($IDTravel)
    {
        return $this->baseQuery()
            ->where('tv.IDTravel', $IDTravel)
            ->first();
    }

    public function findAllTravel()
    {
        $query = $this->baseQuery()
            ->join('agentravel as at', function ($join) {
                $join->on('at.IDTravel', '=', 'tv.IDTravel')
                    ->whereRaw('NOW() BETWEEN at.TglMulaiAgen AND IFNULL(at.TglAkhirAgen, NOW())')
                    ->where('at.IsAktif', 1);
            })
            ->whereRaw('DATE(tv.TglBerlakuPPIU) >= NOW()')
            ->where([
                'tv.IsVerified' => 1,
                'tv.StatusAkun' => 'Aktif',
                'tv.IsShow' => 1
            ])
            ->addSelect([
                'at.LevelWilayah',
                'at.IDProv as IDProvAgen',
                'at.IDKab as IDKabAgen',
                'at.IDKec as IDKecAgen',
                'at.IDDesa as IDDesaAgen'
            ]);

        return $this->applyFilter($query)->get();
    }


    public function findByAgenTravel($IDPerson)
    {
        $query = $this->baseQuery()
            ->join('agentravel as at', function ($join) use ($IDPerson) {
                $join->on('at.IDTravel', '=', 'tv.IDTravel')
                    ->whereRaw('NOW() BETWEEN at.TglMulaiAgen AND IFNULL(at.TglAkhirAgen, NOW())')
                    ->where('at.IsAktif', 1)
                    ->where('at.IDPerson', $IDPerson);
            })
            ->whereRaw('DATE(tv.TglBerlakuPPIU) >= NOW()')
            ->where([
                'tv.IsVerified' => 1,
                'tv.StatusAkun' => 'Aktif',
                'tv.IsShow' => 1
            ])
            ->addSelect([
                'at.LevelWilayah',
                'at.IDProv as IDProvAgen',
                'at.IDKab as IDKabAgen',
                'at.IDKec as IDKecAgen',
                'at.IDDesa as IDDesaAgen'
            ]);

        return $this->applyFilter($query)->get()->toArray();
    }

    private function baseQuery()
    {
        return DB::table('msttravel as tv')
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
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'tv.IDProv')
            ->select([
                'tv.*',
                'desa.NamaDesa',
                'kec.NamaKec',
                'kab.NamaKab',
                'prov.NamaProv'
            ])
            ->groupBy('tv.IDTravel');
    }

    private function applyFilter($query)
    {
        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->where('tv.NamaTravel', 'like', "%{$this->getCari()}%")
                    ->orWhere('tv.NamaPemilik', 'like', "%{$this->getCari()}%")
                    ->orWhere('tv.Alamat', 'like', "%{$this->getCari()}%")
                    ->orWhere('desa.NamaDesa', 'like', "%{$this->getCari()}%")
                    ->orWhere('kec.NamaKec', 'like', "%{$this->getCari()}%")
                    ->orWhere('kab.NamaKab', 'like', "%{$this->getCari()}%")
                    ->orWhere('prov.NamaProv', 'like', "%{$this->getCari()}%");
            });
        }

        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())->offset($this->getPage());
        }

        return $query;
    }




    private function getDatatablesQuery(Request $request)
    {
        $query = DB::table($this->table);

        if ($request->search['value'] ?? false) {
            $query->where(function ($q) use ($request) {
                foreach ($this->column_search as $i => $col) {
                    if ($i === 0) {
                        $q->where($col, 'like', '%' . $request->search['value'] . '%');
                    } else {
                        $q->orWhere($col, 'like', '%' . $request->search['value'] . '%');
                    }
                }
            });
        }

        if ($request->order ?? false) {
            $colIndex = $request->order[0]['column'];
            $dir = $request->order[0]['dir'];
            $query->orderBy($this->column_order[$colIndex], $dir);
        } else {
            foreach ($this->order as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }


    public function count_filtered(Request $request)
    {
        return $this->getDatatablesQuery($request)->count();
    }

    public function count_all()
    {
        return DB::table($this->table)->count();
    }

     public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'IDTravel',
            'prefix' => 'TRV-',
            'pad'    => 7,
        ]);
    }
}
