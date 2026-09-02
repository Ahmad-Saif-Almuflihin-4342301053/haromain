<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class MstKec extends Model
{
    use Compoships;

    protected $table = 'mstkec';
    protected $primaryKey = ['IDKec', 'IDKab', 'IDProv'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDKec',
        'NamaKec',
        'IDKab',
        'IDProv',
    ];

    /**
     * Relasi ke Kabupaten
     */
    public function kabupaten()
    {
        return $this->belongsTo(MstKab::class, ['IDKab', 'IDProv'], ['IDKab', 'IDProv']);
    }

    /**
     * Relasi ke Desa
     */
    public function desa()
    {
        return $this->hasMany(MstDesa::class, ['IDKec', 'IDKab', 'IDProv'], ['IDKec', 'IDKab', 'IDProv']);
    }

    /**
     * Relasi ke Person
     */
    public function persons()
    {
        return $this->hasMany(Person::class, ['IDKec', 'IDKab', 'IDProv'], ['IDKec', 'IDKab', 'IDProv']);
    }
}
