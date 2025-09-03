<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveLotReceh extends Model
{
    use HasFactory;

    protected $table = 'archive_lot_receh';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'no_transaksi',
        'tgl_bln_thn',
        'part_number',
        'serial_number',
        'lot_number',
        'qty',
        'archive_date',
    ];

    protected $casts = [
        'tgl_bln_thn' => 'date',
        'archive_date' => 'datetime',
        'qty' => 'integer',
    ];
}
