<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('role', ['Supir', 'Kenek', 'Sales', 'Admin']);
            $table->unsignedBigInteger('pool_id')->nullable();
            $table->string('no_hp')->nullable();
            $table->foreign('pool_id')->references('id')->on('pools')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
