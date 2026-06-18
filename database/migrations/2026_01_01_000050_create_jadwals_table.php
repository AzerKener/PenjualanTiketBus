<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id');
            $table->unsignedBigInteger('rute_id');
            $table->unsignedBigInteger('pool_id');
            $table->date('tanggal_berangkat');
            $table->time('waktu_berangkat');
            $table->decimal('harga_tiket', 12, 2);
            $table->unsignedBigInteger('supir1_id');
            $table->unsignedBigInteger('supir2_id')->nullable();
            $table->unsignedBigInteger('kenek_id')->nullable();
            $table->enum('status', ['menunggu', 'berangkat', 'selesai'])->default('menunggu');

            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
            $table->foreign('rute_id')->references('id')->on('rutes')->onDelete('cascade');
            $table->foreign('pool_id')->references('id')->on('pools')->onDelete('cascade');
            $table->foreign('supir1_id')->references('id')->on('pegawais')->onDelete('restrict');
            $table->foreign('supir2_id')->references('id')->on('pegawais')->onDelete('set null');
            $table->foreign('kenek_id')->references('id')->on('pegawais')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
