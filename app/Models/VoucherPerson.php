<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherPerson extends Model
{
    use HasFactory;

    protected $table = 'voucherperson';
    // protected $primaryKey = 'IDVoucher';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDVoucher',
        'IDPerson',
        'IDPaket',
        'IDTravel',
        'NamaVoucher',
        'Debet',
        'Kredit',
        'Keterangan',
        'IsKlaim',
    ];

    protected $casts = [
        'Debet' => 'double',
        'Kredit' => 'double',
        'IsKlaim' => 'boolean',
    ];

    public function jamaahPaket()
    {
        return $this->belongsTo(JamaahPaket::class, [
            'IDPaket',
            'IDPerson',
            'IDTravel'
        ]);
    }

    public static function generateIDVoucher(): string
    {
        $year = now()->format('Y');

        $last = self::selectRaw('RIGHT(IDVoucher, 10) AS kode')
            ->where('IDVoucher', 'like', "%$year%")
            ->orderByRaw('CAST(RIGHT(IDVoucher, 10) AS UNSIGNED) DESC')
            ->limit(1)
            ->first();

        $nextNumber = $last ? ((int) $last->kode + 1) : 1;

        return 'VCH-' . $year . '-' . str_pad($nextNumber, 10, '0', STR_PAD_LEFT);
    }

    public function getDetail($IDVoucher)
    {
        return self::where('IDVoucher', $IDVoucher)->first();
    }

    public function getAllVouchers(array $where = [], $limit = 10, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        $query = self::query();

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->orderBy('IDVoucher', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }

    public function countVouchers(array $where = [])
    {
        $query = self::query();

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->count();
    }

    public function getTotalDebet(array $where = [])
    {
        $query = self::query();

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->sum('Debet') ?? 0;
    }

    public function getTotalKredit(array $where = [])
    {
        $query = self::query();

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->sum('Kredit') ?? 0;
    }

    public function getBalance($IDPerson)
    {
        $result = DB::table(DB::raw('(
        SELECT
            vp.*,
            SUBSTRING(
                vp.Keterangan,
                LOCATE(\'(PRS-\', vp.Keterangan) + 1,
                LOCATE(\')\', vp.Keterangan, LOCATE(\'(PRS-\', vp.Keterangan))
                    - (LOCATE(\'(PRS-\', vp.Keterangan) + 1)
            ) AS person_code
        FROM voucherperson vp
    ) as tr'))
            ->leftJoin('jamaahpaket as j', function ($join) {
                $join->on('tr.person_code', '=', 'j.IDPerson')
                    ->where('j.IsPaid', 1);
            })
            ->where('tr.IsKlaim', 0)
            ->where('tr.IDPerson', $IDPerson)
            ->selectRaw("
            SUM(
                CASE
                    WHEN tr.NamaVoucher = 'Voucher Referral Pendaftaran'
                    THEN
                        CASE
                            WHEN j.IDPerson IS NOT NULL THEN tr.Debet
                            ELSE 0
                        END
                    ELSE tr.Debet
                END
            ) AS total_debet
        ")
            ->first();

        return $result->total_debet ?? 0;
    }


    public function getList($IDPerson, $limit = 10, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        return DB::table(DB::raw('(
        SELECT
            vp.*,
            SUBSTRING(
                vp.Keterangan,
                LOCATE(\'(PRS-\', vp.Keterangan) + 1,
                LOCATE(\')\', vp.Keterangan, LOCATE(\'(PRS-\', vp.Keterangan))
                    - (LOCATE(\'(PRS-\', vp.Keterangan) + 1)
            ) AS person_code
        FROM voucherperson vp
    ) as tr'))
            ->leftJoin('jamaahpaket as j', function ($join) {
                $join->on('tr.person_code', '=', 'j.IDPerson')
                    ->where('j.IsPaid', 1);
            })
            ->where('tr.IsKlaim', 0)
            ->where('tr.IDPerson', $IDPerson)
            ->selectRaw("
            tr.IDVoucher,
            tr.IDPerson,
            tr.Kredit,
            tr.NamaVoucher,
            tr.Keterangan,
            tr.IDPaket,
            tr.IDTravel,
            tr.IsKlaim,
            (
                CASE
                    WHEN tr.NamaVoucher = 'Voucher Referral Pendaftaran'
                    THEN
                        CASE
                            WHEN j.IDPerson IS NOT NULL THEN tr.Debet
                            ELSE 0
                        END
                    ELSE tr.Debet
                END
            ) AS Debet
        ")
            ->having('Debet', '>', 0)
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();
    }


    public function createVoucher(array $data)
    {
        if (!isset($data['IDVoucher'])) {
            $data['IDVoucher'] = self::generateIDVoucher();
        }
        return self::create($data);
    }


    public function updateVoucher($IDVoucher, array $data)
    {
        return self::where('IDVoucher', $IDVoucher)->update($data);
    }

    public function deleteVoucher($IDVoucher)
    {
        return self::where('IDVoucher', $IDVoucher)->delete();
    }

    public function klaimVoucher($IDVoucher)
    {
        return self::where('IDVoucher', $IDVoucher)->update(['IsKlaim' => 1]);
    }
}
