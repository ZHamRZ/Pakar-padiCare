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
            foreach (['pengguna_no_telp_unique', 'users_no_telp_unique'] as $indexName) {
                if ($this->hasIndex('pengguna', $indexName)) {
                    $table->dropUnique($indexName);
                }
            }
        });

        $columns = array_values(array_filter([
            Schema::hasColumn('pengguna', 'no_telp') ? 'no_telp' : null,
            Schema::hasColumn('pengguna', 'phone_verified_at') ? 'phone_verified_at' : null,
            Schema::hasColumn('pengguna', 'login_otp_code') ? 'login_otp_code' : null,
            Schema::hasColumn('pengguna', 'login_otp_expires_at') ? 'login_otp_expires_at' : null,
            Schema::hasColumn('pengguna', 'login_otp_sent_at') ? 'login_otp_sent_at' : null,
        ]));

        if ($columns === []) {
            return;
        }

        Schema::table('pengguna', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
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

        $result = DB::select(
            'SELECT 1 FROM information_schema.STATISTICS WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $indexName]
        );

        return $result !== [];
    }
};
