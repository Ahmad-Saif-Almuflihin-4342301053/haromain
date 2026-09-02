<?php

namespace App\Models;

use App\Traits\HasDatatableParams;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MstKategoriKonten extends Model
{
    use HasFactory, HasDatatableParams, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstkategorikonten';
    protected $primaryKey = 'IDKatKonten';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDKatKonten',
        'NamaKategori',
        'IconKategori',
        'JenisKonten',
        'IsAktif',
    ];
    protected $jenisKonten;

    protected $defaultOrder = ['NamaKategori' => 'asc'];

    protected $columnSearch = [
        'NamaKategori'
    ];

    protected $order = [
        'NamaKategori' => 'asc'
    ];

    protected $IDKatKonten;

    public function setJenisKonten($jenis)
    {
        $this->jenisKonten = $jenis;
        return $this;
    }


    public function generateID()
    {
        $today  = date('Ymd');
        $prefix = 'KAT-' . $today . '-';

        return $this->generateSequentialId([
            'column' => 'IDKatKonten',
            'prefix' => $prefix,
            'pad'    => 4,
            'scope'  => function ($q) use ($prefix) {
                $q->where('IDKatKonten', 'like', $prefix . '%');
            }
        ]);
    }


    public function findOne()
    {
        return $this->select(
            'IDKatKonten',
            'NamaKategori',
            'IconKategori',
            'JenisKonten',
            'IsAktif'
        )
            ->when(
                $this->primaryKey,
                fn($q) =>
                $q->where('IDKatKonten', $this->primaryKey)
            )
            ->first();
    }

    public function findAll()
    {
        return $this->select(
            'IDKatKonten',
            'NamaKategori',
            'IconKategori',
            'JenisKonten',
            'IsAktif'
        )
            ->when(
                $this->getCari(),
                fn($q) =>
                $q->where('NamaKategori', 'like', '%' . $this->getCari() . '%')
            )
            ->when(
                $this->getJenisKonten(),
                fn($q) =>
                $q->where('JenisKonten', $this->getJenisKonten())
            )
            ->when(
                $this->getLimit(),
                fn($q) =>
                $q->limit($this->getLimit())->offset($this->getPage())
            )
            ->orderBy('IDKatKonten', 'asc')
            ->get();
    }

    private function getDatatablesQuery()
    {

        $query = self::query();

        if ($this->getJenisKonten()) {
            $query->where('JenisKonten', $this->getJenisKonten());
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
            $query->orderBy($this->columnOrder[$colIndex], $dir);
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

    public function countAllDatatables()
    {
        $query = DB::table($this->table);

        if ($this->getJenisKonten()) {
            $query->where('JenisKonten', $this->getJenisKonten());
        }

        return $query->count();
    }

    public function getIDKatKonten()
    {
        return $this->IDKatKonten;
    }

    public function setIDKatKonten($id)
    {
        $this->IDKatKonten = $id;
        return $this;
    }

    public function getJenisKonten()
    {
        return $this->jenisKonten;
    }
}
