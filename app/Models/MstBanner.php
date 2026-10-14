<?php

namespace App\Models;

use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MstBanner extends Model
{
    use HasFactory, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstbanner';
    protected $primaryKey = 'KodeBanner';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeBanner',
        'Keterangan1',
        'Keterangan2',
        'Gambar',
        'IsAktif',
    ];

    protected $columnOrder = [
        null,
        'Gambar',
        'Keterangan1',
        'Keterangan2',
        'IsAktif'
    ];

    protected $columnSearch = [
        'KodeBanner',
        'Keterangan1',
        'Keterangan2'
    ];

    protected $defaultOrder = [
        'KodeBanner' => 'asc'
    ];

    public function getDatatablesQuery()
    {
        $query = DB::table('mstbanner')
            ->select('mstbanner.*');

        if (!empty(request('search.value'))) {
            $query->where(function ($q) {
                foreach ($this->columnSearch as $item) {
                    $q->orWhere($item, 'like', '%' . request('search.value') . '%');
                }
            });
        }

        if (request()->has('order')) {
            $colIndex = request('order.0.column');
            $dir = request('order.0.dir');
            $col = $this->columnOrder[$colIndex] ?? 'KodeBanner';
            if ($col) {
                $query->orderBy($col, $dir);
            }
        } else {
            foreach ($this->defaultOrder as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    public function countFiltered()
    {
        return $this->getDatatablesQuery()->count();
    }

    public function countAll()
    {
        return DB::table('mstbanner')->count();
    }

     public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'KodeBanner',
            'prefix' => 'BNR-',
            'pad'    => 7,
        ]);
    }

    public static function getLandingBanner()
    {
        return self::where('IsAktif', 1)
            ->get()
            ->toArray();
    }
}
