# Rancangan Migrasi SAW ke Certainty Factor

Dokumen ini menyesuaikan rancangan dengan struktur proyek Laravel pada repo `spk-padi` saat ini.

## 1. Tujuan

Mengganti seluruh logika rekomendasi dan diagnosa yang saat ini masih berbasis SAW menjadi **rule-based expert system dengan metode Certainty Factor (CF)**, tanpa menghilangkan alur fitur yang sudah ada:

- user tetap memilih gejala
- sistem tetap mendiagnosa penyakit
- sistem tetap memberi rekomendasi pupuk dan pestisida
- pilihan prioritas user tetap ada
- custom kriteria tetap ada
- hasil tetap bisa dipreview, disimpan ke riwayat, dan dicetak

## 2. Mapping Fitur Lama ke Pendekatan CF

### Pemilihan gejala

Tetap sama di UI. Yang berubah hanya logika identifikasinya:

- sebelumnya: cocok gejala dihitung berdasarkan jumlah irisan gejala
- sesudah: setiap relasi `penyakit_gejala` punya `mb` dan `md`
- penyakit dihitung memakai kombinasi CF dari gejala yang dipilih user

### Diagnosa penyakit

Gunakan:

```text
CF(rule) = MB - MD
CFcombine = CF1 + CF2 * (1 - CF1)
```

Untuk setiap penyakit:

1. ambil gejala yang dipilih user
2. ambil relasi gejala milik penyakit
3. hitung `cf_rule = mb - md` untuk setiap gejala yang cocok
4. kombinasikan seluruh `cf_rule`
5. penyakit dengan `cf` tertinggi menjadi hasil utama

### Rekomendasi pupuk dan pestisida

Tetap per penyakit, tetapi logikanya berubah:

- sebelumnya: matriks SAW dari `rating_pupuk` dan `rating_pestisida`
- sesudah: relasi `penyakit_pupuk` dan `penyakit_pestisida` menyimpan `mb` dan `md`
- skor akhir alternatif dihitung dari:
  - `cf_dasar` relasi penyakit-alternatif
  - penyesuaian prioritas user
  - penyesuaian custom kriteria

### Pilihan prioritas

Prioritas tidak dihapus. Di CF, prioritas menjadi **rule modifier**.

Contoh:

- `hemat`: alternatif dengan harga lebih rendah mendapat penguatan `mb`
- `efisiensi`: alternatif dengan `cf_dasar` tinggi mendapat penguatan lebih besar
- `seimbang`: penyesuaian kecil dan merata

### Custom kriteria

Tetap ada. Bedanya, custom tidak lagi mengubah bobot SAW, tetapi mengubah kekuatan aturan.

Contoh:

- jika user menaikkan prioritas kriteria biaya, maka alternatif murah mendapat tambahan `mb`
- jika user menaikkan efektivitas, maka alternatif dengan `cf_dasar` tinggi mendapat tambahan `mb`
- jika user menurunkan suatu kriteria, kontribusinya diperkecil

## 3. Desain Basis Data

### Tabel yang dipakai

#### `penyakit_gejala`

Tambahkan:

- `mb decimal(4,3)`
- `md decimal(4,3)`

#### `penyakit_pupuk`

Tabel baru:

- `id`
- `id_penyakit`
- `id_pupuk`
- `mb`
- `md`
- timestamps

#### `penyakit_pestisida`

Tabel baru:

- `id`
- `id_penyakit`
- `id_pestisida`
- `mb`
- `md`
- timestamps

### Catatan kompatibilitas

Kolom `detail_rekomendasi_pupuk.nilai_vi` dan `detail_rekomendasi_pestisida.nilai_vi` boleh tetap dipakai agar view lama tidak perlu dirombak besar. Nilainya cukup diisi dengan **skor CF akhir**, walaupun nama kolomnya masih `nilai_vi`.

