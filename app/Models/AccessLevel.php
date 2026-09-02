<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLevel extends Model
{

    protected $table = 'accesslevel';
    protected $primaryKey = 'LevelID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'LevelID',
        'LevelName',
        'IDTravel',
        'IsAktif'
    ];
}
