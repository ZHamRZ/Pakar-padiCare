<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->decimal('dosis_per_hektar', 10, 2)->nullable()->after('takaran');
            $table->string('satuan_dosis', 20)->default('kg')->after('dosis_per_hektar');
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->decimal('dosis_per_hektar', 10, 2)->nullable()->after('dosis');
            $table->string('satuan_dosis', 20)->default('ml')->after('dosis_per_hektar');
        });
    }

    public function down(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            $table->dropColumn(['dosis_per_hektar', 'satuan_dosis']);
        });

        Schema::table('pestisida', function (Blueprint $table) {
            $table->dropColumn(['dosis_per_hektar', 'satuan_dosis']);
        });
    }
};
