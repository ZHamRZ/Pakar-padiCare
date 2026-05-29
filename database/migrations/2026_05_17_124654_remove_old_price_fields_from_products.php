<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->dropColumn(['harga_per_kg', 'satuan']);
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->dropColumn(['harga', 'satuan_harga']);
        });
    }

    public function down(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->decimal('harga_per_kg', 10, 2)->nullable()->after('frekuensi_aplikasi');
            $table->string('satuan', 20)->default('kg')->after('harga_per_kg');
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->decimal('harga', 10, 2)->nullable()->after('frekuensi_aplikasi');
            $table->string('satuan_harga', 30)->default('per 100ml')->after('harga');
        });
    }
};
