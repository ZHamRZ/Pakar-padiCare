<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

class CfSchema
{
    public static function hasSymptomCfColumns(): bool
    {
        static $result;

        if ($result === null) {
            $result = Schema::hasTable('penyakit_gejala')
                && Schema::hasColumn('penyakit_gejala', 'mb')
                && Schema::hasColumn('penyakit_gejala', 'md');
        }

        return $result;
    }

    public static function hasPupukRuleTable(): bool
    {
        static $result;

        if ($result === null) {
            $result = Schema::hasTable('penyakit_pupuk');
        }

        return $result;
    }

    public static function hasPestisidaRuleTable(): bool
    {
        static $result;

        if ($result === null) {
            $result = Schema::hasTable('penyakit_pestisida');
        }

        return $result;
    }

    public static function isReady(): bool
    {
        return self::hasSymptomCfColumns()
            && self::hasPupukRuleTable()
            && self::hasPestisidaRuleTable();
    }
}
