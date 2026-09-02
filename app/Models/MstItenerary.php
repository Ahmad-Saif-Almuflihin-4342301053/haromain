<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use App\Traits\HasDatatablesPagination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstItenerary extends Model
{
    use HasFactory, HasDatatableParams , HasDatatablesPagination;

    protected $table = 'mstitenerary';
    // protected $primaryKey = 'NoUrut';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NoUrut',
        'IDPaket',
        'IDTravel',
        'TglMulai',
        'TglSelesai',
        'KoorLat',
        'KoorLong',
        'Lokasi',
        'Agenda',
        'HariKe',
        'IsAktif',
    ];

    protected $IDTravel;
    protected $IDPaket;
    protected $NoUrut;
    protected $HariKe;


    public function findCurrent($currentTime = null)
    {
        if (!$this->IDTravel || !$this->IDPaket) {
            return false;
        }

        $currentTime = $currentTime ?? Carbon::now('Asia/Jakarta');

        return DB::table('mstitenerary as i')
            ->leftJoin('mstpaket as p', function ($join) {
                $join->on('p.IDPaket', '=', 'i.IDPaket')
                    ->on('p.IDTravel', '=', 'i.IDTravel');
            })
            ->select('i.*', 'p.NamaPaket', 'p.TglBerangkat')
            ->where('i.TglSelesai', '>=', $currentTime)
            ->where('i.IsAktif', 1)
            ->where('i.IDTravel', $this->IDTravel)
            ->where('i.IDPaket', $this->IDPaket)
            ->orderBy('i.TglMulai', 'asc')
            ->first();
    }

    public function findAll(array $where = [])
    {
        $query = DB::table('mstitenerary as i')
            ->select('i.*')
            ->where('i.IsAktif', 1);

        if ($this->IDTravel) {
            $query->where('i.IDTravel', $this->IDTravel);
        }

        if ($this->IDPaket) {
            $query->where('i.IDPaket', $this->IDPaket);
        }

        if ($this->NoUrut) {
            $query->where('i.NoUrut', $this->NoUrut);
        }

        if ($this->HariKe) {
            $query->where('i.HariKe', $this->HariKe);
        }

        if ($this->getCari()) {
            $query->where(function ($q) {
                $q->where('i.Agenda', 'like', "%{$this->getCari()}%")
                    ->orWhere('i.Lokasi', 'like', "%{$this->getCari()}%");
            });
        }

        if (!empty($where)) {
            $query->where($where);
        }

        if ($this->getLimit() > 0) {
            $query->limit($this->getLimit())->offset($this->getPage());
        }

        return $query
            ->orderBy('i.TglMulai')
            ->orderBy('i.TglSelesai')
            ->get();
    }

    public function createNoUrut()
    {
        if (!$this->IDTravel || !$this->IDPaket) {
            return false;
        }

        $last = DB::table('mstitenerary')
            ->where('IDTravel', $this->IDTravel)
            ->where('IDPaket', $this->IDPaket)
            ->orderByDesc('NoUrut')
            ->value('NoUrut');

        return $last ? ((int) $last + 1) : 1;
    }


    public function saveItinerary($TglMulai, $TglSelesai, $KoorLat, $KoorLong, $Lokasi, $Agenda, $HariKe)
    {
        if (!$this->IDTravel || !$this->IDPaket) {
            return false;
        }

        $NoUrut = $this->createNoUrut();

        return DB::table('mstitenerary')->insert([
            'NoUrut' => $NoUrut,
            'IDTravel' => $this->IDTravel,
            'IDPaket' => $this->IDPaket,
            'TglMulai' => $TglMulai,
            'TglSelesai' => $TglSelesai,
            'KoorLat' => $KoorLat,
            'KoorLong' => $KoorLong,
            'Lokasi' => $Lokasi,
            'Agenda' => $Agenda,
            'IsAktif' => 1,
            'HariKe' => $HariKe,
        ]);
    }

    public function saveAll($IDPaket, $IDTravel, array $dataList)
    {
        $this->IDPaket = $IDPaket;
        $this->IDTravel = $IDTravel;

        $startNo = $this->createNoUrut();
        $rows = [];

        foreach ($dataList as $i => $item) {
            $rows[] = [
                'NoUrut' => $startNo + $i,
                'IDTravel' => $IDTravel,
                'IDPaket' => $IDPaket,
                'TglMulai' => Carbon::parse($item['TglMulai']),
                'TglSelesai' => Carbon::parse($item['TglSelesai']),
                'KoorLat' => $item['KoorLat'] ?? null,
                'KoorLong' => $item['KoorLong'] ?? null,
                'Lokasi' => $item['Lokasi'],
                'Agenda' => $item['Agenda'],
                'IsAktif' => 1,
                'HariKe' => $item['HariKe'],
            ];
        }

        return DB::table('mstitenerary')->insert($rows);
    }

    public function updateItinerary($TglMulai, $TglSelesai, $KoorLat, $KoorLong, $Lokasi, $Agenda)
    {
        if (!$this->IDTravel || !$this->IDPaket || !$this->NoUrut) {
            return false;
        }

        return DB::table('mstitenerary')
            ->where('IDTravel', $this->IDTravel)
            ->where('IDPaket', $this->IDPaket)
            ->where('NoUrut', $this->NoUrut)
            ->update([
                'TglMulai' => $TglMulai,
                'TglSelesai' => $TglSelesai,
                'KoorLat' => $KoorLat,
                'KoorLong' => $KoorLong,
                'Lokasi' => $Lokasi,
                'Agenda' => $Agenda,
            ]);
    }
    public function deleteItinerary()
    {
        if (!$this->IDTravel || !$this->IDPaket || !$this->NoUrut) {
            return false;
        }

        return DB::table('mstitenerary')
            ->where('IDTravel', $this->IDTravel)
            ->where('IDPaket', $this->IDPaket)
            ->where('NoUrut', $this->NoUrut)
            ->update(['IsAktif' => 0]);
    }

    public function setIDTravel($val)
    {
        $this->IDTravel = $val;
        return $this;
    }
    public function setIDPaket($val)
    {
        $this->IDPaket = $val;
        return $this;
    }
    public function setNoUrut($val)
    {
        $this->NoUrut = $val;
        return $this;
    }
    public function setHariKe($val)
    {
        $this->HariKe = $val;
        return $this;
    }

    private function getDatatablesQuery($idpaket, $idtravel)
    {
        $query = DB::table('mstitenerary')
            ->select(
                'mstitenerary.*',
                'mstpaket.NamaPaket',
                'mstpaket.KotaTujuan'
            )
            ->leftJoin('mstpaket', function ($join) {
                $join->on('mstpaket.IDPaket', '=', 'mstitenerary.IDPaket')
                    ->on('mstpaket.IDTravel', '=', 'mstitenerary.IDTravel');
            })
            ->where('mstitenerary.IsAktif', 1)
            ->where('mstitenerary.IDPaket', $idpaket)
            ->where('mstitenerary.IDTravel', $idtravel);

        if (request('search.value')) {
            $search = request('search.value');
            $query->where(function ($q) use ($search) {
                foreach ($this->column_search as $col) {
                    $q->orWhere($col, 'like', "%{$search}%");
                }
            });
        }

        if (request()->has('order.0.column')) {
            $colIndex = request('order.0.column');
            $dir = request('order.0.dir', 'asc');
            $column = $this->column_order[$colIndex] ?? null;

            if ($column) {
                $query->orderBy($column, $dir);
            }
        } else {
            foreach ($this->order as $col => $dir) {
                $query->orderBy($col, $dir);
            }
        }

        return $query;
    }

    public function countFiltered($idpaket, $idtravel)
    {
        return $this->getDatatablesQuery($idpaket, $idtravel)->count();
    }

    public function countAll($idpaket, $idtravel)
    {
        return DB::table($this->table)
            ->where('IDPaket', $idpaket)
            ->where('IDTravel', $idtravel)
            ->count();
    }
    public function insertBatch(array $data): int
    {
        DB::table($this->table)->insert($data);
        return count($data);
    }


    public function generateNoUrut($idpaket, $idtravel)
    {
        $last = DB::table($this->table)
            ->where('IDPaket', $idpaket)
            ->where('IDTravel', $idtravel)
            ->orderByDesc('NoUrut')
            ->value('NoUrut');

        return $last ? $last + 1 : 1;
    }

    public function total(array $where)
    {
        return DB::table($this->table)
            ->where($where)
            ->count();
    }
}
