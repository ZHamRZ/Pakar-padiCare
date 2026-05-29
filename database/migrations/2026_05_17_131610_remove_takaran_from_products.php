<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->dropColumn('takaran');
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->dropColumn('takaran');
        });
    }

    public function down(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->text('takaran')->nullable()->after('fungsi_utama');
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->text('takaran')->nullable()->after('dosis');
        });
    }
};
