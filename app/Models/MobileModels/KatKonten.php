<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KatKonten extends Model
{
    protected $table = 'mstkategorikonten';
    protected $primaryKey = 'IDKatKonten';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDKatKonten',
        'NamaKategori',
        'JenisKonten',
        'IsAktif',
    ];

    private ?string $IDKatKonten = null;
    private ?string $jenisKonten = null;
    private ?string $cari = null;
    private ?int $limit = null;
    private ?int $page = null;

    public function setIDKatKonten(?string $value): self
    {
        $this->IDKatKonten = $value;
        return $this;
    }

    public function setJenisKonten(?string $value): self
    {
        $this->jenisKonten = $value;
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
     * Find all kategori konten
     */
    public function findAllKatKonten(): array
    {
        $query = self::where('IsAktif', 1);

        if ($this->IDKatKonten) {
            $query->where('IDKatKonten', $this->IDKatKonten);
        }

        if ($this->jenisKonten) {
            $query->where('JenisKonten', $this->jenisKonten);
        }

        if ($this->cari) {
            $query->where('NamaKategori', 'like', "%{$this->cari}%");
        }

        if ($this->limit && $this->limit > 0) {
            $page = max($this->page ?? 0, 0);
            $offset = $page * $this->limit;

            $query->limit($this->limit)->offset($offset);
        }

        return $query->orderBy('IDKatKonten', 'ASC')->get()->toArray();
    }

    /**
     * Count all kategori konten
     */
    public function countAllKatKonten(): int
    {
        $query = self::where('IsAktif', 1);

        if ($this->IDKatKonten) {
            $query->where('IDKatKonten', $this->IDKatKonten);
        }

        if ($this->jenisKonten) {
            $query->where('JenisKonten', $this->jenisKonten);
        }

        if ($this->cari) {
            $query->where('NamaKategori', 'like', "%{$this->cari}%");
        }

        return $query->count();
    }
}
