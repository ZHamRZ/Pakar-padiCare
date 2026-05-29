<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('rating_pestisida');
        Schema::dropIfExists('rating_pupuk');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tabel lama tidak dibuat ulang. Nilai pakar disimpan pada penyakit_pupuk
        // dan penyakit_pestisida dengan kolom MB/MD.
    }
};
