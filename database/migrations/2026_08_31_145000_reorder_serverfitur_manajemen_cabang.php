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
        $ordering = [
            1  => 1,  // Manajemen User
            2  => 2,  // Manajemen Travel
            14 => 3,  // Manajemen Cabang
            4  => 4,  // Manajemen Banner
            39 => 5,  // Master Maskapai
            38 => 6,  // Broadcast
            37 => 7,  // Majamenen Slider
            36 => 8,  // Master Kota
            35 => 9,  // Kategori Lokasi
            34 => 10, // Kategori Artikel
            45 => 11, // Manajemen Galeri
            44 => 12, // Manajemen FAQ
            43 => 13, // Manajemen Testimoni
            33 => 14, // Manajemen Lokasi
            32 => 15, // Manajemen Artikel
            3  => 16, // Sistem Setting
            13 => 17, // Manajemen Paket
            40 => 18, // Manajemen Landing
            15 => 19, // Manajemen Agen
            29 => 20, // Manajemen Jamaah
            7  => 21, // Transaksi
            5  => 22, // Monitoring
            6  => 23, // Paket Promo
            17 => 24, // Tagihan Biaya
            8  => 25, // Laporan
        ];

        foreach ($ordering as $fiturId => $noUrut) {
            DB::table('serverfitur')
                ->where('FiturID', $fiturId)
                ->update(['NoUrut' => $noUrut]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('serverfitur')->where('FiturID', 14)->update(['NoUrut' => 6]);
    }
};
