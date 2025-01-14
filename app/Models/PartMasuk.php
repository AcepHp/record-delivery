<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartMasuk extends Model
{
    use HasFactory;

    protected $table = 'part_masuk';
    protected $primaryKey = 'id_pm';
    protected $fillable = [
        'tanggal', 
        'ref_pp', 
        'nama_barang',
        'kode_barang', 
        'qty'
    ];
}
