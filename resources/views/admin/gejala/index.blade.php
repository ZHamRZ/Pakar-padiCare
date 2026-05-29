@extends('layouts.app')

@section('title', 'Data Gejala')
@section('page-title', 'Data Gejala')

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-clipboard2-pulse me-2" style="color: var(--primary);"></i>Data Gejala</h4>
        <p>Susun gejala yang akan dipakai untuk identifikasi penyakit tanaman padi.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="stat-pill">
            <span class="stat-dot"></span>
            {{ $gejala->total() }} Gejala
        </div>
        <a href="{{ route('admin.gejala.create') }}" class="btn btn-spk px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i>Tambah Gejala
        </a>
    </div>
</div>

<div class="data-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6><i class="bi bi-list-check"></i>Daftar Gejala Penyakit</h6>
        <span class="data-count">{{ $gejala->total() }} data</span>
    </div>
    
    {{-- Search Bar --}}
    <div class="p-3 border-bottom bg-light-subtle">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari kode atau nama gejala...">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($gejala->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <h6>Belum Ada Data Gejala</h6>
            <p>Tambahkan data gejala untuk membangun basis pengetahuan diagnosis.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table" id="gejalaTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">Gambar</th>
                        <th>Kode & Nama Gejala</th>
                        <th>Terkait Penyakit</th>
                        <th style="width: 120px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gejala as $item)
                    <tr data-kode="{{ strtolower($item->kode) }}" data-nama="{{ strtolower($item->nama_gejala) }}">
                        <td data-label="Gambar">
                            <div class="img-cell">
                                @if($item->gambar_url)
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_gejala }}">
                                @else
                                <div class="img-placeholder"><i class="bi bi-image"></i></div>
                                @endif
                            </div>
                        </td>
                        <td data-label="Kode & Nama">
                            <div class="nama-cell">
                                <div class="nama-main">{{ $item->nama_gejala }}</div>
                                <span class="kode-badge">{{ $item->kode }}</span>
                            </div>
                        </td>
                        <td data-label="Penyakit">
                            <span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; background: var(--warning-50); color: var(--warning-hover);">
                                <i class="bi bi-virus"></i>
                                {{ $item->penyakit_count }} Penyakit
                            </span>
                        </td>
                        <td data-label="Aksi" class="text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.gejala.edit', $item) }}" class="btn btn-action btn-edit" title="Edit Gejala" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.gejala.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-action btn-delete" title="Hapus Gejala" data-bs-toggle="tooltip" onclick="return confirm('Hapus gejala ini?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4"><div class="empty-state"><div class="empty-icon"><i class="bi bi-inbox"></i></div><h6>Belum Ada Data Gejala</h6><p>Tambahkan data gejala untuk membangun basis pengetahuan diagnosis.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($gejala->hasPages())
    <div class="pagination-wrapper">{{ $gejala->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('gejalaTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const kode = row.dataset.kode || '';
                const nama = row.dataset.nama || '';
                const match = kode.includes(query) || nama.includes(query);
                row.style.display = match ? '' : 'none';
            });
        });
    }
});

</script>
@endpush
