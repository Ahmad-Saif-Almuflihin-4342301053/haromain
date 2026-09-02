<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Closure;

trait DatatablesResponse
{
    protected function datatablesResponse(
        Request $request,
        iterable $rows,
        int $recordsTotal,
        int $recordsFiltered,
        ?Closure $rowTransformer = null,
        array $extra = []
    ) {
        $start = (int) $request->input('start', 0);
        $no = $start;

        foreach ($rows as $row) {
            $row->No = ++$no;

            if ($rowTransformer) {
                $rowTransformer($row);
            }
        }

        return response()->json(array_merge([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows,
        ], $extra));
    }
}
