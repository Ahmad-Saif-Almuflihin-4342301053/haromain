<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiPromo extends Model
{
    use HasFactory, HasDatatablesPagination, HasSequentialId;

    protected $table = 'transaksipromo';
    protected $primaryKey = 'IDTransaksi';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTransaksi',
        'HargaPromo',
        'IDPaket',
        'IDTravel',
        'IsVerified',
        'TglBerakhir',
        'TglMulai',
    ];

    public function getPromoAktif()
    {
        return DB::table('transaksipromo')
            ->select(
                'Gambar1',
                'NamaPaket',
                'KategoriPaket',
                'TglBerangkat',
                'NamaTravel',
                'NamaKab',
                'TglMulai',
                'TglBerakhir',
                'transaksipromo.IDPaket'
            )
            ->join('mstpaket', 'mstpaket.IDPaket', '=', 'transaksipromo.IDPaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'transaksipromo.IDTravel')
            ->join('mstkab', 'mstkab.IDKab', '=', 'msttravel.IDKab')
            ->where('transaksipromo.IsVerified', 1)
            ->whereRaw('DATE(transaksipromo.TglMulai) <= CURRENT_DATE')
            ->whereRaw('DATE(transaksipromo.TglBerakhir) >= CURRENT_DATE')
            ->get();
    }

    public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'IDTransaksi',
            'prefix' => 'TRP-',
            'pad'    => 7,
        ]);
    }

    public function getPromoDetail($IDTransaksi)
    {
        return DB::table($this->table)
            ->where('IDTransaksi', $IDTransaksi)
            ->first();
    }

    public function createPromo(array $data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updatePromo($IDTransaksi, array $data)
    {
        return DB::table($this->table)
            ->where('IDTransaksi', $IDTransaksi)
            ->update($data);
    }


    public function deletePromo($IDTransaksi)
    {
        return DB::table($this->table)
            ->where('IDTransaksi', $IDTransaksi)
            ->delete();
    }

    protected $columnOrder = [
        null,
        'transaksipromo.IDTransaksi',
        'mstpaket.NamaPaket',
        'msttravel.NamaTravel',
        'transaksipromo.TglMulai',
        'transaksipromo.TglBerakhir',
        'transaksipromo.HargaPromo',
        'transaksipromo.IsVerified'
    ];

    protected $columnSearch = [
        'transaksipromo.IDTransaksi',
        'mstpaket.NamaPaket',
        'msttravel.NamaTravel'
    ];

    protected $order = ['transaksipromo.IDTransaksi' => 'desc'];

    private function getDatatablesQuery($where = [])
    {
        $query = DB::table($this->table)
            ->select(
                'transaksipromo.*',
                'mstpaket.NamaPaket',
                'msttravel.NamaTravel'
            )
            ->join('mstpaket', 'mstpaket.IDPaket', '=', 'transaksipromo.IDPaket')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'transaksipromo.IDTravel')
            ->where($where);

        if (!empty(request('search.value'))) {
            $search = request('search.value');
            $query->where(function ($q) use ($search) {
                foreach ($this->columnSearch as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        if (request()->has('order')) {
            $orderColIndex = request('order.0.column');
            $orderDir = request('order.0.dir');
            if (isset($this->columnOrder[$orderColIndex])) {
                $query->orderBy($this->columnOrder[$orderColIndex], $orderDir);
            }
        } else {
            foreach ($this->order as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    public function countFiltered($where = [])
    {
        return $this->getDatatablesQuery($where)->count();
    }

    public function countAll($where = [])
    {
        return DB::table($this->table)
            ->where($where)
            ->count();
    }
}