## 4. Contoh Migration Laravel

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyakit_gejala', function (Blueprint $table) {
            $table->decimal('mb', 4, 3)->default(1)->after('id_gejala');
            $table->decimal('md', 4, 3)->default(0)->after('mb');
        });

        Schema::create('penyakit_pupuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penyakit')->constrained('penyakit')->cascadeOnDelete();
            $table->foreignId('id_pupuk')->constrained('pupuk')->cascadeOnDelete();
            $table->decimal('mb', 4, 3)->default(0.700);
            $table->decimal('md', 4, 3)->default(0.100);
            $table->timestamps();
            $table->unique(['id_penyakit', 'id_pupuk']);
        });

        Schema::create('penyakit_pestisida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penyakit')->constrained('penyakit')->cascadeOnDelete();
            $table->foreignId('id_pestisida')->constrained('pestisida')->cascadeOnDelete();
            $table->decimal('mb', 4, 3)->default(0.700);
            $table->decimal('md', 4, 3)->default(0.100);
            $table->timestamps();
            $table->unique(['id_penyakit', 'id_pestisida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyakit_pestisida');
        Schema::dropIfExists('penyakit_pupuk');

        Schema::table('penyakit_gejala', function (Blueprint $table) {
            $table->dropColumn(['mb', 'md']);
        });
    }
};
```

## 5. Model yang Perlu Diubah

### `app/Models/Penyakit.php`

```php
public function gejala()
{
    return $this->belongsToMany(
        Gejala::class,
        'penyakit_gejala',
        'id_penyakit',
        'id_gejala'
    )->withPivot(['mb', 'md']);
}

public function pupuk()
{
    return $this->belongsToMany(
        Pupuk::class,
        'penyakit_pupuk',
        'id_penyakit',
        'id_pupuk'
    )->withPivot(['mb', 'md'])->withTimestamps();
}

public function pestisida()
{
    return $this->belongsToMany(
        Pestisida::class,
        'penyakit_pestisida',
        'id_penyakit',
        'id_pestisida'
    )->withPivot(['mb', 'md'])->withTimestamps();
}
```

### Model pivot opsional

Supaya update admin lebih mudah, buat model:

- `App\Models\PenyakitPupuk`
- `App\Models\PenyakitPestisida`

Contoh:

```php
class PenyakitPupuk extends Model
{
    protected $table = 'penyakit_pupuk';

    protected $fillable = [
        'id_penyakit',
        'id_pupuk',
        'mb',
        'md',
    ];
}
```

## 6. Service Inti Certainty Factor

Disarankan buat service baru:

- `app/Services/CertaintyFactorService.php`

Service ini menggantikan peran `CertaintyFactorService`.

### Struktur service

```php
<?php

namespace App\Services;

