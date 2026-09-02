<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstKab extends Model
{
    use HasFactory;

    protected $table = 'mstkab';
    // protected $primaryKey = 'IDKab';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDKab',
        'NamaKab',
        'IDProv',
    ];
}
