<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartKeluar extends Model
{
    use HasFactory;

    protected $table = 'part_keluar';
    protected $primaryKey = 'id_pk';
    protected $fillable = [
        'tanggal', 
        'pic', 
        'keperluan', 
        'nama_barang',
        'kode_barang', 
        'qty'
    ];
}
