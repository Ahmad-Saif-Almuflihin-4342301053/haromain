<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserLogin extends Authenticatable
{
    use HasFactory, HasDatatablesPagination;

    protected $table = 'userlogin';
    protected $primaryKey = 'UserName';
    public $incrementing = false;
    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'UserName',
        'UserPsw',
        'Email',
        'HPNo',
        'Alamat',
        'NamaLengkap',
        'IsAktif',
        'UserStatus',
        'LevelID',
        'IDTravel',
        'IDCabang',
        'TempatLahir',
        'TanggalLahir',
        'JenisKelamin',
        'Foto',
    ];

    protected $hidden = [
        'UserPsw',
    ];


    protected static array $columnOrder = [
        null,
        'NamaLengkap',
        'Email',
        'UserName',
        'msttravel.NamaTravel',
        'mstcabang.NamaCabang',
        'HPNo'
    ];

    protected static array $columnSearch = [
        'NamaLengkap',
        'Email',
        'userlogin.UserName',
        'HPNo',
        'msttravel.NamaTravel',
        'mstcabang.NamaCabang'
    ];

    protected static array $order = [
        'NamaLengkap' => 'asc'
    ];

    public function getDatatablesQuery()
    {
        $query = DB::table('userlogin')
            ->leftJoin('msttravel', 'userlogin.IDTravel', '=', 'msttravel.IDTravel')
            ->leftJoin('mstcabang', 'userlogin.IDCabang', '=', 'mstcabang.IDCabang')
            ->select('userlogin.*', 'msttravel.NamaTravel', 'mstcabang.NamaCabang');

        $user = Auth::user();

        if ($user && $user->LevelID == 2) {
            $query->where('userlogin.IDTravel', $user->IDTravel);
            if (!empty($user->IDCabang)) {
                $query->where('userlogin.IDCabang', $user->IDCabang);
            }
        }

        if (request('search.value')) {
            $query->where(function ($q) {
                foreach (self::$columnSearch as $i => $item) {
                    if ($i === 0) {
                        $q->where($item, 'like', '%' . request('search.value') . '%');
                    } else {
                        $q->orWhere($item, 'like', '%' . request('search.value') . '%');
                    }
                }
            });
        }

        if (request('order')) {
            $colIndex = request('order.0.column');
            $dir = request('order.0.dir');
            $query->orderBy(self::$columnOrder[$colIndex], $dir);
        } else {
            $query->orderBy(
                array_key_first(self::$order),
                self::$order[array_key_first(self::$order)]
            );
        }

        return $query;
    }

    public function countFiltered()
    {
        return $this->getDatatablesQuery()->count();
    }

    public function countAll()
    {
        $query = DB::table('userlogin');
        $user = Auth::user();
        if ($user && $user->LevelID == 2) {
            $query->where('IDTravel', $user->IDTravel);
            if (!empty($user->IDCabang)) {
                $query->where('IDCabang', $user->IDCabang);
            }
        }
        return $query->count();
    }

}
