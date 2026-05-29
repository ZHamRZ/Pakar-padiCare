@extends('layouts.app')

@section('title', 'Data Penyakit')
@section('page-title', 'Data Penyakit')

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-virus me-2" style="color: var(--danger);"></i>Data Penyakit</h4>
        <p>Kelola data penyakit tanaman padi dan hubungkan dengan gejala untuk identifikasi akurat.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="stat-pill">
            <span class="stat-dot" style="background: var(--danger);"></span>
            {{ $penyakit->total() }} Penyakit
        </div>
        <a href="{{ route('admin.penyakit.create') }}" class="btn btn-spk px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i>Tambah Penyakit
        </a>
    </div>
</div>

<div class="data-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6><i class="bi bi-list-ul"></i>Daftar Penyakit Tanaman Padi</h6>
        <span class="data-count">{{ $penyakit->total() }} data</span>
    </div>
    
    {{-- Search Bar --}}
    <div class="p-3 border-bottom bg-light-subtle">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari kode atau nama penyakit...">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($penyakit->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <h6>Belum Ada Data Penyakit</h6>
            <p>Tambahkan data penyakit pertama untuk memulai basis pengetahuan sistem pakar.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table" id="penyakitTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">Gambar</th>
                        <th>Kode & Nama</th>
                        <th>Jumlah Gejala</th>
                        <th>Deskripsi</th>
                        <th style="width: 120px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penyakit as $item)
                    <tr data-kode="{{ strtolower($item->kode) }}" data-nama="{{ strtolower($item->nama) }}">
                        <td data-label="Gambar">
                            <div class="img-cell">
                                @if($item->gambar_url)
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}">
                                @else
                                <div class="img-placeholder"><i class="bi bi-image"></i></div>
                                @endif
                            </div>
                        </td>
                        <td data-label="Kode & Nama">
                            <div class="nama-cell">
                                <div class="nama-main">{{ $item->nama }}</div>
                                <span class="kode-badge" style="background: var(--danger-50); color: var(--danger);">{{ $item->kode }}</span>
                            </div>
                        </td>
                        <td data-label="Gejala">
                            <span style="display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; background: var(--primary-50); color: var(--primary);">
                                <i class="bi bi-clipboard2-check"></i>
                                {{ $item->gejala_count }} Gejala
                            </span>
                        </td>
                        <td data-label="Deskripsi">
                            <div style="max-width: 280px; font-size: 0.82rem; color: var(--text-muted); line-height: 1.5;">{{ \Illuminate\Support\Str::limit($item->deskripsi, 100) ?: '<span style="color: var(--text-muted);">Belum ada deskripsi</span>' }}</div>
                        </td>
                        <td data-label="Aksi" class="text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.penyakit.edit', $item) }}" class="btn btn-action btn-edit" title="Edit Penyakit" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.penyakit.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-action btn-delete" title="Hapus Penyakit" data-bs-toggle="tooltip" onclick="return confirm('Hapus penyakit ini?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="bi bi-inbox"></i></div><h6>Belum Ada Data Penyakit</h6><p>Tambahkan data penyakit pertama untuk memulai basis pengetahuan.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($penyakit->hasPages())
    <div class="pagination-wrapper">{{ $penyakit->links() }}</div>
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
    const table = document.getElementById('penyakitTable');
    
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
