<?php

namespace App\Models\MobileModels;

use Illuminate\Database\Eloquent\Model;

class MstProv extends Model
{
    protected $table = 'mstprov';
    protected $primaryKey = 'IDProv';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'IDProv',
        'NamaProv',
    ];

    /**
     * Relasi ke Kabupaten
     */
    public function kabupaten()
    {
        return $this->hasMany(MstKab::class, 'IDProv', 'IDProv');
    }

    /**
     * Relasi ke Person
     */
    public function persons()
    {
        return $this->hasMany(Person::class, 'IDProv', 'IDProv');
    }
}
