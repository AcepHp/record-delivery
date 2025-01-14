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
        Schema::create('part_keluar', function (Blueprint $table) {
            $table->id('id_pk');
            $table->date('tanggal');
            $table->string('PIC');
            $table->string('keperluan');
            $table->string('nama_barang');
            $table->string('kode_barang');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_keluar');
    }
};
