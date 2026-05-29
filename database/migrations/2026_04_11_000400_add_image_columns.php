<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('foto_profil')->nullable()->after('catatan_profil');
        });

        Schema::table('penyakit', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('deskripsi');
        });

        Schema::table('pupuk', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('satuan');
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('satuan_harga');
        });
    }

    public function down(): void
    {
        Schema::table('pestisida', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });

        Schema::table('pupuk', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });

        Schema::table('penyakit', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });

        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn('foto_profil');
        });
    }
};
