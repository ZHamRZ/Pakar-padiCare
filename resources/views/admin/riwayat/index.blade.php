@extends('layouts.app')

@section('title', 'Riwayat Konsultasi')
@section('page-title', 'Riwayat Konsultasi')

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-clock-history me-2" style="color: var(--primary);"></i>Riwayat Konsultasi</h4>
        <p>Pantau semua riwayat diagnosis dan rekomendasi yang telah dilakukan pengguna.</p>
    </div>
    <div class="stat-pill">
        <span class="stat-dot"></span>
        {{ $riwayat->total() }} Total Riwayat
    </div>
</div>

<div class="filter-card">
    <div class="card-header">
        <h6><i class="bi bi-funnel-fill"></i>Filter Pencarian</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.riwayat.index') }}" class="row g-3 align-items-end">
            <div class="col-xl-3 col-md-6">
                <label class="filter-label" for="tanggal_dari">Tanggal Dari</label>
                <input type="date" id="tanggal_dari" name="tanggal_dari" class="form-control filter-input" value="{{ $filters['tanggal_dari'] }}">
            </div>
            <div class="col-xl-3 col-md-6">
                <label class="filter-label" for="tanggal_sampai">Tanggal Sampai</label>
                <input type="date" id="tanggal_sampai" name="tanggal_sampai" class="form-control filter-input" value="{{ $filters['tanggal_sampai'] }}">
            </div>
            <div class="col-xl-3 col-md-6">
                <label class="filter-label" for="id_pengguna">Pengguna</label>
                <select id="id_pengguna" name="id_pengguna" class="form-select filter-input">
                    <option value="">Semua pengguna</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) ($filters['id_pengguna'] ?? '') === (string) $user->id)>
                        {{ $user->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-xl-3 col-md-6">
                <label class="filter-label" for="status">Status</label>
                <select id="status" name="status" class="form-select filter-input">
                    <option value="">Semua status</option>
                    @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 mt-3">
                <button type="submit" class="btn btn-spk btn-filter">
                    <i class="bi bi-search me-1"></i>Cari Riwayat
                </button>
                @if($hasFilter)
                <span class="badge d-flex align-items-center px-3 py-2" style="border-radius: 8px; font-size: 0.8125rem; background: var(--primary-50); color: var(--primary); border: 1px solid var(--primary-200);">
                    <i class="bi bi-check-circle-fill me-1"></i>Filter Aktif
                </span>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="data-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6><i class="bi bi-table"></i>Data Riwayat Konsultasi</h6>
        <span class="data-count">{{ $riwayat->total() }} data ditemukan</span>
    </div>

    @if($riwayat->isEmpty())
    <div class="empty-state">
        <div class="empty-icon"><i class="bi bi-inbox"></i></div>
        <h6>Tidak Ada Data</h6>
        <p>Ubah filter atau tampilkan semua data untuk melihat riwayat konsultasi.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Diagnosis</th>
                    <th>Rekomendasi</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $index => $item)
                @php
                    $pupukUtama = optional(optional($item->detailPupuk->sortBy('peringkat')->first())->pupuk)->nama;
                    $pestisidaUtama = optional(optional($item->detailPestisida->sortBy('peringkat')->first())->pestisida)->nama;
                    $isComplete = ($item->detail_pupuk_count ?? $item->detailPupuk->count()) > 0
                        && ($item->detail_pestisida_count ?? $item->detailPestisida->count()) > 0;
                    $initial = strtoupper(substr($item->user->nama ?? 'U', 0, 1));
                    $waktu = $item->tanggal ?? $item->created_at;
                    $nomor = ($riwayat->currentPage() - 1) * $riwayat->perPage() + $index + 1;
                @endphp
                <tr>
                    <td data-label="No" class="fw-semibold text-muted">{{ $nomor }}</td>
                    <td data-label="Waktu">
                        <div>
                            <div style="font-weight: 700; color: var(--heading); font-size: 0.875rem;">{{ $waktu ? \Carbon\Carbon::parse($waktu)->format('d M Y') : '-' }}</div>
                            <div style="font-size: 0.8rem; color: var(--body-text); margin-top: 2px;"><i class="bi bi-clock me-1"></i>{{ $waktu ? \Carbon\Carbon::parse($waktu)->format('H:i') : '-' }}</div>
                        </div>
                    </td>
                    <td data-label="Pengguna">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            @if($item->user->foto_profil_url)
                                <img src="{{ $item->user->foto_profil_url }}" alt="{{ $item->user->nama }}" style="width: 40px; height: 40px; border-radius: 10px; object-fit: cover; flex-shrink: 0; border: 2px solid var(--border-light);">
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--soft-bg); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; color: var(--primary); flex-shrink: 0; border: 2px solid var(--border-light);">{{ $initial }}</div>
                            @endif
                            <div>
                                <div style="font-weight: 600; color: var(--heading); font-size: 0.875rem;">{{ $item->user->nama ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: var(--body-text);">{{ $item->user->username ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Diagnosis">
                        <div>
                            <div style="font-weight: 600; color: var(--text-heading); font-size: 0.875rem;">{{ $item->penyakit->nama ?? '-' }}</div>
                            <span style="font-size: 0.7rem; padding: 3px 8px; border-radius: 6px; font-weight: 600; background: var(--info-50); color: var(--info); margin-top: 4px; display: inline-block;">{{ $item->penyakit->kode ?? '-' }}</span>
                        </div>
                    </td>
                    <td data-label="Rekomendasi">
                        @if($pupukUtama)
                        <div style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; margin-bottom: 4px; background: var(--primary-50); color: var(--primary);">
                            <i class="bi bi-bag-fill"></i>{{ $pupukUtama }}
                        </div>
                        @else
                        <div style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; color: var(--text-muted); background: var(--bg-hover); margin-bottom: 4px;">
                            <i class="bi bi-dash-circle"></i>Tanpa pupuk
                        </div>
                        @endif
                        @if($pestisidaUtama)
                        <div style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; margin-bottom: 4px; background: var(--warning-50); color: var(--warning-hover);">
                            <i class="bi bi-capsule"></i>{{ $pestisidaUtama }}
                        </div>
                        @else
                        <div style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; color: var(--text-muted); background: var(--bg-hover); margin-bottom: 4px;">
                            <i class="bi bi-dash-circle"></i>Tanpa pestisida
                        </div>
                        @endif
                    </td>
                    <td data-label="Status">
                        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; {{ $isComplete ? 'background: var(--primary-50); color: var(--primary);' : 'background: var(--warning-50); color: var(--warning-hover);' }}">
                            <i class="bi bi-{{ $isComplete ? 'check-circle-fill' : 'exclamation-circle-fill' }}"></i>
                            {{ $isComplete ? 'Lengkap' : 'Tidak Lengkap' }}
                        </span>
                    </td>
                    <td data-label="Aksi" class="text-end">
                        <div class="d-flex flex-column gap-2" style="min-width: 150px;">
                            <a href="{{ route('admin.riwayat.show', $item->id) }}" class="btn btn-spk btn-sm w-100" style="font-size: 0.8rem; padding: 6px 14px;" title="Lihat laporan" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bi bi-eye me-1"></i>Lihat Laporan
                            </a>
                            <a href="{{ route('admin.riwayat.detail', $item->id) }}" class="btn btn-outline-success btn-sm w-100" style="font-size: 0.8rem; padding: 6px 14px;" title="Detail analisis" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bi bi-graph-up me-1"></i>Detail Analisis
                            </a>
                            <a href="{{ route('admin.riwayat.cetak', $item->id) }}" class="btn btn-light-secondary btn-sm w-100" style="font-size: 0.8rem; padding: 6px 14px;" title="Cetak laporan" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="bi bi-printer me-1"></i>Cetak
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($riwayat->hasPages())
    <div class="pagination-wrapper">{{ $riwayat->links() }}</div>
    @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
});
</script>
@endpush
