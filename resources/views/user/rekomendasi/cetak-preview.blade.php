<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Hasil Rekomendasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            margin: 24px;
            line-height: 1.5;
        }

        h1,
        h2,
        h3,
        h4,
        p {
            margin: 0 0 10px;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }

        .btn-print {
            background: #166534;
            color: #fff;
        }

        .btn-download {
            background: #f3f4f6;
            color: #111827;
            border: 1px solid #d1d5db;
        }

        .report-card {
            border: 1px solid #d1d5db;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 24px;
            page-break-inside: avoid;
        }

        .section {
            margin-top: 18px;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chip {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            font-size: 12px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .detail-box {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            background: #fff;
        }

        .detail-box h4 {
            margin-bottom: 8px;
        }

        .detail-list p {
            margin-bottom: 6px;
            font-size: 14px;
        }

        .detail-list strong {
            display: inline-block;
            min-width: 120px;
        }

        .product-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .product-image-placeholder {
            width: 80px;
            height: 80px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border: 1px solid #e5e7eb;
        }

        .product-title {
            flex: 1;
        }

        .product-title h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .product-title small {
            display: block;
            color: #6b7280;
            font-size: 0.75rem;
        }

        .cf-badge {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 8px;
            background: #dcfce7;
            color: #166534;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .high-efficiency-badge {
            margin-top: 8px;
            padding: 6px 10px;
            background: #fef3c7;
            border-radius: 8px;
            color: #92400e;
            font-size: 0.75rem;
            font-weight: 600;
        }

        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        @media print {
            .toolbar {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* HAPUS page-break-inside dari .report-card */
            .report-card {
                border: 1px solid #d1d5db !important;
                margin-bottom: 16px;
            }

            .detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            /* Pindah break-inside ke level box saja */
            .detail-box {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .section {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .chips {
                flex-wrap: wrap;
            }

            /* Tambah di @media print */
            h1,
            h1+p,
            h1~p {
                page-break-after: avoid;
                break-after: avoid;
            }

            .report-card {
                page-break-before: avoid;
                break-before: avoid;
                border: 1px solid #d1d5db !important;
                margin-bottom: 16px;
            }
        }
    </style>
</head>

<body>
    @php
    $formatCurrency = static function ($value) {
    return is_numeric($value) && (float) $value > 0
    ? 'Rp ' . number_format((float) $value, 0, ',', '.')
    : '-';
    };
    $formatUnitPrice = static function ($value, $unit = null) use ($formatCurrency) {
    $formatted = $formatCurrency($value);

    if ($formatted === '-') {
    return '-';
    }

    return trim($formatted . ($unit ? ' / ' . $unit : ''));
    };
    @endphp
    <div class="toolbar">
        <button class="btn btn-print" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="btn btn-download" href="{{ route('user.rekomendasi.preview.cetak', ['download' => 1]) }}">Download
            HTML</a>
    </div>

    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #166534;">
        <h1 style="margin: 0; font-size: 1.1rem; color: #166534;">Hasil Rekomendasi Pupuk dan Pestisida</h1>
        <p style="margin: 0; font-size: 0.8rem; color: #6b7280;">Tanggal cetak: {{ now()->format('d M Y H:i') }}</p>
    </div>

    @foreach($hasilDiagnosa as $hasil)
    @php
    $rekomendasi = $hasil['rekomendasi'];
    $gejala = collect(data_get($rekomendasi, 'gejala_cocok', []));
    $sortedPupuk = $rekomendasi->detailPupuk->sortBy('peringkat')->take(2)->values();
    $sortedPestisida = $rekomendasi->detailPestisida->sortBy('peringkat')->take(2)->values();
    $penyakitGambarUrl = data_get($rekomendasi, 'penyakit.gambar_url') ?? data_get($rekomendasi, 'penyakit.gambar');
    @endphp
    <div class="report-card">
        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
            @if($penyakitGambarUrl)
            <img src="{{ $penyakitGambarUrl }}" alt="{{ $rekomendasi->penyakit->nama ?? 'Penyakit' }}"
                style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;">
            @endif
            <div style="flex: 1;">
                <h2>{{ $rekomendasi->penyakit->nama ?? '-' }}</h2>
                @if($rekomendasi->preferensi_label)
                <p>Prioritas pengguna: {{ $rekomendasi->preferensi_label }}</p>
                @endif
            </div>
        </div>

        <div class="section">
            <h3>Gejala yang Cocok</h3>
            <div class="chips">
                @foreach($gejala as $item)
                <span
                    class="chip">{{ data_get($item, 'kode') ? data_get($item, 'kode') . ' - ' : '' }}{{ data_get($item, 'nama_gejala') }}</span>
                @endforeach
                @if($gejala->isEmpty())
                <span class="chip">Tidak ada gejala cocok yang tersimpan.</span>
                @endif
            </div>
        </div>

        @if(($luasLahan ?? 0) > 0)
        <div class="section" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-top: 18px;">
            <h3 style="color: #166534; margin-bottom: 12px;">
                <i class="bi bi-calculator"></i> Kebutuhan & Estimasi Biaya untuk {{ number_format($luasLahan, 0, ',', '.') }} m²
            </h3>
            @php
                $totalBiayaPupuk = 0;
                $totalBiayaPestisida = 0;
            @endphp
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <h4 style="font-size: 0.9rem; color: #166534; margin-bottom: 8px;">🌱 PUPUK</h4>
                    @foreach($sortedPupuk as $item)
                    @php
                        $pupuk = $item->pupuk;
                        $calc = \App\Helpers\UnitConverter::hitungBiayaAkurat(
                            $luasLahan ?? 0,
                            (float) ($pupuk->dosis_per_hektar ?? 0),
                            $pupuk->satuan_dosis ?? 'kg',
                            (float) ($pupuk->harga_per_unit ?? 0),
                            (float) ($pupuk->satuan_harga_qty ?? 1),
                            $pupuk->satuan_harga_unit ?? 'kg',
                            $pupuk->frekuensi_aplikasi ?? 1
                        );
                        $totalBiayaPupuk += $calc['total_biaya'];
                    @endphp
                    <div style="font-size: 0.85rem; margin-bottom: 6px; padding: 6px; background: #fff; border-radius: 6px;">
                        <strong>{{ $pupuk->nama }}</strong><br>
                        <span style="color: #6b7280;">{{ $calc['kebutuhan_riil'] }} · {{ $pupuk->frekuensi_aplikasi ?? '-' }}</span><br>
                        <span style="color: #166534; font-weight: 700;">Rp {{ number_format($calc['total_biaya'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                
                <div>
                    <h4 style="font-size: 0.9rem; color: #b45309; margin-bottom: 8px;">💧 PESTISIDA</h4>
                    @foreach($sortedPestisida as $item)
                    @php
                        $pestisida = $item->pestisida;
                        $calc = \App\Helpers\UnitConverter::hitungBiayaAkurat(
                            $luasLahan ?? 0,
                            (float) ($pestisida->dosis_per_hektar ?? 0),
                            $pestisida->satuan_dosis ?? 'ml',
                            (float) ($pestisida->harga_per_unit ?? 0),
                            (float) ($pestisida->satuan_harga_qty ?? 1),
                            $pestisida->satuan_harga_unit ?? 'ml',
                            $pestisida->frekuensi_aplikasi ?? 1
                        );
                        $totalBiayaPestisida += $calc['total_biaya'];
                    @endphp
                    <div style="font-size: 0.85rem; margin-bottom: 6px; padding: 6px; background: #fff; border-radius: 6px;">
                        <strong>{{ $pestisida->nama }}</strong><br>
                        <span style="color: #6b7280;">{{ $calc['kebutuhan_riil'] }} · {{ $pestisida->frekuensi_aplikasi ?? '-' }}</span><br>
                        <span style="color: #b45309; font-weight: 700;">Rp {{ number_format($calc['total_biaya'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div style="margin-top: 12px; padding-top: 12px; border-top: 2px solid #bbf7d0; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; color: #166534;">TOTAL ESTIMASI BIAYA</span>
                <span style="font-size: 1.1rem; font-weight: 800; color: #166534;">Rp {{ number_format($totalBiayaPupuk + $totalBiayaPestisida, 0, ',', '.') }}</span>
            </div>
            <div style="font-size: 0.7rem; color: #6b7280; margin-top: 6px; text-align: right;">
                *Estimasi biaya bahan untuk 1 kali aplikasi
            </div>
        </div>
        @endif

        <div class="section">
            <h3>Rekomendasi Pupuk</h3>
            <div class="detail-grid">
                @foreach($sortedPupuk as $item)
                <div class="detail-box">
                    <div class="product-header">
                        @if(data_get($item, 'pupuk.gambar_url'))
                        <img src="{{ data_get($item, 'pupuk.gambar_url') }}" alt="{{ $item->pupuk->nama ?? 'Pupuk' }}"
                            class="product-image">
                        @else
                        <div class="product-image-placeholder">🌱</div>
                        @endif
                        <div class="product-title">
                            <h4 class="mb-1">{{ $item->pupuk->nama ?? '-' }}</h4>
                            <small class="text-muted">{{ $item->pupuk->kode ?? '-' }}</small>
                            <span class="cf-badge">{{ number_format((float) $item->nilai_vi, 4) }}
                                ({{ number_format((float) $item->cf_percentage, 2) }}%)</span>
                        </div>
                    </div>
                    <div class="detail-list">
                        <p><strong>Kandungan</strong> {{ $item->pupuk->kandungan ?? '-' }}</p>
                        <p><strong>Detail Kandungan</strong> {{ $item->pupuk->kandungan_detail ?? '-' }}</p>
                        <p><strong>Fungsi Utama</strong> {{ $item->pupuk->fungsi_utama ?? '-' }}</p>
                        <p><strong>Takaran per Hektar</strong> {{ $item->pupuk->dosis_per_hektar ?? '-' }} {{ $item->pupuk->satuan_dosis ?? '' }}</p>
                        <p><strong>Harga</strong>
                            {{ $formatUnitPrice($item->pupuk->harga_per_unit ?? null, $item->pupuk->satuan_harga_qty . ' ' . $item->pupuk->satuan_harga_unit) }}
                        </p>
                        <p><strong>Efek Penggunaan</strong> {{ $item->pupuk->efek_penggunaan ?? '-' }}</p>
                        <p><strong>Cara Aplikasi</strong> {{ $item->pupuk->cara_aplikasi ?? '-' }}</p>
                        <p><strong>Jadwal Umur</strong> {{ $item->pupuk->jadwal_umur_aplikasi ?? '-' }}</p>
                        <p><strong>Frekuensi</strong> {{ $item->pupuk->frekuensi_aplikasi ?? '-' }}</p>
                        @if($item->is_high_efficiency ?? false)
                        <p class="high-efficiency-badge"><strong>Status</strong> ✓ Produk Efisiensi Tinggi</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="section">
            <h3>Rekomendasi Pestisida</h3>
            <div class="detail-grid">
                @foreach($sortedPestisida as $item)
                <div class="detail-box">
                    <div class="product-header">
                        @if(data_get($item, 'pestisida.gambar_url'))
                        <img src="{{ data_get($item, 'pestisida.gambar_url') }}"
                            alt="{{ $item->pestisida->nama ?? 'Pestisida' }}" class="product-image">
                        @else
                        <div class="product-image-placeholder">💧</div>
                        @endif
                        <div class="product-title">
                            <h4 class="mb-1">{{ $item->pestisida->nama ?? '-' }}</h4>
                            <small class="text-muted">{{ $item->pestisida->kode ?? '-' }}</small>
                            <span class="cf-badge">{{ number_format((float) $item->nilai_vi, 4) }}
                                ({{ number_format((float) $item->cf_percentage, 2) }}%)</span>
                        </div>
                    </div>
                    <div class="detail-list">
                        <p><strong>Bahan Aktif</strong> {{ $item->pestisida->bahan_aktif ?? '-' }}</p>
                        <p><strong>Kandungan Detail</strong> {{ $item->pestisida->kandungan_detail ?? '-' }}</p>
                        <p><strong>Fungsi</strong> {{ $item->pestisida->fungsi ?? '-' }}</p>
                        <p><strong>Dosis</strong> {{ $item->pestisida->dosis ?? '-' }}</p>
                        <p><strong>Takaran per Hektar</strong> {{ $item->pestisida->dosis_per_hektar ?? '-' }} {{ $item->pestisida->satuan_dosis ?? '' }}</p>
                        <p><strong>Harga</strong>
                            {{ $formatUnitPrice($item->pestisida->harga_per_unit ?? null, $item->pestisida->satuan_harga_qty . ' ' . $item->pestisida->satuan_harga_unit) }}
                        </p>
                        <p><strong>Efek Penggunaan</strong> {{ $item->pestisida->efek_penggunaan ?? '-' }}</p>
                        <p><strong>Cara Aplikasi</strong> {{ $item->pestisida->cara_aplikasi ?? '-' }}</p>
                        <p><strong>Jadwal Umur</strong> {{ $item->pestisida->jadwal_umur_aplikasi ?? '-' }}</p>
                        <p><strong>Frekuensi</strong> {{ $item->pestisida->frekuensi_aplikasi ?? '-' }}</p>
                        @if($item->is_high_efficiency ?? false)
                        <p class="high-efficiency-badge"><strong>Status</strong> ✓ Produk Efisiensi Tinggi</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</body>

</html>