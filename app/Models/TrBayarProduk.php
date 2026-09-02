<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrBayarProduk extends Model
{
    use HasFactory;

    protected $table = 'trbayarproduk';
    // protected $primaryKey = 'NoTransBayar';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NoTransBayar',
        'TanggalBayar',
        'IDCustomer',
        'NamaCustomer',
        'JumlahUser',
        'HargaSatuan',
        'TotalHarga',
        'JenisBayar',
        'RekTujuan',
        'BuktiTF',
        'NoRefBayar',
        'IDProduk',
    ];

}
