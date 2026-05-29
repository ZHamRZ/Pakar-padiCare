@extends('layouts.app')

@section('title', 'Data Pupuk')
@section('page-title', 'Data Pupuk')

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-bag-fill me-2" style="color: var(--primary);"></i>Data Pupuk</h4>
        <p>Kelola alternatif pupuk untuk rekomendasi sistem pakar diagnosis padi.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="stat-pill">
            <span class="stat-dot"></span>
            {{ $pupuk->total() }} Pupuk
        </div>
        <a href="{{ route('admin.pupuk.create') }}" class="btn btn-spk px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i>Tambah Pupuk
        </a>
    </div>
</div>

<div class="data-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6><i class="bi bi-list-ul"></i>Daftar Alternatif Pupuk</h6>
        <span class="data-count">{{ $pupuk->total() }} data</span>
    </div>
    
    {{-- Search & Filter Bar --}}
    <div class="p-3 border-bottom bg-light-subtle">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari kode atau nama pupuk...">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($pupuk->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <h6>Belum Ada Data Pupuk</h6>
            <p>Tambahkan data pupuk untuk membangun basis pengetahuan rekomendasi.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table" id="pupukTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">Gambar</th>
                        <th>Kode & Nama</th>
                        <th>Takaran / Ha</th>
                        <th>Harga</th>
                        <th style="width: 120px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupuk as $item)
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
                                <span class="kode-badge">{{ $item->kode }}</span>
                            </div>
                        </td>
                        <td data-label="Takaran/Ha">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-cup-straw text-primary"></i>
                                @php
                                    $dosis = $item->dosis_per_hektar;
                                    $satuan = $item->satuan_dosis ?? '-';
                                    $displayQty = $dosis;
                                    $displayUnit = $satuan;
                                    
                                    if ($satuan === 'g' && $dosis >= 1000) {
                                        $displayQty = $dosis / 1000;
                                        $displayUnit = 'kg';
                                    } elseif ($satuan === 'kg' && $dosis >= 1000) {
                                        $displayQty = $dosis / 1000;
                                        $displayUnit = 'Ton';
                                    } elseif ($satuan === 'ml' && $dosis >= 1000) {
                                        $displayQty = $dosis / 1000;
                                        $displayUnit = 'L';
                                    }
                                @endphp
                                <span class="fw-semibold">{{ $dosis ? number_format($displayQty, 2, ',', '.') . ' ' . $displayUnit : '-' }}</span>
                            </div>
                        </td>
                        <td data-label="Harga">
                            <span class="harga-badge">
                                <i class="bi bi-tag"></i>
                                {{ $item->harga_formatted }}
                            </span>
                        </td>
                        <td data-label="Aksi" class="text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.pupuk.edit', $item) }}" class="btn btn-action btn-edit" title="Edit Pupuk" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.pupuk.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-action btn-delete" title="Hapus Pupuk" data-bs-toggle="tooltip" onclick="return confirm('Hapus data pupuk ini?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state"><div class="empty-icon"><i class="bi bi-inbox"></i></div><h6>Belum Ada Data Pupuk</h6><p>Tambahkan data pupuk untuk membangun basis pengetahuan rekomendasi.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if($pupuk->hasPages())
    <div class="pagination-wrapper">{{ $pupuk->links() }}</div>
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
    const table = document.getElementById('pupukTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const kode = row.dataset.kode || '';
                const nama = row.dataset.nama || '';
                const match = kode.includes(query) || nama.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
        });
    }
});

</script>
@endpush
