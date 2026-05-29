<?php

namespace App\Services;

use App\Models\Penyakit;

class DiagnosisService
{
    public function __construct(
        private CertaintyFactorEngine $cfEngine
    ) {}

    /**
     * Identifikasi penyakit berdasarkan gejala yang dipilih menggunakan Certainty Factor
     */
    public function identify(array $symptomIds, array $userWeights = []): array
    {
        $symptomIds = collect($symptomIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $diagnoses = [];
        // Eager load gejala dengan pivot mb/md untuk performa
        $diseases = Penyakit::with(['gejala' => function ($query) {
            $query->withPivot(['mb', 'md']);
        }])->get();

        foreach ($diseases as $disease) {
            $diseaseSymptoms = $disease->gejala;

            if ($diseaseSymptoms->isEmpty()) {
                continue;
            }

            // Filter gejala yang cocok dengan input user
            $matchedSymptoms = $diseaseSymptoms->filter(function ($gejala) use ($symptomIds) {
                return in_array((int) $gejala->id, $symptomIds, true);
            });

            if ($matchedSymptoms->isEmpty()) {
                continue;
            }

            // Hitung CF menggunakan engine
            $cf = $this->cfEngine->calculateDiagnosisCf(
                $matchedSymptoms,
                $diseaseSymptoms,
                $userWeights
            );

            // Konversi ke persentase untuk display
            $confidencePercent = round($this->cfEngine->toPercentage($cf));

            // Interpretasi CF
            $interpretation = $this->cfEngine->interpret($cf);

            $diagnoses[] = [
                'penyakit' => $disease,
                'cocok' => $matchedSymptoms->count(),
                'total' => $diseaseSymptoms->count(),
                'persen' => $confidencePercent,
                'confidence' => $cf,
                'cf_raw' => $cf,
                'interpretation' => $interpretation,
                'matched_gejala_ids' => $matchedSymptoms->pluck('id')->values()->all(),
                'matched_gejala_detail' => $matchedSymptoms->map(function ($gejala) {
                    return [
                        'id' => $gejala->id,
                        'kode' => $gejala->kode,
                        'nama_gejala' => $gejala->nama_gejala,
                        'gambar_url' => $gejala->gambar_url,
                        'mb' => round((float) ($gejala->pivot->mb ?? 0.7), 3),
                        'md' => round((float) ($gejala->pivot->md ?? 0.1), 3),
                        'cf' => round($this->cfEngine->calculateCf(
                            (float) ($gejala->pivot->mb ?? 0.7),
                            (float) ($gejala->pivot->md ?? 0.1)
                        ), 3),
                    ];
                })->values()->all(),
            ];
        }

        // Urutkan berdasarkan confidence tertinggi
        usort($diagnoses, fn ($left, $right) => $right['confidence'] <=> $left['confidence']);

        return [
            'diagnoses' => $diagnoses,
            'summary' => [
                'top_diagnosis' => $diagnoses[0] ?? null,
                'total_matches' => count($diagnoses),
                'selected_symptom_total' => count($symptomIds),
                'method' => 'Certainty Factor',
                'cf_formula' => 'CF = MB - MD, CFcombine = CF1 + CF2 * (1 - CF1)',
            ],
        ];
    }
}
