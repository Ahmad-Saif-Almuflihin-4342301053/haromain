<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kota extends Model
{
    protected $table = 'mstkota';
    protected $primaryKey = 'IDKota';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDKota',
        'NamaKota',
        'KoorLat',
        'KoorLong',
    ];

    private ?string $IDKota = null;
    private ?string $cari = null;
    private ?int $limit = null;
    private ?int $page = null;

    public function setIDKota(?string $value): self
    {
        $this->IDKota = $value;
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
     * Find all kota
     */
    public function findAllKota(): array
    {
        $query = self::query();

        if ($this->IDKota) {
            $query->where('IDKota', $this->IDKota);
        }

        if ($this->cari) {
            $query->where('NamaKota', 'like', "%{$this->cari}%");
        }

        if ($this->limit && $this->limit > 0) {
            $page = max($this->page ?? 0, 0);
            $offset = $page * $this->limit;

            $query->limit($this->limit)->offset($offset);
        }

        return $query->orderBy('NamaKota', 'ASC')->get()->toArray();
    }

    /**
     * Count all kota
     */
    public function countAllKota(): int
    {
        $query = self::query();

        if ($this->IDKota) {
            $query->where('IDKota', $this->IDKota);
        }

        if ($this->cari) {
            $query->where('NamaKota', 'like', "%{$this->cari}%");
        }

        return $query->count();
    }
}
