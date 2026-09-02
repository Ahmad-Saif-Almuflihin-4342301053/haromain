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
        $fiturIds = [
            4,  // Manajemen Banner
            36, // Master Kota
            32, // Manajemen Artikel
            33, // Manajemen Lokasi
        ];

        foreach ($fiturIds as $fiturId) {
            $exists = DB::table('fiturlevel')
                ->where('LevelID', 2)
                ->where('FiturID', $fiturId)
                ->exists();

            if (!$exists) {
                DB::table('fiturlevel')->insert([
                    'LevelID'    => 2,
                    'FiturID'    => $fiturId,
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
                    ->where('FiturID', $fiturId)
                    ->update([
                        'ViewData'   => 1,
                        'AddData'    => 1,
                        'EditData'   => 1,
                        'DeleteData' => 1,
                        'PrintData'  => 1,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('fiturlevel')
            ->where('LevelID', 2)
            ->whereIn('FiturID', [4, 36, 32, 33])
            ->delete();
    }
};
