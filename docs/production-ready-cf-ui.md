# Production-Ready Improvement for Full CF

Dokumen ini fokus pada peningkatan kualitas tampilan, struktur kode, dan penyajian hasil untuk aplikasi sistem pakar pertanian berbasis Certainty Factor, tanpa mengubah fitur dan tanpa mengubah metode.

## 1. Prinsip Upgrade

- metode tetap full Certainty Factor
- fitur tetap sama
- output lama tetap kompatibel, terutama `nilai_vi`
- istilah teknis tidak ditampilkan di permukaan
- detail teknis tetap ada di advanced mode

## 2. Struktur Folder yang Lebih Rapi

Saran minimal yang aman untuk project ini:

```text
app/
  Http/
    Controllers/
      User/
        DiagnosisController.php
        RekomendasiController.php
      Admin/
        RiwayatController.php
  Services/
    CertaintyFactorService.php
    DiagnosisService.php
    RecommendationService.php
  Support/
    ExpertSystemPresenter.php

resources/
  views/
    components/
      expert-system/
        confidence-bar.blade.php
        result-card.blade.php
        advanced-details.blade.php
    user/
      diagnosis/
      rekomendasi/
      riwayat/
```

Penjelasan:

- `CertaintyFactorService` tetap menjadi inti perhitungan.
- `DiagnosisService` fokus pada diagnosis penyakit dari gejala.
- `RecommendationService` fokus pada ranking pupuk dan pestisida.
- `ExpertSystemPresenter` khusus untuk format tampilan non-teknis.
- komponen Blade dipakai ulang agar view tidak penuh duplikasi.

## 3. Helper Presentasi Non-Teknis

File contoh sudah ditambahkan:

- [app/Support/ExpertSystemPresenter.php](/mnt/c/Users/zamha/Downloads/Feem/skripsi/program/spk-padi/app/Support/ExpertSystemPresenter.php:1)

Yang disediakan:

- `percent()` untuk format persen
- `confidenceLabel()` untuk label `Rendah`, `Sedang`, `Tinggi`
- `confidenceTone()` untuk warna progress bar
- `recommendationBadge()` untuk badge prioritas
- `lowConfidenceMessage()` untuk feedback saat skor rendah
- `shortDescription()` untuk deskripsi singkat yang rapi di card

Contoh pakai di Blade:

```blade
@php
    use App\Support\ExpertSystemPresenter;

    $score = $item->nilai_vi ?? 0;
@endphp

<span class="badge text-bg-{{ ExpertSystemPresenter::confidenceTone($score) }}">
    {{ ExpertSystemPresenter::confidenceLabel($score) }}
</span>

<div>{{ ExpertSystemPresenter::percent($score) }}</div>
```

## 4. Komponen UI yang Bisa Langsung Dipakai

### Progress bar keyakinan

Komponen:

- [resources/views/components/expert-system/confidence-bar.blade.php](/mnt/c/Users/zamha/Downloads/Feem/skripsi/program/spk-padi/resources/views/components/expert-system/confidence-bar.blade.php:1)

Pemakaian:

```blade
<x-expert-system.confidence-bar :value="$rekomendasi->nilai_vi" />
```

### Card hasil rekomendasi

Komponen:

- [resources/views/components/expert-system/result-card.blade.php](/mnt/c/Users/zamha/Downloads/Feem/skripsi/program/spk-padi/resources/views/components/expert-system/result-card.blade.php:1)

Pemakaian:

```blade
@php
    use App\Support\ExpertSystemPresenter;

    $topPupuk = $rekomendasi->detailPupuk->first();
@endphp

<x-expert-system.result-card
    type="Pupuk"
    :title="$topPupuk->pupuk->nama"
    :code="$topPupuk->pupuk->kode"
    :description="ExpertSystemPresenter::shortDescription(
        $topPupuk->pupuk->fungsi_utama,
        $topPupuk->pupuk->efek_penggunaan
    )"
    :score="$topPupuk->nilai_vi"
    :rank="$topPupuk->peringkat"
    :image-url="$topPupuk->pupuk->gambar_url"
    :badge="data_get($rekomendasi, 'preferensi_label', 'Seimbang')"
/>
```

### Advanced detail

Komponen:

- [resources/views/components/expert-system/advanced-details.blade.php](/mnt/c/Users/zamha/Downloads/Feem/skripsi/program/spk-padi/resources/views/components/expert-system/advanced-details.blade.php:1)

Pemakaian:

