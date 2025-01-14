<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M_Model extends Model
{
    use HasFactory;

    protected $table = 'master_model';
    protected $primaryKey = 'id';
    protected $fillable = [
        'model',
        'plant_dest', 
        'pic'
    ];
}
