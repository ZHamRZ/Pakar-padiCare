<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('email');
            $table->text('catatan_profil')->nullable()->after('alamat');
        });

        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->string('preferensi_label', 50)->nullable()->after('tanggal');
            $table->json('preferensi_pengguna')->nullable()->after('preferensi_label');
        });
    }

    public function down(): void
    {
        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->dropColumn(['preferensi_label', 'preferensi_pengguna']);
        });

        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'catatan_profil']);
        });
    }
};
