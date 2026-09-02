<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    protected $primaryKey = 'IDPengumuman';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'IDPengumuman',
        'IDTravel',
        'IDPaket',
        'Isi',
        //'SomePerson',
        'Tanggal',
    ];

    private ?string $IDTravel = null;
    private ?string $IDPaket = null;
    private ?string $IDPerson = null;
    private ?string $IDProv = null;
    private ?string $cari = null;
    private ?int $limit = null;
    private ?int $page = null;

    public function setIDTravel(?string $value): self
    {
        $this->IDTravel = $value;
        return $this;
    }

    public function setIDPaket(?string $value): self
    {
        $this->IDPaket = $value;
        return $this;
    }

    public function setIDPerson(?string $value): self
    {
        $this->IDPerson = $value;
        return $this;
    }

    public function setIDProv(?string $value): self
    {
        $this->IDProv = $value;
        return $this;
    }

    public function setCari(?string $value): self
    {
        $this->cari = $value;
        return $this;
    }

    public function setLimit(?int $value): self
    {
        $this->limit = $value;
        return $this;
    }

    public function setPage(?int $value): self
    {
        $this->page = $value;
        return $this;
    }

    /**
     * Find all pengumuman
     */
    public function findAllPengumuman(): array
    {
        $query = self::select('pengumuman.*', 'pk.NamaPaket', 'tv.NamaTravel')
            ->leftJoin('mstpaket as pk', function ($join) {
                $join->on('pk.IDTravel', '=', 'pengumuman.IDTravel')
                    ->on('pk.IDPaket', '=', 'pengumuman.IDPaket');
            })
            ->leftJoin('msttravel as tv', 'tv.IDTravel', '=', 'pengumuman.IDTravel');

        if ($this->IDTravel) {
            $query->where('pengumuman.IDTravel', $this->IDTravel);
        }

        if ($this->IDPaket) {
            $query->where('pengumuman.IDPaket', $this->IDPaket);
        }

        /* if ($this->IDPerson) {
            $query->where(function ($q) {
                $q->whereNull('pengumuman.SomePerson')
                    ->orWhereRaw("JSON_CONTAINS(pengumuman.SomePerson, ?)", ['"' . $this->IDPerson . '"']);
            });
        } */

        if ($this->cari) {
            $query->where('pengumuman.Isi', 'like', "%{$this->cari}%");
        }

        if ($this->limit && $this->limit > 0) {
            $page = max($this->page ?? 0, 0);
            $offset = $page * $this->limit;

            $query->limit($this->limit)->offset($offset);
        }

        return $query->orderBy('pengumuman.Tanggal', 'DESC')->get()->toArray();
    }

    /**
     * Count all pengumuman
     */
    public function countAllPengumuman(): int
    {
        $query = self::query();

        if ($this->IDTravel) {
            $query->where('IDTravel', $this->IDTravel);
        }

        if ($this->IDPaket) {
            $query->where('IDPaket', $this->IDPaket);
        }

        $query->where('DiBaca', 0);

        /* if ($this->IDPerson) {
            $query->where(function ($q) {
                $q->whereNull('SomePerson')
                    ->orWhereRaw("JSON_CONTAINS(SomePerson, ?)", ['"' . $this->IDPerson . '"']);
            });
        } */

        return $query->count();
    }

    /**
     * Save pengumuman
     */
    public function savePengumuman(string $Isi, ?array $SomePerson = null, ?string $Tanggal = null): bool
    {
        return self::insert([
            'IDTravel' => $this->IDTravel,
            'IDPaket' => $this->IDPaket,
            'Isi' => $Isi,
            //'SomePerson' => $SomePerson ? json_encode($SomePerson) : null,
            'Tanggal' => $Tanggal ?? now(),
        ]);
    }

    /**
     * Update pengumuman
     */
    public static function updatePengumuman(int $IDPengumuman, array $data): bool
    {
        return self::where('IDPengumuman', $IDPengumuman)->update($data) > 0;
    }

    /**
     * Delete pengumuman
     */
    public static function deletePengumuman(int $IDPengumuman): bool
    {
        return self::where('IDPengumuman', $IDPengumuman)->delete() > 0;
    }
}
