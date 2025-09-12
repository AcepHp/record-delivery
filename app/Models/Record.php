<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $table = 'record';
    protected $primaryKey = 'id';
    protected $fillable = [
        'no_transaksi', 
        'tgl_bln_thn', 
        'tgl_bln_thn_dlv', 
        'model',
        'plant_dest', 
        'tipe_delv', 
        'pic',
        'qty',
        'flag',
        'delivery_instruction'
    ];

    public $timestamps = false; 
}
