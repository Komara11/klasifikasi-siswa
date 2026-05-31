<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekomendasi Peminatan</title>
    <style>
        @page { margin: 20mm 15mm 25mm 15mm; }
        
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 10px; 
            color: #222; 
            line-height: 1.4;
        }

        /* Header */
        .header { 
            text-align: center; 
            border-bottom: 3px double #1E3A5F; 
            padding-bottom: 12px; 
            margin-bottom: 15px; 
        }
        .header h1 { 
            font-size: 15px; 
            margin: 0 0 2px 0; 
            color: #1E3A5F; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        .header .subtitle { 
            font-size: 12px; 
            font-weight: bold;
            color: #333; 
            margin: 6px 0 3px 0; 
        }
        .header .info { 
            font-size: 9px; 
            color: #666; 
            margin: 1px 0; 
        }

        /* Summary Stats */
        .summary {
            margin: 10px 0 15px 0;
            padding: 10px;
            background: #f5f7fa;
            border: 1px solid #d9e2ec;
            border-radius: 4px;
        }
        .summary-title {
            font-size: 10px;
            font-weight: bold;
            color: #1E3A5F;
            margin-bottom: 6px;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-grid td {
            padding: 3px 8px;
            font-size: 9px;
            border: none;
            background: none;
        }
        .summary-grid .label {
            color: #666;
            font-weight: normal;
        }
        .summary-grid .value {
            font-weight: bold;
            color: #1E3A5F;
        }

        /* Main Table */
        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            page-break-inside: auto;
        }
        table.main-table th { 
            background: #1E3A5F; 
            color: white; 
            padding: 6px 5px; 
            text-align: left; 
            font-size: 8.5px; 
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #1a3355;
        }
        table.main-table td { 
            padding: 5px 5px; 
            border: 1px solid #d9e2ec; 
            font-size: 9px; 
            vertical-align: top;
        }
        table.main-table tr:nth-child(even) { 
            background: #f8fafc; 
        }
        table.main-table tr { 
            page-break-inside: avoid; 
        }

        /* Badge style for recommendation */
        .badge-path { 
            display: inline-block;
            padding: 2px 8px; 
            border-radius: 8px; 
            font-weight: bold; 
            font-size: 8.5px; 
            color: white;
            text-align: center;
        }
        .badge-ipa { background: #2563eb; }
        .badge-ips { background: #d97706; }
        .badge-bahasa { background: #7c3aed; }
        .badge-vokasi { background: #059669; }

        /* Probability mini bars */
        .prob-row {
            margin: 1px 0;
            font-size: 8px;
            white-space: nowrap;
        }
        .prob-label {
            display: inline-block;
            width: 30px;
            font-weight: bold;
            color: #555;
        }
        .prob-bar-bg {
            display: inline-block;
            width: 50px;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            vertical-align: middle;
            overflow: hidden;
        }
        .prob-bar {
            display: block;
            height: 100%;
            border-radius: 3px;
        }
        .prob-bar-ipa { background: #2563eb; }
        .prob-bar-ips { background: #d97706; }
        .prob-bar-bahasa { background: #7c3aed; }
        .prob-bar-vokasi { background: #059669; }
        .prob-value {
            display: inline-block;
            width: 28px;
            text-align: right;
            font-size: 8px;
            color: #555;
        }

        /* Factor text */
        .factor-text {
            font-size: 8px;
            color: #555;
            line-height: 1.3;
            max-width: 180px;
        }

        /* Signature */
        .signature { 
            margin-top: 30px; 
            text-align: right;
            page-break-inside: avoid;
        }
        .signature p { margin: 2px 0; }
        .signature .name { 
            font-weight: bold; 
            text-decoration: underline; 
            margin-top: 50px; 
        }

        /* Footer note */
        .footer-note {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #d9e2ec;
            font-size: 8px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Document Header -->
    <div class="header">
        <h1>{{ $settings['school_name'] }}</h1>
        <div class="subtitle">Laporan Rekapitulasi Hasil Rekomendasi Peminatan Siswa</div>
        <div class="info">Tahun Ajaran: {{ $settings['academic_year'] }}</div>
        <div class="info">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Summary Stats -->
    @php
        $total = $results->count();
        $distrib = $results->groupBy('recommended_path')->map->count();
    @endphp
    <div class="summary">
        <div class="summary-title">Ringkasan Distribusi Peminatan</div>
        <table class="summary-grid">
            <tr>
                <td class="label">Total Siswa Terklasifikasi:</td>
                <td class="value">{{ $total }} siswa</td>
                <td class="label">IPA:</td>
                <td class="value">{{ $distrib['IPA'] ?? 0 }} siswa ({{ $total > 0 ? round(($distrib['IPA'] ?? 0) / $total * 100) : 0 }}%)</td>
                <td class="label">Bahasa:</td>
                <td class="value">{{ $distrib['Bahasa'] ?? 0 }} siswa ({{ $total > 0 ? round(($distrib['Bahasa'] ?? 0) / $total * 100) : 0 }}%)</td>
            </tr>
            <tr>
                <td class="label"></td>
                <td class="value"></td>
                <td class="label">IPS:</td>
                <td class="value">{{ $distrib['IPS'] ?? 0 }} siswa ({{ $total > 0 ? round(($distrib['IPS'] ?? 0) / $total * 100) : 0 }}%)</td>
                <td class="label">Vokasi:</td>
                <td class="value">{{ $distrib['Vokasi'] ?? 0 }} siswa ({{ $total > 0 ? round(($distrib['Vokasi'] ?? 0) / $total * 100) : 0 }}%)</td>
            </tr>
        </table>
    </div>

    <!-- Main Data Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20px; text-align: center;">No</th>
                <th style="width: 55px;">NIS</th>
                <th style="width: 120px;">Nama Siswa</th>
                <th style="width: 45px;">Kelas</th>
                <th style="width: 65px;">Rekomendasi</th>
                <th style="width: 130px;">Kecocokan</th>
                <th>Alasan Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $i => $r)
            @php
                $badgeClass = match($r->recommended_path) {
                    'IPA' => 'badge-ipa',
                    'IPS' => 'badge-ips',
                    'Bahasa' => 'badge-bahasa',
                    'Vokasi' => 'badge-vokasi',
                    default => ''
                };
            @endphp
            <tr>
                <td style="text-align: center; color: #888;">{{ $i + 1 }}</td>
                <td style="font-weight: bold; color: #1E3A5F;">{{ $r->student->nis }}</td>
                <td style="font-weight: 600;">{{ $r->student->name }}</td>
                <td>{{ $r->student->classroom->name }}</td>
                <td>
                    <span class="badge-path {{ $badgeClass }}">{{ $r->recommended_path }}</span>
                    @if($r->vocational_major)
                        <br><span style="font-size: 8px; color: #059669; font-weight: bold;">{{ $r->vocational_major }}</span>
                    @endif
                </td>
                <td>
                    <div class="prob-row">
                        <span class="prob-label">IPA</span>
                        <span class="prob-bar-bg"><span class="prob-bar prob-bar-ipa" style="width: {{ round($r->ipa_probability * 100) }}%"></span></span>
                        <span class="prob-value">{{ round($r->ipa_probability * 100, 1) }}%</span>
                    </div>
                    <div class="prob-row">
                        <span class="prob-label">IPS</span>
                        <span class="prob-bar-bg"><span class="prob-bar prob-bar-ips" style="width: {{ round($r->ips_probability * 100) }}%"></span></span>
                        <span class="prob-value">{{ round($r->ips_probability * 100, 1) }}%</span>
                    </div>
                    <div class="prob-row">
                        <span class="prob-label">Bhs</span>
                        <span class="prob-bar-bg"><span class="prob-bar prob-bar-bahasa" style="width: {{ round($r->bahasa_probability * 100) }}%"></span></span>
                        <span class="prob-value">{{ round($r->bahasa_probability * 100, 1) }}%</span>
                    </div>
                    <div class="prob-row">
                        <span class="prob-label">Vok</span>
                        <span class="prob-bar-bg"><span class="prob-bar prob-bar-vokasi" style="width: {{ round($r->vokasi_probability * 100) }}%"></span></span>
                        <span class="prob-value">{{ round($r->vokasi_probability * 100, 1) }}%</span>
                    </div>
                </td>
                <td class="factor-text">{{ Str::limit($r->dominant_factor, 120) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer Note -->
    <div class="footer-note">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Rekomendasi Peminatan Pendidikan Lanjutan Siswa — {{ $settings['school_name'] }}
    </div>

    <!-- Signature -->
    <div class="signature">
        <p>Sumber, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala {{ $settings['school_name'] }},</p>
        <p class="name">{{ $settings['principal_name'] }}</p>
        <p style="font-size: 8px; color: #666;">NIP. {{ $settings['principal_nip'] }}</p>
    </div>
</body>
</html>