use App\Models\DetailRekomendasiPestisida;
use App\Models\DetailRekomendasiPupuk;
use App\Models\Gejala;
use App\Models\Kriteria;
use App\Models\Penyakit;
use App\Models\Rekomendasi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CertaintyFactorService
{
    public function diagnose(array $gejalaDipilih): array
    {
        $penyakitList = Penyakit::with(['gejala' => fn ($query) => $query->orderBy('kode')])->get();
        $hasil = [];

        foreach ($penyakitList as $penyakit) {
            $matchedRules = $penyakit->gejala
                ->filter(fn ($gejala) => in_array((int) $gejala->id, $gejalaDipilih, true))
                ->map(function ($gejala) {
                    $mb = (float) ($gejala->pivot->mb ?? 0);
                    $md = (float) ($gejala->pivot->md ?? 0);
                    $cf = $this->calculateCf($mb, $md);

                    return [
                        'gejala_id' => $gejala->id,
                        'kode' => $gejala->kode,
                        'nama_gejala' => $gejala->nama_gejala,
                        'mb' => $mb,
                        'md' => $md,
                        'cf' => $cf,
                    ];
                })
                ->filter(fn (array $rule) => $rule['cf'] > 0)
                ->sortByDesc('cf')
                ->values();

            if ($matchedRules->isEmpty()) {
                continue;
            }

            $cfGabungan = $this->combineRules($matchedRules->pluck('cf')->all());

            $hasil[] = [
                'penyakit' => $penyakit,
                'cf' => round($cfGabungan, 6),
                'persen' => round($cfGabungan * 100, 2),
                'cocok' => $matchedRules->count(),
                'total' => $penyakit->gejala->count(),
                'detail_cf' => $matchedRules->all(),
            ];
        }

        usort($hasil, fn ($a, $b) => $b['cf'] <=> $a['cf']);

        return $hasil;
    }

    public function hitung(int $idUser, int $idPenyakit, array $preferensi = []): Rekomendasi
    {
        $preview = $this->preview($idPenyakit, $preferensi);
        $preferensiSnapshot = $this->buildPreferenceSnapshot($preview['kriteria'], $preferensi);

        return DB::transaction(function () use ($idUser, $idPenyakit, $preview, $preferensiSnapshot) {
            $rekomendasi = Rekomendasi::create([
                'id_user' => $idUser,
                'id_penyakit' => $idPenyakit,
                'tanggal' => now(),
                'preferensi_label' => $preferensiSnapshot['preset_label'],
                'preferensi_pengguna' => $preferensiSnapshot,
            ]);

            foreach ($preview['pupuk'] as $item) {
                DetailRekomendasiPupuk::create([
                    'id_rekomendasi' => $rekomendasi->id,
                    'id_pupuk' => $item['id'],
                    'nilai_vi' => $item['vi'],
                    'peringkat' => $item['peringkat'],
                ]);
            }

            foreach ($preview['pestisida'] as $item) {
                DetailRekomendasiPestisida::create([
                    'id_rekomendasi' => $rekomendasi->id,
                    'id_pestisida' => $item['id'],
                    'nilai_vi' => $item['vi'],
                    'peringkat' => $item['peringkat'],
                ]);
            }

            return $rekomendasi;
        });
    }

    public function preview(int $idPenyakit, array $preferensi = []): array
    {
        $kriteria = $this->buildCriteriaProfile(Kriteria::orderBy('kode')->get(), $preferensi);
        $penyakit = Penyakit::with(['pupuk', 'pestisida'])->findOrFail($idPenyakit);

        return [
            'rumus' => [
                'cf_rule' => 'CF = MB - MD',
                'cf_combine' => 'CFcombine = CF1 + CF2 * (1 - CF1)',
                'prioritas' => 'CF akhir = CF dasar + penyesuaian prioritas user',
            ],
            'kriteria' => $kriteria,
            'pupuk' => $this->rankAlternatives($penyakit->pupuk, 'pupuk', $kriteria, $preferensi),
            'pestisida' => $this->rankAlternatives($penyakit->pestisida, 'pestisida', $kriteria, $preferensi),
        ];
    }

    public function getPreferencePresets(): array
    {
        return [
            'seimbang' => [
                'label' => 'Seimbang',
                'description' => 'Menjaga keseimbangan biaya, efektivitas, dan faktor lapangan lainnya.',
            ],
            'hemat' => [
                'label' => 'Hemat Biaya',
                'description' => 'Memperkuat rekomendasi dengan harga lebih rendah.',
            ],
            'efisiensi' => [
                'label' => 'Efisiensi Penanganan',
                'description' => 'Memperkuat rekomendasi dengan keyakinan pakar tertinggi.',
            ],
            'custom' => [
                'label' => 'Atur Sendiri',
                'description' => 'Nilai MB dan MD akhir dipengaruhi preferensi per kriteria dari pengguna.',
            ],
        ];
    }

    public function calculateCf(float $mb, float $md): float
    {
        return round(max(-1, min(1, $mb - $md)), 6);
    }

    public function combineCf(float $cf1, float $cf2): float
    {
        return round($cf1 + ($cf2 * (1 - $cf1)), 6);
    }

    private function combineRules(array $rules): float
    {
        $combined = 0.0;

        foreach ($rules as $ruleCf) {
            $combined = $combined === 0.0
                ? (float) $ruleCf
                : $this->combineCf($combined, (float) $ruleCf);
        }

        return round($combined, 6);
    }

    private function rankAlternatives(Collection $alternatives, string $type, Collection $kriteria, array $preferensi): array
    {
        if ($alternatives->isEmpty()) {
            throw new RuntimeException('Relasi alternatif belum diisi oleh pakar.');
        }

        $items = $alternatives->map(function ($item) use ($type, $kriteria, $preferensi) {
            $baseMb = (float) $item->pivot->mb;
            $baseMd = (float) $item->pivot->md;
            $baseCf = $this->calculateCf($baseMb, $baseMd);

            [$adjustedMb, $adjustedMd, $detail] = $this->applyPriorityRules(
                item: $item,
                type: $type,
                baseMb: $baseMb,
                baseMd: $baseMd,
                baseCf: $baseCf,
                kriteria: $kriteria,
                preferensi: $preferensi,
            );

            $finalCf = $this->calculateCf($adjustedMb, $adjustedMd);

            return [
                'id' => $item->id,
                'kode' => $item->kode,
                'nama' => $item->nama,
                'vi' => $finalCf,
                'meta' => [
                    'gambar_url' => $item->gambar_url ?? null,
                    'kandungan' => $type === 'pupuk' ? $item->kandungan : null,
                    'kandungan_detail' => $item->kandungan_detail ?? null,
                    'bahan_aktif' => $type === 'pestisida' ? ($item->bahan_aktif ?? null) : null,
                    'fungsi_utama' => $type === 'pupuk' ? ($item->fungsi_utama ?? null) : null,
                    'fungsi' => $type === 'pestisida' ? ($item->fungsi ?? null) : null,
                    'takaran' => $item->takaran ?? null,
                    'dosis' => $type === 'pestisida' ? ($item->dosis ?? null) : null,
                    'efek_penggunaan' => $item->efek_penggunaan ?? null,
                    'cara_aplikasi' => $item->cara_aplikasi ?? null,
                    'jadwal_umur_aplikasi' => $item->jadwal_umur_aplikasi ?? null,
                    'frekuensi_aplikasi' => $item->frekuensi_aplikasi ?? null,
                ],
                'detail' => $detail,
            ];
        })->sortByDesc('vi')->values();

        return $items->map(function (array $item, int $index) {
            $item['peringkat'] = $index + 1;
            return $item;
        })->all();
    }

    private function applyPriorityRules(
        object $item,
        string $type,
        float $baseMb,
        float $baseMd,
        float $baseCf,
        Collection $kriteria,
        array $preferensi,
    ): array {
        $preset = $preferensi['preset'] ?? 'seimbang';
        $harga = $type === 'pupuk'
            ? (float) ($item->harga_per_kg ?? 0)
            : (float) ($item->harga ?? 0);

        $adjustedMb = $baseMb;
        $adjustedMd = $baseMd;
        $detail = [
            'base' => [
                'mb' => $baseMb,
                'md' => $baseMd,
                'cf' => $baseCf,
            ],
        ];

        if ($preset === 'hemat' && $harga > 0) {
            $bonus = $harga <= 50000 ? 0.12 : ($harga <= 100000 ? 0.07 : 0.03);
            $adjustedMb += $bonus;
            $detail['preset'] = [
                'label' => 'Hemat',
                'mb_bonus' => $bonus,
                'alasan' => 'Alternatif dengan harga lebih rendah diperkuat.',
            ];
        }

        if ($preset === 'efisiensi') {
            $bonus = $baseCf >= 0.8 ? 0.10 : ($baseCf >= 0.6 ? 0.06 : 0.02);
            $adjustedMb += $bonus;
            $detail['preset'] = [
                'label' => 'Efisiensi',
                'mb_bonus' => $bonus,
                'alasan' => 'Alternatif dengan keyakinan pakar lebih tinggi diperkuat.',
            ];
        }

        if ($preset === 'seimbang') {
            $adjustedMb += 0.03;
            $detail['preset'] = [
                'label' => 'Seimbang',
                'mb_bonus' => 0.03,
                'alasan' => 'Penyesuaian moderat tanpa dominasi satu faktor.',
            ];
        }

        foreach ($kriteria as $kriteriaItem) {
            $signal = $this->resolveCriterionSignal($kriteriaItem, $item, $type, $baseCf);
            $intensity = ((int) $kriteriaItem['preferensi_user']) / 5;
            $deltaMb = round($signal * 0.08 * $intensity, 6);
            $deltaMd = round((1 - $signal) * 0.03 * $intensity, 6);

            $adjustedMb += $deltaMb;
            $adjustedMd += $deltaMd;

            $detail['kriteria'][$kriteriaItem['kode']] = [
                'kriteria' => $kriteriaItem['nama'],
                'preferensi_user' => $kriteriaItem['preferensi_user'],
                'signal' => round($signal, 4),
                'mb_bonus' => $deltaMb,
                'md_bonus' => $deltaMd,
            ];
        }

        return [
            min(1, round($adjustedMb, 6)),
            min(1, round($adjustedMd, 6)),
            $detail,
        ];
    }

    private function resolveCriterionSignal(array $kriteriaItem, object $item, string $type, float $baseCf): float
    {
        $nama = strtolower($kriteriaItem['nama'] . ' ' . ($kriteriaItem['keterangan'] ?? ''));

        if (str_contains($nama, 'harga') || str_contains($nama, 'biaya')) {
            $harga = $type === 'pupuk'
                ? (float) ($item->harga_per_kg ?? 0)
                : (float) ($item->harga ?? 0);

            if ($harga <= 0) {
                return 0.5;
            }

            return $harga <= 50000 ? 1.0 : ($harga <= 100000 ? 0.7 : 0.4);
        }

        if (
            str_contains($nama, 'efektif') ||
            str_contains($nama, 'efisiensi') ||
            str_contains($nama, 'manfaat') ||
            str_contains($nama, 'hasil')
        ) {
            return max(0.1, min(1, $baseCf));
        }

        if (
            str_contains($nama, 'aman') ||
            str_contains($nama, 'dampak') ||
            str_contains($nama, 'residu') ||
            str_contains($nama, 'risiko')
        ) {
            return 0.6;
        }

        return 0.5;
    }

    private function buildCriteriaProfile(Collection $kriteria, array $preferensi = []): Collection
    {
        $custom = collect($preferensi['kriteria'] ?? []);

        return $kriteria->map(function ($item) use ($custom) {
            return [
                'id' => $item->id,
                'kode' => $item->kode,
                'nama' => $item->nama,
                'jenis' => $item->jenis,
                'keterangan' => $item->keterangan,
                'preferensi_user' => (int) ($custom[$item->id] ?? 3),
            ];
        })->values();
    }

    private function buildPreferenceSnapshot(Collection $kriteria, array $preferensi): array
    {
        $presets = $this->getPreferencePresets();
        $preset = $preferensi['preset'] ?? 'seimbang';

        return [
            'preset' => $preset,
            'preset_label' => $presets[$preset]['label'] ?? 'Seimbang',
            'alasan' => $preferensi['alasan'] ?? null,
            'catatan' => $preferensi['catatan'] ?? null,
            'gejala_terpilih' => $preferensi['gejala_terpilih'] ?? [],
            'kriteria' => $kriteria->values()->all(),
        ];
    }
}
```

## 7. Integrasi ke Controller yang Sudah Ada

### `app/Http/Controllers/User/DiagnosisController.php`

Bagian identifikasi penyakit:

```php
use App\Services\CertaintyFactorService;

