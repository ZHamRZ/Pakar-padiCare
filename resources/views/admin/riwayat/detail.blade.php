@extends('layouts.app')

@section('title', 'Detail Analisis')
@section('page-title', 'Detail Analisis')

@push('styles')
<style>
    .info-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .info-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-heading);
    }
    .flow-step {
        display: flex;
        gap: 14px;
        padding: 14px 16px;
        border-radius: 12px;
        background: #fff;
        border: 1px solid var(--border-light);
    }
    .flow-step .step-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .flow-step .step-num.amber { background: #b45309; }
    .flow-step .step-num.blue { background: #2563eb; }
    .score-meter {
        height: 8px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        flex: 1;
    }
    .score-meter .fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        transition: width 0.6s ease;
    }
    .score-meter .fill.medium { background: linear-gradient(90deg, #eab308, #ca8a04); }
    .score-meter .fill.low { background: linear-gradient(90deg, #f97316, #ea580c); }
    .compare-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-light);
    }
    .compare-row:last-child { border-bottom: none; }
    .compare-row .product-info { min-width: 160px; flex-shrink: 0; }
    .compare-row .product-info .name {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-heading);
    }
    .compare-row .product-info .code {
        font-size: 0.7rem;
        color: var(--text-muted);
    }
    .pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .pill-green { background: #f0fdf4; color: #15803d; }
    .pill-amber { background: #fffbeb; color: #b45309; }
    .pill-blue { background: #eff6ff; color: #2563eb; }
    .pill-red { background: #fef2f2; color: #dc2626; }
    .detail-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .winner-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        color: #166534;
        border: 1px solid #bbf7d0;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.riwayat.show', $rekomendasi->id) }}" class="btn btn-light-secondary btn-sm" style="padding: 0.45rem 0.75rem; font-size: 0.8rem;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4><i class="bi bi-graph-up me-2" style="color: var(--primary);"></i>Detail Analisis CF</h4>
            <p>Perhitungan Certainty Factor lengkap untuk setiap alternatif.</p>
        </div>
    </div>
    <div class="stat-pill">
        <span class="stat-dot"></span>
        #{{ $rekomendasi->id }}
    </div>
</div>

{{-- Info & How It Works --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="data-card h-100">
            <div class="card-header">
                <h6><i class="bi bi-clipboard-data"></i>Informasi Analisis</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary); flex-shrink: 0;">
                            {{ strtoupper(substr($rekomendasi->user->nama ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="info-label">Pengguna</div>
                            <div class="info-value">{{ $rekomendasi->user->nama ?? '-' }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="info-label">Penyakit</div>
                        <div class="info-value d-flex align-items-center gap-2">
                            {{ $rekomendasi->penyakit->nama ?? '-' }}
                            @if($rekomendasi->penyakit->kode ?? null)
                            <span class="pill pill-green">{{ $rekomendasi->penyakit->kode }}</span>
                            @endif
                        </div>
                    </div>
                    @if($rekomendasi->preferensi_label)
                    <div>
                        <div class="info-label">Strategi Rekomendasi</div>
                        @php
                            $badgeData = App\Support\ExpertSystemPresenter::recommendationBadge($rekomendasi->preferensi_label);
                        @endphp
                        <div class="info-value d-flex align-items-center gap-2">
                            @if(!empty($badgeData['icon']))
                            <i class="bi {{ $badgeData['icon'] }} text-{{ $badgeData['tone'] }}"></i>
                            @endif
                            {{ $badgeData['label'] }} — menyesuaikan prioritas pengguna pada saat diagnosis
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="data-card h-100">
            <div class="card-header">
                <h6><i class="bi bi-question-circle"></i>Cara Membaca Halaman Ini</h6>
            </div>
            <div class="card-body">
                <div style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.7;">
                    <p>Halaman ini menunjukkan bagaimana sistem menghitung skor menggunakan metode <strong>Certainty Factor (CF)</strong> pada tiga tahap:</p>
                    <ol style="padding-left: 1.2rem; margin: 0;">
                        <li class="mb-1"><strong>Gejala → Penyakit</strong> — CF setiap gejala (MB − MD) dikombinasikan secara sekuensial, lalu dikalikan faktor kelengkapan</li>
                        <li class="mb-1"><strong>Penyakit → Produk</strong> — CF dasar dari relasi penyakit-produk (MB − MD)</li>
                        <li class="mb-1"><strong>Penyesuaian</strong> — perubahan skor berdasarkan strategi pengguna (Hemat/Efisiensi/Seimbang)</li>
                        <li><strong>Skor akhir</strong> — hasil yang menentukan peringkat rekomendasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Penyakit-Gejala CF Calculation --}}
<div class="data-card mb-4">
    <div class="card-header">
        <h6><i class="bi bi-virus" style="color: #2563eb;"></i>Penyakit — Perhitungan CF Gejala</h6>
        <span class="data-count">{{ $gejalaPreview['matchedCount'] }} / {{ $gejalaPreview['totalGejala'] }} gejala cocok</span>
    </div>
    <div class="card-body">
        @if(empty($gejalaPreview['rows']))
        <div class="text-center py-4" style="color: var(--text-muted);">
            <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
            Tidak ada data gejala yang cocok dengan penyakit ini.
        </div>
        @else
        {{-- Winner banner --}}
        @php
            $finalPct = $gejalaPreview['finalCf'];
        @endphp
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 p-3" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 12px; border: 1px solid #93c5fd;">
            <div class="d-flex align-items-center gap-2" style="font-weight: 700; color: #1e40af;">
                <i class="bi bi-check-circle-fill"></i>
                {{ $rekomendasi->penyakit->nama ?? 'Penyakit' }}
            </div>
            <div class="d-flex align-items-center gap-3" style="font-size: 0.85rem;">
                <span style="color: #1e40af; font-weight: 600;">
                    CF Kombinasi: {{ number_format($gejalaPreview['combinedCf'], 4) }}
                </span>
                <i class="bi bi-arrow-right" style="color: #64748b;"></i>
                <span style="font-weight: 700; color: #1e40af;">
                    CF Akhir: {{ number_format($finalPct, 4) }} ({{ number_format($finalPct * 100, 2) }}%)
                </span>
            </div>
        </div>

        {{-- Visual Comparison --}}
        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); margin-bottom: 10px;">
            <i class="bi bi-bar-chart-fill me-1" style="color: #2563eb;"></i>
            Nilai CF per Gejala
        </div>
        <div style="background: var(--bg-hover); border-radius: 12px; padding: 4px 16px; margin-bottom: 20px;">
            @foreach($gejalaPreview['rows'] as $idx => $row)
            @php
                $cfScore = (float) $row['cf'];
                $pct = max(0, min(1, ($cfScore + 1) / 2));
            @endphp
            <div class="compare-row">
                <div class="product-info">
                    <div class="name">{{ $row['nama'] }}</div>
                    <div class="code">{{ $row['kode'] }}</div>
                </div>
                <div class="score-meter">
                    <div class="fill {{ $cfScore < 0.3 ? 'low' : ($cfScore < 0.7 ? 'medium' : '') }}" style="width: {{ $pct * 100 }}%;"></div>
                </div>
                <div style="min-width: 80px; text-align: right; font-weight: 700; font-size: 0.85rem; color: {{ $idx === 0 ? '#2563eb' : 'var(--text-muted)' }};">
                    {{ number_format($cfScore, 4) }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Per-Symptom Calculation Steps --}}
        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); margin-bottom: 12px;">
            <i class="bi bi-list-ol me-1" style="color: #2563eb;"></i>
            Rincian Perhitungan per Gejala
        </div>

        <div class="accordion" id="accordion-gejala">
            @foreach($gejalaPreview['rows'] as $idx => $row)
            @php
                $isFirst = $idx === 0;
                $cfSoFar = 0;
                for ($i = 0; $i <= $idx; $i++) {
                    if ($i === 0) {
                        $cfSoFar = $gejalaPreview['rows'][$i]['cf'];
                    } else {
                        $cf1 = $cfSoFar;
                        $cf2 = $gejalaPreview['rows'][$i]['cf'];
                        if ($cf1 >= 0 && $cf2 >= 0) {
                            $cfSoFar = $cf1 + $cf2 * (1 - $cf1);
                        } elseif ($cf1 <= 0 && $cf2 <= 0) {
                            $cfSoFar = $cf1 + $cf2 * (1 + $cf1);
                        } else {
                            $minAbs = min(abs($cf1), abs($cf2));
                            $cfSoFar = ($cf1 + $cf2) / (1 - $minAbs);
                        }
                    }
                }
            @endphp
            <div class="accordion-item border rounded-4 mb-2" style="overflow: hidden;">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }} fw-semibold" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-gejala-{{ $idx }}">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <span style="min-width: 24px; font-size: 0.85rem; color: var(--text-muted);">#{{ $idx + 1 }}</span>
                            <span style="flex: 1; font-size: 0.88rem;">{{ $row['kode'] }} — {{ $row['nama'] }}</span>
                            <span class="pill pill-blue" style="flex-shrink: 0; font-size: 0.7rem;">
                                CF: {{ number_format($row['cf'], 4) }}
                            </span>
                        </div>
                    </button>
                </h2>
                <div id="collapse-gejala-{{ $idx }}"
                     class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                     data-bs-parent="#accordion-gejala">
                    <div class="accordion-body" style="background: #f8fafc;">
                        <div class="flow-step mb-3">
                            <div class="step-num" style="background: #2563eb;">{{ $idx + 1 }}</div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading);">
                                        Hitung CF Gejala
                                    </span>
                                    <span class="detail-chip pill-blue">{{ $row['kode'] }}</span>
                                </div>

                                <div style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 6px;">
                                    CF = MB − MD = {{ number_format($row['mb'], 4) }} − {{ number_format($row['md'], 4) }}
                                </div>

                                <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 0.82rem;">
                                    <span style="color: #15803d;">
                                        <i class="bi bi-plus-circle me-1"></i>MB: +{{ number_format($row['mb'], 4) }}
                                    </span>
                                    <span style="color: #dc2626;">
                                        <i class="bi bi-dash-circle me-1"></i>MD: −{{ number_format($row['md'], 4) }}
                                    </span>
                                    <span style="font-weight: 700; color: #2563eb;">
                                        CF = {{ number_format($row['cf'], 4) }}
                                    </span>
                                </div>

                                {{-- Combination with previous --}}
                                @if($idx > 0)
                                @php
                                    $prevCf = 0;
                                    for ($i = 0; $i < $idx; $i++) {
                                        if ($i === 0) {
                                            $prevCf = $gejalaPreview['rows'][$i]['cf'];
                                        } else {
                                            $cf1 = $prevCf;
                                            $cf2 = $gejalaPreview['rows'][$i]['cf'];
                                            if ($cf1 >= 0 && $cf2 >= 0) {
                                                $prevCf = $cf1 + $cf2 * (1 - $cf1);
                                            } elseif ($cf1 <= 0 && $cf2 <= 0) {
                                                $prevCf = $cf1 + $cf2 * (1 + $cf1);
                                            } else {
                                                $minAbs = min(abs($cf1), abs($cf2));
                                                $prevCf = ($cf1 + $cf2) / (1 - $minAbs);
                                            }
                                        }
                                    }
                                @endphp
                                <div class="mt-3 p-3" style="background: #fff; border-radius: 10px; border: 1px solid #e2e8f0;">
                                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 6px;">
                                        <strong>Kombinasi sekuensial</strong> dengan {{ $idx }} gejala sebelumnya:
                                    </div>
                                    <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-heading);">
                                        CF<sub>gabungan</sub> = CF<sub>{{ $idx }}</sub> + CF<sub>baru</sub> × (1 − CF<sub>{{ $idx }}</sub>)
                                    </div>
                                    <div style="font-size: 0.82rem; color: #2563eb; font-weight: 700; margin-top: 4px;">
                                        {{ number_format($prevCf, 4) }} + {{ number_format($row['cf'], 4) }} × (1 − {{ number_format($prevCf, 4) }})
                                        = {{ number_format($cfSoFar, 4) }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Summary for this step --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 p-3" style="background: #fff; border-radius: 12px; border: 1px solid var(--border-light);">
                            <div style="font-size: 0.82rem;">
                                <span style="color: var(--text-muted);">Kombinasi setelah gejala ke-{{ $idx + 1 }}:</span>
                                <span style="font-weight: 700; color: #2563eb; margin-left: 6px;">{{ number_format($cfSoFar, 4) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Final summary --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4 p-3" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 12px; border: 1px solid #93c5fd;">
            <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size: 0.82rem;">
                <span style="color: #1e40af;">
                    <strong>CF Kombinasi:</strong> {{ number_format($gejalaPreview['combinedCf'], 4) }}
                </span>
                <span style="color: #64748b;">
                    <strong>Faktor Kelengkapan:</strong> {{ $gejalaPreview['matchedCount'] }}/{{ $gejalaPreview['totalGejala'] }} ({{ number_format($gejalaPreview['completenessFactor'] * 100, 0) }}%)
                </span>
                <i class="bi bi-arrow-right" style="color: #64748b;"></i>
                <span style="font-weight: 700; color: #1e40af;">
                    <strong>CF Akhir:</strong> {{ number_format($gejalaPreview['combinedCf'], 4) }} × (0.7 + 0.3 × {{ number_format($gejalaPreview['completenessFactor'], 4) }})
                    = {{ number_format($gejalaPreview['finalCf'], 4) }}
                </span>
            </div>
            <span class="pill pill-blue" style="font-size: 0.75rem;">
                {{ number_format($gejalaPreview['finalCf'] * 100, 1) }}%
            </span>
        </div>
        @endif
    </div>
</div>

{{-- Analysis Sections --}}
@foreach(['pupuk' => ['label' => 'Pupuk', 'icon' => 'bi-bag-fill', 'color' => 'var(--primary)', 'accent' => 'green', 'pill' => 'pill-green'],
          'pestisida' => ['label' => 'Pestisida', 'icon' => 'bi-shield-fill-check', 'color' => '#b45309', 'accent' => 'amber', 'pill' => 'pill-amber']] as $key => $meta)
@php
    $items = collect($preview[$key] ?? []);
@endphp
<div class="data-card mb-4">
    <div class="card-header">
        <h6><i class="{{ $meta['icon'] }}" style="color: {{ $meta['color'] }};"></i>{{ $meta['label'] }} — Perhitungan CF</h6>
        <span class="data-count">{{ $items->count() }} item</span>
    </div>
    <div class="card-body">
        @if($items->isEmpty())
        <div class="text-center py-4" style="color: var(--text-muted);">
            <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
            Belum ada data {{ strtolower($meta['label']) }} pada riwayat ini.
        </div>
        @else
        {{-- Winner banner --}}
        @php
            $topItem = $items->first();
            $topScore = (float) data_get($topItem, 'vi', 0);
        @endphp
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 p-3" style="background: linear-gradient(135deg, {{ $meta['accent'] === 'green' ? '#f0fdf4, #dcfce7' : '#fffbeb, #fef3c7' }}); border-radius: 12px; border: 1px solid {{ $meta['accent'] === 'green' ? '#bbf7d0' : '#fde68a' }};">
            <div class="winner-badge">
                <i class="bi bi-award-fill"></i>
                Peringkat #1 — {{ data_get($topItem, 'nama', '-') }}
            </div>
            <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem; font-weight: 700; color: {{ $meta['accent'] === 'green' ? '#166534' : '#92400e' }};">
                <i class="bi bi-star-fill"></i>
                Skor CF: {{ number_format($topScore, 4) }} ({{ number_format($topScore * 100, 2) }}%)
            </div>
        </div>

        {{-- Visual Comparison --}}
        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); margin-bottom: 10px;">
            <i class="bi bi-bar-chart-fill me-1" style="color: {{ $meta['color'] }};"></i>
            Perbandingan Skor
        </div>
        <div style="background: var(--bg-hover); border-radius: 12px; padding: 4px 16px; margin-bottom: 20px;">
            @foreach($items as $item)
            @php
                $score = (float) data_get($item, 'vi', 0);
                $pct = min($score, 1);
                $rank = (int) data_get($item, 'peringkat', 0);
            @endphp
            <div class="compare-row">
                <div class="product-info">
                    <div class="name">{{ data_get($item, 'nama', '-') }}</div>
                    <div class="code">{{ data_get($item, 'kode', '-') }}</div>
                </div>
                <div class="score-meter">
                    <div class="fill {{ $pct < 0.5 ? 'low' : ($pct < 0.8 ? 'medium' : '') }}" style="width: {{ $pct * 100 }}%;"></div>
                </div>
                <div style="min-width: 80px; text-align: right; font-weight: 700; font-size: 0.85rem; color: {{ $rank === 1 ? ($meta['accent'] === 'green' ? '#15803d' : '#b45309') : 'var(--text-muted)' }};">
                    {{ number_format($score, 4) }}
                </div>
                @if($rank === 1)
                <span class="pill {{ $meta['pill'] }}" style="flex-shrink: 0;">
                    <i class="bi bi-award-fill"></i> #1
                </span>
                @else
                <span style="min-width: 48px; text-align: center; font-size: 0.75rem; font-weight: 600; color: var(--text-muted);">#{{ $rank }}</span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Per-Product Calculation Steps --}}
        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading); margin-bottom: 12px;">
            <i class="bi bi-list-ol me-1" style="color: {{ $meta['color'] }};"></i>
            Rincian Perhitungan per Produk
        </div>

        <div class="accordion" id="accordion-{{ $key }}">
            @foreach($items as $item)
            @php
                $score = (float) data_get($item, 'vi', 0);
                $detailRows = data_get($item, 'detail', []);
                $detailKeys = array_keys($detailRows);
                $isFirst = $loop->first;
            @endphp
            <div class="accordion-item border rounded-4 mb-2" style="overflow: hidden;">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $isFirst ? '' : 'collapsed' }} fw-semibold" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $key }}-{{ $loop->index }}">
                        <div class="d-flex align-items-center gap-3 w-100">
                            <span style="min-width: 24px; font-size: 0.85rem; color: var(--text-muted);">#{{ $loop->iteration }}</span>
                            <span style="flex: 1; font-size: 0.88rem;">{{ data_get($item, 'nama', '-') }}</span>
                            <span class="pill {{ $meta['pill'] }}" style="flex-shrink: 0; font-size: 0.7rem;">
                                CF: {{ number_format($score, 4) }}
                            </span>
                            @if($loop->first)
                            <span class="pill pill-green" style="flex-shrink: 0; font-size: 0.68rem;">
                                <i class="bi bi-award-fill"></i> Tertinggi
                            </span>
                            @endif
                        </div>
                    </button>
                </h2>
                <div id="collapse-{{ $key }}-{{ $loop->index }}"
                     class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                     data-bs-parent="#accordion-{{ $key }}">
                    <div class="accordion-body" style="background: #f8fafc;">
                        {{-- Flow steps --}}
                        @php
                            $cfAwal = (float) data_get($item, 'cf_meta.cf_awal', data_get($item, 'vi', 0));
                            $cfAkhir = (float) data_get($item, 'cf_meta.cf_akhir', $score);
                            $stepNum = 0;
                        @endphp

                        @foreach($detailRows as $kode => $detail)
                        @php
                            $stepNum++;
                            $jenis = data_get($detail, 'jenis', '');
                            $impact = (float) data_get($detail, 'impact', 0);
                            $catatan = data_get($detail, 'catatan', '');
                            $mb = (float) data_get($detail, 'mb_bonus', 0);
                            $md = (float) data_get($detail, 'md_bonus', 0);
                        @endphp
                        <div class="flow-step mb-3">
                            <div class="step-num {{ $jenis === 'preferensi' ? 'amber' : ($jenis === 'hasil' ? 'blue' : '') }}">
                                {{ $stepNum }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-heading);">
                                        @if($jenis === 'cf')
                                            Hitung CF Dasar
                                        @elseif($jenis === 'preferensi')
                                            Penyesuaian Strategi
                                        @else
                                            Skor Akhir
                                        @endif
                                    </span>
                                    <span class="detail-chip {{ $jenis === 'cf' ? 'pill-green' : ($jenis === 'preferensi' ? 'pill-amber' : 'pill-blue') }}">
                                        {{ $kode }}
                                    </span>
                                </div>

                                <div style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 6px;">
                                    {{ $catatan }}
                                </div>

                                <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 0.82rem;">
                                    @if($mb != 0)
                                    <span style="color: #15803d;">
                                        <i class="bi bi-plus-circle me-1"></i>MB: +{{ number_format($mb, 4) }}
                                    </span>
                                    @endif
                                    @if($md != 0)
                                    <span style="color: #dc2626;">
                                        <i class="bi bi-dash-circle me-1"></i>MD: {{ $md < 0 ? '-' : '+' }}{{ number_format(abs($md), 4) }}
                                    </span>
                                    @endif
                                    <span style="font-weight: 700; color: var(--text-heading);">
                                        Dampak: {{ $impact >= 0 ? '+' : '' }}{{ number_format($impact, 4) }}
                                    </span>
                                </div>

                                {{-- Cumulative score --}}
                                @php
                                    $cumulativeImpact = 0;
                                    foreach(array_slice($detailRows, 0, $stepNum) as $dr) {
                                        $cumulativeImpact += (float) data_get($dr, 'impact', 0);
                                    }
                                    $cumulativeScore = max(0, min(1, $cumulativeImpact));
                                @endphp
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <span style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted);">Akumulasi:</span>
                                    <div class="score-meter" style="height: 5px; max-width: 120px;">
                                        <div class="fill {{ $cumulativeScore < 0.5 ? 'low' : ($cumulativeScore < 0.8 ? 'medium' : '') }}" style="width: {{ $cumulativeScore * 100 }}%;"></div>
                                    </div>
                                    <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-heading);">{{ number_format($cumulativeScore, 4) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- Summary --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 p-3" style="background: #fff; border-radius: 12px; border: 1px solid var(--border-light);">
                            <div class="d-flex align-items-center gap-3" style="font-size: 0.82rem;">
                                <span style="color: var(--text-muted);">
                                    <strong>CF dasar:</strong> {{ number_format($cfAwal, 4) }}
                                </span>
                                <i class="bi bi-arrow-right" style="color: var(--text-muted);"></i>
                                <span style="font-weight: 700; color: {{ $meta['color'] }};">
                                    <strong>CF akhir:</strong> {{ number_format($cfAkhir, 4) }}
                                </span>
                            </div>
                            @php
                                $pctLabel = App\Support\ExpertSystemPresenter::confidenceLabel($cfAkhir);
                                $pctTone = App\Support\ExpertSystemPresenter::confidenceTone($cfAkhir);
                            @endphp
                            <span class="pill {{ $pctTone === 'warning' ? 'pill-red' : ($pctTone === 'info' ? 'pill-blue' : 'pill-green') }}">
                                {{ $pctLabel }} — {{ number_format($cfAkhir * 100, 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endforeach

@endsection