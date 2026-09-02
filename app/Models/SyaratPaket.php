<?php

namespace App\Models;

use App\Traits\HasSequentialId;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SyaratPaket extends Model
{
    use HasFactory, HasSequentialId;

    protected $table = 'syaratpaket';
    protected $primaryKey = 'NoUrutSyarat';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NoUrutSyarat',
        'IDPaket',
        'IDTravel',
        'NamaSyarat',
        'Keterangan',
    ];

    public function totalMonitor(array $where)
    {
        return $this->where($where)->count();
    }

    public function totalUploadMonitor(array $where)
    {
        return DB::table('dokumensyarat')
            ->where($where)
            ->count();
    }

    public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'NoUrutSyarat',
        ], true);
    }

    public function getByPaket($IDPaket)
    {
        return DB::table($this->table)
            ->where('IDPaket', $IDPaket)
            ->orderBy('NoUrutSyarat', 'ASC')
            ->get();
    }
}