public function __construct(private CertaintyFactorService $cf) {}

public function identifikasi(Request $request)
{
    $request->validate([
        'gejala' => 'required|array|min:1',
        'gejala.*' => 'exists:gejala,id',
    ]);

    $idGejalaInput = collect($request->gejala)->map(fn ($id) => (int) $id)->all();
    $skorPenyakit = $this->cf->diagnose($idGejalaInput);

    if (empty($skorPenyakit)) {
        return back()
            ->withInput()
            ->with('error', 'Penyakit tidak ditemukan berdasarkan gejala yang dipilih.');
    }

    $gejalaInput = Gejala::whereIn('id', $idGejalaInput)->get();
    $kriteria = Kriteria::orderBy('kode')->get();
    $presetPreferensi = $this->cf->getPreferencePresets();

    return view('user.diagnosis.hasil', compact('skorPenyakit', 'gejalaInput', 'kriteria', 'presetPreferensi'));
}
```

Bagian proses rekomendasi:

```php
$preview = $this->cf->preview($idPenyakit, $preferensi);
$rekomendasi = Auth::check()
    ? $this->cf->hitung(Auth::id(), $idPenyakit, $preferensi)
    : null;
```

### `app/Http/Controllers/User/RekomendasiController.php`

Ganti dependency:

```php
use App\Services\CertaintyFactorService;

