@extends('layouts.app')

@section('title', 'Detail Riwayat')
@section('page-title', 'Detail Riwayat')

@push('styles')
<style>
    .disease-hero {
        background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
        border: 1px solid #bbf7d0;
        border-radius: 20px;
        padding: 1.5rem;
    }
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
    .stat-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border-light);
    }
    .stat-box {
        background: var(--bg-hover);
        border-radius: 12px;
        padding: 14px;
        text-align: center;
    }
    .stat-box .number {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--primary);
    }
    .stat-box .label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-top: 2px;
    }
    .stat-box.amber .number { color: #b45309; }
    .top-pick {
        border: 2px solid var(--primary);
        border-radius: 20px;
        position: relative;
    }
    .top-pick::before {
        content: 'Rekomendasi Utama';
        position: absolute;
        top: -10px;
        left: 16px;
        background: var(--primary);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 2px 12px;
        border-radius: 999px;
        z-index: 2;
    }
    .symptom-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 500;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
    .score-meter {
        height: 6px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        margin: 6px 0 4px;
    }
    .score-meter .fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        transition: width 0.6s ease;
    }
    .score-meter .fill.medium { background: linear-gradient(90deg, #eab308, #ca8a04); }
    .score-meter .fill.low { background: linear-gradient(90deg, #f97316, #ea580c); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.riwayat.index') }}" class="btn btn-light-secondary btn-sm" style="padding: 0.45rem 0.75rem; font-size: 0.8rem;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4><i class="bi bi-eye me-2" style="color: var(--primary);"></i>Detail Laporan</h4>
            <p>Ringkasan hasil diagnosis dan rekomendasi untuk pasien.</p>
        </div>
    </div>
    <div class="stat-pill">
        <span class="stat-dot"></span>
        #{{ $rekomendasi->id }}
    </div>
</div>

<div class="row g-4">
    {{-- Left: Disease Overview --}}
    <div class="col-xl-4 col-lg-4">
        <div class="data-card">
            <div class="card-header">
                <h6><i class="bi bi-clipboard2-pulse"></i>Diagnosis</h6>
            </div>
            <div class="card-body" style="padding: 1.25rem 1.5rem;">
                {{-- Patient --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; color: var(--primary); flex-shrink: 0;">
                        {{ strtoupper(substr($rekomendasi->user->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="info-value" style="font-size: 1rem;">{{ $rekomendasi->user->nama ?? '-' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $rekomendasi->user->username ?? '-' }}</div>
                    </div>
                </div>

                {{-- Disease --}}
                <div class="mb-3">
                    <div class="info-label">Penyakit Terdiagnosis</div>
                    <div class="info-value" style="font-size: 1.1rem;">{{ $rekomendasi->penyakit->nama ?? '-' }}</div>
                    @if($rekomendasi->penyakit->kode ?? null)
                    <span style="display: inline-block; font-size: 0.7rem; padding: 2px 10px; border-radius: 6px; font-weight: 600; background: #f0fdf4; color: #15803d; margin-top: 4px;">
                        {{ $rekomendasi->penyakit->kode }}
                    </span>
                    @endif
                    @if($rekomendasi->penyakit->deskripsi ?? null)
                    <p style="font-size: 0.78rem; color: var(--text-muted); line-height: 1.6; margin-top: 8px; margin-bottom: 0;">
                        {{ $rekomendasi->penyakit->deskripsi }}
                    </p>
                    @endif
                </div>

                {{-- Disease Image --}}
                @if($rekomendasi->penyakit->gambar_url ?? null)
                <div class="mb-3">
                    <img src="{{ $rekomendasi->penyakit->gambar_url }}" alt="{{ $rekomendasi->penyakit->nama }}"
                         style="width: 100%; height: 160px; object-fit: cover; border-radius: 12px; background: #f8fafc;">
                </div>
                @endif

                {{-- Date --}}
                <div class="mb-3">
                    <div class="info-label">Tanggal Diagnosis</div>
                    <div class="info-value">{{ optional($rekomendasi->created_at)->format('d M Y H:i') ?: '-' }}</div>
                </div>

                {{-- Preference --}}
                @if($rekomendasi->preferensi_label)
                <div class="mb-3">
                    <div class="info-label">Strategi Rekomendasi</div>
                    @php
                        $badgeData = App\Support\ExpertSystemPresenter::recommendationBadge($rekomendasi->preferensi_label);
                    @endphp
                    <div class="info-value d-flex align-items-center gap-2">
                        @if(!empty($badgeData['icon']))
                        <i class="bi {{ $badgeData['icon'] }} text-{{ $badgeData['tone'] }}"></i>
                        @endif
                        {{ $badgeData['label'] }}
                    </div>
                </div>
                @endif

                {{-- Status --}}
                <div class="mb-3">
                    <div class="info-label">Status Rekomendasi</div>
                    @php
                        $hasPupuk = $rekomendasi->detailPupuk->isNotEmpty();
                        $hasPestisida = $rekomendasi->detailPestisida->isNotEmpty();
                        $isComplete = $hasPupuk && $hasPestisida;
                    @endphp
                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; {{ $isComplete ? 'background: #f0fdf4; color: #15803d;' : 'background: #fffbeb; color: #b45309;' }}">
                        <i class="bi bi-{{ $isComplete ? 'check-circle-fill' : 'exclamation-circle-fill' }}"></i>
                        {{ $isComplete ? 'Lengkap (pupuk + pestisida)' : 'Tidak Lengkap' }}
                    </span>
                </div>

                {{-- Stats --}}
                <div class="stat-grid">
                    <div class="stat-box">
                        <div class="number">{{ $rekomendasi->detailPupuk->count() }}</div>
                        <div class="label">Pupuk Direkomendasikan</div>
                    </div>
                    <div class="stat-box amber">
                        <div class="number">{{ $rekomendasi->detailPestisida->count() }}</div>
                        <div class="label">Pestisida Direkomendasikan</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gejala Terpilih --}}
        @php
            $gejalaTerpilih = $rekomendasi->preferensi_pengguna['gejala_terpilih'] ?? [];
            $penyakitGejala = $rekomendasi->penyakit->gejala ?? collect();
            $selectedIds = collect($gejalaTerpilih)->pluck('id')->toArray();
            $matchedGejala = $penyakitGejala->filter(fn($g) => in_array($g->id, $selectedIds));
        @endphp
        <div class="data-card mt-4">
            <div class="card-header">
                <h6><i class="bi bi-clipboard2-pulse" style="color: var(--primary);"></i>Gejala Terpilih</h6>
                <span class="data-count">{{ count($gejalaTerpilih) }} gejala</span>
            </div>
            <div class="card-body" style="padding: 1rem 1.25rem;">
                @if(!empty($gejalaTerpilih))
                <div class="d-flex flex-column gap-2">
                    @foreach($gejalaTerpilih as $gejala)
                    @php
                        $isMatched = $matchedGejala->contains('id', $gejala['id'] ?? 0);
                    @endphp
                    <div class="d-flex align-items-center gap-3 p-2" style="border-radius: 10px; background: {{ $isMatched ? '#f0fdf4' : '#fef2f2' }}; border: 1px solid {{ $isMatched ? '#bbf7d0' : '#fecaca' }};">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $isMatched ? '#dcfce7' : '#fee2e2' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi {{ $isMatched ? 'bi-check-lg' : 'bi-x-lg' }}" style="color: {{ $isMatched ? '#15803d' : '#dc2626' }}; font-size: 0.85rem;"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.82rem; color: var(--text-heading);">{{ $gejala['nama_gejala'] ?? $gejala['nama'] ?? '-' }}</div>
                            @if(!empty($gejala['kode']))
                            <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">{{ $gejala['kode'] }}</span>
                            @endif
                            @if(!$isMatched)
                            <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size: 0.65rem;">Tidak terkait penyakit ini</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-3" style="color: var(--text-muted); font-size: 0.82rem;">
                    <i class="bi bi-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 6px;"></i>
                    Data gejala tidak tersedia.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Recommendations --}}
    <div class="col-xl-8 col-lg-8">
        {{-- Pupuk --}}
        @if($rekomendasi->detailPupuk->isNotEmpty())
        @php $sortedPupuk = $rekomendasi->detailPupuk->sortBy('peringkat')->values(); @endphp
        <div class="data-card mb-4">
            <div class="card-header">
                <h6><i class="bi bi-bag-fill" style="color: var(--primary);"></i>Rekomendasi Pupuk</h6>
                <span class="data-count">{{ $sortedPupuk->count() }} item</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($sortedPupuk as $index => $item)
                    <div class="col-xl-6">
                        <div class="{{ $index === 0 ? 'top-pick p-3' : 'p-3' }}" style="border-radius: 20px; border: {{ $index === 0 ? '2px solid var(--primary)' : '1px solid var(--border-light)' }}; background: #fff; height: 100%;">
                            <div class="d-flex gap-3 h-100">
                                {{-- Image --}}
                                @if(optional($item->pupuk)->gambar_url)
                                <img src="{{ $item->pupuk->gambar_url }}" alt="{{ $item->pupuk->nama }}"
                                     style="width: 72px; height: 72px; object-fit: cover; border-radius: 14px; flex-shrink: 0;">
                                @else
                                <div style="width: 72px; height: 72px; border-radius: 14px; background: linear-gradient(135deg, #f0fdf4, #dcfce7); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-bag" style="font-size: 1.5rem; color: var(--primary);"></i>
                                </div>
                                @endif

                                {{-- Content --}}
                                <div class="flex-grow-1 d-flex flex-column">
                                    <div class="d-flex flex-wrap gap-1 mb-1">
                                        @if($item->pupuk->kode ?? null)
                                        <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">{{ $item->pupuk->kode }}</span>
                                        @endif
                                    </div>

                                    <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-heading); line-height: 1.3; margin-bottom: 2px;">
                                        {{ $item->pupuk->nama ?? '-' }}
                                    </div>

                                    <div style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ App\Support\ExpertSystemPresenter::shortDescription(optional($item->pupuk)->fungsi_utama, optional($item->pupuk)->efek_penggunaan) }}
                                    </div>

                                    {{-- Score meter --}}
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.72rem; font-weight: 600;">
                                            <span style="color: var(--text-muted);">Tingkat kecocokan</span>
                                            <span style="color: {{ $index === 0 ? 'var(--primary)' : 'var(--text-muted)' }};">
                                                {{ App\Support\ExpertSystemPresenter::percent($item->nilai_vi) }}
                                            </span>
                                        </div>
                                        @php
                                            $pct = App\Support\ExpertSystemPresenter::rawPercent($item->nilai_vi) / 100;
                                        @endphp
                                        <div class="score-meter">
                                            <div class="fill {{ $pct < 0.5 ? 'low' : ($pct < 0.8 ? 'medium' : '') }}" style="width: {{ $pct * 100 }}%;"></div>
                                        </div>
                                    </div>

                                    {{-- Quick info --}}
                                    <div class="d-flex flex-wrap gap-2 mt-2 pt-2" style="border-top: 1px solid var(--border-light); font-size: 0.72rem; color: var(--text-muted);">
                                        @if($item->pupuk->dosis_per_hektar ?? null)
                                        <span><i class="bi bi-cup-straw me-1"></i>{{ $item->pupuk->dosis_per_hektar }} {{ $item->pupuk->satuan_dosis ?? '' }}/ha</span>
                                        @endif
                                        @if($item->pupuk->frekuensi_aplikasi ?? null)
                                        <span><i class="bi bi-calendar me-1"></i>{{ $item->pupuk->frekuensi_aplikasi }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Pestisida --}}
        @if($rekomendasi->detailPestisida->isNotEmpty())
        @php $sortedPestisida = $rekomendasi->detailPestisida->sortBy('peringkat')->values(); @endphp
        <div class="data-card mb-4">
            <div class="card-header">
                <h6><i class="bi bi-shield-fill-check" style="color: #b45309;"></i>Rekomendasi Pestisida</h6>
                <span class="data-count">{{ $sortedPestisida->count() }} item</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($sortedPestisida as $index => $item)
                    <div class="col-xl-6">
                        <div class="{{ $index === 0 ? 'top-pick p-3' : 'p-3' }}" style="border-radius: 20px; border: {{ $index === 0 ? '2px solid #b45309' : '1px solid var(--border-light)' }}; background: #fff; height: 100%;">
                            @if($index === 0)
                            <style>
                                .top-pick-pest::before {
                                    content: 'Rekomendasi Utama';
                                    position: absolute;
                                    top: -10px;
                                    left: 16px;
                                    background: #b45309;
                                    color: #fff;
                                    font-size: 0.65rem;
                                    font-weight: 700;
                                    text-transform: uppercase;
                                    letter-spacing: 0.04em;
                                    padding: 2px 12px;
                                    border-radius: 999px;
                                    z-index: 2;
                                }
                            </style>
                            <div class="top-pick-pest" style="position: relative;"></div>
                            @endif
                            <div class="d-flex gap-3 h-100">
                                {{-- Image --}}
                                @if(optional($item->pestisida)->gambar_url)
                                <img src="{{ $item->pestisida->gambar_url }}" alt="{{ $item->pestisida->nama }}"
                                     style="width: 72px; height: 72px; object-fit: cover; border-radius: 14px; flex-shrink: 0;">
                                @else
                                <div style="width: 72px; height: 72px; border-radius: 14px; background: linear-gradient(135deg, #fffbeb, #fef3c7); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-shield" style="font-size: 1.5rem; color: #b45309;"></i>
                                </div>
                                @endif

                                {{-- Content --}}
                                <div class="flex-grow-1 d-flex flex-column">
                                    <div class="d-flex flex-wrap gap-1 mb-1">
                                        @if($item->pestisida->kode ?? null)
                                        <span class="badge bg-light text-dark border" style="font-size: 0.68rem;">{{ $item->pestisida->kode }}</span>
                                        @endif
                                    </div>

                                    <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-heading); line-height: 1.3; margin-bottom: 2px;">
                                        {{ $item->pestisida->nama ?? '-' }}
                                    </div>

                                    <div style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ App\Support\ExpertSystemPresenter::shortDescription(optional($item->pestisida)->fungsi, optional($item->pestisida)->efek_penggunaan) }}
                                    </div>

                                    {{-- Score meter --}}
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.72rem; font-weight: 600;">
                                            <span style="color: var(--text-muted);">Tingkat kecocokan</span>
                                            <span style="color: {{ $index === 0 ? '#b45309' : 'var(--text-muted)' }};">
                                                {{ App\Support\ExpertSystemPresenter::percent($item->nilai_vi) }}
                                            </span>
                                        </div>
                                        @php
                                            $pct = App\Support\ExpertSystemPresenter::rawPercent($item->nilai_vi) / 100;
                                        @endphp
                                        <div class="score-meter">
                                            <div class="fill {{ $pct < 0.5 ? 'low' : ($pct < 0.8 ? 'medium' : '') }}" style="width: {{ $pct * 100 }}%;"></div>
                                        </div>
                                    </div>

                                    {{-- Quick info --}}
                                    <div class="d-flex flex-wrap gap-2 mt-2 pt-2" style="border-top: 1px solid var(--border-light); font-size: 0.72rem; color: var(--text-muted);">
                                        @if($item->pestisida->dosis_per_hektar ?? null)
                                        <span><i class="bi bi-cup-straw me-1"></i>{{ $item->pestisida->dosis_per_hektar }} {{ $item->pestisida->satuan_dosis ?? '' }}/ha</span>
                                        @endif
                                        @if($item->pestisida->efek_penggunaan ?? null)
                                        <span><i class="bi bi-shield-check me-1"></i>{{ Str::limit($item->pestisida->efek_penggunaan, 30) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.riwayat.detail', $rekomendasi->id) }}" class="btn btn-spk">
                <i class="bi bi-graph-up me-1"></i>Detail Analisis CF
            </a>
            <a href="{{ route('admin.riwayat.cetak', $rekomendasi->id) }}" class="btn btn-outline-success">
                <i class="bi bi-printer me-1"></i>Cetak Laporan
            </a>
        </div>
    </div>
</div>
@endsection