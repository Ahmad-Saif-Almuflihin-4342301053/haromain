<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class MstDesa extends Model
{
    use Compoships;

    protected $table = 'mstdesa';
    protected $primaryKey = ['IDDesa', 'IDKec', 'IDKab', 'IDProv'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDDesa',
        'NamaDesa',
        'IDKec',
        'IDKab',
        'IDProv',
    ];

    /**
     * Relasi ke Kecamatan
     */
    public function kecamatan()
    {
        return $this->belongsTo(MstKec::class, ['IDKec', 'IDKab', 'IDProv'], ['IDKec', 'IDKab', 'IDProv']);
    }

    /**
     * Relasi ke Person
     */
    public function persons()
    {
        return $this->hasMany(Person::class, ['IDDesa', 'IDKec', 'IDKab', 'IDProv'], ['IDDesa', 'IDKec', 'IDKab', 'IDProv']);
    }
}
