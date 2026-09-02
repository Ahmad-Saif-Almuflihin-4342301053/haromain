<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Level 1 (Superadmin)
        $existsLevel1 = DB::table('fiturlevel')
            ->where('LevelID', 1)
            ->where('FiturID', 15)
            ->exists();

        if (!$existsLevel1) {
            DB::table('fiturlevel')->insert([
                'LevelID'    => 1,
                'FiturID'    => 15,
                'ViewData'   => 1,
                'AddData'    => 1,
                'EditData'   => 1,
                'DeleteData' => 1,
                'PrintData'  => 1,
                'IDTravel'   => null,
            ]);
        } else {
            DB::table('fiturlevel')
                ->where('LevelID', 1)
                ->where('FiturID', 15)
                ->update([
                    'ViewData'   => 1,
                    'AddData'    => 1,
                    'EditData'   => 1,
                    'DeleteData' => 1,
                    'PrintData'  => 1,
                ]);
        }

        // Level 2 (Admin Travel)
        $existsLevel2 = DB::table('fiturlevel')
            ->where('LevelID', 2)
            ->where('FiturID', 15)
            ->exists();

        if (!$existsLevel2) {
            DB::table('fiturlevel')->insert([
                'LevelID'    => 2,
                'FiturID'    => 15,
                'ViewData'   => 1,
                'AddData'    => 1,
                'EditData'   => 1,
                'DeleteData' => 1,
                'PrintData'  => 1,
                'IDTravel'   => null,
            ]);
        } else {
            DB::table('fiturlevel')
                ->where('LevelID', 2)
                ->where('FiturID', 15)
                ->update([
                    'ViewData'   => 1,
                    'AddData'    => 1,
                    'EditData'   => 1,
                    'DeleteData' => 1,
                    'PrintData'  => 1,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('fiturlevel')
            ->whereIn('LevelID', [1, 2])
            ->where('FiturID', 15)
            ->update([
                'ViewData' => 0
            ]);
    }
};
