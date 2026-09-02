<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Itenerary extends Model
{
    protected $table = 'mstitenerary';
    protected $primaryKey = ['IDTravel', 'IDPaket', 'NoUrut'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTravel',
        'IDPaket',
        'NoUrut',
        'HariKe',
        'TglMulai',
        'TglSelesai',
        'KoorLat',
        'KoorLong',
        'Lokasi',
        'Agenda',
    ];

    private ?string $IDTravel = null;
    private ?string $IDPaket = null;
    private ?int $NoUrut = null;
    private ?int $HariKe = null;
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

    public function setNoUrut(?int $value): self
    {
        $this->NoUrut = $value;
        return $this;
    }

    public function setHariKe(?int $value): self
    {
        $this->HariKe = $value;
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
     * Find all mstitenerary
     */
    public function findAllItenerary(): array
    {
        $query = self::query();

        if ($this->IDTravel) {
            $query->where('IDTravel', $this->IDTravel);
        }

        if ($this->IDPaket) {
            $query->where('IDPaket', $this->IDPaket);
        }

        if ($this->NoUrut) {
            $query->where('NoUrut', $this->NoUrut);
        }

        if ($this->HariKe) {
            $query->where('HariKe', $this->HariKe);
        }

        if ($this->cari) {
            $query->where(function ($q) {
                $q->where('Lokasi', 'like', "%{$this->cari}%")
                    ->orWhere('Agenda', 'like', "%{$this->cari}%");
            });
        }

        if ($this->limit && $this->limit > 0) {
            $page = max($this->page ?? 0, 0);
            $offset = $page * $this->limit;

            $query->limit($this->limit)->offset($offset);
        }

        return $query->orderBy('HariKe', 'ASC')
            ->orderBy('NoUrut', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * Find current mstitenerary (berdasarkan waktu)
     */
    public function findCurrent(?string $currentTime = null): ?array
    {
        $now = $currentTime ?? now()->format('Y-m-d H:i:s');

        return self::where('IDTravel', $this->IDTravel)
            ->where('IDPaket', $this->IDPaket)
            ->where('TglMulai', '<=', $now)
            ->where('TglSelesai', '>=', $now)
            ->first()
                ?->toArray();
    }

    /**
     * Save all mstitenerary (batch insert)
     */
    public function saveAll(string $IDPaket, string $IDTravel, array $insertData, bool $replaceExisting = true): bool
    {
        // Delete existing first only if replace mode is true
        if ($replaceExisting) {
            self::where('IDTravel', $IDTravel)
                ->where('IDPaket', $IDPaket)
                ->delete();
        }

        if (empty($insertData)) {
            return true;
        }

        // Prepare data
        $preparedData = [];
        $maxNoUrut = $replaceExisting ? 0 : self::getNextNoUrut($IDTravel, $IDPaket);
        
        foreach ($insertData as $index => $item) {
            $preparedData[] = [
                'IDTravel' => $IDTravel,
                'IDPaket' => $IDPaket,
                'NoUrut' => $replaceExisting ? ($index + 1) : ($maxNoUrut + $index + 1),
                'HariKe' => $item['HariKe'] ?? null,
                'TglMulai' => $item['TglMulai'] ?? null,
                'TglSelesai' => $item['TglSelesai'] ?? null,
                'KoorLat' => $item['KoorLat'] ?? null,
                'KoorLong' => $item['KoorLong'] ?? null,
                'Lokasi' => $item['Lokasi'] ?? null,
                'Agenda' => $item['Agenda'] ?? null,
            ];
        }

        return self::insert($preparedData);
    }

    /**
     * Save single mstitenerary
     */
    public function saveSingle(array $data): bool
    {
        // Get max NoUrut
        $maxNoUrut = self::where('IDTravel', $this->IDTravel)
            ->where('IDPaket', $this->IDPaket)
            ->max('NoUrut') ?? 0;

        $data['IDTravel'] = $this->IDTravel;
        $data['IDPaket'] = $this->IDPaket;
        $data['NoUrut'] = $maxNoUrut + 1;

        return self::insert($data);
    }

    /**
     * Get next NoUrut for incremental insert
     */
    private static function getNextNoUrut(string $IDTravel, string $IDPaket): int
    {
        return self::where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->max('NoUrut') ?? 0;
    }

    /**
     * Update mstitenerary
     */
    public static function updateItenerary(string $IDTravel, string $IDPaket, int $NoUrut, array $data): bool
    {
        return self::where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->where('NoUrut', $NoUrut)
            ->update($data) > 0;
    }

    /**
     * Delete mstitenerary
     */
    public static function deleteItenerary(string $IDTravel, string $IDPaket, int $NoUrut): bool
    {
        return self::where('IDTravel', $IDTravel)
            ->where('IDPaket', $IDPaket)
            ->where('NoUrut', $NoUrut)
            ->delete() > 0;
    }
}
