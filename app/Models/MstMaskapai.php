<?php

namespace App\Models;

use Illuminate\Http\Request;
use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;

class MstMaskapai extends Model
{
    use HasDatatableParams, HasDatatablesPagination;
    protected $table = 'mstmaskapai';
    protected $primaryKey = 'IDMaskapai';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'IDMaskapai',
        'Maskapai',
        'Logo',
        'IsAktif',
    ];

    public $timestamps = false;

    protected static $columnOrder  = [null, 'Maskapai', 'Logo', 'IsAktif', null];
    protected static $columnSearch = ['Maskapai'];
    protected static $order        = ['Maskapai' => 'asc'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->IDMaskapai)) {
                $maxNumber = self::query()
                    ->selectRaw("MAX(CAST(REGEXP_REPLACE(IDMaskapai, '[^0-9]', '') AS UNSIGNED)) as max_number")
                    ->where('IDMaskapai', 'REGEXP', '^[A-Za-z_-]*[0-9]+$')
                    ->value('max_number');

                $newNumber = ((int) $maxNumber) + 1;
                $model->IDMaskapai = 'MAS-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public static function findByID($id)
    {
        return self::select('IDMaskapai', 'Maskapai', 'Logo', 'IsAktif')
            ->where('IDMaskapai', $id)
            ->first();
    }

    public function setIDMaskapai(?string $IDMaskapai)
    {
        $this->IDMaskapai = $IDMaskapai;
        return $this;
    }
    public function getIDMaskapai(): ?string
    {
        return $this->IDMaskapai;
    }

    public function findAll(): array
    {
        $query = DB::table($this->table)
            ->select('IDMaskapai', 'Maskapai', 'Logo', 'IsAktif')
            ->orderBy('Maskapai', 'ASC');

        if ($this->getCari()) {
            $query->where('Maskapai', 'like', '%' . $this->getCari() . '%');
        }

        if ($this->getIDMaskapai()) {
            $query->where('IDMaskapai', $this->getIDMaskapai());
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
            $query->where('Maskapai', 'like', '%' . $this->getCari() . '%');
        }

        if ($this->getIDMaskapai()) {
            $query->where('IDMaskapai', $this->getIDMaskapai());
        }

        return $query->count();
    }

    private function datatablesQuery(Request $request)
    {
        $query = self::query();


        if ($request->input('search.value')) {
            $query->where(function ($q) use ($request) {
                foreach (self::$columnSearch as $column) {
                    $q->orWhere($column, 'like', '%' . $request->input('search.value') . '%');
                }
            });
        }

        if ($request->has('order')) {
            $colIndex = $request->input('order.0.column');
            $dir = $request->input('order.0.dir');
            $column = self::$columnOrder[$colIndex] ?? null;

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
        return $this->datatablesQuery($request)->count();
    }

    public static function countAllDatatables()
    {
        return self::count();
    }

    public static function getLandingMaskapaiList(): array
    {
        return self::select('Maskapai')
            ->where('IsAktif', 1)
            ->orderBy('Maskapai', 'ASC')
            ->get()
            ->toArray();
    }
}
