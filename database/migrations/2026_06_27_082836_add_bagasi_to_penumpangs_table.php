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
        Schema::table('penumpangs', function (Blueprint $table) {
            $table->integer('bagasi_kg')->default(20);
            $table->integer('biaya_bagasi')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penumpangs', function (Blueprint $table) {
            //
        });
    }
};
