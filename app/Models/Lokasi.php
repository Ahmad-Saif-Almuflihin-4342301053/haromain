<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Lokasi
{
    protected ?string $IDProv  = null;
    protected ?string $IDKab   = null;
    protected ?string $IDKec   = null;
    protected ?string $IDDesa  = null;

    public function findProvinsi(): array
    {
        $query = DB::table('mstprov as prov')
            ->select('prov.*');

        if ($this->getIDProv() !== null) {
            $query->where('prov.IDProv', $this->getIDProv());
        }

        return $query->get()->toArray();
    }

    public function findKabupaten(): array
    {
        $query = DB::table('mstkab as kab')
            ->select('kab.*', 'prov.NamaProv')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'kab.IDProv');

        if ($this->getIDProv() !== null) {
            $query->where('kab.IDProv', $this->getIDProv());
        }

        if ($this->getIDKab() !== null) {
            $query->where('kab.IDKab', $this->getIDKab());
        }

        return $query->get()->toArray();
    }

    public function findKecamatan(): array
    {
        $query = DB::table('mstkec as kec')
            ->select('kec.*', 'prov.NamaProv', 'kab.NamaKab')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'kec.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDProv', '=', 'kec.IDProv')
                     ->on('kab.IDKab', '=', 'kec.IDKab');
            });

        if ($this->getIDProv() !== null) {
            $query->where('kec.IDProv', $this->getIDProv());
        }

        if ($this->getIDKab() !== null) {
            $query->where('kec.IDKab', $this->getIDKab());
        }

        if ($this->getIDKec() !== null) {
            $query->where('kec.IDKec', $this->getIDKec());
        }

        return $query->get()->toArray();
    }

    public function findDesa(): array
    {
        $query = DB::table('mstdesa as desa')
            ->select(
                'desa.*',
                'prov.NamaProv',
                'kab.NamaKab',
                'kec.NamaKec'
            )
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'desa.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDProv', '=', 'desa.IDProv')
                     ->on('kab.IDKab', '=', 'desa.IDKab');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDProv', '=', 'desa.IDProv')
                     ->on('kec.IDKab', '=', 'desa.IDKab')
                     ->on('kec.IDKec', '=', 'desa.IDKec');
            });

        if ($this->getIDProv() !== null) {
            $query->where('desa.IDProv', $this->getIDProv());
        }

        if ($this->getIDKab() !== null) {
            $query->where('desa.IDKab', $this->getIDKab());
        }

        if ($this->getIDKec() !== null) {
            $query->where('desa.IDKec', $this->getIDKec());
        }

        if ($this->getIDDesa() !== null) {
            $query->where('desa.IDDesa', $this->getIDDesa());
        }

        return $query->get()->toArray();
    }

    public function getIDProv(): ?string
    {
        return $this->IDProv;
    }

    public function setIDProv($IDProv): self
    {
        $this->IDProv = $IDProv;
        return $this;
    }

    public function getIDKab(): ?string
    {
        return $this->IDKab;
    }

    public function setIDKab($IDKab): self
    {
        $this->IDKab = $IDKab;
        return $this;
    }

    public function getIDKec(): ?string
    {
        return $this->IDKec;
    }

    public function setIDKec($IDKec): self
    {
        $this->IDKec = $IDKec;
        return $this;
    }

    public function getIDDesa(): ?string
    {
        return $this->IDDesa;
    }

    public function setIDDesa($IDDesa): self
    {
        $this->IDDesa = $IDDesa;
        return $this;
    }
}
