<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerFitur extends Model
{
    use HasFactory;

    protected $table = 'serverfitur';
    protected $primaryKey = 'FiturID';
    public $timestamps = false;

    protected $fillable = [
        'FiturName',
        'NoUrut',
        'Icon',
        'URL',
        'IsHasSubmenu',
        'MainFiturID',
    ];
}
