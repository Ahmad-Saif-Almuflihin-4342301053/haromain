<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstDesa extends Model
{


    protected $table = 'mstdesa';
    // protected $primaryKey = 'IDDesa';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDDesa',
        'IDKec',
        'IDKab',
        'IDProv',
        'NamaDesa',
    ];
}
