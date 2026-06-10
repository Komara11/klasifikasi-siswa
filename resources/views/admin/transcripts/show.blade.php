@extends('layouts.app')

@section('title', 'Transkrip Nilai - ' . $student->name)

@section('content')
<div class="space-y-space-lg">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.scores.index', ['student_id' => $student->id]) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-primary/80 transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Input Nilai
        </a>
    </div>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="font-h1 text-primary text-xl font-bold">Transkrip Nilai</h2>
            <p class="text-on-surface-variant font-body-sm mt-0.5">{{ $student->name }} — {{ $student->classroom->name }}</p>
        </div>
        <a href="{{ route('admin.transcripts.pdf', $student) }}" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95 shadow-sm">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Unduh PDF
        </a>
    </div>

    <!-- Transcript Card -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm overflow-hidden print:shadow-none print:border-none">
        <!-- Document Header -->
        <div class="bg-gradient-to-r from-primary to-primary-container px-6 py-5 text-center text-white">
            <p class="text-[10px] uppercase tracking-widest opacity-80">Pemerintah Kabupaten Cirebon · Dinas Pendidikan</p>
            <h3 class="text-lg font-bold mt-1">{{ $settings['school_name'] }}</h3>
            <p class="text-[11px] opacity-80 mt-0.5">{{ $settings['school_address'] ?? '' }}</p>
        </div>

        <div class="p-5 sm:p-6">
            <!-- Title -->
            <div class="text-center mb-5">
                <h4 class="text-base font-bold text-on-surface underline underline-offset-4 tracking-wider uppercase">Transkrip Nilai</h4>
                <p class="text-xs text-on-surface-variant mt-1">Tahun Ajaran {{ $settings['academic_year'] }}</p>
            </div>

            <!-- Student Info -->
            <div class="bg-surface rounded-xl p-4 border border-outline-variant/20 mb-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1.5 text-sm">
                    <div class="flex">
                        <span class="text-on-surface-variant w-36 shrink-0">Nama Siswa</span>
                        <span class="font-bold text-on-surface">: {{ strtoupper($student->name) }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-on-surface-variant w-36 shrink-0">Kelas</span>
                        <span class="font-bold text-on-surface">: {{ $student->classroom->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-on-surface-variant w-36 shrink-0">NIS</span>
                        <span class="font-bold text-on-surface">: {{ $student->nis }}</span>
                    </div>
                    <div class="flex">
                        <span class="text-on-surface-variant w-36 shrink-0">Jenis Kelamin</span>
                        <span class="font-bold text-on-surface">: {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                    @if($student->birth_date)
                    <div class="flex">
                        <span class="text-on-surface-variant w-36 shrink-0">Tanggal Lahir</span>
                        <span class="font-bold text-on-surface">: {{ \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Score Table -->
            @php
                $semesters = [1, 2, 3, 4, 5];
                $totalAll = 0;
                $countAll = 0;
            @endphp

            <div class="dense-table-wrapper" style="border: none;">
                <table class="w-full text-left border-collapse dense-table" style="min-width: 600px;">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th>Mata Pelajaran</th>
                            @foreach($semesters as $sem)
                            <th class="text-center w-16">Smt {{ $sem }}</th>
                            @endforeach
                            <th class="text-center w-20">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
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
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="text-center text-on-surface-variant">{{ $idx + 1 }}</td>
                            <td class="font-medium">{{ $subj->name }}</td>
                            @foreach($semesters as $sem)
                            <td class="text-center {{ isset($scores[$sem][$subj->id]) ? 'font-semibold' : 'text-outline' }}">
                                {{ isset($scores[$sem][$subj->id]) ? number_format($scores[$sem][$subj->id]->score, 0) : '-' }}
                            </td>
                            @endforeach
                            <td class="text-center font-bold text-primary">{{ $subjectAvg !== null ? number_format($subjectAvg, 1) : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary/5 border-t-2 border-primary/30">
                            <td colspan="2" class="text-right font-bold text-sm pr-4">Total Nilai</td>
                            @foreach($semesters as $sem)
                            <td></td>
                            @endforeach
                            <td class="text-center font-bold text-primary text-sm">{{ $countAll > 0 ? number_format($totalAll, 1) : '-' }}</td>
                        </tr>
                        <tr class="bg-primary/5">
                            <td colspan="2" class="text-right font-bold text-sm pr-4">Rata-rata Kumulatif</td>
                            @foreach($semesters as $sem)
                            <td></td>
                            @endforeach
                            <td class="text-center font-bold text-primary text-sm">{{ $countAll > 0 ? number_format($totalAll / $countAll, 1) : '-' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Signature -->
            <div class="mt-8 flex justify-end">
                <div class="text-center text-sm">
                    <p class="text-on-surface-variant">Sumber, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="text-on-surface-variant">Kepala {{ $settings['school_name'] }},</p>
                    <p class="font-bold text-on-surface mt-14 underline">{{ $settings['principal_name'] }}</p>
                    <p class="text-xs text-outline">NIP. {{ $settings['principal_nip'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
