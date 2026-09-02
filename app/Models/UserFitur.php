<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFitur extends Model
{
    use HasFactory;

    protected $table = 'userfitur';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'UserName',
        'FiturID',
        'ViewData',
        'AddData',
        'EditData',
        'DeleteData',
        'PrintData',
    ];

}
