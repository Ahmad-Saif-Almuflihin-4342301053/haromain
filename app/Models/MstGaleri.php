<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDatatableParams;
use App\Traits\HasDatatablesPagination;
use App\Traits\HasSequentialId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class MstGaleri extends Model
{
    use HasFactory, HasDatatableParams, HasDatatablesPagination, HasSequentialId;

    protected $table = 'mstgaleri';
    protected $primaryKey = 'IDGaleri';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public $JenisGaleriFilter;
    public $KategoriGaleriFilter;

    protected $fillable = [
        'IDGaleri',
        'NamaGaleri',
        'KontenGaleri',
        'JenisGaleri',
        'KategoriGaleri',
    ];

    public function setJenisGaleri($jenis)
    {
        $this->JenisGaleriFilter = $jenis;
        return $this;
    }

    public function setKategoriGaleri($kategori)
    {
        $this->KategoriGaleriFilter = $kategori;
        return $this;
    }

    public function generateID($prefix = 'IMG-')
    {
        return $this->generateSequentialId([
            'column' => 'IDGaleri',
            'prefix' => $prefix,
            'pad' => 3,
            'scope' => function ($q) use ($prefix) {
                $q->where('IDGaleri', 'like', $prefix . '%');
            }
        ]);
    }

    private function getDatatablesQuery()
    {
        $query = DB::table('mstgaleri');

        if ($this->JenisGaleriFilter) {
            $query->where('JenisGaleri', $this->JenisGaleriFilter);
        }

        if ($this->KategoriGaleriFilter) {
            $query->where('KategoriGaleri', $this->KategoriGaleriFilter);
        }

        if (request('search.value')) {
            $search = request('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('NamaGaleri', 'like', "%{$search}%")
                  ->orWhere('KategoriGaleri', 'like', "%{$search}%");
            });
        }

        if (request('order')) {
            $order = request('order')[0];
            $column = request('columns')[$order['column']]['data'] ?? 'IDGaleri';
            $dir = $order['dir'];
            // Sanitize column name to prevent SQL injection if needed, but for now simplistic
            $query->orderBy($column, $dir);
        } else {
            $query->orderBy('IDGaleri', 'desc');
        }

        return $query;
    }

    public function count_filtered()
    {
        return $this->getDatatablesQuery()->count();
    }

    public function count_all_datatables()
    {
        $query = DB::table('mstgaleri');
        if ($this->JenisGaleriFilter) {
            $query->where('JenisGaleri', $this->JenisGaleriFilter);
        }
        return $query->count();
    }

    /**
     * Scope untuk filter berdasarkan jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('JenisGaleri', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('KategoriGaleri', $kategori);
    }
}




// -- phpMyAdmin SQL Dump
// -- version 5.2.3
// -- https://www.phpmyadmin.net/
// --
// -- Host: localhost
// -- Generation Time: Feb 06, 2026 at 08:48 AM
// -- Server version: 8.0.45-0ubuntu0.24.04.1
// -- PHP Version: 8.2.30

// SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
// START TRANSACTION;
// SET time_zone = "+00:00";


// /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
// /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
// /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
// /*!40101 SET NAMES utf8mb4 */;

// --
// -- Database: `haromain`
// --

// -- --------------------------------------------------------

// --
// -- Table structure for table `mstgaleri`
// --

// CREATE TABLE `mstgaleri` (
//   `IDGaleri` varchar(25) NOT NULL,
//   `NamaGaleri` varchar(255) DEFAULT NULL,
//   `KontenGaleri` varchar(255) DEFAULT NULL,
//   `JenisGaleri` varchar(255) DEFAULT NULL,
//   `KategoriGaleri` varchar(255) DEFAULT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

// --
// -- Dumping data for table `mstgaleri`
// --

// INSERT INTO `mstgaleri` (`IDGaleri`, `NamaGaleri`, `KontenGaleri`, `JenisGaleri`, `KategoriGaleri`) VALUES
// ('IMG-001', 'Dokumentasi Jamaah 1', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-002', 'Dokumentasi Jamaah 2', 'https://images.unsplash.com/photo-1464983953574-0892a716854b?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-003', 'Dokumentasi Jamaah 3', 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-004', 'Dokumentasi Jamaah 4', 'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-005', 'Dokumentasi Jamaah 5', 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-006', 'Dokumentasi Jamaah 6', 'https://images.unsplash.com/photo-1519985176271-adb1088fa94c?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-007', 'Dokumentasi Jamaah 7', 'https://images.unsplash.com/photo-1493246507139-91e8fad9978e?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-008', 'Dokumentasi Jamaah 8', 'https://images.unsplash.com/photo-1476900543704-4312b78632f8?w=1200', 'gambar', 'Dokumentasi TES'),
// ('IMG-009', 'Dokumentasi Jamaah 8', 'https://images.unsplash.com/photo-1476900543704-4312b78632f8?w=1200', 'gambar', 'Dokumentasi TES COBA'),
// ('IMG-010', 'Dokumentasi Jamaah 6', 'https://images.unsplash.com/photo-1519985176271-adb1088fa94c?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('IMG-011', 'Dokumentasi Jamaah 7', 'https://images.unsplash.com/photo-1493246507139-91e8fad9978e?w=1200', 'gambar', 'Dokumentasi Foto Jamaah & Kegiatan'),
// ('VID-001', 'Video Keberangkatan Jamaah Haji 1', 'm-j1LRgaJ7o', 'video', 'Dokumentasi Keberangkatan & Kepulangan'),
// ('VID-002', 'Video Keberangkatan Jamaah Haji 2', 'Z36U8RsmpxA', 'video', 'Dokumentasi Keberangkatan & Kepulangan'),
// ('VID-003', 'Video Keberangkatan Jamaah Haji 3', 'vFraQQttWYw', 'video', 'Dokumentasi Keberangkatan & Kepulangan'),
// ('VID-004', 'Video Keberangkatan Jamaah Haji 4', 'gMNBcSsswWc', 'video', 'COBA');

// --
// -- Indexes for dumped tables
// --

// --
// -- Indexes for table `mstgaleri`
// --
// ALTER TABLE `mstgaleri`
//   ADD PRIMARY KEY (`IDGaleri`);
// COMMIT;

// /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
// /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
// /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
