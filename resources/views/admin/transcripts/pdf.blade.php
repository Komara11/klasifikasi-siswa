<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai - {{ $student->name }}</title>
    <style>
        @page { margin: 20mm 15mm 20mm 15mm; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #222;
            line-height: 1.5;
        }

        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 3px double #0D47A1;
            padding-bottom: 10px;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .header-logo {
            width: 65px;
            text-align: center;
        }
        .header-logo img {
            width: 55px;
            height: auto;
        }
        .header-text {
            text-align: center;
            padding-left: 10px;
        }
        .header-text h1 {
            font-size: 16px;
            margin: 0;
            color: #0D47A1;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-text .accreditation {
            font-size: 9px;
            color: #555;
            margin: 2px 0;
        }
        .header-text .address {
            font-size: 8.5px;
            color: #666;
            margin: 1px 0;
        }

        /* Title */
        .doc-title {
            text-align: center;
            margin: 15px 0 12px 0;
        }
        .doc-title h2 {
            font-size: 14px;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #222;
        }
        .doc-title .doc-number {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        /* Student Info */
        .student-info {
            margin: 12px 0 15px 0;
        }
        .student-info table {
            border-collapse: collapse;
        }
        .student-info td {
            padding: 2px 0;
            border: none;
            font-size: 10px;
            vertical-align: top;
        }
        .student-info .label {
            width: 150px;
            color: #333;
        }
        .student-info .separator {
            width: 15px;
            text-align: center;
        }
        .student-info .value {
            font-weight: bold;
            color: #111;
        }

        /* Score Table */
        table.score-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.score-table th {
            background: #0D47A1;
            color: white;
            padding: 6px 5px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #093d8a;
        }
        table.score-table th.subject-col {
            text-align: left;
            padding-left: 8px;
        }
        table.score-table td {
            padding: 5px 5px;
            border: 1px solid #c0c8d4;
            font-size: 9.5px;
            text-align: center;
            vertical-align: middle;
        }
        table.score-table td.subject-name {
            text-align: left;
            padding-left: 8px;
        }
        table.score-table tr:nth-child(even) {
            background: #f7f9fc;
        }
        table.score-table tr.summary-row td {
            font-weight: bold;
            background: #e8edf5;
            border-top: 2px solid #0D47A1;
            font-size: 10px;
        }

        /* Signature */
        .signature {
            margin-top: 30px;
            text-align: right;
            page-break-inside: avoid;
        }
        .signature p { margin: 2px 0; font-size: 10px; }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 55px;
        }
        .signature .nip {
            font-size: 9px;
            color: #555;
        }

        /* Footer */
        .footer-note {
            margin-top: 20px;
            padding-top: 6px;
            border-top: 1px solid #d9e2ec;
            font-size: 7.5px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            @if($logoPath)
            <td class="header-logo">
                <img src="{{ $logoPath }}" alt="Logo">
            </td>
            @endif
            <td class="header-text">
                <p class="accreditation" style="margin:0">PEMERINTAH KABUPATEN CIREBON</p>
                <p class="accreditation" style="margin:0">DINAS PENDIDIKAN</p>
                <h1>{{ $settings['school_name'] }}</h1>
                <p class="address">{{ $settings['school_address'] }}</p>
            </td>
        </tr>
    </table>

    <!-- Document Title -->
    <div class="doc-title">
        <h2>Transkrip Nilai</h2>
        <p class="doc-number">Tahun Ajaran {{ $settings['academic_year'] }}</p>
    </div>

    <!-- Student Info -->
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="separator">:</td>
                <td class="value">{{ strtoupper($student->name) }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Siswa (NIS)</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->nis }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="separator">:</td>
                <td class="value">{{ $student->classroom->name }}</td>
            </tr>
            @if($student->birth_date)
            <tr>
                <td class="label">Tanggal Lahir</td>
                <td class="separator">:</td>
                <td class="value">{{ \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Score Table -->
    @php
        $semesters = [1, 2, 3, 4, 5];
        $totalAll = 0;
        $countAll = 0;
    @endphp

    <table class="score-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th class="subject-col">Mata Pelajaran</th>
                @foreach($semesters as $sem)
                <th style="width: 45px;">Smt {{ $sem }}</th>
                @endforeach
                <th style="width: 55px;">Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $idx => $subj)
            @php
                $subjectTotal = 0;
                $subjectCount = 0;
                foreach ($semesters as $sem) {
                    $val = $scores[$sem][$subj->id]->score ?? null;
                    if ($val !== null) {
                        $subjectTotal += $val;
                        $subjectCount++;
                    }
                }
                $subjectAvg = $subjectCount > 0 ? round($subjectTotal / $subjectCount, 1) : null;
                if ($subjectAvg !== null) {
                    $totalAll += $subjectAvg;
                    $countAll++;
                }
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="subject-name">{{ $subj->name }}</td>
                @foreach($semesters as $sem)
                <td>{{ isset($scores[$sem][$subj->id]) ? number_format($scores[$sem][$subj->id]->score, 0) : '-' }}</td>
                @endforeach
                <td style="font-weight: bold;">{{ $subjectAvg !== null ? number_format($subjectAvg, 1) : '-' }}</td>
            </tr>
            @endforeach

            <!-- Summary Rows -->
            <tr class="summary-row">
                <td colspan="2" style="text-align: right; padding-right: 10px;">Total Nilai</td>
                <td colspan="{{ count($semesters) }}"></td>
                <td>{{ $countAll > 0 ? number_format($totalAll, 1) : '-' }}</td>
            </tr>
            <tr class="summary-row">
                <td colspan="2" style="text-align: right; padding-right: 10px;">Rata-rata Kumulatif</td>
                <td colspan="{{ count($semesters) }}"></td>
                <td>{{ $countAll > 0 ? number_format($totalAll / $countAll, 1) : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer Note -->
    <div class="footer-note">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Klasifikasi Siswa — {{ $settings['school_name'] }}
    </div>

    <!-- Signature -->
    <div class="signature">
        <p>Sumber, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala {{ $settings['school_name'] }},</p>
        <p class="name">{{ $settings['principal_name'] }}</p>
        <p class="nip">NIP. {{ $settings['principal_nip'] }}</p>
    </div>
</body>
</html>
