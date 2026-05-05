<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekomendasi</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; margin: 24px; line-height: 1.5; }
        h1, h2, h3, h4, p { margin: 0 0 12px; }
        .section { margin-top: 24px; }
        .toolbar { display: flex; gap: 12px; margin-bottom: 24px; }
        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }
        .btn-print { background: #166534; color: #fff; border: 0; }
        .btn-download { background: #f3f4f6; color: #111827; border: 1px solid #d1d5db; }
        .chips { display: flex; flex-wrap: wrap; gap: 8px; }
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
            page-break-inside: avoid;
        }
        .detail-list p { margin-bottom: 6px; font-size: 14px; }
        .detail-list strong { display: inline-block; min-width: 120px; }
        .item-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .item-title {
            flex: 1;
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
        @media print {
            .toolbar { display: none !important; }
            body { margin: 0; }
            .detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    @php
        $selectedSymptoms = collect(data_get($rekomendasi, 'preferensi_pengguna.gejala_terpilih', []));
        $matchedSymptoms = collect(optional($rekomendasi->penyakit)->gejala)
            ->filter(fn ($gejala) => $selectedSymptoms->pluck('id')->map(fn ($id) => (int) $id)->contains((int) $gejala->id))
            ->values();
    @endphp
    <div class="toolbar">
        <button class="btn btn-print" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="btn btn-download" href="{{ route('user.rekomendasi.cetak', ['id' => $rekomendasi->id, 'download' => 1]) }}">Download HTML</a>
    </div>

    <h2>Laporan Rekomendasi Pupuk dan Pestisida</h2>
    <p>Nama Pengguna: {{ $rekomendasi->user->nama ?? '-' }}</p>
    <p>Penyakit: {{ $rekomendasi->penyakit->nama ?? '-' }}</p>
    <p>Tanggal: {{ optional($rekomendasi->created_at)->format('d M Y H:i') }}</p>

    <div class="section">
        <h3>Gejala yang Cocok</h3>
        <div class="chips">
            @foreach($matchedSymptoms as $item)
            <span class="chip">{{ $item->kode ? $item->kode . ' - ' : '' }}{{ $item->nama_gejala }}</span>
            @endforeach
            @if($matchedSymptoms->isEmpty())
            <span class="chip">Tidak ada gejala cocok yang tersimpan.</span>
            @endif
        </div>
    </div>

    <div class="section">
        <h3>Rekomendasi Pupuk</h3>
        <div class="detail-grid">
            @foreach($rekomendasi->detailPupuk->sortBy('peringkat')->take(2) as $item)
            <div class="detail-box">
                <div class="item-header">
                    @if(data_get($item, 'pupuk.gambar_url'))
                    <img src="{{ data_get($item, 'pupuk.gambar_url') }}" alt="{{ $item->pupuk->nama ?? 'Pupuk' }}" class="item-image">
                    @else
                    <div class="item-image d-flex align-items-center justify-content-center bg-light text-muted">
                        🌱
                    </div>
                    @endif
                    <div class="item-title">
                        <h4 class="mb-1">{{ $item->pupuk->nama ?? '-' }}</h4>
                        <small class="text-muted">{{ $item->pupuk->kode ?? '-' }}</small>
                        <span class="cf-badge">{{ number_format((float) $item->nilai_vi, 4) }} ({{ number_format((float) $item->cf_percentage ?? 0, 2) }}%)</span>
                    </div>
                </div>
                <div class="detail-list">
                    <p><strong>Kandungan</strong> {{ $item->pupuk->kandungan ?? '-' }}</p>
                    <p><strong>Detail</strong> {{ $item->pupuk->kandungan_detail ?? '-' }}</p>
                    <p><strong>Fungsi</strong> {{ $item->pupuk->fungsi_utama ?? '-' }}</p>
                    <p><strong>Takaran</strong> {{ $item->pupuk->takaran ?? '-' }}</p>
                    <p><strong>Efek</strong> {{ $item->pupuk->efek_penggunaan ?? '-' }}</p>
                    <p><strong>Cara</strong> {{ $item->pupuk->cara_aplikasi ?? '-' }}</p>
                    <p><strong>Jadwal</strong> {{ $item->pupuk->jadwal_umur_aplikasi ?? '-' }}</p>
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
            @foreach($rekomendasi->detailPestisida->sortBy('peringkat')->take(2) as $item)
            <div class="detail-box">
                <div class="item-header">
                    @if(data_get($item, 'pestisida.gambar_url'))
                    <img src="{{ data_get($item, 'pestisida.gambar_url') }}" alt="{{ $item->pestisida->nama ?? 'Pestisida' }}" class="item-image">
                    @else
                    <div class="item-image d-flex align-items-center justify-content-center bg-light text-muted">
                        💧
                    </div>
                    @endif
                    <div class="item-title">
                        <h4 class="mb-1">{{ $item->pestisida->nama ?? '-' }}</h4>
                        <small class="text-muted">{{ $item->pestisida->kode ?? '-' }}</small>
                        <span class="cf-badge">{{ number_format((float) $item->nilai_vi, 4) }} ({{ number_format((float) $item->cf_percentage ?? 0, 2) }}%)</span>
                    </div>
                </div>
                <div class="detail-list">
                    <p><strong>Bahan aktif</strong> {{ $item->pestisida->bahan_aktif ?? '-' }}</p>
                    <p><strong>Kandungan Detail</strong> {{ $item->pestisida->kandungan_detail ?? '-' }}</p>
                    <p><strong>Fungsi</strong> {{ $item->pestisida->fungsi ?? '-' }}</p>
                    <p><strong>Dosis</strong> {{ $item->pestisida->dosis ?? '-' }}</p>
                    <p><strong>Takaran</strong> {{ $item->pestisida->takaran ?? '-' }}</p>
                    <p><strong>Efek</strong> {{ $item->pestisida->efek_penggunaan ?? '-' }}</p>
                    <p><strong>Cara</strong> {{ $item->pestisida->cara_aplikasi ?? '-' }}</p>
                    <p><strong>Jadwal</strong> {{ $item->pestisida->jadwal_umur_aplikasi ?? '-' }}</p>
                    <p><strong>Frekuensi</strong> {{ $item->pestisida->frekuensi_aplikasi ?? '-' }}</p>
                    @if($item->is_high_efficiency ?? false)
                    <p class="high-efficiency-badge"><strong>Status</strong> ✓ Produk Efisiensi Tinggi</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
