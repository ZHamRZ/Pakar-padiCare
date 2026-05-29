<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Pupuk ──
        Schema::table('pupuk', function (Blueprint $table) {
            $table->decimal('harga_per_unit', 12, 2)->nullable()->after('satuan');
            $table->decimal('satuan_harga_qty', 10, 2)->default(1)->after('harga_per_unit');
            $table->string('satuan_harga_unit', 10)->default('kg')->after('satuan_harga_qty');
        });

        // ── Pestisida ──
        Schema::table('pestisida', function (Blueprint $table) {
            $table->decimal('harga_per_unit', 12, 2)->nullable()->after('satuan_harga');
            $table->decimal('satuan_harga_qty', 10, 2)->default(100)->after('harga_per_unit');
            $table->string('satuan_harga_unit', 10)->default('ml')->after('satuan_harga_qty');
        });

        // ── Migrate existing data ──
        // Pupuk: harga_per_kg → harga_per_unit, satuan → parse qty & unit
        DB::table('pupuk')->get()->each(function ($pupuk) {
            $hargaPerUnit = $pupuk->harga_per_kg ?? 0;
            $satuan = strtolower($pupuk->satuan ?? 'kg');

            // Parse "per 1kg" or "1kg" or just "kg"
            preg_match('/(\d+)\s*(kg|g|ml|l)/i', $satuan, $match);
            $qty = $match[1] ?? 1;
            $unit = strtolower($match[2] ?? 'kg');

            DB::table('pupuk')->where('id', $pupuk->id)->update([
                'harga_per_unit' => $hargaPerUnit,
                'satuan_harga_qty' => $qty,
                'satuan_harga_unit' => $unit,
            ]);
        });

        // Pestisida: harga → harga_per_unit, satuan_harga → parse qty & unit
        DB::table('pestisida')->get()->each(function ($pestisida) {
            $hargaPerUnit = $pestisida->harga ?? 0;
            $satuanHarga = strtolower($pestisida->satuan_harga ?? 'per 100ml');

            // Parse "per 100ml" or "per 1kg"
            preg_match('/per\s+(\d+)\s*(ml|l|kg|g)/i', $satuanHarga, $match);
            $qty = $match[1] ?? 100;
            $unit = strtolower($match[2] ?? 'ml');

            DB::table('pestisida')->where('id', $pestisida->id)->update([
                'harga_per_unit' => $hargaPerUnit,
                'satuan_harga_qty' => $qty,
                'satuan_harga_unit' => $unit,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->dropColumn(['harga_per_unit', 'satuan_harga_qty', 'satuan_harga_unit']);
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->dropColumn(['harga_per_unit', 'satuan_harga_qty', 'satuan_harga_unit']);
        });
    }
};
