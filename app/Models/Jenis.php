<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;

    protected $table = 'jenis';
    protected $primaryKey = 'id_jns';
    public $timestamps = false; 
    
    protected $fillable = [
        'uom', 
        'mata_uang'
    ];

    
}

