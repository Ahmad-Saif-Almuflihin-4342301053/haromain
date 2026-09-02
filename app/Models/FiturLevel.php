<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FiturLevel extends Model
{
    use HasFactory;

    protected $table = 'fiturlevel';
    // protected $primaryKey = 'FiturID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'FiturID',
        'LevelID',
        'IDTravel',
        'ViewData',
        'AddData',
        'EditData',
        'DeleteData',
        'PrintData'
    ];


     public function getMenuByLevel($levelId)
    {
        return DB::table('fiturlevel')
            ->select('serverfitur.*')
            ->join('serverfitur', 'serverfitur.FiturID', '=', 'fiturlevel.FiturID')
            ->where('fiturlevel.LevelID', $levelId)
            ->where('fiturlevel.ViewData', 1)
            ->whereNull('serverfitur.MainFiturID')
            ->orderBy('serverfitur.NoUrut', 'asc')
            ->get();
    }

    public function getSubmenuByLevel($levelId, $mainFiturId)
    {
        return DB::table('fiturlevel')
            ->select('serverfitur.*')
            ->join('serverfitur', 'serverfitur.FiturID', '=', 'fiturlevel.FiturID')
            ->where('fiturlevel.LevelID', $levelId)
            ->where('fiturlevel.ViewData', 1)
            ->where('serverfitur.MainFiturID', $mainFiturId)
            ->orderBy('serverfitur.NoUrut', 'asc')
            ->get();
    }
}