```blade
<x-expert-system.advanced-details target="cf-pupuk-{{ $loop->index }}">
    <div class="small text-muted mb-3">
        Detail ini ditujukan untuk admin, peneliti, atau pengguna yang ingin melihat proses analisis sistem pakar.
    </div>

    <pre class="small mb-0">{{ json_encode($preview['pupuk'][$loop->index]['detail'], JSON_PRETTY_PRINT) }}</pre>
</x-expert-system.advanced-details>
```

## 5. Contoh Perbaikan Hasil Diagnosa

Target tampilan:

- penyakit utama tampil sebagai hero card
- keyakinan tampil dalam persen
- label keyakinan tampil jelas
- daftar gejala cocok tampil sebagai chips atau cards kecil
- istilah teknis tidak tampil di layar utama

Contoh Blade:

```blade
@php
    use App\Support\ExpertSystemPresenter;

    $utama = $skorPenyakit[0] ?? null;
    $score = data_get($utama, 'cf', data_get($utama, 'persen', 0) / 100);
    $warning = ExpertSystemPresenter::lowConfidenceMessage($score);
@endphp

@if($utama)
<div class="card border-0 shadow-sm mb-4" style="border-radius: 24px;">
    <div class="card-body p-4 p-lg-5">
        <span class="badge bg-success-subtle text-success border border-success-subtle mb-3">Diagnosis Utama</span>
        <h2 class="fw-bold mb-2">{{ data_get($utama, 'penyakit.nama') }}</h2>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge text-bg-{{ ExpertSystemPresenter::confidenceTone($score) }}">
                {{ ExpertSystemPresenter::confidenceLabel($score) }}
            </span>
            <span class="badge bg-light text-dark border">
                Tingkat keyakinan {{ ExpertSystemPresenter::percent($score) }}
            </span>
        </div>

        <x-expert-system.confidence-bar :value="$score" />

        <div class="mt-4">
            <div class="small text-muted mb-2">Gejala yang paling mendukung hasil ini</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach(data_get($utama, 'detail_cf', []) as $gejala)
                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                    {{ $gejala['kode'] }} - {{ $gejala['nama_gejala'] }}
                </span>
                @endforeach
            </div>
        </div>

        @if($warning)
        <div class="alert alert-warning mt-4 mb-0">
            {{ $warning }}
        </div>
        @endif
    </div>
</div>
@endif
```

Perubahan ini membuat hasil diagnosa lebih terasa seperti dashboard analisis, bukan keluaran debug sistem pakar.

## 6. Contoh Perbaikan Card Rekomendasi

Target tampilan:

- pupuk dan pestisida tampil sebagai card
- skor memakai persen, bukan angka mentah
- badge prioritas tampil visual
- deskripsi singkat lebih mudah dipahami

Contoh:

```blade
<div class="row g-4">
    @foreach($rekomendasi->detailPupuk as $item)
    <div class="col-lg-6">
        <x-expert-system.result-card
            type="Pupuk"
            :title="$item->pupuk->nama"
            :code="$item->pupuk->kode"
            :description="App\Support\ExpertSystemPresenter::shortDescription(
                $item->pupuk->fungsi_utama,
                $item->pupuk->efek_penggunaan
            )"
            :score="$item->nilai_vi"
            :rank="$item->peringkat"
            :image-url="$item->pupuk->gambar_url"
            :badge="$rekomendasi->preferensi_label"
        />
    </div>
    @endforeach
</div>
```

Yang berubah:

- skor CF disajikan sebagai progress bar
- label prioritas terlihat cepat
- card lebih cocok untuk user non-teknis

## 7. Contoh Advanced Mode

Di tampilan utama:

- jangan tampilkan `MB`, `MD`, `CFcombine`
- tampilkan hanya hasil akhir dan narasi sederhana

Di advanced mode:

- detail teknis tetap tersedia dalam collapse
- label tombol dibuat ramah: `Lihat Detail Perhitungan`

Contoh:

```blade
<x-expert-system.advanced-details target="detail-pestisida-{{ $loop->index }}">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>Aturan</th>
                    <th>Nilai Dasar</th>
                    <th>Penyesuaian</th>
                    <th>Skor Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preview['pestisida'][$loop->index]['detail'] as $kode => $detail)
                <tr>
                    <td>{{ $kode }}</td>
                    <td>{{ data_get($detail, 'cf', '-') }}</td>
                    <td>{{ data_get($detail, 'mb_bonus', '-') }}</td>
                    <td>{{ data_get($detail, 'signal', '-') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-expert-system.advanced-details>
```

