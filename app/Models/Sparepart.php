<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $table = 'mntrng_sparepart';
    protected $primaryKey = 'id_sp';
    public $timestamps = false; 
    
    protected $fillable = [
    'nama_barang','kode_barang','address','fa_l1','fa_l2','fa_l3','fa_l5','fa_l6', 'fa_rework1', 'fa_rework2', 'fa_sab', 'smt_offline','smt_l1_top','smt_l1_bot','smt_l1_bckend','smt_l2_topbot','smt_l2_bckend','utility','total_qty','ms_ss','lifetime','leadtime','min_stock','stock_wrhs','part_masuk','part_keluar','stock_akhir_wrhs','uom','harga','mata_uang','vendor', 'status'
    ];

    
}

