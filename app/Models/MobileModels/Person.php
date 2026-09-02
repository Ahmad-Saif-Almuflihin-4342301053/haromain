<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Person extends Model
{
    use Compoships;
    protected $table = 'mstperson';
    protected $primaryKey = 'IDPerson';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDPerson',
        'NamaJamaah',
        'NIK',
        'Email',
        'NoHP',
        'UserName',
        'Password',
        'StatusPerson',
        'KodeVerifikasi',
        'IsVerified',
        'IsAktif',
        'IDProv',
        'IDKab',
        'IDKec',
        'IDDesa',
        'TempatLahir',
        'TanggalLahir',
        'Alamat',
        'FotoProfil',
        'Keterangan',
        'Tgl_Register',
        'LastModified',
        'KodeReferral',
        'IDPersonReferral',
        'Token',
        'KoorLat',
        'KoorLong',
    ];

    protected $hidden = [
        'Password',
        'KodeVerifikasi',
    ];

    // Constants
    public const STATUS_JAMAAH = 'Jamaah';
    public const STATUS_AGEN = 'Agen';
    public const STATUS_KELUARGA = 'Keluarga';

    /**
     * Relasi ke Provinsi
     */
    public function provinsi()
    {
        return $this->belongsTo(MstProv::class, 'IDProv', 'IDProv');
    }

    /**
     * Relasi ke Kabupaten
     */
    public function kabupaten()
    {
        return $this->belongsTo(MstKab::class, ['IDKab', 'IDProv'], ['IDKab', 'IDProv']);
    }

    /**
     * Relasi ke Kecamatan
     */
    public function kecamatan()
    {
        return $this->belongsTo(MstKec::class, ['IDKec', 'IDKab', 'IDProv'], ['IDKec', 'IDKab', 'IDProv']);
    }

    /**
     * Relasi ke Desa
     */
    public function desa()
    {
        return $this->belongsTo(MstDesa::class, ['IDDesa', 'IDKec', 'IDKab', 'IDProv'], ['IDDesa', 'IDKec', 'IDKab', 'IDProv']);
    }

    /**
     * Generate Kode Referral
     */
    public static function generateKodeReferral(int $length = 6): string
    {
        $ymd = now()->format('ymd');
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $randomCode = '';
            for ($i = 0; $i < $length; $i++) {
                $randomCode .= $characters[rand(0, strlen($characters) - 1)];
            }
            $kodeReferral = $ymd . $randomCode;
            $exists = self::where('KodeReferral', $kodeReferral)->exists();
            $attempt++;
        } while ($exists && $attempt < $maxAttempts);

        if ($exists) {
            $kodeReferral = $ymd . $randomCode . substr(time(), -3);
        }

        return $kodeReferral;
    }

    /**
     * Generate ID Person
     */
    public static function generateIDPerson(): string
    {
        $strDate = now()->format('Y');

        $result = self::selectRaw('RIGHT(IDPerson, 10) AS kode')
            ->where('IDPerson', 'like', "PRS-{$strDate}%")
            ->orderByRaw('CAST(RIGHT(IDPerson, 10) AS SIGNED) DESC')
            ->first();

        $kode = $result ? (int) $result->kode + 1 : 1;

        return "PRS-{$strDate}-" . str_pad($kode, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Random String
     */
    public static function generateRandomString(int $length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Get detail person dengan lokasi
     */
    public static function getDetail(string $IDPerson): ?array
    {
        return self::select('p.*', 'prov.NamaProv', 'kab.NamaKab', 'kec.NamaKec', 'desa.NamaDesa')
            ->from('mstperson as p')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'p.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDKab', '=', 'p.IDKab')
                    ->on('kab.IDProv', '=', 'p.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDKec', '=', 'p.IDKec')
                    ->on('kec.IDKab', '=', 'p.IDKab')
                    ->on('kec.IDProv', '=', 'p.IDProv');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDDesa', '=', 'p.IDDesa')
                    ->on('desa.IDKec', '=', 'p.IDKec')
                    ->on('desa.IDKab', '=', 'p.IDKab')
                    ->on('desa.IDProv', '=', 'p.IDProv');
            })
            ->where('p.IDPerson', $IDPerson)
            ->first()
                ?->toArray();
    }

    /**
     * Find by email
     */
    public static function findByEmail(string $email, ?int $isAktif = null): ?array
    {
        $query = self::where('Email', $email);

        if ($isAktif !== null) {
            $query->where('IsAktif', $isAktif);
        }

        return $query->get()->toArray();
    }

    /**
     * Find by Kode Referral
     */
    public static function findByReferral(string $kodeReferral, ?int $isAktif = null): ?array
    {
        $query = self::where('KodeReferral', $kodeReferral);

        if ($isAktif !== null) {
            $query->where('IsAktif', $isAktif);
        }

        return $query->get()->toArray();
    }

    /**
     * Find by NoHP
     */
    public static function findByNoHP(string $noHP, ?int $isAktif = null): ?array
    {
        $query = self::where('NoHP', $noHP);

        if ($isAktif !== null) {
            $query->where('IsAktif', $isAktif);
        }

        return $query->get()->toArray();
    }

    /**
     * Find by NIK
     */
    public static function findByNIK(string $nik, ?int $isAktif = null): ?array
    {
        $query = self::where('NIK', $nik);

        if ($isAktif !== null) {
            $query->where('IsAktif', $isAktif);
        }

        return $query->get()->toArray();
    }

    /**
     * Find by Login
     */
    public static function findByLogin(string $userNameOrNoHP, string $password, ?string $statusPerson = null): ?array
    {
        $query = self::select('p.*', 'prov.NamaProv', 'kab.NamaKab', 'kec.NamaKec', 'desa.NamaDesa')
            ->from('mstperson as p')
            ->leftJoin('mstprov as prov', 'prov.IDProv', '=', 'p.IDProv')
            ->leftJoin('mstkab as kab', function ($join) {
                $join->on('kab.IDKab', '=', 'p.IDKab')
                    ->on('kab.IDProv', '=', 'p.IDProv');
            })
            ->leftJoin('mstkec as kec', function ($join) {
                $join->on('kec.IDKec', '=', 'p.IDKec')
                    ->on('kec.IDKab', '=', 'p.IDKab')
                    ->on('kec.IDProv', '=', 'p.IDProv');
            })
            ->leftJoin('mstdesa as desa', function ($join) {
                $join->on('desa.IDDesa', '=', 'p.IDDesa')
                    ->on('desa.IDKec', '=', 'p.IDKec')
                    ->on('desa.IDKab', '=', 'p.IDKab')
                    ->on('desa.IDProv', '=', 'p.IDProv');
            })
            ->where('p.Password', md5($password))
            ->where(function ($q) use ($userNameOrNoHP) {
                $q->where('p.UserName', $userNameOrNoHP)
                    ->orWhere('p.NoHP', $userNameOrNoHP);
            });

        if ($statusPerson) {
            $query->where('p.StatusPerson', $statusPerson);
        }

        return $query->first()?->toArray();
    }

    /**
     * Verifikasi akun
     */
    public static function verifikasi(string $IDPerson, string $kodeVerifikasi): bool
    {
        $person = self::where('IDPerson', $IDPerson)
            ->where('KodeVerifikasi', $kodeVerifikasi)
            ->first();

        if (!$person) {
            return false;
        }

        return self::where('IDPerson', $IDPerson)
            ->update([
                'IsVerified' => 1,
                'LastModified' => now(),
            ]) > 0;
    }

    /**
     * Update password
     */
    public static function updatePassword(string $IDPerson, string $password): bool
    {
        return self::where('IDPerson', $IDPerson)
            ->update([
                'Password' => md5($password),
                'LastModified' => now(),
            ]) > 0;
    }

    /**
     * Find tokens by person IDs
     */
    public static function findTokenPerson(array $personIds): array
    {
        return self::whereIn('IDPerson', $personIds)
            ->whereNotNull('Token')
            ->pluck('Token')
            ->toArray();
    }
}
