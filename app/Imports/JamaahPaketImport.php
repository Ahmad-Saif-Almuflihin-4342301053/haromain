<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class JamaahPaketImport implements ToArray
{
    /**
    * @param array $array
    */
    public function array(array $array)
    {
        // Not used when calling Excel::toArray()
        return $array;
    }
}
