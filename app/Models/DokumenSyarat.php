<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenSyarat extends Model
{
    use HasFactory;

    protected $table = 'dokumensyarat';
    public $incrementing = false;
    // protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDPaket',
        'IDPerson',
        'IDTravel',
        'NoUrutSyarat',
        'FileDokumen',
        'Keterangan',
        'TglUpload'
    ];

    protected $casts = [
        'NoUrutSyarat' => 'integer',
        'TglUpload' => 'datetime',
    ];
}
