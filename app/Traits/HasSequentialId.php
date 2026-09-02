<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasSequentialId
{
    /**
     * Generator ID fleksibel & reusable
     */
    protected function generateSequentialId(array $config, bool $justIncrement = false)
    {
        $table   = $config['table']   ?? $this->getTable();
        $column  = $config['column'];
        $prefix  = $config['prefix']  ?? '';
        $pad     = $config['pad']     ?? 7;
        $scope   = $config['scope']   ?? null;

        $query = DB::table($table);

        // Optional scope (contoh: by date prefix)
        if (is_callable($scope)) {
            $scope($query);
        }

        // Ambil ID terakhir
        $lastId = $query
            ->orderBy($column, 'desc')
            ->value($column);

        if ($justIncrement) {

            return $lastId ? ((int) $lastId) + 1 : 1;
        } else {
            // Jika belum ada data
            if (!$lastId) {
                return $prefix . str_pad(1, $pad, '0', STR_PAD_LEFT);
            }

            // Ambil angka terakhir
            $lastNumber = substr($lastId, -$pad);
            $nextNumber = str_pad(((int) $lastNumber) + 1, $pad, '0', STR_PAD_LEFT);

            return substr_replace($lastId, $nextNumber, -$pad);
        }
    }
}
