<?php

namespace Tests\Unit\Services;

use App\Services\CertaintyFactorEngine;
use PHPUnit\Framework\TestCase;

class CertaintyFactorEngineTest extends TestCase
{
    private CertaintyFactorEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new CertaintyFactorEngine;
    }

    /** @test */
    public function it_calculates_cf_from_mb_and_md()
    {
        // CF = MB - MD
        $cf = $this->engine->calculateCf(0.8, 0.2);
        $this->assertEquals(0.6, $cf);

        $cf = $this->engine->calculateCf(0.9, 0.1);
        $this->assertEquals(0.8, $cf);

        $cf = $this->engine->calculateCf(0.5, 0.5);
        $this->assertEquals(0.0, $cf);
    }

    /** @test */
    public function it_normalizes_mb_and_md_to_range_0_to_1()
    {
        // Values > 1 should be normalized to 1
        $cf = $this->engine->calculateCf(1.5, 0.2);
        $this->assertEquals(0.8, $cf);

        // Values < 0 should be normalized to 0
        $cf = $this->engine->calculateCf(-0.1, 0.2);
        $this->assertEquals(-0.2, $cf);
    }

    /** @test */
    public function it_combines_two_positive_cf_values()
    {
        // CFcombine = CF1 + CF2 * (1 - CF1)
        $result = $this->engine->combineCf(0.6, 0.7);
        // 0.6 + 0.7 * (1 - 0.6) = 0.6 + 0.7 * 0.4 = 0.6 + 0.28 = 0.88
        $this->assertEquals(0.88, $result);

        $result = $this->engine->combineCf(0.5, 0.5);
        // 0.5 + 0.5 * (1 - 0.5) = 0.5 + 0.25 = 0.75
        $this->assertEquals(0.75, $result);
    }

    /** @test */
    public function it_combines_two_negative_cf_values()
    {
        // CFcombine = CF1 + CF2 * (1 + CF1) for negative values
        $result = $this->engine->combineCf(-0.6, -0.7);
        // -0.6 + (-0.7) * (1 + (-0.6)) = -0.6 + (-0.7) * 0.4 = -0.6 - 0.28 = -0.88
        $this->assertEquals(-0.88, $result);
    }

    /** @test */
    public function it_combines_cf_values_with_opposite_signs()
    {
        // CFcombine = (CF1 + CF2) / (1 - min(|CF1|, |CF2|))
        $result = $this->engine->combineCf(0.8, -0.4);
        // (0.8 + (-0.4)) / (1 - min(0.8, 0.4)) = 0.4 / (1 - 0.4) = 0.4 / 0.6 = 0.666...
        $this->assertEqualsWithDelta(0.666667, $result, 0.0001);

        $result = $this->engine->combineCf(-0.5, 0.6);
        // (-0.5 + 0.6) / (1 - min(0.5, 0.6)) = 0.1 / 0.5 = 0.2
        $this->assertEquals(0.2, $result);
    }

    /** @test */
    public function it_handles_division_by_zero_in_opposite_sign_combination()
    {
        // When min(|CF1|, |CF2|) = 1, denominator becomes 0
        $result = $this->engine->combineCf(1.0, -1.0);
        $this->assertEquals(0.0, $result);
    }

    /** @test */
    public function it_combines_multiple_cf_values_sequentially()
    {
        $cfValues = [0.5, 0.6, 0.7];

        // First combination: 0.5 + 0.6 * (1 - 0.5) = 0.5 + 0.3 = 0.8
        // Second combination: 0.8 + 0.7 * (1 - 0.8) = 0.8 + 0.14 = 0.94
        $result = $this->engine->combineMultipleCf($cfValues);
        $this->assertEquals(0.94, $result);
    }

    /** @test */
    public function it_returns_zero_for_empty_cf_array()
    {
        $result = $this->engine->combineMultipleCf([]);
        $this->assertEquals(0.0, $result);
    }

    /** @test */
    public function it_applies_user_weight_to_cf()
    {
        $baseCf = 0.8;

        // Full weight (1.0) should not change CF
        $result = $this->engine->calculateWithUserWeight($baseCf, 1.0);
        $this->assertEquals(0.8, $result);

        // Half weight (0.5) should reduce CF
        $result = $this->engine->calculateWithUserWeight($baseCf, 0.5);
        $this->assertEquals(0.4, $result);

        // Zero weight should result in zero CF
        $result = $this->engine->calculateWithUserWeight($baseCf, 0.0);
        $this->assertEquals(0.0, $result);
    }

    /** @test */
    public function it_applies_preference_adjustment_based_on_preset()
    {
        $baseCf = 0.7;

        // Hemat preset with low price should boost CF
        $result = $this->engine->applyPreferenceAdjustment(
            $baseCf, 'hemat', 1.0, ['harga' => 30000]
        );
        $this->assertGreaterThan($baseCf, $result);

        // Hemat preset with high price should reduce CF
        $result = $this->engine->applyPreferenceAdjustment(
            $baseCf, 'hemat', 1.0, ['harga' => 150000]
        );
        $this->assertLessThan($baseCf, $result);

        // Efisiensi preset with high base CF should boost
        $result = $this->engine->applyPreferenceAdjustment(
            0.85, 'efisiensi', 1.0, []
        );
        $this->assertGreaterThan(0.85, $result);

        // User confidence < 1.0 should reduce CF
        $result = $this->engine->applyPreferenceAdjustment(
            0.8, 'seimbang', 0.5, []
        );
        $this->assertLessThan(0.8, $result);
    }

    /** @test */
    public function it_converts_cf_to_percentage()
    {
        // CF = -1 → 0%
        $this->assertEquals(0.0, $this->engine->toPercentage(-1.0));

        // CF = 0 → 50%
        $this->assertEquals(50.0, $this->engine->toPercentage(0.0));

        // CF = 1 → 100%
        $this->assertEquals(100.0, $this->engine->toPercentage(1.0));

        // CF = 0.6 → 80%
        $this->assertEquals(80.0, $this->engine->toPercentage(0.6));
    }

    /** @test */
    public function it_converts_percentage_to_cf()
    {
        // 0% → CF = -1
        $this->assertEquals(-1.0, $this->engine->fromPercentage(0.0));

        // 50% → CF = 0
        $this->assertEquals(0.0, $this->engine->fromPercentage(50.0));

        // 100% → CF = 1
        $this->assertEquals(1.0, $this->engine->fromPercentage(100.0));

        // 80% → CF = 0.6
        $this->assertEquals(0.6, $this->engine->fromPercentage(80.0));
    }

    /** @test */
    public function it_interprets_cf_values()
    {
        $interpretation = $this->engine->interpret(0.85);
        $this->assertEquals('Sangat Tinggi', $interpretation['label']);
        $this->assertEquals('success', $interpretation['color']);

        $interpretation = $this->engine->interpret(0.65);
        $this->assertEquals('Tinggi', $interpretation['label']);

        $interpretation = $this->engine->interpret(0.45);
        $this->assertEquals('Sedang', $interpretation['label']);
        $this->assertEquals('warning', $interpretation['color']);

        $interpretation = $this->engine->interpret(0.15);
        $this->assertEquals('Rendah', $interpretation['label']);

        $interpretation = $this->engine->interpret(-0.5);
        $this->assertEquals('Tidak Direkomendasikan', $interpretation['label']);
        $this->assertEquals('danger', $interpretation['color']);
    }

    /** @test */
    public function it_ensures_cf_stays_within_range_minus_one_to_one()
    {
        // Test extreme combinations
        $result = $this->engine->combineCf(0.99, 0.99);
        $this->assertLessThanOrEqual(1.0, $result);
        $this->assertGreaterThanOrEqual(-1.0, $result);

        $result = $this->engine->combineCf(-0.99, -0.99);
        $this->assertLessThanOrEqual(1.0, $result);
        $this->assertGreaterThanOrEqual(-1.0, $result);

        // Test with preference adjustments
        $result = $this->engine->applyPreferenceAdjustment(0.95, 'hemat', 1.0, ['harga' => 10000]);
        $this->assertLessThanOrEqual(1.0, $result);
        $this->assertGreaterThanOrEqual(-1.0, $result);
    }
}
