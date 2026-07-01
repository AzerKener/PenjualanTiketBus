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
        // Change enum to string to support "Virtual Bank" and any other future methods
        DB::statement("ALTER TABLE pemesanans MODIFY metode_pembayaran VARCHAR(50)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pemesanans MODIFY metode_pembayaran ENUM('Cash', 'Transfer', 'E-Wallet')");
    }
};
