<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria_preferences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('label');
            $table->string('group')->default('general');
            $table->json('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default preferences
        $defaults = [
            [
                'key' => 'budget_threshold_hemat',
                'label' => 'Batas Budget Hemat (Rp/ha)',
                'group' => 'budget',
                'value' => json_encode(['min' => 0, 'max' => 75000]),
                'description' => 'Budget per hektar di bawah nilai ini akan otomatis menggunakan preset Hemat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'budget_threshold_seimbang',
                'label' => 'Batas Budget Seimbang (Rp/ha)',
                'group' => 'budget',
                'value' => json_encode(['min' => 75000, 'max' => 200000]),
                'description' => 'Budget per hektar di rentang ini akan menggunakan preset Seimbang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'budget_threshold_efisiensi',
                'label' => 'Batas Budget Efisiensi (Rp/ha)',
                'group' => 'budget',
                'value' => json_encode(['min' => 200000, 'max' => 9999999]),
                'description' => 'Budget per hektar di atas nilai ini akan menggunakan preset Efisiensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_confidence',
                'label' => 'Default Tingkat Keyakinan',
                'group' => 'confidence',
                'value' => json_encode(['value' => 1.0]),
                'description' => 'Tingkat keyakinan default untuk pengguna baru (0.0 - 1.0)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('kriteria_preferences')->insert($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria_preferences');
    }
};
