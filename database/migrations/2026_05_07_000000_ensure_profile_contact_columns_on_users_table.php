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
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->after('username');
            }

            if (!Schema::hasColumn('users', 'email_verification_token')) {
                $table->string('email_verification_token')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email_verification_token');
            }

            if (!Schema::hasColumn('users', 'no_telp')) {
                $table->string('no_telp', 30)->nullable()->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('no_telp');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email') && !$this->hasIndex('users', 'users_email_unique')) {
                $table->unique('email');
            }

            if (Schema::hasColumn('users', 'no_telp') && !$this->hasIndex('users', 'users_no_telp_unique')) {
                $table->unique('no_telp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->hasIndex('users', 'users_no_telp_unique')) {
                $table->dropUnique('users_no_telp_unique');
            }
        });
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
