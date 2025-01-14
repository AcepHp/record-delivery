<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    
    protected $table = 'test1';
    protected $primaryKey = 'id';
    public $timestamps = false; 
    
    protected $fillable = [
        'A', 
        'B', 
        'C'
    ];
}
