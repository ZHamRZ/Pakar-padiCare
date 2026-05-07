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
        Schema::table('users', function (Blueprint $table) {
            // Drop unique index first if exists
            if ($this->hasIndex('users', 'users_email_unique')) {
                $table->dropUnique('users_email_unique');
            }
            
            // Drop no_telp unique index if exists
            if ($this->hasIndex('users', 'users_no_telp_unique')) {
                $table->dropUnique('users_no_telp_unique');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Drop email-related columns
            if (Schema::hasColumn('users', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            
            // Drop phone-related columns
            if (Schema::hasColumn('users', 'no_telp')) {
                $table->dropColumn('no_telp');
            }
            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $table->dropColumn('phone_verified_at');
            }
            if (Schema::hasColumn('users', 'login_otp_code')) {
                $table->dropColumn('login_otp_code');
            }
            if (Schema::hasColumn('users', 'login_otp_expires_at')) {
                $table->dropColumn('login_otp_expires_at');
            }
            if (Schema::hasColumn('users', 'login_otp_sent_at')) {
                $table->dropColumn('login_otp_sent_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore email-related columns
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            
            // Restore phone-related columns
            if (!Schema::hasColumn('users', 'no_telp')) {
                $table->string('no_telp')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'login_otp_code')) {
                $table->string('login_otp_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'login_otp_expires_at')) {
                $table->timestamp('login_otp_expires_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'login_otp_sent_at')) {
                $table->timestamp('login_otp_sent_at')->nullable();
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
