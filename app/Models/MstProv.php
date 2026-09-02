<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstProv extends Model
{
    use HasFactory;

    protected $table = 'mstprov';
    // protected $primaryKey = 'IDProv';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDProv',
        'NamaProv',
    ];
}
