<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateMasterDataTakaranDanHargaSeeder extends Seeder
{
    public function run(): void
    {
        // ── PUPUK (Standardized to Grams) ────────────────────────
        // Data berdasarkan tabel "PASTI" terbaru.
        $pupukData = [
            // PK01: Urea, 100 kg/ha -> 100,000 g. Price 1,800/kg -> 1,800/1,000g
            ['kode' => 'PK01', 'dosis_per_hektar' => 100000, 'satuan_dosis' => 'g', 'harga_per_unit' => 1800, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK02: NPK Phonska, 100 kg/ha -> 100,000 g. Price 1,840/kg -> 1,840/1,000g
            ['kode' => 'PK02', 'dosis_per_hektar' => 100000, 'satuan_dosis' => 'g', 'harga_per_unit' => 1840, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK03: ZA, 75 kg/ha -> 75,000 g. Price 1,360/kg -> 1,360/1,000g
            ['kode' => 'PK03', 'dosis_per_hektar' => 75000, 'satuan_dosis' => 'g', 'harga_per_unit' => 1360, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK04: Dolomit, 1,500 kg/ha -> 1,500,000 g. Price 1,250/kg -> 1,250/1,000g
            ['kode' => 'PK04', 'dosis_per_hektar' => 1500000, 'satuan_dosis' => 'g', 'harga_per_unit' => 1250, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK05: Kandang, 3,500 kg/ha -> 3,500,000 g. Price 1,000/kg -> 1,000/1,000g
            ['kode' => 'PK05', 'dosis_per_hektar' => 3500000, 'satuan_dosis' => 'g', 'harga_per_unit' => 1000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK06: Silika, 1 L/ha -> 1,000 ml. Price 115,000/L -> 115,000/1,000ml
            ['kode' => 'PK06', 'dosis_per_hektar' => 1000, 'satuan_dosis' => 'ml', 'harga_per_unit' => 115000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PK07: MKP, 3.5 kg/ha -> 3,500 g. Price 42,500/kg -> 42,500/1,000g
            ['kode' => 'PK07', 'dosis_per_hektar' => 3500, 'satuan_dosis' => 'g', 'harga_per_unit' => 42500, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK08: KNO3, 12.5 kg/ha -> 12,500 g. Price 32,500/kg -> 32,500/1,000g
            ['kode' => 'PK08', 'dosis_per_hektar' => 12500, 'satuan_dosis' => 'g', 'harga_per_unit' => 32500, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK09: Mikro, 7.5 kg/ha -> 7,500 g. Price 30,000/kg -> 30,000/1,000g
            ['kode' => 'PK09', 'dosis_per_hektar' => 7500, 'satuan_dosis' => 'g', 'harga_per_unit' => 30000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PK10: Mutiara, 250 kg/ha -> 250,000 g. Price 18,000/kg -> 18,000/1,000g
            ['kode' => 'PK10', 'dosis_per_hektar' => 250000, 'satuan_dosis' => 'g', 'harga_per_unit' => 18000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PU01: KCL, 50 kg/ha -> 50,000 g. Price 10,000/kg -> 10,000/1,000g
            ['kode' => 'PU01', 'dosis_per_hektar' => 50000, 'satuan_dosis' => 'g', 'harga_per_unit' => 10000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PU02: SP-36, 75 kg/ha -> 75,000 g. Price 5,000/kg -> 5,000/1,000g
            ['kode' => 'PU02', 'dosis_per_hektar' => 75000, 'satuan_dosis' => 'g', 'harga_per_unit' => 5000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
        ];

        foreach ($pupukData as $data) {
            DB::table('pupuk')
                ->where('kode', $data['kode'])
                ->update([
                    'dosis_per_hektar' => $data['dosis_per_hektar'],
                    'satuan_dosis' => $data['satuan_dosis'],
                    'harga_per_unit' => $data['harga_per_unit'],
                    'satuan_harga_qty' => $data['satuan_harga_qty'],
                    'satuan_harga_unit' => $data['satuan_harga_unit'],
                ]);
        }

        // ── PESTISIDA (Standardized to Grams or Milliliters) ─────
        // Data berdasarkan tabel "PASTI" terbaru.
        $pestisidaData = [
            // PS01: Nordox, 135 g. Price 200k/100g
            ['kode' => 'PS01', 'dosis_per_hektar' => 135, 'satuan_dosis' => 'g', 'harga_per_unit' => 200000, 'satuan_harga_qty' => 100, 'satuan_harga_unit' => 'g'],
            // PS02: Mankozeb, 2.25 kg -> 2250 g. Price 60k/kg -> 60k/1000g
            ['kode' => 'PS02', 'dosis_per_hektar' => 2250, 'satuan_dosis' => 'g', 'harga_per_unit' => 60000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PS03: Heksakonazol, 0.75 L -> 750 ml. Price 160k/L -> 160k/1000ml
            ['kode' => 'PS03', 'dosis_per_hektar' => 750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 160000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PS04: BPMC, 1.5 L -> 1500 ml. Price 100k/L -> 100k/1000ml
            ['kode' => 'PS04', 'dosis_per_hektar' => 1500, 'satuan_dosis' => 'ml', 'harga_per_unit' => 100000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PS05: Agrept, 150 g. Price 65k/100g
            ['kode' => 'PS05', 'dosis_per_hektar' => 150, 'satuan_dosis' => 'g', 'harga_per_unit' => 65000, 'satuan_harga_qty' => 100, 'satuan_harga_unit' => 'g'],
            // PS06: Nativo, 300 g. Price 285k/100g
            ['kode' => 'PS06', 'dosis_per_hektar' => 300, 'satuan_dosis' => 'g', 'harga_per_unit' => 285000, 'satuan_harga_qty' => 100, 'satuan_harga_unit' => 'g'],
            // PS07: Seltima, 0.75 L -> 750 ml. Price 205k/L -> 205k/1000ml
            ['kode' => 'PS07', 'dosis_per_hektar' => 750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 205000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PS08: Kasumin, 1.75 L -> 1750 ml. Price 140k/L -> 140k/1000ml
            ['kode' => 'PS08', 'dosis_per_hektar' => 1750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 140000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PS09: Plenum, 150 g. Price 325k/100g
            ['kode' => 'PS09', 'dosis_per_hektar' => 150, 'satuan_dosis' => 'g', 'harga_per_unit' => 325000, 'satuan_harga_qty' => 100, 'satuan_harga_unit' => 'g'],
            // PS10: Amistartop, 0.75 L -> 750 ml. Price 235k/250ml
            ['kode' => 'PS10', 'dosis_per_hektar' => 750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 235000, 'satuan_harga_qty' => 250, 'satuan_harga_unit' => 'ml'],
            // PS11: Bactocyn, 0.75 L -> 750 ml. Price 80k/L -> 80k/1000ml
            ['kode' => 'PS11', 'dosis_per_hektar' => 750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 80000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PS12: Filia, 0.75 L -> 750 ml. Price 200k/250ml
            ['kode' => 'PS12', 'dosis_per_hektar' => 750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 200000, 'satuan_harga_qty' => 250, 'satuan_harga_unit' => 'ml'],
            // PS13: Validacin, 1.5 L -> 1500 ml. Price 105k/L -> 105k/1000ml
            ['kode' => 'PS13', 'dosis_per_hektar' => 1500, 'satuan_dosis' => 'ml', 'harga_per_unit' => 105000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PS14: Winder, 0.75 L -> 750 ml. Price 130k/L -> 130k/1000ml
            ['kode' => 'PS14', 'dosis_per_hektar' => 750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 130000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
            // PS15: Regent, 28 g. Price 12.5k/1.6g
            ['kode' => 'PS15', 'dosis_per_hektar' => 28, 'satuan_dosis' => 'g', 'harga_per_unit' => 12500, 'satuan_harga_qty' => 1.6, 'satuan_harga_unit' => 'g'],
            // PS16: Antracol, 2 kg -> 2000 g. Price 125k/kg -> 125k/1000g
            ['kode' => 'PS16', 'dosis_per_hektar' => 2000, 'satuan_dosis' => 'g', 'harga_per_unit' => 125000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'g'],
            // PS17: Ridomil, 1.75 kg -> 1750 g. Price 200k/500g
            ['kode' => 'PS17', 'dosis_per_hektar' => 1750, 'satuan_dosis' => 'g', 'harga_per_unit' => 200000, 'satuan_harga_qty' => 500, 'satuan_harga_unit' => 'g'],
            // PS18: Topaz, 0.75 L -> 750 ml. Price 185k/L -> 185k/1000ml
            ['kode' => 'PS18', 'dosis_per_hektar' => 750, 'satuan_dosis' => 'ml', 'harga_per_unit' => 185000, 'satuan_harga_qty' => 1000, 'satuan_harga_unit' => 'ml'],
        ];

        foreach ($pestisidaData as $data) {
            DB::table('pestisida')
                ->where('kode', $data['kode'])
                ->update([
                    'dosis_per_hektar' => $data['dosis_per_hektar'],
                    'satuan_dosis' => $data['satuan_dosis'],
                    'harga_per_unit' => $data['harga_per_unit'],
                    'satuan_harga_qty' => $data['satuan_harga_qty'],
                    'satuan_harga_unit' => $data['satuan_harga_unit'],
                ]);
        }

        $this->command->info('Master data takaran dan harga berhasil diupdate sesuai tabel PASTI (Single Value).');
    }
}
