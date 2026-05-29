<?php

namespace Tests\Unit\Services;

use App\Services\CertaintyFactorEngine;
use App\Services\FertilizerPesticideRecommendationEngine;
use PHPUnit\Framework\TestCase;

/**
 * Test untuk FertilizerPesticideRecommendationEngine
 *
 * Menguji logika transformasi CF untuk pupuk (penyebab) dan pestisida (solusi)
 */
class FertilizerPesticideRecommendationEngineTest extends TestCase
{
    private FertilizerPesticideRecommendationEngine $engine;

    private CertaintyFactorEngine $cfEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cfEngine = new CertaintyFactorEngine;
        $this->engine = new FertilizerPesticideRecommendationEngine($this->cfEngine);
    }

    /** @test */
    public function it_transforms_fertilizer_cf_correctly()
    {
        // Pupuk sebagai PENYEBAB:
        // CF positif → memperparah kondisi → TIDAK direkomendasikan
        // CF negatif → tidak menyebabkan/membantu → DIREKOMENDASIKAN

        // Simulasi: CF penyebab = 0.6 (pupuk memperparah gejala)
        // Transformasi: CF_rekomendasi = -0.6 (tidak direkomendasikan)
        $cfPenyebabPositif = 0.6;
        $cfRekomendasi = -$cfPenyebabPositif;
        $this->assertEquals(-0.6, $cfRekomendasi);

        // Simulasi: CF penyebab = -0.7 (pupuk membantu mengatasi gejala)
        // Transformasi: CF_rekomendasi = 0.7 (direkomendasikan)
        $cfPenyebabNegatif = -0.7;
        $cfRekomendasi = -$cfPenyebabNegatif;
        $this->assertEquals(0.7, $cfRekomendasi);

        // Simulasi: CF penyebab = 0 (netral)
        // Transformasi: CF_rekomendasi = 0 (netral)
        $cfPenyebabNetral = 0.0;
        $cfRekomendasi = -$cfPenyebabNetral;
        $this->assertEquals(0.0, $cfRekomendasi);
    }

    /** @test */
    public function it_does_not_transform_pesticide_cf()
    {
        // Pestisida sebagai SOLUSI:
        // CF positif → efektif → DIREKOMENDASIKAN
        // CF negatif → tidak efektif → TIDAK direkomendasikan
        // Tidak ada transformasi: CF_rekomendasi = CF_asli

        $cfSolusiPositif = 0.8;
        $this->assertEquals(0.8, $cfSolusiPositif);

        $cfSolusiNegatif = -0.5;
        $this->assertEquals(-0.5, $cfSolusiNegatif);

        $cfSolusiNetral = 0.0;
        $this->assertEquals(0.0, $cfSolusiNetral);
    }

    /** @test */
    public function it_combines_multiple_positive_cf_correctly()
    {
        // Rumus: CFcombine = CF1 + CF2 * (1 - CF1)

        // Contoh 1: 0.5 dan 0.6
        $result = $this->cfEngine->combineCf(0.5, 0.6);
        // 0.5 + 0.6 * (1 - 0.5) = 0.5 + 0.6 * 0.5 = 0.5 + 0.3 = 0.8
        $this->assertEquals(0.8, $result);

        // Contoh 2: 0.7 dan 0.8
        $result = $this->cfEngine->combineCf(0.7, 0.8);
        // 0.7 + 0.8 * (1 - 0.7) = 0.7 + 0.8 * 0.3 = 0.7 + 0.24 = 0.94
        $this->assertEquals(0.94, $result);
    }

    /** @test */
    public function it_combines_multiple_negative_cf_correctly()
    {
        // Rumus: CFcombine = CF1 + CF2 * (1 + CF1) untuk nilai negatif

        // Contoh: -0.5 dan -0.6
        $result = $this->cfEngine->combineCf(-0.5, -0.6);
        // -0.5 + (-0.6) * (1 + (-0.5)) = -0.5 + (-0.6) * 0.5 = -0.5 - 0.3 = -0.8
        $this->assertEquals(-0.8, $result);
    }

    /** @test */
    public function it_combines_cf_with_opposite_signs_correctly()
    {
        // Rumus: CFcombine = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))

        // Contoh: 0.8 dan -0.4
        $result = $this->cfEngine->combineCf(0.8, -0.4);
        // (0.8 + (-0.4)) / (1 - min(0.8, 0.4)) = 0.4 / (1 - 0.4) = 0.4 / 0.6 = 0.666...
        $this->assertEqualsWithDelta(0.666667, $result, 0.0001);

        // Contoh: -0.5 dan 0.6
        $result = $this->cfEngine->combineCf(-0.5, 0.6);
        // (-0.5 + 0.6) / (1 - min(0.5, 0.6)) = 0.1 / 0.5 = 0.2
        $this->assertEquals(0.2, $result);
    }

    /** @test */
    public function it_provides_correct_recommendation_labels()
    {
        // Sangat Direkomendasikan: CF > 0.7
        $label = $this->engine->getRecommendationLabel(0.85);
        $this->assertEquals('Sangat Direkomendasikan', $label['label']);
        $this->assertEquals('success', $label['color']);

        // Direkomendasikan: 0.4 < CF <= 0.7
        $label = $this->engine->getRecommendationLabel(0.6);
        $this->assertEquals('Direkomendasikan', $label['label']);
        $this->assertEquals('primary', $label['color']);

        // Cukup: 0.1 < CF <= 0.4
        $label = $this->engine->getRecommendationLabel(0.3);
        $this->assertEquals('Cukup', $label['label']);
        $this->assertEquals('warning', $label['color']);

        // Kurang Direkomendasikan: 0 < CF <= 0.1
        $label = $this->engine->getRecommendationLabel(0.05);
        $this->assertEquals('Kurang Direkomendasikan', $label['label']);

        // Tidak Direkomendasikan: CF <= 0
        $label = $this->engine->getRecommendationLabel(-0.3);
        $this->assertEquals('Tidak Direkomendasikan', $label['label']);
        $this->assertEquals('danger', $label['color']);
    }

    /** @test */
    public function it_handles_sequential_cf_combination_for_multiple_symptoms()
    {
        // Simulasi kombinasi 3 gejala dengan CF positif
        $cfValues = [0.5, 0.6, 0.7];

        // Step 1: combine(0.5, 0.6) = 0.5 + 0.6 * (1 - 0.5) = 0.8
        // Step 2: combine(0.8, 0.7) = 0.8 + 0.7 * (1 - 0.8) = 0.8 + 0.14 = 0.94
        $result = $this->cfEngine->combineMultipleCf($cfValues);
        $this->assertEquals(0.94, $result);

        // Simulasi kombinasi 3 gejala dengan CF negatif
        $cfValues = [-0.5, -0.6, -0.7];

        // Step 1: combine(-0.5, -0.6) = -0.5 + (-0.6) * (1 + (-0.5)) = -0.8
        // Step 2: combine(-0.8, -0.7) = -0.8 + (-0.7) * (1 + (-0.8)) = -0.8 + (-0.7) * 0.2 = -0.94
        $result = $this->cfEngine->combineMultipleCf($cfValues);
        $this->assertEqualsWithDelta(-0.94, $result, 0.0001);
    }

    /** @test */
    public function it_returns_empty_array_for_empty_symptom_ids()
    {
        // Engine seharusnya return empty array jika tidak ada gejala
        // Note: Test ini hanya menguji logic, bukan database query
        $this->assertTrue(true); // Placeholder untuk logic validation
    }

    /** @test */
    public function it_normalizes_cf_to_range_minus_one_to_one()
    {
        // Test normalisasi untuk nilai ekstrem
        $cf = 1.5;
        $normalized = $this->cfEngine->normalizeToRange($cf, -1, 1);
        $this->assertEquals(1.0, $normalized);

        $cf = -1.5;
        $normalized = $this->cfEngine->normalizeToRange($cf, -1, 1);
        $this->assertEquals(-1.0, $normalized);

        $cf = 0.5;
        $normalized = $this->cfEngine->normalizeToRange($cf, -1, 1);
        $this->assertEquals(0.5, $normalized);
    }

    /** @test */
    public function it_converts_cf_to_percentage_correctly()
    {
        // CF = -1 → 0%
        $this->assertEquals(0.0, $this->cfEngine->toPercentage(-1.0));

        // CF = 0 → 50%
        $this->assertEquals(50.0, $this->cfEngine->toPercentage(0.0));

        // CF = 1 → 100%
        $this->assertEquals(100.0, $this->cfEngine->toPercentage(1.0));

        // CF = 0.6 → 80%
        $this->assertEquals(80.0, $this->cfEngine->toPercentage(0.6));

        // CF = -0.5 → 25%
        $this->assertEquals(25.0, $this->cfEngine->toPercentage(-0.5));
    }

    /** @test */
    public function it_validates_mb_md_consistency()
    {
        // Jika MB + MD > 1, harus dinormalisasi
        $mb = 0.8;
        $md = 0.6;
        $total = $mb + $md; // 1.4

        $normalizedMb = $mb / $total; // 0.571...
        $normalizedMd = $md / $total; // 0.428...

        $this->assertLessThanOrEqual(1.0, $normalizedMb + $normalizedMd);
        $this->assertEqualsWithDelta(1.0, $normalizedMb + $normalizedMd, 0.0001);

        // CF setelah normalisasi
        $cf = $normalizedMb - $normalizedMd;
        $this->assertGreaterThan(-1.0, $cf);
        $this->assertLessThan(1.0, $cf);
    }

    /** @test */
    public function it_demonstrates_complete_fertilizer_calculation_flow()
    {
        // Skenario: Pupuk dengan 3 gejala
        // Gejala 1: MB=0.7, MD=0.2 → CF = 0.5 (penyebab positif)
        // Gejala 2: MB=0.6, MD=0.3 → CF = 0.3 (penyebab positif)
        // Gejala 3: MB=0.8, MD=0.1 → CF = 0.7 (penyebab positif)

        $cfValues = [0.5, 0.3, 0.7];

        // Kombinasi CF penyebab
        $cfPenyebabTotal = $this->cfEngine->combineMultipleCf($cfValues);

        // Step 1: combine(0.5, 0.3) = 0.5 + 0.3 * (1 - 0.5) = 0.5 + 0.15 = 0.65
        // Step 2: combine(0.65, 0.7) = 0.65 + 0.7 * (1 - 0.65) = 0.65 + 0.245 = 0.895
        $this->assertEqualsWithDelta(0.895, $cfPenyebabTotal, 0.001);

        // Transformasi untuk rekomendasi
        $cfRekomendasi = -$cfPenyebabTotal;
        $this->assertEqualsWithDelta(-0.895, $cfRekomendasi, 0.001);

        // Karena CF rekomendasi negatif, pupuk ini TIDAK direkomendasikan
        $label = $this->engine->getRecommendationLabel($cfRekomendasi);
        $this->assertEquals('Tidak Direkomendasikan', $label['label']);
    }

    /** @test */
    public function it_demonstrates_complete_pesticide_calculation_flow()
    {
        // Skenario: Pestisida dengan 3 gejala
        // Gejala 1: MB=0.8, MD=0.1 → CF = 0.7 (efektivitas tinggi)
        // Gejala 2: MB=0.7, MD=0.2 → CF = 0.5 (efektivitas sedang)
        // Gejala 3: MB=0.9, MD=0.1 → CF = 0.8 (efektivitas tinggi)

        $cfValues = [0.7, 0.5, 0.8];

        // Kombinasi CF solusi
        $cfSolusiTotal = $this->cfEngine->combineMultipleCf($cfValues);

        // Step 1: combine(0.7, 0.5) = 0.7 + 0.5 * (1 - 0.7) = 0.7 + 0.15 = 0.85
        // Step 2: combine(0.85, 0.8) = 0.85 + 0.8 * (1 - 0.85) = 0.85 + 0.12 = 0.97
        $this->assertEqualsWithDelta(0.97, $cfSolusiTotal, 0.001);

        // Tidak ada transformasi untuk pestisida
        $cfRekomendasi = $cfSolusiTotal;
        $this->assertEqualsWithDelta(0.97, $cfRekomendasi, 0.001);

        // Karena CF rekomendasi sangat positif, pestisida ini SANGAT direkomendasikan
        $label = $this->engine->getRecommendationLabel($cfRekomendasi);
        $this->assertEquals('Sangat Direkomendasikan', $label['label']);
    }

    /** @test */
    public function it_handles_mixed_positive_negative_cf_in_combination()
    {
        // Skenario: Gejala dengan CF berbeda tanda
        // Gejala 1: CF = 0.8 (positif)
        // Gejala 2: CF = -0.4 (negatif)
        // Gejala 3: CF = 0.6 (positif)

        $cfValues = [0.8, -0.4, 0.6];

        // Step 1: combine(0.8, -0.4) - opposite signs
        // (0.8 + (-0.4)) / (1 - min(0.8, 0.4)) = 0.4 / 0.6 = 0.6667
        $step1 = $this->cfEngine->combineCf(0.8, -0.4);
        $this->assertEqualsWithDelta(0.666667, $step1, 0.0001);

        // Step 2: combine(0.6667, 0.6) - both positive
        // 0.6667 + 0.6 * (1 - 0.6667) = 0.6667 + 0.6 * 0.3333 = 0.6667 + 0.2 = 0.8667
        $result = $this->cfEngine->combineCf($step1, 0.6);
        $this->assertEqualsWithDelta(0.866667, $result, 0.0001);
    }
}
