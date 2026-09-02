<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrKasTravel extends Model
{
    use HasFactory, HasDatatablesPagination;

    protected $table = 'trkastravel';
    // protected $primaryKey = 'NoTransaksi';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NoTransaksi',
        'IDTravel',
        'TanggalTransaksi',
        'JenisTransaksi',
        'UraianTransaksi',
        'Debet',
        'Kredit',
        'NamaPersonTransaksi',
        'IDUserTransaksi',
        'NoInvoice',
        'IDCabang',
        'TanggalInvoice',
        'IsVerified',
        'IDVA',
        'ExpiredVA',
        'NoVa',
        'Bank',
    ];

    protected array $columnOrder = [
        null,
        'trkastravel.NoInvoice',
        'trkastravel.TanggalInvoice',
        'NamaPaket',
        'trkastravel.Kredit',
        'trkastravel.Debet'
    ];

    protected array $columnSearch = [
        'trkastravel.NoInvoice',
        'trkastravel.TanggalInvoice',
        'NamaPaket',
        'trkastravel.Kredit',
        'trkastravel.Debet',
        'TanggalTransaksi',
        'NamaTravel',
        'UraianTransaksi',
        'NamaCabang'
    ];

    protected array $defaultOrder = [
        'trkastravel.NoTransaksi' => 'asc'
    ];

    public function generateIDTrx(): string
    {
        $strDate = date('Ymd');

        $last = DB::table($this->table)
            ->selectRaw('RIGHT(NoTransaksi, 10) AS kode')
            ->where('NoTransaksi', 'like', "%{$strDate}%")
            ->orderByRaw('CAST(RIGHT(NoTransaksi, 10) AS SIGNED) DESC')
            ->limit(1)
            ->first();

        $kode = $last ? ((int) $last->kode + 1) : 1;

        return 'TRX-' . $strDate . '-' . str_pad($kode, 10, '0', STR_PAD_LEFT);
    }


    public function generateNoInvoice(): string
    {
        $strDate = date('Ymd');

        $last = DB::table($this->table)
            ->selectRaw('RIGHT(NoInvoice, 10) AS kode')
            ->where('NoInvoice', 'like', "%{$strDate}%")
            ->orderByRaw('CAST(RIGHT(NoInvoice, 10) AS SIGNED) DESC')
            ->limit(1)
            ->first();

        $kode = $last ? ((int) $last->kode + 1) : 1;

        return 'INV-' . $strDate . '-' . str_pad($kode, 10, '0', STR_PAD_LEFT);
    }



    public function getNoInvoice(array $where)
    {
        return DB::table($this->table)
            ->select('NoInvoice')
            ->whereNotNull('NoInvoice')
            ->where($where)
            ->groupBy('NoInvoice')
            ->get();
    }

    public function findJoinTravel(array $where)
    {
        return DB::table('trkastravel')
            ->select(
                'trkastravel.NoInvoice',
                'trkastravel.Kredit',
                'msttravel.NamaTravel',
                'trkastravel.Bank',
                'trkastravel.NoVa',
                'trkastravel.ExpiredVA'
            )
            ->join('mstcabang', 'mstcabang.IDCabang', '=', 'trkastravel.IDCabang')
            ->join('msttravel', 'msttravel.IDTravel', '=', 'mstcabang.IDTravel')
            ->where($where)
            ->first();
    }


    private function getDatatablesQuery(array $where)
    {
        $query = DB::table('trkastravel')
            ->distinct()
            ->select(
                'trkastravel.NoInvoice',
                'trkastravel.TanggalInvoice',
                'trkastravel.Kredit',
                'trkastravel.Debet',
                'mstpaket.NamaPaket',
                'msttravel.IDTravel',
                'msttravel.NamaTravel',
                'mstpaket.IDPaket',
                'NamaCabang',
                'TanggalTransaksi',
                'trkastravel.NoTransaksi',
                'UraianTransaksi',
                'NominalPaid',
                'trkastravel.NoVa',
                'trkastravel.JenisTransaksi'
            )
            ->leftJoin('jamaahpaket', 'jamaahpaket.NoInvoice', '=', 'trkastravel.NoInvoice')
            ->leftJoin('mstpaket', 'jamaahpaket.IDPaket', '=', 'mstpaket.IDPaket')
            ->join('mstcabang', 'trkastravel.IDCabang', '=', 'mstcabang.IDCabang')
            ->join('msttravel', 'trkastravel.IDTravel', '=', 'msttravel.IDTravel')
            ->where($where)
            ;

        if (request()->has('search.value')) {
            $search = request('search.value');
            $query->where(function ($q) use ($search) {
                foreach ($this->columnSearch as $i => $col) {
                    $i === 0
                        ? $q->where($col, 'like', "%{$search}%")
                        : $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        if (request()->has('order.0.column')) {
            $colIndex = request('order.0.column');
            $dir = request('order.0.dir');
            $query->orderBy($this->columnOrder[$colIndex], $dir);
        } else {
            foreach ($this->defaultOrder as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    public function countFiltered(array $where)
    {
        return $this->getDatatablesQuery($where)->get()->count();
    }

    public function countAll(array $where)
    {
        return $this->getDatatablesQuery($where)->get()->count();
    }
}
