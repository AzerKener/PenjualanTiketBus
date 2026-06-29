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
        Schema::table('jadwals', function (Blueprint $table) {
            $table->boolean('notified_h_1')->default(false)->after('keterangan');
            $table->boolean('notified_h_3_hours')->default(false)->after('notified_h_1');
            $table->boolean('notified_h_1_hour')->default(false)->after('notified_h_3_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropColumn(['notified_h_1', 'notified_h_3_hours', 'notified_h_1_hour']);
        });
    }
};
