<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;

class Konten extends Model
{
    protected $table = 'mstkonten';
    protected $primaryKey = 'IDKonten';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDKonten',
        'IDKatKonten',
        'IDKota',
        'JenisKonten',
        'JudulKonten',
        'IsiKonten',
        'Gambar',
        'Lat',
        'Lng',
        'IsAktif',
        'TanggalKonten',
        'NoUrut'
    ];

    // Constants
    public const JENIS_ARTIKEL = 'ARTIKEL';
    public const JENIS_LOKASI = 'LOKASI';
    public const JENIS_SLIDER = 'SLIDER';

    private ?string $jenisKonten = null;
    private ?string $IDKatKonten = null;
    private ?string $IDKota = null;
    private ?float $lat = null;
    private ?float $lng = null;
    private ?string $cari = null;
    private ?int $limit = null;
    private ?int $page = null;

    public function setJenisKonten(?string $value): self
    {
        $this->jenisKonten = $value;
        return $this;
    }

    public function setIDKatKonten(?string $value): self
    {
        $this->IDKatKonten = $value;
        return $this;
    }

    public function setIDKota(?string $value): self
    {
        $this->IDKota = $value;
        return $this;
    }

    public function setLat(?float $value): self
    {
        $this->lat = $value;
        return $this;
    }

    public function setLng(?float $value): self
    {
        $this->lng = $value;
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
     * Find all konten
     */
    public function findAllKonten(): array
    {
        $query = self::select('mstkonten.*', 'kk.NamaKategori', 'kt.NamaKota')
            ->leftJoin('mstkategorikonten as kk', 'kk.IDKatKonten', '=', 'mstkonten.IDKatKonten')
            ->leftJoin('mstkota as kt', 'kt.IDKota', '=', 'mstkonten.IDKota')
            ->where('mstkonten.IsAktif', 1);

        if ($this->jenisKonten) {
            $query->where('mstkonten.JenisKonten', $this->jenisKonten);
        }

        if ($this->IDKatKonten) {
            $query->where('mstkonten.IDKatKonten', $this->IDKatKonten);
        }

        if ($this->IDKota) {
            $query->where('mstkonten.IDKota', $this->IDKota);
        }

        if ($this->cari) {
            $query->where(function ($q) {
                $q->where('mstkonten.JudulKonten', 'like', "%{$this->cari}%")
                    ->orWhere('mstkonten.IsiKonten', 'like', "%{$this->cari}%");
            });
        }

        // Haversine formula untuk sorting by distance
        if ($this->lat && $this->lng) {
            $query->selectRaw("
                ROUND((6371 * acos(
                    cos(radians(?)) * cos(radians(mstkonten.Lat)) *
                    cos(radians(mstkonten.Lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(mstkonten.Lat))
                )), 2) AS Jarak
            ", [$this->lat, $this->lng, $this->lat])
                ->orderBy('Jarak', 'ASC');
        } else {
            //$query->orderBy('mstkonten.TanggalKonten', 'DESC');
            $query->orderByRaw('CASE WHEN mstkonten.NoUrut IS NOT NULL THEN 0 ELSE 1 END ASC')
                ->orderBy('mstkonten.NoUrut', 'ASC')
                ->orderBy('mstkonten.TanggalKonten', 'DESC');
        }

        if ($this->limit && $this->limit > 0) {
            $page = max($this->page ?? 0, 0);
            $offset = $page * $this->limit;

            $query->limit($this->limit)->offset($offset);
        }

        return $query->get()->toArray();
    }

    /**
     * Count all konten
     */
    public function countAllKonten(): int
    {
        $query = self::where('IsAktif', 1);

        if ($this->jenisKonten) {
            $query->where('JenisKonten', $this->jenisKonten);
        }

        if ($this->IDKatKonten) {
            $query->where('IDKatKonten', $this->IDKatKonten);
        }

        if ($this->IDKota) {
            $query->where('IDKota', $this->IDKota);
        }

        if ($this->cari) {
            $query->where(function ($q) {
                $q->where('JudulKonten', 'like', "%{$this->cari}%")
                    ->orWhere('IsiKonten', 'like', "%{$this->cari}%");
            });
        }

        return $query->count();
    }
}
