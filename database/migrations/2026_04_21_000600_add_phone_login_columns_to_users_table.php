<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'login_otp_code')) {
                $table->string('login_otp_code')->nullable()->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'login_otp_expires_at')) {
                $table->timestamp('login_otp_expires_at')->nullable()->after('login_otp_code');
            }

            if (!Schema::hasColumn('users', 'login_otp_sent_at')) {
                $table->timestamp('login_otp_sent_at')->nullable()->after('login_otp_expires_at');
            }
        });

        if (!$this->hasUniqueIndex('users', 'users_no_telp_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('no_telp');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->hasUniqueIndex('users', 'users_no_telp_unique')) {
                $table->dropUnique('users_no_telp_unique');
            }

            $columns = array_filter([
                Schema::hasColumn('users', 'phone_verified_at') ? 'phone_verified_at' : null,
                Schema::hasColumn('users', 'login_otp_code') ? 'login_otp_code' : null,
                Schema::hasColumn('users', 'login_otp_expires_at') ? 'login_otp_expires_at' : null,
                Schema::hasColumn('users', 'login_otp_sent_at') ? 'login_otp_sent_at' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    private function hasUniqueIndex(string $table, string $indexName): bool
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('$table')");

            return collect($indexes)->contains(fn ($index) => $index->name === $indexName);
        }

        $result = DB::select('SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$indexName]);

        return $result !== [];
    }
};
