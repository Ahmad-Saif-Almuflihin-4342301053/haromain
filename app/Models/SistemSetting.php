<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SistemSetting extends Model
{
    use HasFactory, HasDatatablesPagination;

    const KODE_BIAYA_TRIP = 1;
    const KODE_VOUCHER_REFERRAL = 4;
    const KODE_VOUCHER_PENDAFTARAN = 5;

    protected $table = 'sistemsetting';
    // protected $primaryKey = 'KodeSetting';
    public $incrementing = false;
    public $timestamps = false;

    protected array $columnOrder = [null, 'KodeSetting', 'Jenis'];
    protected array $columnSearch = ['KodeSetting', 'Jenis'];
    protected array $order = ['KodeSetting' => 'asc'];


    protected $fillable = [
        'Jenis',
        'Value',
        'Value1',
    ];

    public function getBiayaTrip()
    {
        return DB::table('sistemsetting')
            ->where('KodeSetting', self::KODE_BIAYA_TRIP)
            ->first();
    }

    public function getVoucher()
    {
        return DB::table('sistemsetting')
            ->where('KodeSetting', self::KODE_VOUCHER_PENDAFTARAN)
            ->orWhere('KodeSetting', self::KODE_VOUCHER_REFERRAL)
            ->get();
    }

    protected function getDatatablesQuery(Request $request)
    {
        $query = DB::table($this->table);

        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                foreach ($this->columnSearch as $item) {
                    $q->orWhere($item, 'like', "%{$search}%");
                }
            });
        }

        if ($request->has('order')) {
            $colIndex = $request->input('order.0.column');
            $dir = $request->input('order.0.dir');
            $query->orderBy($this->columnOrder[$colIndex], $dir);
        } else {
            $query->orderBy(key($this->order), current($this->order));
        }

        return $query;
    }

    public function countFiltered(Request $request)
    {
        return $this->getDatatablesQuery($request)->count();
    }

    public function countAll()
    {
        return DB::table($this->table)->count();
    }

    public function getVoucherSettings()
    {
        return DB::table($this->table)
            ->whereIn('KodeSetting', ['VOUCHER_REFERRAL', 'VOUCHER_PENDAFTARAN'])
            ->get();
    }
}
