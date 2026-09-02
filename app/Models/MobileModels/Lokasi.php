<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Lokasi extends Model
{
    private ?string $IDProv = null;
    private ?string $IDKab = null;
    private ?string $IDKec = null;
    private ?string $IDDesa = null;

    public function setIDProv(?string $value): self
    {
        $this->IDProv = $value;
        return $this;
    }

    public function setIDKab(?string $value): self
    {
        $this->IDKab = $value;
        return $this;
    }

    public function setIDKec(?string $value): self
    {
        $this->IDKec = $value;
        return $this;
    }

    public function setIDDesa(?string $value): self
    {
        $this->IDDesa = $value;
        return $this;
    }

    /**
     * Find Provinsi
     */
    public function findProvinsi(): array
    {
        $query = DB::table('mstprov');

        if ($this->IDProv) {
            $query->where('IDProv', $this->IDProv);
        }

        return $query->orderBy('NamaProv', 'ASC')->get()->toArray();
    }

    /**
     * Find Kabupaten
     */
    public function findKabupaten(): array
    {
        $query = DB::table('mstkab');

        if ($this->IDProv) {
            $query->where('IDProv', $this->IDProv);
        }

        if ($this->IDKab) {
            $query->where('IDKab', $this->IDKab);
        }

        return $query->orderBy('NamaKab', 'ASC')->get()->toArray();
    }

    /**
     * Find Kecamatan
     */
    public function findKecamatan(): array
    {
        $query = DB::table('mstkec');

        if ($this->IDProv) {
            $query->where('IDProv', $this->IDProv);
        }

        if ($this->IDKab) {
            $query->where('IDKab', $this->IDKab);
        }

        if ($this->IDKec) {
            $query->where('IDKec', $this->IDKec);
        }

        return $query->orderBy('NamaKec', 'ASC')->get()->toArray();
    }

    /**
     * Find Desa
     */
    public function findDesa(): array
    {
        $query = DB::table('mstdesa');

        if ($this->IDProv) {
            $query->where('IDProv', $this->IDProv);
        }

        if ($this->IDKab) {
            $query->where('IDKab', $this->IDKab);
        }

        if ($this->IDKec) {
            $query->where('IDKec', $this->IDKec);
        }

        if ($this->IDDesa) {
            $query->where('IDDesa', $this->IDDesa);
        }

        return $query->orderBy('NamaDesa', 'ASC')->get()->toArray();
    }
}
