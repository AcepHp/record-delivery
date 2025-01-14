<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $table = 'komponen_mesin';
    protected $primaryKey = 'id_km';
    public $timestamps = false; 
    
    protected $fillable = [
        'line', 
        'no_station', 
        'nama_station',
        'nama_barang', 
        'kode_barang',
        'qty',
        'created_at', 
        'updated_at'
    ];

    
}

