<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstKec extends Model
{
    use HasFactory;

    protected $table = 'mstkec';
    protected $primaryKey = 'IDKec';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDKec',
        'NamaKec',
        'IDKab',
        'IDProv',
    ];
}
