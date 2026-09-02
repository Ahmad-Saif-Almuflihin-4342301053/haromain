<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class MstKab extends Model
{
    use Compoships;

    protected $table = 'mstkab';
    protected $primaryKey = ['IDKab', 'IDProv'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDKab',
        'NamaKab',
        'IDProv',
    ];

    /**
     * Relasi ke Provinsi
     */
    public function provinsi()
    {
        return $this->belongsTo(MstProv::class, 'IDProv', 'IDProv');
    }

    /**
     * Relasi ke Kecamatan
     */
    public function kecamatan()
    {
        return $this->hasMany(MstKec::class, ['IDKab', 'IDProv'], ['IDKab', 'IDProv']);
    }

    /**
     * Relasi ke Person
     */
    public function persons()
    {
        return $this->hasMany(Person::class, ['IDKab', 'IDProv'], ['IDKab', 'IDProv']);
    }
}
