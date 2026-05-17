@extends('layouts.app')

@section('title', 'Parameter Prioritas')
@section('page-title', 'Parameter Prioritas')

@section('content')
<!-- Toast Notification -->
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<div class="row g-4">
    <div class="col-12">
        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>
</div>

<form id="kriteriaForm">
    @csrf
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-sliders me-2"></i>Daftar Parameter Preferensi Pengguna</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Kode</th>
                                    <th>Nama Parameter</th>
                                    <th style="width: 120px;">Jenis</th>
                                    <th style="width: 130px;">Faktor Dasar</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kriteria as $item)
                                <tr>
                                    <td><span class="badge text-bg-info">{{ $item->kode }}</span></td>
                                    <td>
                                        <input type="text" name="kriteria[{{ $item->id }}][nama]" 
                                               value="{{ old('kriteria.'.$item->id.'.nama', $item->nama) }}" 
                                               class="form-control form-control-sm" required>
                                    </td>
                                    <td>
                                        <select name="kriteria[{{ $item->id }}][jenis]" class="form-select form-select-sm">
                                            <option value="manfaat" {{ old('kriteria.'.$item->id.'.jenis', $item->jenis) === 'manfaat' ? 'selected' : '' }}>Manfaat</option>
                                            <option value="biaya" {{ old('kriteria.'.$item->id.'.jenis', $item->jenis) === 'biaya' ? 'selected' : '' }}>Biaya</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="1" 
                                               name="kriteria[{{ $item->id }}][bobot]" 
                                               value="{{ old('kriteria.'.$item->id.'.bobot', $item->bobot) }}" 
                                               class="form-control form-control-sm" required>
                                    </td>
                                    <td>
                                        <input type="text" name="kriteria[{{ $item->id }}][keterangan]" 
                                               value="{{ old('kriteria.'.$item->id.'.keterangan', $item->keterangan) }}" 
                                               class="form-control form-control-sm" placeholder="Opsional">
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada parameter prioritas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Action Buttons di Bawah Tabel --}}
                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" id="btnReset" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </button>
                        <button type="button" id="btnSaveAll" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Semua Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header"><i class="bi bi-book me-2"></i>Panduan Parameter Certainty Factor</div>
                <div class="card-body">
                    <div class="display-6 fw-bold text-success">{{ number_format($averageBobot, 2) }}</div>
                    <p class="text-muted">Nilai ini menunjukkan rata-rata faktor dasar yang dipakai sebagai adjustment MB/MD saat pengguna memilih prioritas seimbang, hemat biaya, atau efisiensi tinggi.</p>
                    <div class="alert alert-info">
                        <strong>Metode Certainty Factor (CF):</strong><br>
                        Parameter ini digunakan untuk menyesuaikan nilai MB (Measure of Belief) dan MD (Measure of Disbelief) dalam rumus CF = MB - MD. 
                        Semakin tinggi bobot, semakin besar pengaruh preferensi pengguna terhadap rekomendasi akhir.
                    </div>
                    <hr>
                    <h6 class="fw-bold">Cara Menggunakan:</h6>
                    <ol class="small mb-0">
                        <li>Edit langsung nilai di tabel sebelah kiri</li>
                        <li>Ubah nama, jenis (manfaat/biaya), faktor dasar (0-1), dan keterangan</li>
                        <li>Klik tombol <strong>"Simpan Semua Perubahan"</strong> untuk menyimpan</li>
                        <li>Atau gunakan tombol Edit untuk mengubah satu parameter secara detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSaveAll = document.getElementById('btnSaveAll');
    const btnReset = document.getElementById('btnReset');
    const toastContainer = document.getElementById('toastContainer');
    const form = document.getElementById('kriteriaForm');
    
    // Fungsi menampilkan Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show shadow-sm`;
        toast.style.minWidth = '300px';
        toast.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        toastContainer.appendChild(toast);
        
        // Auto dismiss setelah 5 detik
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 150);
        }, 5000);
    }
    
    // Event handler untuk tombol Reset
    if (btnReset) {
        btnReset.addEventListener('click', function() {
            if (!confirm('Apakah Anda yakin ingin mereset semua perubahan? Data akan dikembalikan ke nilai terakhir yang disimpan.')) {
                return;
            }
            form.reset();
            showToast('Form telah direset ke nilai default', 'info');
        });
    }
    
    // Event handler untuk tombol Simpan Semua
    if (btnSaveAll) {
        btnSaveAll.addEventListener('click', function() {
            // Konfirmasi sebelum menyimpan
            if (!confirm('Apakah Anda yakin ingin menyimpan semua perubahan pada parameter prioritas?')) {
                return;
            }
            
            // Tampilkan loading state
            const originalText = btnSaveAll.innerHTML;
            btnSaveAll.disabled = true;
            btnSaveAll.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';
            
            // Kumpulkan data dari form
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            
            // Ambil semua input dalam tabel
            const inputs = document.querySelectorAll('#kriteriaForm input, #kriteriaForm select');
            inputs.forEach(input => {
                if (input.name) {
                    formData.append(input.name, input.value);
                }
            });
            
            // Kirim via AJAX
            fetch('{{ route("admin.kriteria.updateBulk") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Cek content type response
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Jika bukan JSON, anggap sukses jika status OK
                    if (response.ok) {
                        return { success: true, message: 'Data berhasil disimpan' };
                    }
                    throw new Error('Server mengembalikan response yang tidak valid');
                }
            })
            .then(data => {
                if (data.success || data.message) {
                    showToast('✅ Parameter prioritas Certainty Factor berhasil diperbarui!', 'success');
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('❌ ' + (error.message || 'Terjadi kesalahan saat menyimpan data'), 'danger');
            })
            .finally(() => {
                // Kembalikan tombol ke keadaan semula
                btnSaveAll.disabled = false;
                btnSaveAll.innerHTML = originalText;
            });
        });
    }
});
</script>
@endsection
