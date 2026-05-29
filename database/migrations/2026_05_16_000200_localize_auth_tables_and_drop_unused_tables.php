<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasTable('pengguna')) {
            Schema::rename('users', 'pengguna');
        }

        if (Schema::hasTable('password_reset_tokens') && ! Schema::hasTable('token_reset_sandi')) {
            Schema::rename('password_reset_tokens', 'token_reset_sandi');
        }

        $this->renameRekomendasiUserForeignKey();
        $this->localizeKriteriaJenis();

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('gejala_pestisida');
        Schema::dropIfExists('gejala_pupuk');
    }

    public function down(): void
    {
        if (Schema::hasTable('kriteria') && Schema::hasColumn('kriteria', 'jenis')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE kriteria MODIFY jenis ENUM('benefit', 'cost', 'manfaat', 'biaya') NOT NULL");
            }

            DB::table('kriteria')->where('jenis', 'manfaat')->update(['jenis' => 'benefit']);
            DB::table('kriteria')->where('jenis', 'biaya')->update(['jenis' => 'cost']);

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE kriteria MODIFY jenis ENUM('benefit', 'cost') NOT NULL");
            }
        }

        if (Schema::hasTable('rekomendasi') && Schema::hasColumn('rekomendasi', 'id_pengguna') && ! Schema::hasColumn('rekomendasi', 'id_user')) {
            $this->dropForeignIfExists('rekomendasi', 'id_pengguna');

            Schema::table('rekomendasi', function (Blueprint $table) {
                $table->renameColumn('id_pengguna', 'id_user');
            });

            if (Schema::hasTable('users')) {
                Schema::table('rekomendasi', function (Blueprint $table) {
                    $table->foreign('id_user')->references('id')->on('users')->cascadeOnDelete();
                });
            }
        }

        if (Schema::hasTable('token_reset_sandi') && ! Schema::hasTable('password_reset_tokens')) {
            Schema::rename('token_reset_sandi', 'password_reset_tokens');
        }

        if (Schema::hasTable('pengguna') && ! Schema::hasTable('users')) {
            Schema::rename('pengguna', 'users');
        }
    }

    private function renameRekomendasiUserForeignKey(): void
    {
        if (! Schema::hasTable('rekomendasi') || ! Schema::hasColumn('rekomendasi', 'id_user') || Schema::hasColumn('rekomendasi', 'id_pengguna')) {
            return;
        }

        $this->dropForeignIfExists('rekomendasi', 'id_user');

        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->renameColumn('id_user', 'id_pengguna');
        });

        if (Schema::hasTable('pengguna')) {
            Schema::table('rekomendasi', function (Blueprint $table) {
                $table->foreign('id_pengguna')->references('id')->on('pengguna')->cascadeOnDelete();
            });
        }
    }

    private function localizeKriteriaJenis(): void
    {
        if (! Schema::hasTable('kriteria') || ! Schema::hasColumn('kriteria', 'jenis')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kriteria MODIFY jenis ENUM('benefit', 'cost', 'manfaat', 'biaya') NOT NULL");
        }

        DB::table('kriteria')->where('jenis', 'benefit')->update(['jenis' => 'manfaat']);
        DB::table('kriteria')->where('jenis', 'cost')->update(['jenis' => 'biaya']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kriteria MODIFY jenis ENUM('manfaat', 'biaya') NOT NULL");
        }
    }

    private function dropForeignIfExists(string $table, string $column): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $constraint = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($constraint) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }
};
