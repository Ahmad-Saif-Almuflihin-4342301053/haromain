<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimoniUser extends Model
{
    use HasFactory;

    protected $table = 'testimoniuser';
    protected $primaryKey = 'IDTestimoni';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDTestimoni',
        'IDPaket',
        'IDPerson',
        'IDTravel',
        'JudulTesti',
        'NarasiTesti',
        'Bintang',
        'TglSubmit',
    ];

}
