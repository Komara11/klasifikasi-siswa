@extends('layouts.app')
@section('title', 'Input Nilai Akademik')
@section('content')
<div class="space-y-space-lg" x-data="{ loading: false }">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
        <div class="h-16 bg-surface-container-low rounded-xl skeleton w-full"></div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-6 shadow-sm">
            <div class="h-6 bg-surface-container-high rounded skeleton w-1/4"></div>
            @for($s = 0; $s < 2; $s++)
            <div class="border border-outline-variant/30 rounded-lg p-4 space-y-4">
                <div class="h-4 bg-surface-container-high rounded skeleton w-24"></div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @for($i = 0; $i < 6; $i++)
                    <div class="space-y-2">
                        <div class="h-3 bg-surface-container-low rounded skeleton w-12"></div>
                        <div class="h-10 bg-surface-container-low rounded-xl skeleton w-full"></div>
                    </div>
                    @endfor
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-space-lg">
        <!-- Back button when student selected -->
        @if($selectedStudent)
        <div>
            <a href="{{ route('admin.scores.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-primary/80 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Daftar Siswa
            </a>
        </div>
        @endif

        <h2 class="font-h1 text-primary text-xl font-bold">Input Nilai Akademik</h2>

        @if(!$selectedStudent)



        <!-- Search & Filter -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.scores.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS siswa..."
                    class="flex-1 border border-outline-variant bg-surface rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"/>
                <div class="w-full sm:w-48">
                    <x-custom-select 
                        name="classroom" 
                        :options="$classrooms->map(fn($c) => ['value' => $c->name, 'label' => $c->name])->toArray()" 
                        :selected="request('classroom', '')" 
                        placeholder="Semua Kelas"
                    />
                </div>
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white px-4 py-2 rounded-lg text-xs font-bold cursor-pointer transition-colors">Cari</button>
            </form>
        </div>

        <!-- Mobile Student List -->
        <div class="sm:hidden space-y-2.5">
            @forelse($students as $student)
            <div class="mobile-card-item">
                <div class="flex gap-3 items-start">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-primary text-sm truncate">{{ $student->name }}</p>
                        <p class="text-[11px] text-outline mt-0.5">NIS: {{ $student->nis }} • {{ $student->classroom->name }}</p>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-t border-outline-variant/20">
                    <a href="{{ route('admin.scores.index', ['student_id' => $student->id]) }}" class="w-full flex items-center justify-center gap-1 px-3 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-[11px] font-bold transition-all">
                        <span class="material-symbols-outlined text-[14px]">edit_note</span> Input Nilai
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-6 text-on-surface-variant text-sm">
                {{ request('search') || request('classroom') ? 'Tidak ada siswa yang cocok dengan pencarian.' : 'Belum ada data siswa.' }}
            </div>
            @endforelse
        </div>

        <!-- Desktop Student List -->
        <div class="hidden sm:block bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
            <div class="dense-table-wrapper" style="border: none; border-radius: 0;">
                <table class="w-full text-left border-collapse dense-table" style="min-width: 500px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30">
                        @forelse($students as $student)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="text-on-surface-variant">{{ $loop->iteration }}</td>
                            <td class="font-bold text-primary">{{ $student->nis }}</td>
                            <td class="font-semibold">{{ $student->name }}</td>
                            <td>{{ $student->classroom->name }}</td>
                            <td>
                                <a href="{{ route('admin.scores.index', ['student_id' => $student->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-lg text-[11px] font-bold transition-all" title="Input Nilai">
                                    <span class="material-symbols-outlined text-[14px]">edit_note</span> Input Nilai
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-on-surface-variant">
                                {{ request('search') || request('classroom') ? 'Tidak ada siswa yang cocok dengan pencarian.' : 'Belum ada data siswa.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($selectedStudent)
        <!-- Score Input Form -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-lg shadow-sm" x-data="{ 
            viewMode: {{ session('show_table', false) ? 'true' : 'false' }}, 
            activeSubject: {{ session('next_subject', $subjects->first()->id ?? 0) }},
            showImport: false
        }">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                <h3 class="font-h2 text-primary font-bold">Data Nilai Siswa: {{ $selectedStudent->name }} <span class="text-outline font-normal text-sm">({{ $selectedStudent->classroom->name }})</span></h3>
                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('admin.transcripts.pdf', $selectedStudent) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span> Unduh PDF
                    </a>
                    <button type="button" @click="showImport = !showImport" class="flex-1 sm:flex-none bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">upload_file</span> Import Excel/CSV
                    </button>
                    <button type="button" @click="viewMode = !viewMode" class="flex-1 sm:flex-none bg-primary/10 text-primary hover:bg-primary/20 px-4 py-2.5 rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]" x-text="viewMode ? 'edit_note' : 'table_chart'"></span> 
                        <span x-text="viewMode ? 'Input / Edit Nilai' : 'Lihat Tabel Nilai'"></span>
                    </button>
                </div>
            </div>

            <!-- Import CSV Panel -->
            <div x-show="showImport" x-cloak x-transition class="mb-6 border border-emerald-200 bg-emerald-50/50 rounded-xl p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-emerald-800">Import Nilai dari Excel/CSV</h4>
                        <p class="text-[11px] text-emerald-600">Upload file .xlsx atau .csv untuk mengisi nilai otomatis.</p>
                    </div>
                </div>

                <div class="bg-white border border-emerald-200 rounded-xl p-5">
                    <p class="text-xs font-bold text-emerald-700 mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">file_upload</span>
                        Upload File Excel/CSV
                    </p>
                    <form method="POST" action="{{ route('admin.scores.import') }}" enctype="multipart/form-data" @submit="loading = true">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                        <input type="file" name="import_file" accept=".csv,.txt,.xlsx,.xls" required
                            class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 file:cursor-pointer border border-gray-200 rounded-lg cursor-pointer mb-3"/>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-6 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg text-xs font-bold transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">cloud_upload</span> Mulai Import Nilai
                        </button>
                    </form>
                </div>
            </div>
            <div x-show="viewMode" x-cloak x-transition>
                @php
                    $semesters = [1, 2, 3, 4, 5];
                    $totalAll = 0;
                    $countAll = 0;
                    $subjectData = [];
                    foreach ($subjects as $idx => $subj) {
                        $subjectTotal = 0;
                        $subjectCount = 0;
                        foreach ($semesters as $sem) {
                            $val = $scores[$sem][$subj->id]->score ?? null;
                            if ($val !== null) {
                                $subjectTotal += $val;
                                $subjectCount++;
                            }
                        }
                        $avg = $subjectCount > 0 ? round($subjectTotal / $subjectCount, 1) : null;
                        if ($avg !== null) {
                            $totalAll += $avg;
                            $countAll++;
                        }
                        $subjectData[] = ['subj' => $subj, 'avg' => $avg, 'idx' => $idx];
                    }
                @endphp

                {{-- Scrollable Table for all screens --}}
                <div class="border border-outline-variant/30 rounded-xl overflow-x-auto mb-2 -mx-1">
                    <table class="w-full text-left border-collapse dense-table" style="min-width: 580px;">
                        <thead>
                            <tr class="bg-surface-container-low">
                                <th class="w-10 text-center">No</th>
                                <th>Mata Pelajaran</th>
                                @foreach($semesters as $sem)
                                <th class="text-center w-14">Smt {{ $sem }}</th>
                                @endforeach
                                <th class="text-center w-16">Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/20">
                            @foreach($subjectData as $item)
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="text-center text-on-surface-variant">{{ $item['idx'] + 1 }}</td>
                                <td class="font-medium whitespace-nowrap">{{ $item['subj']->name }}</td>
                                @foreach($semesters as $sem)
                                <td class="text-center {{ isset($scores[$sem][$item['subj']->id]) ? 'font-semibold' : 'text-outline' }}">
                                    {{ isset($scores[$sem][$item['subj']->id]) ? number_format($scores[$sem][$item['subj']->id]->score, 0) : '-' }}
                                </td>
                                @endforeach
                                <td class="text-center font-bold text-primary">{{ $item['avg'] !== null ? number_format($item['avg'], 1) : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary/5 border-t border-primary/20">
                                <td colspan="2" class="text-right font-bold text-sm pr-4 py-3">Total Nilai</td>
                                @foreach($semesters as $sem)
                                <td></td>
                                @endforeach
                                <td class="text-center font-bold text-primary text-sm py-3">{{ $countAll > 0 ? number_format($totalAll, 1) : '-' }}</td>
                            </tr>
                            <tr class="bg-primary/5 border-t border-primary/10">
                                <td colspan="2" class="text-right font-bold text-sm pr-4 py-3">Rata-rata Kumulatif</td>
                                @foreach($semesters as $sem)
                                <td></td>
                                @endforeach
                                <td class="text-center font-bold text-primary text-sm py-3">{{ $countAll > 0 ? number_format($totalAll / $countAll, 1) : '-' }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- EDIT MODE: Forms -->
            <div x-show="!viewMode" x-cloak x-transition>

            <!-- Subject Selection Dropdown -->
            <div class="mb-6 border-b border-outline-variant/50 pb-6">
                <label class="block text-sm font-bold mb-2 text-on-surface-variant">Pilih Mata Pelajaran untuk Diinput:</label>
                <div class="relative">
                    <select x-model="activeSubject" class="w-full sm:w-1/2 appearance-none border border-primary/40 bg-primary/5 text-primary rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer transition-all">
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-primary pointer-events-none sm:right-[calc(50%+12px)]">expand_more</span>
                </div>
                <p class="text-[11px] text-outline mt-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">info</span> Pilih mata pelajaran lalu isi nilainya dari Semester 1 sampai 5.
                </p>
            </div>

            <!-- Subject Forms -->
            @foreach($subjects as $subj)
            <form method="POST" action="{{ route('admin.scores.store') }}" 
                  x-show="activeSubject == {{ $subj->id }}" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 translate-y-2"
                  x-transition:enter-end="opacity-100 translate-y-0"
                  class="mb-2" 
                  @submit="loading = true">
                @csrf
                <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                <input type="hidden" name="subject_id" value="{{ $subj->id }}">
                
                <div class="border border-outline-variant/50 rounded-xl p-5 sm:p-6 bg-surface">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">menu_book</span>
                        </div>
                        <h4 class="font-bold text-base text-primary">{{ $subj->name }}</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                        @for($sem = 1; $sem <= 5; $sem++)
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase">Semester {{ $sem }}</label>
                            <input type="number" name="scores[{{ $sem }}]" min="0" max="100" step="0.01"
                                value="{{ $scores[$sem][$subj->id]->score ?? '' }}"
                                class="w-full border border-outline-variant rounded-lg px-3 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none bg-white transition-all placeholder:text-outline/40"
                                placeholder="Nilai..."/>
                        </div>
                        @endfor
                    </div>
                    
                    <div class="mt-6 pt-5 border-t border-outline-variant/30 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl text-sm font-bold cursor-pointer transition-all active:scale-95 flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">save</span> Simpan Nilai
                        </button>
                    </div>
                </div>
            </form>
            @endforeach
            </div> <!-- End of Edit Mode -->
        </div>
        @endif
    </div>
</div>
@endsection
