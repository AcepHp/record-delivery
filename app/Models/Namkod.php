<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Namkod extends Model
{
    use HasFactory;

    protected $table = 'nama_kode_barang';
    protected $primaryKey = 'id_nkb';
    public $timestamps = false; 
    
    protected $fillable = [ 
        'nama_barang', 
        'kode_barang'
    ];

    
}

