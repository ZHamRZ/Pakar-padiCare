@extends('layouts.app')

@section('title', 'Monitoring Riwayat User')
@section('page-title', 'Monitoring Riwayat User')

@push('styles')
<style>
    .monitor-panel {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04);
    }

    .monitor-table th {
        font-size: .74rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: .04em;
        white-space: nowrap;
    }

    .monitor-table td {
        vertical-align: middle;
    }

    .filter-label {
        font-size: .78rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }

    .empty-result {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 28px;
        background: #f8fafc;
        color: #64748b;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="monitor-panel p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
        <div>
            <div class="fw-bold fs-5">Filter Pencarian Riwayat</div>
            <div class="text-muted small">Cari berdasarkan tanggal konsultasi, pengguna, dan status kelengkapan hasil.</div>
        </div>
        <span class="badge bg-{{ $hasFilter ? 'success' : 'secondary' }}">
            {{ $hasFilter ? 'Filter aktif' : 'Menampilkan semua data' }}
        </span>
    </div>

    <form method="GET" action="{{ route('admin.riwayat.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="filter-label" for="tanggal_dari">Tanggal Dari</label>
            <input type="date" id="tanggal_dari" name="tanggal_dari" class="form-control" value="{{ $filters['tanggal_dari'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="filter-label" for="tanggal_sampai">Tanggal Sampai</label>
            <input type="date" id="tanggal_sampai" name="tanggal_sampai" class="form-control" value="{{ $filters['tanggal_sampai'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="filter-label" for="id_pengguna">Pengguna</label>
            <select id="id_pengguna" name="id_pengguna" class="form-select">
                <option value="">Semua pengguna</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) ($filters['id_pengguna'] ?? '') === (string) $user->id)>
                    {{ $user->nama }}{{ $user->username ? ' - ' . $user->username : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="filter-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-search me-1"></i>Cari Riwayat
            </button>
            <a href="{{ route('admin.riwayat.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
            </a>
        </div>
    </form>
</div>

<div class="monitor-panel p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <div class="fw-bold fs-5">Tabel Riwayat Konsultasi</div>
            <div class="text-muted small">{{ $riwayat->total() }} data ditemukan.</div>
        </div>
    </div>

    @if($riwayat->isEmpty())
    <div class="empty-result">
        <div class="fw-semibold mb-1">Data tidak ditemukan</div>
        <div class="small">Ubah kriteria filter atau tampilkan semua data riwayat konsultasi.</div>
    </div>
    @else
    <div class="table-responsive">
        <table class="table monitor-table align-middle">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Diagnosis</th>
                    <th>Rekomendasi Utama</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $item)
                @php
                    $pupukUtama = optional(optional($item->detailPupuk->sortBy('peringkat')->first())->pupuk)->nama;
                    $pestisidaUtama = optional(optional($item->detailPestisida->sortBy('peringkat')->first())->pestisida)->nama;
                    $isComplete = ($item->detail_pupuk_count ?? $item->detailPupuk->count()) > 0
                        && ($item->detail_pestisida_count ?? $item->detailPestisida->count()) > 0;
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold">{{ optional($item->tanggal ?? $item->created_at)->format('d M Y') }}</div>
                        <div class="small text-muted">{{ optional($item->tanggal ?? $item->created_at)->format('H:i') }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->user->nama ?? '-' }}</div>
                        <div class="small text-muted">{{ $item->user->username ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->penyakit->nama ?? '-' }}</div>
                        <div class="small text-muted">{{ $item->penyakit->kode ?? 'Kode tidak tersedia' }}</div>
                    </td>
                    <td>
                        <div class="small"><strong>Pupuk:</strong> {{ $pupukUtama ?: '-' }}</div>
                        <div class="small"><strong>Pestisida:</strong> {{ $pestisidaUtama ?: '-' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $isComplete ? 'success' : 'warning' }}">
                            {{ $isComplete ? 'Lengkap' : 'Tidak Lengkap' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Aksi riwayat">
                            <a href="{{ route('admin.riwayat.show', $item->id) }}" class="btn btn-outline-success" title="Lihat laporan lengkap">
                                <i class="bi bi-file-earmark-text"></i>
                            </a>
                            <a href="{{ route('admin.riwayat.detail', $item->id) }}" class="btn btn-outline-secondary" title="Lihat detail analisis">
                                <i class="bi bi-clipboard-data"></i>
                            </a>
                            <a href="{{ route('admin.riwayat.cetak', $item->id) }}" class="btn btn-outline-dark" title="Cetak atau ekspor laporan">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@if($riwayat->hasPages())
<div class="mt-4">{{ $riwayat->links() }}</div>
@endif
@endsection
