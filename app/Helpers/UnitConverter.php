<?php

namespace App\Helpers;

class UnitConverter
{
    /**
     * Faktor konversi ke satuan basis (gram untuk berat, ml untuk volume)
     */
    private static array $weightFactors = [
        'kg' => 1000,
        'g' => 1,
        'gr' => 1,
        'gram' => 1,
        'ton' => 1000000,
        't' => 1000000,
    ];

    private static array $volumeFactors = [
        'l' => 1000,
        'liter' => 1000,
        'litre' => 1000,
        'ml' => 1,
        'mililiter' => 1,
    ];

    /**
     * Dapatkan faktor konversi ke satuan basis
     * Berat -> gram, Volume -> ml
     */
    public static function getFactor(string $unit): float
    {
        $unit = strtolower(trim($unit));

        if (isset(self::$weightFactors[$unit])) {
            return self::$weightFactors[$unit];
        }

        if (isset(self::$volumeFactors[$unit])) {
            return self::$volumeFactors[$unit];
        }

        // Default: anggap satuan sudah sesuai
        return 1;
    }

    /**
     * Cek apakah satuan termasuk kategori berat
     */
    public static function isWeightUnit(string $unit): bool
    {
        $unit = strtolower(trim($unit));

        return isset(self::$weightFactors[$unit]);
    }

    /**
     * Cek apakah satuan termasuk kategori volume
     */
    public static function isVolumeUnit(string $unit): bool
    {
        $unit = strtolower(trim($unit));

        return isset(self::$volumeFactors[$unit]);
    }

    /**
     * Konversi nilai ke satuan basis (gram atau ml)
     */
    public static function toBaseUnit(float $value, string $unit): float
    {
        return $value * self::getFactor($unit);
    }

    /**
     * Konversi dari satuan basis ke satuan target
     */
    public static function fromBaseUnit(float $baseValue, string $targetUnit): float
    {
        $factor = self::getFactor($targetUnit);

        return $factor > 0 ? $baseValue / $factor : $baseValue;
    }

    /**
     * Format satuan untuk tampilan (auto-scale ke satuan yang lebih besar jika perlu)
     */
    public static function formatDisplay(float $value, string $unit): array
    {
        $unit = strtolower(trim($unit));
        $baseValue = self::toBaseUnit($value, $unit);

        // Untuk berat
        if (self::isWeightUnit($unit)) {
            if ($baseValue >= 1000000) {
                return ['value' => $baseValue / 1000000, 'unit' => 'Ton'];
            }
            if ($baseValue >= 1000) {
                return ['value' => $baseValue / 1000, 'unit' => 'kg'];
            }

            return ['value' => $baseValue, 'unit' => 'g'];
        }

        // Untuk volume
        if (self::isVolumeUnit($unit)) {
            if ($baseValue >= 1000) {
                return ['value' => $baseValue / 1000, 'unit' => 'L'];
            }

            return ['value' => $baseValue, 'unit' => 'ml'];
        }

        return ['value' => $value, 'unit' => $unit];
    }

    /**
     * Hitung estimasi biaya untuk 1 kali aplikasi
     * Menghitung biaya proporsional berdasarkan bahan yang benar-benar terpakai di lahan.
     *
     * @param  float  $luasLahanM2  Luas lahan dalam m²
     * @param  float  $dosisPerHa  Dosis rekomendasi per hektar (angka saja)
     * @param  string  $satuanDosis  Satuan dosis (kg, g, L, ml, dll)
     * @param  float  $hargaKemasan  Harga per satu kemasan
     * @param  float  $isiKemasan  Jumlah isi dalam satu kemasan
     * @param  string  $satuanKemasan  Satuan isi kemasan (kg, g, L, ml, dll)
     * @param  int|string  $frekuensi  Jadwal aplikasi (hanya untuk info, tidak mempengaruhi biaya)
     * @return array ['total_biaya' => float, 'kebutuhan_riil' => string, 'kebutuhan_dasar' => float, 'satuan_dosis' => string]
     */
    public static function hitungBiayaAkurat(
        float $luasLahanM2,
        float $dosisPerHa,
        string $satuanDosis,
        float $hargaKemasan,
        float $isiKemasan,
        string $satuanKemasan,
        int|string $frekuensi = 1
    ): array {
        // 1. Konversi Luas ke Hektar
        $luasHa = $luasLahanM2 / 10000;

        // 2. Hitung Kebutuhan Dasar (dosis × luas) - 1 kali aplikasi
        $kebutuhanDasar = $luasHa * $dosisPerHa;

        // 3. Normalisasi Satuan ke Basis (gram untuk berat, ml untuk volume)
        $faktorDosis = self::getFactor($satuanDosis);
        $faktorKemasan = self::getFactor($satuanKemasan);

        $kebutuhanDalamBasis = $kebutuhanDasar * $faktorDosis;
        $isiKemasanDalamBasis = $isiKemasan * $faktorKemasan;

        // 4. Hitung biaya proporsional (bahan yang terpakai)
        $totalBiaya = 0;
        if ($isiKemasanDalamBasis > 0) {
            $totalBiaya = ($kebutuhanDalamBasis / $isiKemasanDalamBasis) * $hargaKemasan;
        }

        // Format kebutuhan riil untuk tampilan
        $display = self::formatDisplay($kebutuhanDasar, $satuanDosis);
        $kebutuhanRiil = number_format($display['value'], 2, ',', '.').' '.$display['unit'];

        return [
            'total_biaya' => round($totalBiaya),
            'kebutuhan_riil' => $kebutuhanRiil,
            'kebutuhan_dasar' => round($kebutuhanDasar, 2),
            'satuan_dosis' => $satuanDosis,
        ];
    }
}
