<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KriteriaPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KriteriaController extends Controller
{
    public function index()
    {
        $preferences = KriteriaPreference::orderBy('group')->orderBy('label')->get();

        $budgetPrefs = $preferences->where('group', 'budget')->mapWithKeys(function ($pref) {
            $val = $pref->value;

            return [
                $pref->key => [
                    'id' => $pref->id,
                    'key' => $pref->key,
                    'label' => $pref->label,
                    'description' => $pref->description,
                    'min' => number_format($val['min'] ?? 0, 0, ',', '.'),
                    'max' => number_format($val['max'] ?? 0, 0, ',', '.'),
                    'min_raw' => $val['min'] ?? 0,
                    'max_raw' => $val['max'] ?? 0,
                ]
            ];
        });

        $confidencePref = $preferences->firstWhere('key', 'default_confidence');

        return view('admin.kriteria.index', compact(
            'budgetPrefs',
            'confidencePref',
        ));
    }

    public function update(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'budget_hemat_max' => 'required|numeric|min:10000|max:500000',
            'budget_seimbang_max' => 'required|numeric|min:50000|max:1000000',
            'default_confidence' => 'required|numeric|min:0|max:1',
        ], [
            'budget_hemat_max.max' => 'Budget Hemat maksimal Rp 500.000/ha.',
            'budget_seimbang_max.max' => 'Budget Seimbang maksimal Rp 1.000.000/ha.',
            'budget_hemat_max.min' => 'Budget Hemat minimal Rp 10.000/ha.',
            'budget_seimbang_max.min' => 'Budget Seimbang minimal Rp 50.000/ha.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $hematMax = (float) $request->budget_hemat_max;
        $seimbangMax = (float) $request->budget_seimbang_max;

        if ($seimbangMax <= $hematMax) {
            return response()->json([
                'success' => false,
                'message' => 'Budget Seimbang harus lebih besar dari Budget Hemat.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            KriteriaPreference::set('budget_threshold_hemat', [
                'min' => 0,
                'max' => $hematMax,
            ]);

            KriteriaPreference::set('budget_threshold_seimbang', [
                'min' => $hematMax,
                'max' => $seimbangMax,
            ]);

            KriteriaPreference::set('budget_threshold_efisiensi', [
                'min' => $seimbangMax,
                'max' => 9999999,
            ]);

            KriteriaPreference::set('default_confidence', [
                'value' => (float) $request->default_confidence,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Preferensi Certainty Factor berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: '.$e->getMessage(),
            ], 500);
        }
    }
}
