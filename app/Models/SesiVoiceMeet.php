<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SesiVoiceMeet extends Model
{
    use HasFactory;

    protected $table = 'sesivoicemeet';
    protected $primaryKey = 'IDVoiceMeet';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDVoiceMeet',
        'NamaSesi',
        'TglJamMulai',
        'TglJamSelesai',
        'StatusSesi',
        'IDPerson',
        'IDPaket',
        'IDTravel',
    ];

    protected $casts = [
        // 'TglJamMulai' => 'datetime', 
        // 'TglJamSelesai' => 'datetime',
    ];

    protected $appends = [
        'Tanggal',
        'JamMulai',
        'JamSelesai',
        'NamaWaktu',
    ];

    /**
     * Relationship dengan MstPerson (creator/leader)
     */
    public function person()
    {
        return $this->belongsTo(MstPerson::class, 'IDPerson', 'IDPerson');
    }

    /**
     * Relationship dengan MstPaket
     */
    public function paket()
    {
        return $this->belongsTo(MstPaket::class, 'IDPaket', 'IDPaket');
    }

    /**
     * Accessor: Extract tanggal dari TglJamMulai (Converted to App Timezone for display)
     */
    public function getTanggalAttribute()
    {
        if (!$this->TglJamMulai) {
            return null;
        }
        // $this->TglJamMulai is raw UTC string
        return Carbon::parse($this->TglJamMulai, 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d');
    }

    /**
     * Accessor: Extract jam mulai dari TglJamMulai (Converted to App Timezone for display)
     */
    public function getJamMulaiAttribute()
    {
        if (!$this->TglJamMulai) {
            return null;
        }
        return Carbon::parse($this->TglJamMulai, 'UTC')->setTimezone(config('app.timezone'))->format('H:i');
    }

    /**
     * Accessor: Extract jam selesai dari TglJamSelesai (Converted to App Timezone for display)
     */
    public function getJamSelesaiAttribute()
    {
        if (!$this->TglJamSelesai) {
            return null;
        }
        return Carbon::parse($this->TglJamSelesai, 'UTC')->setTimezone(config('app.timezone'))->format('H:i');
    }

    /**
     * Accessor: Hitung NamaWaktu berdasarkan jam mulai (Converted to App Timezone)
     * Pagi: 05:00 - 10:59
     * Siang: 11:00 - 14:59
     * Sore: 15:00 - 17:59
     * Malam: 18:00 - 04:59
     */
    public function getNamaWaktuAttribute()
    {
        if (!$this->TglJamMulai) {
            return null;
        }

        $hour = Carbon::parse($this->TglJamMulai, 'UTC')->setTimezone(config('app.timezone'))->hour;

        if ($hour >= 5 && $hour < 11) {
            return 'Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            return 'Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            return 'Sore';
        } else {
            return 'Malam';
        }
    }

    /**
     * Generate ID untuk Voice Meet
     * Format: VM-0000001
     */
    public static function generateID()
    {
        $prefix = 'VM-';

        // Ambil ID terakhir
        $lastRecord = DB::table('sesivoicemeet')
            ->where('IDVoiceMeet', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(IDVoiceMeet, 4) AS UNSIGNED) DESC')
            ->first();

        if (!$lastRecord) {
            return $prefix . '0000001';
        }

        // Extract nomor dari ID terakhir
        $lastNumber = (int) substr($lastRecord->IDVoiceMeet, 3);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 7, '0', STR_PAD_LEFT);
    }

    /**
     * Get all meets dengan data creator
     */
    public static function getAllWithCreator()
    {
        return self::with('person:IDPerson,NamaJamaah')
            // Note: orderBy TglJamMulai works on UTC values, which is correct for sorting
            ->orderBy('TglJamMulai', 'desc')
            ->get();
    }

    /**
     * Get meet by ID dengan data creator
     */
    public static function getDetailWithCreator($IDVoiceMeet)
    {
        return self::with('person:IDPerson,NamaJamaah')
            ->where('IDVoiceMeet', $IDVoiceMeet)
            ->first();
    }

    /**
     * Transform to API response format
     */
    public function toApiResponse()
    {
        return [
            'IDMeet' => $this->IDVoiceMeet,
            'IDTravel' => $this->IDTravel,
            'IDPaket' => $this->IDPaket,
            'NamaPaket' => $this->paket ? $this->paket->NamaPaket : null,
            'NamaSesi' => $this->NamaSesi,
            'NamaWaktu' => $this->NamaWaktu,
            'Tanggal' => $this->Tanggal,
            'JamMulai' => $this->JamMulai,
            'JamSelesai' => $this->JamSelesai,
            // Standard fields in ISO8601 format
            'TglJamMulai' => $this->TglJamMulai ? Carbon::parse($this->TglJamMulai, 'UTC')->toIso8601String() : null,
            'TglJamSelesai' => $this->TglJamSelesai ? Carbon::parse($this->TglJamSelesai, 'UTC')->toIso8601String() : null,
            'Status' => $this->StatusSesi,
            'CreatedBy' => $this->IDPerson,
            'NamaCreator' => $this->person ? $this->person->NamaJamaah : null,
            'LastModified' => null,
        ];
    }
}
