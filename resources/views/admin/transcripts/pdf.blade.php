<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai - {{ $student->name }}</title>
    <style>
        @page { margin: 15mm 15mm 15mm 15mm; }

        body {
            font-family: 'Times New Roman', 'DejaVu Serif', serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* ===== HEADER / KOP SURAT ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .header-logo {
            width: 70px;
            text-align: center;
        }
        .header-logo img {
            width: 60px;
            height: auto;
        }
        .header-text {
            text-align: center;
            padding: 0 10px;
        }
        .header-text .institution {
            font-size: 11px;
            margin: 0;
            text-transform: uppercase;
        }
        .header-text .school-name {
            font-size: 16px;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-text .accreditation {
            font-size: 9px;
            margin: 1px 0;
            font-weight: bold;
        }
        .header-text .address {
            font-size: 9px;
            margin: 1px 0;
        }
        .header-line {
            border: none;
            border-top: 1px solid #000;
            margin: 2px 0 0 0;
        }

        /* ===== TITLE ===== */
        .doc-title {
            text-align: center;
            margin: 18px 0 5px 0;
        }
        .doc-title h2 {
            font-size: 14px;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: bold;
        }
        .doc-title .doc-number {
            font-size: 10px;
            margin-top: 2px;
        }

        /* ===== STUDENT INFO ===== */
        .student-info {
            margin: 15px 0 12px 0;
        }
        .student-info table {
            border-collapse: collapse;
        }
        .student-info td {
            padding: 1.5px 0;
            border: none;
            font-size: 11px;
            vertical-align: top;
        }
        .student-info .label {
            width: 180px;
        }
        .student-info .separator {
            width: 12px;
            text-align: center;
        }
        .student-info .value {
            text-transform: uppercase;
        }

        /* ===== SCORE TABLE ===== */
        table.score-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.score-table th,
        table.score-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 10px;
            vertical-align: middle;
        }
        table.score-table th {
            background: none;
            color: #000;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        table.score-table td {
            text-align: center;
        }
        table.score-table td.subject-name {
            text-align: left;
            padding-left: 8px;
        }
        table.score-table td.no-col {
            text-align: center;
            width: 25px;
        }

        /* Semester header spanning */
        table.score-table th.semester-group {
            text-align: center;
            font-weight: bold;
        }

        /* Summary row */
        table.score-table tr.summary-row td {
            font-weight: bold;
            border-top: 2px solid #000;
        }

        /* ===== SIGNATURE ===== */
        .signature {
            margin-top: 25px;
            text-align: right;
            page-break-inside: avoid;
        }
        .signature p {
            margin: 2px 0;
            font-size: 11px;
        }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 60px;
        }
        .signature .nip {
            font-size: 10px;
        }
    </style>
</head>
<body>
    {{-- ===== KOP SURAT ===== --}}
    <table class="header-table">
        <tr>
            @if($logoPath)
            <td class="header-logo">
                <img src="{{ $logoPath }}" alt="Logo">
            </td>
            @endif
            <td class="header-text">
                <p class="institution">Pemerintah Kabupaten Cirebon</p>
                <p class="institution">Dinas Pendidikan</p>
                <p class="school-name">{{ $settings['school_name'] }}</p>
                @if(!empty($settings['school_address']))
                <p class="address">{{ $settings['school_address'] }}</p>
                @endif
            </td>
        </tr>
    </table>
    <hr class="header-line">

    {{-- ===== JUDUL DOKUMEN ===== --}}
    <div class="doc-title">
        <h2>Transkip Nilai</h2>
        <p class="doc-number">Tahun Ajaran {{ $settings['academic_year'] }}</p>
    </div>

    {{-- ===== INFO SISWA ===== --}}
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Nama</td>
                <td class="separator">:</td>
                <td class="value">{{ strtoupper($student->name) }}</td>
            </tr>
            @if($student->birth_date)
            <tr>
                <td class="label">Tempat dan Tanggal Lahir</td>
                <td class="separator">:</td>
                <td class="value">{{ \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Nomor Induk Siswa /NIS</td>
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
        </table>
    </div>

    {{-- ===== TABEL NILAI ===== --}}
    @php
        $semesters = [1, 2, 3, 4, 5];
        $totalAll = 0;
        $countAll = 0;
    @endphp

    <table class="score-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="text-align: left; padding-left: 8px;">Mata Pelajaran</th>
                <th colspan="{{ count($semesters) }}" class="semester-group">Nilai Semester</th>
                <th rowspan="2" style="width: 50px;">Nilai<br>Rata-<br>rata</th>
            </tr>
            <tr>
                @foreach($semesters as $sem)
                <th style="width: 35px;">{{ $sem }}</th>
                @endforeach
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
                $subjectAvg = $subjectCount > 0 ? round($subjectTotal / $subjectCount, 0) : null;
                if ($subjectAvg !== null) {
                    $totalAll += $subjectAvg;
                    $countAll++;
                }
            @endphp
            <tr>
                <td class="no-col">{{ $idx + 1 }}</td>
                <td class="subject-name">{{ $subj->name }}</td>
                @foreach($semesters as $sem)
                <td>{{ isset($scores[$sem][$subj->id]) ? number_format($scores[$sem][$subj->id]->score, 0) : '-' }}</td>
                @endforeach
                <td style="font-weight: bold;">{{ $subjectAvg !== null ? $subjectAvg : '-' }}</td>
            </tr>
            @endforeach

            {{-- Rata-rata Kumulatif --}}
            <tr class="summary-row">
                <td colspan="2" style="text-align: right; padding-right: 10px; font-weight: bold; font-style: italic;">Nilai Rata — rata Kumulatif</td>
                @foreach($semesters as $sem)
                <td></td>
                @endforeach
                <td style="font-weight: bold;">{{ $countAll > 0 ? round($totalAll / $countAll, 0) : '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ===== TANDA TANGAN ===== --}}
    <div class="signature">
        <p>Sumber, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Kepala Sekolah,</p>
        <p class="name">{{ $settings['principal_name'] }}</p>
        <p class="nip">NIP. {{ $settings['principal_nip'] }}</p>
    </div>

</body>
</html>
