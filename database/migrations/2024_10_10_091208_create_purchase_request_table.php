<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_request', function (Blueprint $table) {
            $table->id();
            $table->string('ref_pp');
            $table->date('req_date');    
            $table->string('nama_barang');    
            $table->string('kode_barang');    
            $table->integer('qty');    
            $table->string('status_submit');    
            $table->date('submit_date');    
            $table->integer('qty_rcvid');    
            $table->date('received_date');    
            $table->integer('sisa_rcvid');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request');
    }
};
