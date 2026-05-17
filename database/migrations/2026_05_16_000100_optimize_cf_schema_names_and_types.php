<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->modifyColumnTypes();
        $this->addCfRangeChecks('penyakit_gejala');
        $this->addCfRangeChecks('penyakit_pupuk');
        $this->addCfRangeChecks('penyakit_pestisida');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->dropCheck('penyakit_gejala', 'penyakit_gejala_mb_range_check');
        $this->dropCheck('penyakit_gejala', 'penyakit_gejala_md_range_check');
        $this->dropCheck('penyakit_pupuk', 'penyakit_pupuk_mb_range_check');
        $this->dropCheck('penyakit_pupuk', 'penyakit_pupuk_md_range_check');
        $this->dropCheck('penyakit_pestisida', 'penyakit_pestisida_mb_range_check');
        $this->dropCheck('penyakit_pestisida', 'penyakit_pestisida_md_range_check');

        if (Schema::hasTable('penyakit')) {
            DB::statement('ALTER TABLE penyakit MODIFY nama VARCHAR(100) NOT NULL');
        }

        if (Schema::hasTable('pupuk')) {
            DB::statement('ALTER TABLE pupuk MODIFY nama VARCHAR(100) NOT NULL');
            DB::statement('ALTER TABLE pupuk MODIFY kandungan VARCHAR(200) NULL');
        }

        if (Schema::hasTable('pestisida')) {
            DB::statement('ALTER TABLE pestisida MODIFY nama VARCHAR(100) NOT NULL');
            DB::statement('ALTER TABLE pestisida MODIFY bahan_aktif VARCHAR(200) NULL');
            DB::statement('ALTER TABLE pestisida MODIFY dosis VARCHAR(100) NULL');
        }
    }

    private function modifyColumnTypes(): void
    {
        if (Schema::hasTable('penyakit')) {
            DB::statement('ALTER TABLE penyakit MODIFY nama VARCHAR(120) NOT NULL');
        }

        if (Schema::hasTable('pupuk')) {
            DB::statement('ALTER TABLE pupuk MODIFY nama VARCHAR(120) NOT NULL');
            DB::statement('ALTER TABLE pupuk MODIFY kandungan VARCHAR(160) NULL');
            DB::statement("ALTER TABLE pupuk MODIFY satuan VARCHAR(20) NOT NULL DEFAULT 'kg'");
        }

        if (Schema::hasTable('pestisida')) {
            DB::statement('ALTER TABLE pestisida MODIFY nama VARCHAR(120) NOT NULL');
            DB::statement('ALTER TABLE pestisida MODIFY bahan_aktif VARCHAR(160) NULL');
            DB::statement('ALTER TABLE pestisida MODIFY dosis VARCHAR(80) NULL');
            DB::statement("ALTER TABLE pestisida MODIFY satuan_harga VARCHAR(30) NOT NULL DEFAULT 'per 100ml'");
        }

        foreach (['penyakit_gejala', 'penyakit_pupuk', 'penyakit_pestisida'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            DB::statement("ALTER TABLE {$table} MODIFY mb DECIMAL(4,3) NOT NULL DEFAULT 0.700");
            DB::statement("ALTER TABLE {$table} MODIFY md DECIMAL(4,3) NOT NULL DEFAULT 0.100");
        }
    }

    private function addCfRangeChecks(string $table): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $this->addCheck($table, "{$table}_mb_range_check", 'mb >= 0 AND mb <= 1');
        $this->addCheck($table, "{$table}_md_range_check", 'md >= 0 AND md <= 1');
    }

    private function addCheck(string $table, string $name, string $expression): void
    {
        if ($this->constraintExists($table, $name)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} CHECK ({$expression})");
    }

    private function dropCheck(string $table, string $name): void
    {
        if (!Schema::hasTable($table) || !$this->constraintExists($table, $name)) {
            return;
        }

        DB::statement("ALTER TABLE {$table} DROP CHECK {$name}");
    }

    private function constraintExists(string $table, string $name): bool
    {
        $schema = DB::getDatabaseName();

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $schema)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $name)
            ->exists();
    }
};
