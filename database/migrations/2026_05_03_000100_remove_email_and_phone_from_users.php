<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            // Drop unique index first if exists
            if ($this->hasIndex('pengguna', 'pengguna_email_unique')) {
                $table->dropUnique('pengguna_email_unique');
            }
            
        });

        Schema::table('pengguna', function (Blueprint $table) {
            // Drop email-related columns
            if (Schema::hasColumn('pengguna', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('pengguna', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            // Restore email-related columns
            if (!Schema::hasColumn('pengguna', 'email')) {
                $table->string('email')->nullable()->unique();
            }
            if (!Schema::hasColumn('pengguna', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('$table')");

            return collect($indexes)->contains(fn ($index) => $index->name === $indexName);
        }

        $result = DB::select(
            "SELECT 1 FROM information_schema.STATISTICS 
             WHERE table_schema = DATABASE() 
             AND table_name = ? 
             AND index_name = ?",
            [$table, $indexName]
        );
        
        return !empty($result);
    }
};
