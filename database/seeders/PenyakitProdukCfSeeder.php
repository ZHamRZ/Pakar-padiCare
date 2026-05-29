<?php

namespace Database\Seeders;

use App\Models\Penyakit;
use App\Models\PenyakitPestisida;
use App\Models\PenyakitPupuk;
use App\Models\Pestisida;
use App\Models\Pupuk;
use Illuminate\Database\Seeder;

class PenyakitProdukCfSeeder extends Seeder
{
    public function run(): void
    {
        // ── RELASI PENYAKIT - PUPUK (CF Spesifik) ────────────────────────────
        $p01 = Penyakit::where('kode', 'P01')->first(); // Blast
        $p02 = Penyakit::where('kode', 'P02')->first(); // Hawar Daun Bakteri (Kresek)
        $p03 = Penyakit::where('kode', 'P03')->first(); // Tungro
        $p04 = Penyakit::where('kode', 'P04')->first(); // Busuk Pelepah
        $p05 = Penyakit::where('kode', 'P05')->first(); // Bercak Coklat

        $pk01 = Pupuk::where('kode', 'PK01')->first(); // Urea
        $pk04 = Pupuk::where('kode', 'PK04')->first(); // Dolomit
        $pk06 = Pupuk::where('kode', 'PK06')->first(); // Silika Cair
        $pk07 = Pupuk::where('kode', 'PK07')->first(); // MKP
        $pk08 = Pupuk::where('kode', 'PK08')->first(); // KNO3 Putih
        $pk09 = Pupuk::where('kode', 'PK09')->first(); // Pupuk Mikro
        $pk10 = Pupuk::where('kode', 'PK10')->first(); // NPK Mutiara

        $timestamp = now();

        // Data relasi penyakit-pupuk dari input user
        $penyakitPupukData = [
            // P01 (Blast)
            ['id_penyakit' => $p01->id, 'id_pupuk' => $pk06->id, 'mb' => 0.900, 'md' => 0.050], // Silika Cair: CF 0.850
            ['id_penyakit' => $p01->id, 'id_pupuk' => $pk07->id, 'mb' => 0.850, 'md' => 0.050], // MKP: CF 0.800
            ['id_penyakit' => $p01->id, 'id_pupuk' => $pk01->id, 'mb' => 0.100, 'md' => 0.900], // Urea: CF -0.800 (Hindari)

            // P02 (Kresek)
            ['id_penyakit' => $p02->id, 'id_pupuk' => $pk08->id, 'mb' => 0.800, 'md' => 0.100], // KNO3 Putih: CF 0.700
            ['id_penyakit' => $p02->id, 'id_pupuk' => $pk01->id, 'mb' => 0.100, 'md' => 0.850], // Urea: CF -0.750 (Hindari)

            // P03 (Tungro)
            ['id_penyakit' => $p03->id, 'id_pupuk' => $pk10->id, 'mb' => 0.850, 'md' => 0.100], // NPK Mutiara: CF 0.750

            // P04 (Busuk Pelepah)
            ['id_penyakit' => $p04->id, 'id_pupuk' => $pk04->id, 'mb' => 0.750, 'md' => 0.100], // Dolomit: CF 0.650

            // P05 (Bercak Coklat)
            ['id_penyakit' => $p05->id, 'id_pupuk' => $pk09->id, 'mb' => 0.850, 'md' => 0.100], // Pupuk Mikro: CF 0.750
        ];

        foreach ($penyakitPupukData as $data) {
            PenyakitPupuk::updateOrCreate(
                [
                    'id_penyakit' => $data['id_penyakit'],
                    'id_pupuk' => $data['id_pupuk'],
                ],
                [
                    'mb' => $data['mb'],
                    'md' => $data['md'],
                    'updated_at' => $timestamp,
                ]
            );
        }

        // ── RELASI PENYAKIT - PESTISIDA (CF Spesifik) ────────────────────────────
        $ps01 = Pestisida::where('kode', 'PS01')->first(); // Nordox 56 WP
        $ps02 = Pestisida::where('kode', 'PS02')->first(); // Mankozeb
        $ps03 = Pestisida::where('kode', 'PS03')->first(); // Heksakonazol
        $ps04 = Pestisida::where('kode', 'PS04')->first(); // BPMC (Bassa)
        $ps05 = Pestisida::where('kode', 'PS05')->first(); // Agrept 20 WP
        $ps06 = Pestisida::where('kode', 'PS06')->first(); // Nativo 75 WG
        $ps07 = Pestisida::where('kode', 'PS07')->first(); // Seltima 100 CS
        $ps08 = Pestisida::where('kode', 'PS08')->first(); // Kasumin 20 L
        $ps09 = Pestisida::where('kode', 'PS09')->first(); // Plenum 50 WG
        $ps10 = Pestisida::where('kode', 'PS10')->first(); // Amistartop 325 SC

        // Data relasi penyakit-pestisida dari input user
        $penyakitPestisidaData = [
            // P01 (Blast)
            ['id_penyakit' => $p01->id, 'id_pestisida' => $ps06->id, 'mb' => 0.950, 'md' => 0.050], // Nativo: CF 0.900
            ['id_penyakit' => $p01->id, 'id_pestisida' => $ps10->id, 'mb' => 0.900, 'md' => 0.050], // Amistartop: CF 0.850
            ['id_penyakit' => $p01->id, 'id_pestisida' => $ps02->id, 'mb' => 0.600, 'md' => 0.200], // Mankozeb: CF 0.400

            // P02 (Kresek)
            ['id_penyakit' => $p02->id, 'id_pestisida' => $ps08->id, 'mb' => 0.900, 'md' => 0.050], // Kasumin: CF 0.850
            ['id_penyakit' => $p02->id, 'id_pestisida' => $ps01->id, 'mb' => 0.850, 'md' => 0.100], // Nordox: CF 0.750
            ['id_penyakit' => $p02->id, 'id_pestisida' => $ps05->id, 'mb' => 0.700, 'md' => 0.150], // Agrept: CF 0.550

            // P03 (Tungro)
            ['id_penyakit' => $p03->id, 'id_pestisida' => $ps09->id, 'mb' => 0.950, 'md' => 0.020], // Plenum: CF 0.930
            ['id_penyakit' => $p03->id, 'id_pestisida' => $ps04->id, 'mb' => 0.750, 'md' => 0.150], // BPMC: CF 0.600

            // P04 (Busuk Pelepah)
            ['id_penyakit' => $p04->id, 'id_pestisida' => $ps03->id, 'mb' => 0.800, 'md' => 0.100], // Heksakonazol: CF 0.700
            ['id_penyakit' => $p04->id, 'id_pestisida' => $ps10->id, 'mb' => 0.900, 'md' => 0.050], // Amistartop: CF 0.850

            // P05 (Bercak Coklat)
            ['id_penyakit' => $p05->id, 'id_pestisida' => $ps07->id, 'mb' => 0.900, 'md' => 0.050], // Seltima: CF 0.850
        ];

        foreach ($penyakitPestisidaData as $data) {
            PenyakitPestisida::updateOrCreate(
                [
                    'id_penyakit' => $data['id_penyakit'],
                    'id_pestisida' => $data['id_pestisida'],
                ],
                [
                    'mb' => $data['mb'],
                    'md' => $data['md'],
                    'updated_at' => $timestamp,
                ]
            );
        }

        $this->command?->info('Relasi CF penyakit-pupuk dan penyakit-pestisida berhasil diisi.');
    }
}
