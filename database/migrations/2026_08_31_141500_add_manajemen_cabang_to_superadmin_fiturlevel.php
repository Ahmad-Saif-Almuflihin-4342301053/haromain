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
        $exists = DB::table('fiturlevel')
            ->where('LevelID', 1)
            ->where('FiturID', 14)
            ->exists();

        if (!$exists) {
            DB::table('fiturlevel')->insert([
                'LevelID'    => 1,
                'FiturID'    => 14,
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
                ->where('FiturID', 14)
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
            ->where('LevelID', 1)
            ->where('FiturID', 14)
            ->delete();
    }
};