public function __construct(private CertaintyFactorService $cf) {}
```

Lalu:

```php
$preview = $this->cf->preview($rekomendasi->id_penyakit, $rekomendasi->preferensi_pengguna ?? []);
```

### `app/Http/Controllers/Admin/RiwayatController.php`

Sama, cukup ganti service ke `CertaintyFactorService`.

## 8. Admin Input Rule CF

### Gejala per penyakit

Form penyakit yang sekarang hanya checklist gejala perlu ditingkatkan menjadi:

- centang gejala
- isi `mb`
- isi `md`

Contoh struktur request:

```php
gejala_rules[5][selected] = 1
gejala_rules[5][mb] = 0.8
gejala_rules[5][md] = 0.1
```

Lalu saat simpan:

```php
$syncData = [];

foreach ($request->input('gejala_rules', []) as $idGejala => $rule) {
    if (!filled($rule['selected'] ?? null)) {
        continue;
    }

    $syncData[$idGejala] = [
        'mb' => $rule['mb'] ?? 0,
        'md' => $rule['md'] ?? 0,
    ];
}

$penyakit->gejala()->sync($syncData);
```

### Pupuk dan pestisida per penyakit

Controller rating admin saat ini tidak lagi perlu membaca `kriteria` per alternatif. Ubah menjadi input `mb/md` per penyakit-alternatif.

Contoh `RatingController`:

```php
public function pupuk()
{
    $penyakit = Penyakit::orderBy('kode')->get();
    $pupuk = Pupuk::orderBy('kode')->get();
    $rules = PenyakitPupuk::all()->keyBy(fn ($item) => "{$item->id_penyakit}_{$item->id_pupuk}");

    return view('admin.rating.pupuk', compact('penyakit', 'pupuk', 'rules'));
}

