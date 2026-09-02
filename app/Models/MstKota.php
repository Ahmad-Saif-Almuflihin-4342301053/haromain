<?php

namespace App\Models;

use Illuminate\Http\Request;
use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\Auth;

class MstKota extends Model
{
    use HasFactory, HasDatatableParams, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstkota';
    protected $primaryKey = 'IDKota';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDKota',
        'IDTravel',
        'NamaKota',
        'IsAktif',
    ];

    protected static $columnOrder  = [null, 'IDKota', 'msttravel.NamaTravel', 'NamaKota', 'IsAktif'];
    protected static $columnSearch = ['mstkota.NamaKota', 'msttravel.NamaTravel'];
    protected static $order        = ['mstkota.NamaKota' => 'asc'];

    private $IDKota = null;

    public function generateID()
    {
        $today  = date('Ymd');
        $prefix = 'KOT-' . $today . '-';

        return $this->generateSequentialId([
            'column' => 'IDKota',
            'prefix' => $prefix,
            'pad'    => 4,
            'scope'  => function ($q) use ($prefix) {
                $q->where('IDKota', 'like', $prefix . '%');
            }
        ]);
    }


    public static function findByID($id)
    {
        return self::select('IDKota', 'NamaKota', 'IsAktif')
            ->where('IDKota', $id)
            ->first();
    }

    public function setIDKota(?string $IDKota)
    {
        $this->IDKota = $IDKota;
        return $this;
    }
    public function getIDKota(): ?string
    {
        return $this->IDKota;
    }

    public function findAll(): array
    {
        $query = DB::table($this->table)
            ->select('IDKota', 'NamaKota', 'IsAktif')
            ->orderBy('NamaKota', 'ASC');

        if ($this->getCari()) {
            $query->where('NamaKota', 'like', '%' . $this->getCari() . '%');
        }

        if ($this->getIDKota()) {
            $query->where('IDKota', $this->getIDKota());
        }

        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())->offset($this->getPage());
        }

        return $query->get()->toArray();
    }

    public function countAll(): int
    {
        $query = DB::table($this->table);

        if ($this->getCari()) {
            $query->where('NamaKota', 'like', '%' . $this->getCari() . '%');
        }

        if ($this->getIDKota()) {
            $query->where('IDKota', $this->getIDKota());
        }

        return $query->count();
    }

    public function getDatatablesQuery(Request $request)
    {
        $query = self::query()
            ->leftJoin('msttravel', 'msttravel.IDTravel', '=', 'mstkota.IDTravel')
            ->select('mstkota.*', 'msttravel.NamaTravel');

        $user = Auth::user();
        if ($user && (int)$user->LevelID === 2) {
            $query->where('mstkota.IDTravel', $user->IDTravel);
        }

        if ($request->input('search.value')) {
            $query->where(function ($q) use ($request) {
                foreach (self::$columnSearch as $column) {
                    $q->orWhere($column, 'like', '%' . $request->input('search.value') . '%');
                }
            });
        }

        if ($request->has('order')) {
            $colIndex = $request->input('order.0.column');
            $dir      = $request->input('order.0.dir');
            $column   = self::$columnOrder[$colIndex] ?? null;

            if ($column) {
                $query->orderBy($column, $dir);
            }
        } else {
            foreach (self::$order as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    public function countFiltered(Request $request)
    {
        return $this->getDatatablesQuery($request)->count();
    }

    public static function countAllDatatables()
    {
        $user = Auth::user();
        if ($user && (int)$user->LevelID === 2) {
            return self::where('IDTravel', $user->IDTravel)->count();
        }
        return self::count();
    }
}
