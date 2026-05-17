<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel gejala_pupuk dan gejala_pestisida tidak digunakan lagi.
        // Rekomendasi produk berbasis penyakit memakai penyakit_pupuk dan penyakit_pestisida.
        Schema::dropIfExists('gejala_pestisida');
        Schema::dropIfExists('gejala_pupuk');
    }

    public function down(): void
    {
        Schema::dropIfExists('gejala_pestisida');
        Schema::dropIfExists('gejala_pupuk');
    }
};