public function simpanPupuk(Request $request)
{
    $request->validate([
        'rules' => 'required|array',
        'rules.*.*.mb' => 'required|numeric|min:0|max:1',
        'rules.*.*.md' => 'required|numeric|min:0|max:1',
    ]);

    foreach ($request->rules as $idPenyakit => $items) {
        foreach ($items as $idPupuk => $rule) {
            PenyakitPupuk::updateOrCreate(
                ['id_penyakit' => $idPenyakit, 'id_pupuk' => $idPupuk],
                ['mb' => $rule['mb'], 'md' => $rule['md']]
            );
        }
    }

    return back()->with('success', 'Rule CF pupuk berhasil disimpan.');
}
```

View admin cukup diubah menjadi kolom:

- nama pupuk
- MB
- MD

Hal yang sama berlaku untuk pestisida.

## 9. Kenapa Fitur Lama Tetap Berjalan

### Preview hasil

Tetap bisa dipakai karena `preview()` masih mengembalikan struktur yang sama:

- `pupuk`
- `pestisida`
- `detail`
- `peringkat`
- `vi`

Nama `vi` hanya dipertahankan untuk kompatibilitas view. Isinya sekarang adalah **CF akhir**, bukan nilai SAW.

### Riwayat

Tetap berjalan karena:

- tabel `rekomendasi` tidak berubah
- detail rekomendasi tetap menyimpan skor akhir dan ranking
- controller riwayat hanya perlu membaca service CF baru

### Print dan detail perhitungan

Tetap berjalan dengan sedikit perubahan label teks:

- ubah tulisan "Detail Perhitungan SAW" menjadi "Detail Perhitungan Certainty Factor"
- ubah penjelasan normalisasi SAW menjadi penjelasan kombinasi CF

### Custom kriteria

Tidak hilang, hanya berubah bentuk:

- sebelumnya: mengubah bobot akhir `wj`
- sesudah: mengubah `mb/md` hasil rule adjustment

Secara UX, form yang dipilih user tetap bisa sama.

## 10. Langkah Implementasi yang Disarankan

1. Tambahkan migration tabel rule CF.
2. Buat `CertaintyFactorService`.
3. Ganti dependency `CertaintyFactorService` di controller user dan admin.
4. Ubah diagnosa penyakit agar memakai `diagnose()`.
5. Ubah admin input rating menjadi input `mb/md`.
6. Ubah label di view dari SAW menjadi CF.
7. Setelah stabil, hapus dependensi lama terhadap `rating_pupuk` dan `rating_pestisida`.

## 11. Catatan Penting

- Nilai `mb` dan `md` harus berasal dari pakar.
- Jika ingin migrasi bertahap, tabel SAW lama bisa dibiarkan sementara, tetapi **jangan lagi dipakai dalam logika perhitungan**.
- Jika ingin kompatibilitas penuh tanpa mengubah banyak blade, pertahankan shape array hasil service seperti service lama.

## 12. File Repo Saat Ini yang Paling Relevan untuk Diubah

- `app/Services/CertaintyFactorService.php`
- `app/Http/Controllers/User/DiagnosisController.php`
- `app/Http/Controllers/User/RekomendasiController.php`
- `app/Http/Controllers/Admin/RiwayatController.php`
- `app/Http/Controllers/Admin/RatingController.php`
- `app/Models/Penyakit.php`
- `app/Models/Gejala.php`
- `app/Models/Pupuk.php`
- `app/Models/Pestisida.php`
- `resources/views/admin/rating/pupuk.blade.php`
- `resources/views/admin/rating/pestisida.blade.php`
- `resources/views/user/rekomendasi/detail.blade.php`
- `resources/views/user/rekomendasi/show.blade.php`
- `resources/views/admin/riwayat/detail.blade.php`

## 13. Rekomendasi Praktis untuk Repo Ini

Untuk codebase ini, pendekatan paling aman adalah:

- buat service baru `CertaintyFactorService`
- pertahankan format output lama agar blade tetap minim perubahan
- gunakan `nilai_vi` sebagai nama kolom penyimpanan skor akhir sementara
- ubah panel admin rating menjadi editor rule `mb/md`

Dengan pendekatan itu, fitur lama tetap utuh, tetapi mesin hitungnya sudah sepenuhnya berbasis CF.
