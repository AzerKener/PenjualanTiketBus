<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_id');
            $table->unsignedBigInteger('jadwal_pulang_id')->nullable();
            $table->enum('tipe_pemesanan', ['Online', 'Sales_Pool']);
            $table->enum('metode_pembayaran', ['Cash', 'Transfer', 'E-Wallet']);
            $table->decimal('total_bayar', 12, 2);
            $table->boolean('is_round_trip')->default(false);
            $table->string('nama_pemesan');
            $table->string('no_hp_pemesan')->nullable();
            $table->timestamp('tanggal_transaksi')->useCurrent();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sales_id')->nullable();
            $table->enum('status_pembayaran', ['pending', 'lunas'])->default('pending');

            $table->foreign('jadwal_id')->references('id')->on('jadwals')->onDelete('restrict');
            $table->foreign('jadwal_pulang_id')->references('id')->on('jadwals')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('sales_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
