<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Riwayat Konsultasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            margin: 24px;
            line-height: 1.5;
        }

        h1, h2, h3, h4, p {
            margin: 0 0 10px;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 0;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
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

        .section {
            margin-top: 24px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
            page-break-inside: avoid;
        }

        .label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 9px;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            font-size: 11px;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 11px;
            font-weight: 700;
        }

        @media print {
            body {
                margin: 0;
            }

            .toolbar {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        $selectedSymptoms = collect(data_get($rekomendasi, 'preferensi_pengguna.gejala_terpilih', []));
        $selectedSymptomIds = $selectedSymptoms->pluck('id')->map(fn ($id) => (int) $id);
        $matchedSymptoms = collect(optional($rekomendasi->penyakit)->gejala)
            ->filter(fn ($gejala) => $selectedSymptomIds->contains((int) $gejala->id))
            ->values();
        $isComplete = $rekomendasi->detailPupuk->isNotEmpty() && $rekomendasi->detailPestisida->isNotEmpty();
    @endphp

    <div class="toolbar">
        <button type="button" class="btn btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="btn btn-download" href="{{ route('admin.riwayat.cetak', ['id' => $rekomendasi->id, 'download' => 1]) }}">Download HTML</a>
        <a class="btn btn-download" href="{{ route('admin.riwayat.show', $rekomendasi->id) }}">Kembali</a>
    </div>

    <h2>Laporan Lengkap Konsultasi PadiCare</h2>
    <p>Dokumen ini dibuat dari riwayat konsultasi user yang dimonitor oleh admin.</p>

    <div class="summary">
        <div class="box">
            <div class="label">Pengguna</div>
            <p><strong>{{ $rekomendasi->user->nama ?? '-' }}</strong></p>
            <p>{{ $rekomendasi->user->username ?? '-' }}</p>
        </div>
        <div class="box">
            <div class="label">Waktu Konsultasi</div>
            <p><strong>{{ optional($rekomendasi->tanggal ?? $rekomendasi->created_at)->format('d M Y H:i') }}</strong></p>
            <p><span class="badge">{{ $isComplete ? 'Lengkap' : 'Tidak Lengkap' }}</span></p>
        </div>
        <div class="box">
            <div class="label">Diagnosis</div>
            <p><strong>{{ $rekomendasi->penyakit->nama ?? '-' }}</strong></p>
            <p>{{ $rekomendasi->penyakit->kode ?? '-' }}</p>
        </div>
        <div class="box">
            <div class="label">Prioritas User</div>
            <p><strong>{{ $rekomendasi->preferensi_label ?: 'Analisis Sistem Pakar' }}</strong></p>
            <p>{{ data_get($rekomendasi, 'preferensi_pengguna.alasan', '-') }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Gejala yang Dipakai</h3>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Gejala</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matchedSymptoms as $gejala)
                <tr>
                    <td>{{ $gejala->kode ?? '-' }}</td>
                    <td>{{ $gejala->nama_gejala ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2">Tidak ada gejala terpilih yang tersimpan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Rekomendasi Pupuk</h3>
        <table>
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Skor</th>
                    <th>Takaran</th>
                    <th>Cara Aplikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekomendasi->detailPupuk->sortBy('peringkat') as $item)
                <tr>
                    <td>{{ $item->peringkat }}</td>
                    <td>{{ $item->pupuk->kode ?? '-' }}</td>
                    <td>{{ $item->pupuk->nama ?? '-' }}</td>
                    <td>{{ number_format((float) $item->nilai_vi, 4) }}</td>
                    <td>{{ $item->pupuk->takaran ?? '-' }}</td>
                    <td>{{ $item->pupuk->cara_aplikasi ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">Tidak ada data rekomendasi pupuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Rekomendasi Pestisida</h3>
        <table>
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Skor</th>
                    <th>Dosis</th>
                    <th>Cara Aplikasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekomendasi->detailPestisida->sortBy('peringkat') as $item)
                <tr>
                    <td>{{ $item->peringkat }}</td>
                    <td>{{ $item->pestisida->kode ?? '-' }}</td>
                    <td>{{ $item->pestisida->nama ?? '-' }}</td>
                    <td>{{ number_format((float) $item->nilai_vi, 4) }}</td>
                    <td>{{ $item->pestisida->dosis ?? '-' }}</td>
                    <td>{{ $item->pestisida->cara_aplikasi ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">Tidak ada data rekomendasi pestisida.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
