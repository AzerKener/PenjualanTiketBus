<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('pemesanans', function (Blueprint $table) {
        $table->integer('bagasi')->default(20);
        $table->decimal('biaya_bagasi',10,2)->default(0);
    });
}

public function down(): void
{
    Schema::table('pemesanans', function (Blueprint $table) {
        $table->dropColumn(['bagasi','biaya_bagasi']);
    });
}
};
