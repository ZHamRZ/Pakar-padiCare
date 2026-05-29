<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('gejala', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 10)->unique();
            $table->string('nama_gejala', 200);
            $table->timestamps();
        });

        Schema::create('penyakit_gejala', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_penyakit');
            $table->foreign('id_penyakit')->references('id')->on('penyakit')->cascadeOnDelete();
            $table->unsignedInteger('id_gejala');
            $table->foreign('id_gejala')->references('id')->on('gejala')->cascadeOnDelete();
            $table->unique(['id_penyakit', 'id_gejala']);
        });

        Schema::create('kriteria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->enum('jenis', ['manfaat', 'biaya']);
            $table->decimal('bobot', 5, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('pupuk', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->string('kandungan', 200)->nullable();
            $table->text('fungsi_utama')->nullable();
            $table->decimal('harga_per_kg', 10, 2);
            $table->string('satuan', 20)->default('kg');
            $table->timestamps();
        });

        Schema::create('pestisida', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->enum('jenis', ['fungisida', 'bakterisida', 'insektisida', 'herbisida']);
            $table->string('bahan_aktif', 200)->nullable();
            $table->string('dosis', 100)->nullable();
            $table->decimal('harga', 10, 2);
            $table->string('satuan_harga', 30)->default('per 100ml');
            $table->timestamps();
        });

        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_pengguna');
            $table->foreign('id_pengguna')->references('id')->on('pengguna')->cascadeOnDelete();
            $table->unsignedInteger('id_penyakit');
            $table->foreign('id_penyakit')->references('id')->on('penyakit')->cascadeOnDelete();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();
        });

        Schema::create('detail_rekomendasi_pupuk', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_rekomendasi');
            $table->foreign('id_rekomendasi')->references('id')->on('rekomendasi')->cascadeOnDelete();
            $table->unsignedInteger('id_pupuk');
            $table->foreign('id_pupuk')->references('id')->on('pupuk')->cascadeOnDelete();
            $table->decimal('nilai_vi', 8, 6);
            $table->integer('peringkat');
        });

        Schema::create('detail_rekomendasi_pestisida', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_rekomendasi');
            $table->foreign('id_rekomendasi')->references('id')->on('rekomendasi')->cascadeOnDelete();
            $table->unsignedInteger('id_pestisida');
            $table->foreign('id_pestisida')->references('id')->on('pestisida')->cascadeOnDelete();
            $table->decimal('nilai_vi', 8, 6);
            $table->integer('peringkat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_rekomendasi_pestisida');
        Schema::dropIfExists('detail_rekomendasi_pupuk');
        Schema::dropIfExists('rekomendasi');
        Schema::dropIfExists('pestisida');
        Schema::dropIfExists('pupuk');
        Schema::dropIfExists('kriteria');
        Schema::dropIfExists('penyakit_gejala');
        Schema::dropIfExists('gejala');
    }
};
