<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, change column to string to allow new values
        Schema::table('kriteria', function (Blueprint $table) {
            $table->string('jenis', 20)->default('Efektif')->change();
        });

        // Now convert existing data: manfaat → Efektif, biaya → Murah
        DB::table('kriteria')->where('jenis', 'manfaat')->update(['jenis' => 'Efektif']);
        DB::table('kriteria')->where('jenis', 'biaya')->update(['jenis' => 'Murah']);

        // Dampak Lingkungan → Aman
        DB::table('kriteria')
            ->where('nama', 'like', '%Dampak%')
            ->orWhere('nama', 'like', '%Lingkungan%')
            ->update(['jenis' => 'Aman']);

        // Finally, change to new enum
        Schema::table('kriteria', function (Blueprint $table) {
            $table->enum('jenis', ['Murah', 'Efektif', 'Aman'])->default('Efektif')->change();
        });
    }

    public function down(): void
    {
        // Revert to string first
        Schema::table('kriteria', function (Blueprint $table) {
            $table->string('jenis', 20)->default('manfaat')->change();
        });

        // Revert data
        DB::table('kriteria')->where('jenis', 'Efektif')->update(['jenis' => 'manfaat']);
        DB::table('kriteria')->where('jenis', 'Murah')->update(['jenis' => 'biaya']);
        DB::table('kriteria')->where('jenis', 'Aman')->update(['jenis' => 'biaya']);

        // Change back to old enum
        Schema::table('kriteria', function (Blueprint $table) {
            $table->enum('jenis', ['manfaat', 'biaya'])->default('manfaat')->change();
        });
    }
};