## 8. Contoh Refactor Controller

Target:

- controller tipis
- perhitungan tetap di service
- formatting tampilan tidak dicampur dengan query

Contoh `DiagnosisController`:

```php
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Services\DiagnosisService;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function __construct(private DiagnosisService $diagnosisService) {}

    public function identifikasi(Request $request)
    {
        $validated = $request->validate([
            'gejala' => 'required|array|min:1',
            'gejala.*' => 'exists:gejala,id',
        ]);

        $hasil = $this->diagnosisService->identify($validated['gejala']);
        $gejalaInput = Gejala::whereIn('id', $validated['gejala'])->orderBy('kode')->get();

        return view('user.diagnosis.hasil', [
            'gejalaInput' => $gejalaInput,
            'skorPenyakit' => $hasil['diagnoses'],
            'diagnosisSummary' => $hasil['summary'],
        ]);
    }
}
```

Contoh `RecommendationService`:

```php
<?php

namespace App\Services;

class RecommendationService
{
    public function previewForDisease(int $diseaseId, array $preferences = []): array
    {
        return app(CertaintyFactorService::class)->preview($diseaseId, $preferences);
    }

    public function saveForUser(int $userId, int $diseaseId, array $preferences = [])
    {
        return app(CertaintyFactorService::class)->hitung($userId, $diseaseId, $preferences);
    }
}
```

Penjelasan:

- `CertaintyFactorService` tetap jadi mesin utama.
- service kecil dipakai sebagai boundary per use-case.
- controller jadi lebih bersih dan mudah diuji.

## 9. Contoh Tampilan Riwayat yang Lebih Informatif

Target:

- tampil per card, bukan tabel penuh
- tampil penyakit utama, tanggal, top pupuk, top pestisida
- tombol `Lihat Detail` lebih jelas

Contoh:

```blade
<div class="row g-4">
    @foreach($riwayat as $item)
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 22px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="small text-muted">Tanggal analisis</div>
                        <div class="fw-semibold">{{ optional($item->created_at)->format('d M Y H:i') }}</div>
                    </div>
                    <span class="badge bg-light text-dark border">{{ $item->preferensi_label ?: 'Analisis Sistem Pakar' }}</span>
                </div>

                <h5 class="fw-bold mb-1">{{ $item->penyakit->nama }}</h5>
                <p class="text-muted small mb-3">Hasil tersimpan ini memuat rekomendasi pupuk dan pestisida terbaik untuk kondisi yang pernah dianalisis.</p>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="small text-muted">Pupuk utama</div>
                            <div class="fw-semibold">{{ optional(optional($item->detailPupuk->first())->pupuk)->nama ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="small text-muted">Pestisida utama</div>
                            <div class="fw-semibold">{{ optional(optional($item->detailPestisida->first())->pestisida)->nama ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('user.rekomendasi.show', $item->id) }}" class="btn btn-outline-success">Lihat Detail</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
```

## 10. Narasi UI yang Perlu Diganti

Gunakan bahasa ini:

- `Perhitungan SAW` -> `Analisis Sistem Pakar`
- `Nilai Vi` -> `Skor Keyakinan`
- `Detail SAW` -> `Detail Analisis`
- `Hasil perhitungan` -> `Hasil analisis`

Contoh narasi yang lebih ramah:

- `Sistem menilai bahwa penyakit ini paling mungkin terjadi berdasarkan gejala yang Anda pilih.`
- `Rekomendasi berikut disusun agar Anda bisa segera mengambil tindakan awal di lapangan.`
- `Jika hasil masih rendah, sebaiknya lakukan pengecekan tambahan atau konsultasi dengan pakar pertanian.`

## 11. Urutan Implementasi yang Disarankan

1. Tambahkan helper presentasi.
2. Tambahkan komponen Blade reusable.
3. Rapikan tampilan hasil diagnosa.
4. Rapikan tampilan hasil rekomendasi dan preview.
5. Ubah riwayat dari tabel ke card layout jika diinginkan.
6. Sembunyikan detail teknis ke advanced mode.
7. Baru setelah itu pecah controller ke `DiagnosisService` dan `RecommendationService`.

## 12. Dampak dari Perubahan Ini

- user non-teknis lebih cepat memahami hasil
- tampilan terasa lebih profesional dan siap produksi
- controller lebih bersih
- komponen UI lebih reusable
- metode CF tetap utuh
- fitur lama tetap aman
