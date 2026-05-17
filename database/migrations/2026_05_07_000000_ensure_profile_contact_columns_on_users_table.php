<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            if (!Schema::hasColumn('pengguna', 'email')) {
                $table->string('email')->nullable()->after('username');
            }

            if (!Schema::hasColumn('pengguna', 'email_verification_token')) {
                $table->string('email_verification_token')->nullable()->after('email');
            }

            if (!Schema::hasColumn('pengguna', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email_verification_token');
            }
        });

        Schema::table('pengguna', function (Blueprint $table) {
            if (Schema::hasColumn('pengguna', 'email') && !$this->hasIndex('pengguna', 'pengguna_email_unique')) {
                $table->unique('email');
            }
        });
    }

    public function down(): void
    {
        //
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('$table')");

            return collect($indexes)->contains(fn ($index) => $index->name === $indexName);
        }

        $result = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$indexName]);

        return $result !== [];
    }
};
