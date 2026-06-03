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
                                <a href="{{ route('admin.scores.index', ['student_id' => $student->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary hover:bg-primary/90 text-white rounded-lg text-[11px] font-bold transition-all">
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
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-lg shadow-sm">
            <h3 class="font-h2 text-primary font-bold mb-4">Nilai Semester: {{ $selectedStudent->name }} <span class="text-outline font-normal text-sm">({{ $selectedStudent->classroom->name }})</span></h3>

            @for($sem = 1; $sem <= 5; $sem++)
            <form method="POST" action="{{ route('admin.scores.store') }}" class="mb-6" @submit="loading = true">
                @csrf
                <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
                <input type="hidden" name="semester" value="{{ $sem }}">
                <div class="border border-outline-variant/50 rounded-lg p-4 mb-2">
                    <h4 class="font-bold text-sm text-primary mb-3">Semester {{ $sem }}</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        @foreach($subjects as $subj)
                        <div>
                            <label class="block text-[10px] font-bold text-on-surface-variant mb-1 uppercase">{{ $subj->name }}</label>
                            <input type="number" name="scores[{{ $subj->id }}]" min="0" max="100" step="0.01"
                                value="{{ $scores[$sem][$subj->id]->score ?? '' }}"
                                class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm text-center focus:ring-1 focus:ring-primary outline-none bg-surface"
                                placeholder="0-100"/>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-xs font-bold cursor-pointer transition-all active:scale-95">
                            Simpan Semester {{ $sem }}
                        </button>
                    </div>
                </div>
            </form>
            @endfor
        </div>
        @endif
    </div>
</div>
@endsection
