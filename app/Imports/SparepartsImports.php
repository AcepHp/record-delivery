<?php

namespace App\Imports;

use App\Models\Test;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;

class SparepartsImports implements ToModel
{
    use Importable;

    public function model(array $row)
    {
        return new Test([
            'A'       => $row[0],
            'B'       => $row[1],
            'C'       => $this->transformDate($row[2]),
        ]);
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            } else {
                return Carbon::parse($value)->format($format);
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
