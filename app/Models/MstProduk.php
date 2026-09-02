<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstProduk extends Model
{
    use HasFactory;

    protected $table = 'mstproduk';
    // protected $primaryKey = 'IDProduk';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDProduk',
        'NamaProduk',
        'Biaya',
        'Masa',
        'Satuan',
        'Keterangan',
    ];
}
