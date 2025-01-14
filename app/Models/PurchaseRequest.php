<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;
    protected $table = 'purchase_request';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'ref_pp',
        'req_date',
        'nama_barang',
        'kode_barang',
        'qty_pr',
        'status_submit',
        'submit_date',
        'qty_rcvid',
        'sisa_rcvid'
    ];
}
