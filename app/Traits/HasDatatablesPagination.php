<?php

namespace App\Traits;

trait HasDatatablesPagination
{
    /**
     * Ambil data DataTables dengan limit & offset
     *
     * Class yang memakai trait INI WAJIB punya salah satu method:
     * - getDatatablesQuery()
     * - _get_datatables_query()
     */
    public function getDatatables($params = null)
    {
        $query = $this->resolveDatatablesQuery($params);

        [$length, $start] = $this->resolveDatatablesLimit();

        if ($length !== null && $length != -1) {
            $query->limit($length)->offset($start);
        }

        return $query->get();
    }

    /**
     * Alias untuk kompatibilitas legacy (CI-style)
     */
    public function get_datatables($params = null)
    {
        return $this->getDatatables($params);
    }

    /**
     * Resolve query dari class pemakai
     */
    protected function resolveDatatablesQuery($params = null)
    {
        if (method_exists($this, 'getDatatablesQuery')) {
            return $this->getDatatablesQuery($params);
        }

        if (method_exists($this, '_get_datatables_query')) {
            return $this->_get_datatables_query($params);
        }

        if (method_exists($this, 'datatablesQuery')) {
            return $this->datatablesQuery($params);
        }

        throw new \RuntimeException(
            'Class harus memiliki method getDatatablesQuery() atau _get_datatables_query()'
        );
    }

    /**
     * Resolve length & start dari request atau $_POST
     */
    protected function resolveDatatablesLimit(): array
    {
        // Laravel request()
        if (function_exists('request')) {
            $length = request('length');
            $start  = request('start', 0);

            if ($length !== null) {
                return [$length, $start];
            }
        }

        // Fallback CI / legacy
        if (isset($_POST['length'])) {
            return [
                $_POST['length'],
                $_POST['start'] ?? 0
            ];
        }

        // Tidak ada pagination
        return [null, null];
    }
}
