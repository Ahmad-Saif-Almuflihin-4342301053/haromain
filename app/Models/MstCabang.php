<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstCabang extends Model
{
    use HasFactory, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstcabang';
    protected $primaryKey = 'IDCabang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDCabang',
        'IDTravel',
        'NamaCabang',
        'NamaPimpinanCabang',
        'NoHPPimp',
        'TglAwalBerdiri',
        'TglAkhirCabang',
        'UserName',
        'Password',
        'IsAktif',
        'LevelWilayah',
        'NamaCP',
        'TelpCP',
        'IDDesa',
        'IDKec',
        'IDKab',
        'IDProv',
        'DokPembukaanCabang',
    ];

    protected array $column_order = ['NamaCabang', 'NamaPimpinanCabang', 'NoHPPimp', 'LevelWilayah', 'NamaTravel'];
    protected array $column_search = ['NamaCabang', 'NamaPimpinanCabang', 'NoHPPimp', 'LevelWilayah', 'msttravel.NamaTravel'];
    protected array $order = ['IDCabang' => 'asc'];


    private function getDatatablesQuery()
    {
        $query = DB::table($this->table)
            ->leftJoin('msttravel', 'msttravel.IDTravel', '=', $this->table . '.IDTravel')
            ->select($this->table . '.*', 'msttravel.NamaTravel');

        if (Auth::user() && Auth::user()->IDTravel) {
            $query->where($this->table . '.IDTravel', Auth::user()->IDTravel);
        }

        // Search
        $searchValue = $_POST['search']['value'] ?? null;
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                foreach ($this->column_search as $i => $item) {
                    if ($i === 0) {
                        $q->where($item, 'like', "%{$searchValue}%");
                    } else {
                        $q->orWhere($item, 'like', "%{$searchValue}%");
                    }
                }
            });
        }

        // Order
        if (isset($_POST['order'])) {
            $colIndex = $_POST['order'][0]['column'];
            $dir = $_POST['order'][0]['dir'];
            $column = $this->column_order[$colIndex] ?? 'IDCabang';
            $query->orderBy($column, $dir);
        } else {
            $query->orderBy(key($this->order), $this->order[key($this->order)]);
        }

        return $query;
    }

    public function countFiltered()
    {
        $query = $this->getDatatablesQuery();
        return $query->count();
    }

    public function countAll()
    {
        $query = DB::table($this->table);
        if (Auth::user() && Auth::user()->IDTravel) {
            $query->where('IDTravel', Auth::user()->IDTravel);
        }
        return $query->count();
    }

    public function getByTravel($IDTravel)
    {
        return DB::table($this->table.' as c')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'c.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDProv', '=', 'c.IDProv')
                    ->on('kab.IDKab', '=', 'c.IDKab');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDProv', '=', 'c.IDProv')
                    ->on('kec.IDKab', '=', 'c.IDKab')
                    ->on('kec.IDKec', '=', 'c.IDKec');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDProv', '=', 'c.IDProv')
                    ->on('desa.IDKab', '=', 'c.IDKab')
                    ->on('desa.IDKec', '=', 'c.IDKec')
                    ->on('desa.IDDesa', '=', 'c.IDDesa');
            })
            ->where('c.IsAktif', 1)
            ->where('c.IDTravel', 'like', $IDTravel)
            ->select(
                'c.*',
                'prov.NamaProv',
                'kab.NamaKab',
                'kec.NamaKec',
                'desa.NamaDesa'
            )
            ->get()
            ->toArray();
    }

     public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'IDCabang',
            'prefix' => 'CBG-',
            'pad'    => 7,
        ]);
    }
}
