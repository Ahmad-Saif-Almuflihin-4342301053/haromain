<?php

namespace App\Models;

use App\Traits\HasSequentialId;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DokumenTravel extends Model
{
    use HasFactory, HasSequentialId;

    protected $table = 'dokumentravel';
    // protected $primaryKey = 'IDDokumen';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDDokumen',
        'IDTravel',
        'NamaDokumen',
        'FileDokumen',
        'MasaAktifSampai',
        'IsVerified',
        'KeteranganVerifikasi',
        'TglDokumen',
        'VerifyUser',
        'VerifyDate'
    ];

     public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'IDDokumen',
            'prefix' => 'DOC-',
            'pad'    => 7,
        ]);
    }
}
