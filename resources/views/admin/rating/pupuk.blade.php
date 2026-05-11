@extends('layouts.app')

@section('title', 'Aturan CF Pupuk')
@section('page-title', 'Aturan CF Pupuk')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Input Rule Certainty Factor Pupuk per Penyakit</span>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleExpandBtn">
            <i class="bi bi-arrows-expand"></i> Tampilkan Semua
        </button>
    </div>
    <div class="card-body">
        @unless($cfReady ?? false)
        <div class="alert alert-warning">
            Tabel rule CF pupuk belum tersedia di database. Jalankan migration terlebih dahulu agar panel ini bisa dipakai.
        </div>
        @endunless
        @if($penyakit->isEmpty() || $pupuk->isEmpty())
        <div class="alert alert-warning mb-0">Lengkapi data penyakit dan pupuk sebelum mengisi aturan CF.</div>
        @elseif(!($cfReady ?? false))
        <div class="alert alert-light border mb-0">Setelah migration dijalankan, form rule CF pupuk akan aktif otomatis.</div>
        @else
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                Pakar mengisi nilai <strong>MB</strong> dan <strong>MD</strong> untuk hubungan antara penyakit dan pupuk. 
                Gunakan filter untuk memudahkan input data.
            </div>
            <div>
                <select id="filterPenyakit" class="form-select form-select-sm" style="min-width: 200px;">
                    <option value="">-- Semua Penyakit --</option>
                    @foreach($penyakit as $p)
                    <option value="{{ $p->id }}">{{ $p->kode }} - {{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <form action="{{ route('admin.rating.pupuk.simpan') }}" method="POST" id="ratingForm">
            @csrf
            @if($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Data aturan CF pupuk belum sesuai.</div>
                <div>Semua nilai MB dan MD wajib diisi, harus numerik, dan berada pada rentang 0-1. Contoh: 0.100 atau 0.900.</div>
            </div>
            @endif
            
            @foreach($penyakit as $index => $penyakitItem)
            <div class="border rounded-4 p-3 mb-3 penyakit-item" data-penyakit-id="{{ $penyakitItem->id }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <span class="badge bg-primary me-2">{{ $penyakitItem->kode }}</span>
                        {{ $penyakitItem->nama }}
                    </h5>
                    <button type="button" class="btn btn-sm btn-link toggle-penyakit" data-target="penyakit-{{ $penyakitItem->id }}">
                        <i class="bi bi-chevron-up"></i>
                    </button>
                </div>
                <div id="penyakit-{{ $penyakitItem->id }}" class="table-responsive">
                    <table class="table table-bordered align-middle table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%;">Pupuk</th>
                                <th style="width: 18%;">MB</th>
                                <th style="width: 18%;">MD</th>
                                <th style="width: 18%;">CF Dasar</th>
                                <th style="width: 22%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pupuk as $pupukItem)
                            @php($key = $penyakitItem->id . '_' . $pupukItem->id)
                            @php($rule = $rules->get($key))
                            @php($mb = old("rules.{$penyakitItem->id}.{$pupukItem->id}.mb", optional($rule)->mb ?? 0.700))
                            @php($md = old("rules.{$penyakitItem->id}.{$pupukItem->id}.md", optional($rule)->md ?? 0.100))
                            @php($mbErrorKey = "rules.{$penyakitItem->id}.{$pupukItem->id}.mb")
                            @php($mdErrorKey = "rules.{$penyakitItem->id}.{$pupukItem->id}.md")
                            <tr>
                                <td>
                                    <strong>{{ $pupukItem->nama }}</strong><br>
                                    <small class="text-muted">{{ $pupukItem->kode }}</small>
                                </td>
                                <td>
                                    <input type="number" min="0" max="1" step="0.001" required inputmode="decimal"
                                        name="rules[{{ $penyakitItem->id }}][{{ $pupukItem->id }}][mb]"
                                        value="{{ $mb }}"
                                        class="form-control form-control-sm cf-input @error($mbErrorKey) is-invalid @enderror"
                                        data-cf="{{ $penyakitItem->id }}-{{ $pupukItem->id }}">
                                    @error($mbErrorKey)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" min="0" max="1" step="0.001" required inputmode="decimal"
                                        name="rules[{{ $penyakitItem->id }}][{{ $pupukItem->id }}][md]"
                                        value="{{ $md }}"
                                        class="form-control form-control-sm cf-input @error($mdErrorKey) is-invalid @enderror"
                                        data-cf="{{ $penyakitItem->id }}-{{ $pupukItem->id }}">
                                    @error($mdErrorKey)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="fw-semibold cf-result" data-cf="{{ $penyakitItem->id }}-{{ $pupukItem->id }}">
                                    {{ number_format((float) $mb - (float) $md, 3) }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-danger reset-btn" 
                                                data-mb="0.700" data-md="0.100"
                                                data-target="{{ $penyakitItem->id }}-{{ $pupukItem->id }}">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary reset-all-btn" data-penyakit-id="{{ $penyakitItem->id }}">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Semua
                        </button>
                        <button type="button" class="btn btn-primary save-all-btn" data-penyakit-id="{{ $penyakitItem->id }}">
                            <i class="bi bi-save-all"></i> Simpan Semua
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
            
        </form>
        @endif
    </div>
</div>

<style>
.penyakit-item { transition: all 0.3s ease; }
.cf-input { text-align: center; }
.cf-result { background-color: #f8f9fa; }
.table-sm th, .table-sm td { padding: 0.5rem; }
.save-all-btn { min-width: 150px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter penyakit
    const filterSelect = document.getElementById('filterPenyakit');
    const penyakitItems = document.querySelectorAll('.penyakit-item');
    
    filterSelect.addEventListener('change', function() {
        const selectedId = this.value;
        penyakitItems.forEach(item => {
            if (!selectedId || item.dataset.penyakitId === selectedId) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Toggle expand/collapse per penyakit
    document.querySelectorAll('.toggle-penyakit').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const content = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.replace('bi-chevron-down', 'bi-chevron-up');
            } else {
                content.style.display = 'none';
                icon.classList.replace('bi-chevron-up', 'bi-chevron-down');
            }
        });
    });
    
    // Auto-calculate CF
    document.querySelectorAll('.cf-input').forEach(input => {
        input.addEventListener('input', function() {
            const cfId = this.dataset.cf;
            const row = this.closest('tr');
            const mbInput = row.querySelector('input[name*="[mb]"]');
            const mdInput = row.querySelector('input[name*="[md]"]');
            const cfResult = document.querySelector(`.cf-result[data-cf="${cfId}"]`);
            
            const mb = parseFloat(mbInput.value) || 0;
            const md = parseFloat(mdInput.value) || 0;
            const cf = mb - md;
            
            cfResult.textContent = cf.toFixed(3);
            cfResult.style.color = cf >= 0 ? '#198754' : '#dc3545';
        });
    });
    
    // Reset button
    document.querySelectorAll('.reset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const row = this.closest('tr');
            const mbInput = row.querySelector('input[name*="[mb]"]');
            const mdInput = row.querySelector('input[name*="[md]"]');
            
            mbInput.value = this.dataset.mb;
            mdInput.value = this.dataset.md;
            mbInput.dispatchEvent(new Event('input'));
        });
    });
    
    // Toggle expand all
    const toggleBtn = document.getElementById('toggleExpandBtn');
    let expanded = true;
    toggleBtn.addEventListener('click', function() {
        const icon = this.querySelector('i');
        const allContents = document.querySelectorAll('[id^="penyakit-"]');
        
        if (expanded) {
            allContents.forEach(content => {
                content.style.display = 'none';
                const btn = document.querySelector(`.toggle-penyakit[data-target="${content.id}"] i`);
                if (btn) btn.classList.replace('bi-chevron-up', 'bi-chevron-down');
            });
            icon.classList.replace('bi-arrows-expand', 'bi-arrows-collapse');
            this.innerHTML = '<i class="bi bi-arrows-collapse"></i> Sembunyikan Semua';
        } else {
            allContents.forEach(content => {
                content.style.display = 'block';
                const btn = document.querySelector(`.toggle-penyakit[data-target="${content.id}"] i`);
                if (btn) btn.classList.replace('bi-chevron-down', 'bi-chevron-up');
            });
            icon.classList.replace('bi-arrows-collapse', 'bi-arrows-expand');
            this.innerHTML = '<i class="bi bi-arrows-expand"></i> Tampilkan Semua';
        }
        expanded = !expanded;
    });
    
    // Count total rules
    function countRules() {
        const inputs = document.querySelectorAll('input[name*="[mb]"]');
        const count = inputs.length;
    }
    countRules();
    
    // Remove save-single-btn event listeners since we removed the buttons
    
    // Reset all button - reset semua nilai untuk satu penyakit
    document.querySelectorAll('.reset-all-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const penyakitId = this.dataset.penyakitId;
            const penyakitItem = document.querySelector(`.penyakit-item[data-penyakit-id="${penyakitId}"]`);
            const rows = penyakitItem.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const mbInput = row.querySelector('input[name*="[mb]"]');
                const mdInput = row.querySelector('input[name*="[md]"]');
                
                mbInput.value = 0.700;
                mdInput.value = 0.100;
                mbInput.dispatchEvent(new Event('input'));
            });
        });
    });
    
    // Save all button - simpan semua untuk satu penyakit
    document.querySelectorAll('.save-all-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const penyakitId = this.dataset.penyakitId;
            const penyakitItem = document.querySelector(`.penyakit-item[data-penyakit-id="${penyakitId}"]`);
            const rows = penyakitItem.querySelectorAll('tbody tr');
            
            const rulesData = {};
            let hasError = false;
            
            rows.forEach(row => {
                const mbInput = row.querySelector('input[name*="[mb]"]');
                const mdInput = row.querySelector('input[name*="[md]"]');
                const pupukId = mbInput.name.match(/rules\[(\d+)\]\[(\d+)\]\[mb\]/)[2];
                
                const mb = parseFloat(mbInput.value);
                const md = parseFloat(mdInput.value);
                
                if (isNaN(mb) || isNaN(md) || mb < 0 || mb > 1 || md < 0 || md > 1) {
                    hasError = true;
                    return;
                }
                
                rulesData[pupukId] = {
                    mb: mb,
                    md: md
                };
            });
            
            if (hasError) {
                showErrorToast('Nilai MB dan MD harus berupa angka antara 0 dan 1');
                return;
            }
            
            // Kirim request AJAX untuk menyimpan semua rule untuk penyakit ini
            fetch("{{ route('admin.rating.pupuk.simpan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    rules: {
                        [penyakitId]: rulesData
                    }
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => {
                        throw new Error(errData.message || 'Network response was not ok');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccessToast('Semua data untuk penyakit ini berhasil disimpan!');
                } else {
                    showErrorToast('Gagal menyimpan data: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorToast(error.message || 'Terjadi kesalahan saat menyimpan data');
            });
        });
    });
});
</script>
@endsection
