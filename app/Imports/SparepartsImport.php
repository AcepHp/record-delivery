<?php

namespace App\Imports;

use App\Models\Sparepart;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;

class SparepartsImport implements ToModel
{
    use Importable;

    public function model(array $row)
    {
        return new Sparepart([
            'nama_barang'       => $row[0],
            'kode_barang'       => $row[1],
            'address'           => $row[2],
            'total_qty'         => $row[3],
            'leadtime'          => $row[4],
            'lifetime'          => $row[5],
            'min_stock'         => $row[6],
            'stock_akhir_wrhs'  => $row[7],
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
