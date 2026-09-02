<?php

namespace App\Models;

use App\Traits\HasSequentialId;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServerLog extends Model
{
    use HasFactory, HasSequentialId;

    protected $table = 'serverlog';
    protected $primaryKey = 'LogID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'DateTimeLog',
        'LogID',
        'Action',
        'Description',
        'UserName',
        'NoTransaksi',
        'JenisTransaksi',
    ];

     public function generateID()
    {
        return $this->generateSequentialId([
            'column' => 'LogID',
            'prefix' => 'LOG-',
            'pad'    => 7,
        ]);
    }

    public function insertLog($NoTransaksi, $Action, $JenisTransaksi)
    {

        $username=auth()->user()->UserName;
        $data = [
            'LogID'        => $this->generateID(),
            'DateTimeLog'  => now(),
            'Action'       => $Action,
            'UserName'     => $username,
            'NoTransaksi'  => $NoTransaksi,
            'JenisTransaksi' => $JenisTransaksi,
        ];



        $data['Description'] = "{$username} {$Action} {$JenisTransaksi} {$NoTransaksi}";

        // dd($data);

        return DB::table($this->table)->insert($data);
    }
}
