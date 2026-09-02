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
        'IDTravel',
        'Keterangan1',
        'Keterangan2',
        'Gambar',
        'IsAktif',
    ];

    protected $columnOrder = [
        null,
        'Gambar',
        'msttravel.NamaTravel',
        'Keterangan1',
        'Keterangan2',
        'IsAktif'
    ];

    protected $columnSearch = [
        'KodeBanner',
        'Keterangan1',
        'Keterangan2',
        'msttravel.NamaTravel'
    ];

    protected $defaultOrder = [
        'KodeBanner' => 'asc'
    ];

    public function getDatatablesQuery()
    {
        $query = DB::table('mstbanner')
            ->leftJoin('msttravel', 'msttravel.IDTravel', '=', 'mstbanner.IDTravel')
            ->select('mstbanner.*', 'msttravel.NamaTravel');

        $user = Auth::user();
        if ($user && (int)$user->LevelID === 2) {
            $query->where('mstbanner.IDTravel', $user->IDTravel);
        } elseif ($user && (int)$user->LevelID === 1) {
            $filterTravel = request('IDTravel');
            if ($filterTravel === 'haromain') {
                $query->whereNull('mstbanner.IDTravel');
            } elseif (!empty($filterTravel)) {
                $query->where('mstbanner.IDTravel', $filterTravel);
            }
        }

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
        $user = Auth::user();
        if ($user && (int)$user->LevelID === 2) {
            return DB::table('mstbanner')->where('IDTravel', $user->IDTravel)->count();
        } elseif ($user && (int)$user->LevelID === 1) {
            $filterTravel = request('IDTravel');
            if ($filterTravel === 'haromain') {
                return DB::table('mstbanner')->whereNull('IDTravel')->count();
            } elseif (!empty($filterTravel)) {
                return DB::table('mstbanner')->where('IDTravel', $filterTravel)->count();
            }
        }
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
        $banners = self::where('IsAktif', 1)
            ->whereNull('IDTravel')
            ->get()
            ->toArray();

        if (empty($banners)) {
            $banners = self::where('IsAktif', 1)
                ->get()
                ->toArray();
        }

        return $banners;
    }
}
