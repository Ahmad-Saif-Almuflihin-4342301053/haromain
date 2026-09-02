<?php

namespace App\Models;

use App\Traits\HasDatatableParams;
use App\Traits\HasSequentialId;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelPaket extends Model
{
    use HasFactory, HasDatatableParams, HasSequentialId;

    protected $table = 'hotelpaket';
    // protected $primaryKey = 'IDHotel';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDHotel',
        'NamaHotel',
        'AlamatHotel',
        'Bintang',
        'TglMasuk',
        'TglKeluar',
        'KoorLat',
        'KoorLong',
        'IDPaket',
        'IDTravel'
    ];

    protected $whereConditions = [];
    protected $tglHariIni;

     public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'IDHotel',
            'prefix' => 'HTL-',
            'pad'    => 7,
        ]);
    }

    public function findByPaket($IDTravel, $IDPaket)
    {
        date_default_timezone_set("Asia/Jakarta");
        $this->tglHariIni = date('Y-m-d');

        // Simpan kondisi sementara
        $this->whereConditions = [
            'hp.IDTravel' => $IDTravel,
            'hp.IDPaket' => $IDPaket
        ];

        return $this->findAll();
    }

    private function findAll()
    {
        $query = DB::table('hotelpaket as hp')
            ->join('mstpaket as pk', function ($join) {
                $join->on('pk.IDTravel', '=', 'hp.IDTravel')
                    ->on('pk.IDPaket', '=', 'hp.IDPaket');
            })
            ->select('hp.*', 'pk.NamaPaket');

        if (!empty($this->whereConditions)) {
            $query->where($this->whereConditions);
        }

        // Filter tanggal
        if ($this->tglHariIni) {
            $tgl = $this->tglHariIni;
            $query->whereRaw("(DATE('$tgl') BETWEEN hp.TglMasuk AND hp.TglKeluar)");
        }

        // Search
        if ($this->getCari()) {
            $cari = $this->getCari();
            $query->where(function ($q) use ($cari) {
                $q->where('hp.NamaHotel', 'like', "%$cari%")
                    ->orWhere('hp.AlamatHotel', 'like', "%$cari%");
            });
        }

        // Pagination
        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())
                ->offset($this->getPage());
        }

        return $query->get()->toArray();
    }
}
