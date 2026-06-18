<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Pegawai;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter users table role ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Sales', 'Supir', 'Kenek', 'User') DEFAULT 'User'");

        Schema::table('pegawais', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        // Backfill user_id for existing pegawais
        $pegawais = Pegawai::all();
        foreach ($pegawais as $pegawai) {
            $user = User::where('name', $pegawai->nama)->first();
            if ($user) {
                $pegawai->update(['user_id' => $user->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Cannot safely drop enum value in raw SQL if data exists, so we just remove the Kenek role if any and revert enum
        DB::table('users')->where('role', 'Kenek')->delete();
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Sales', 'Supir', 'User') DEFAULT 'User'");
    }
};
