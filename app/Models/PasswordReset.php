<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $table = 'passwordreset';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'UserName',
        'Token',
        'TglBuat',
        'Jenis',
    ];
}
