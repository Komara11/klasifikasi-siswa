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
        <h2 class="font-h1 text-primary text-xl font-bold">Input Nilai Akademik</h2>

        <!-- Student Selector -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.scores.index') }}" class="flex flex-col sm:flex-row gap-3" x-data>
                <div class="flex-1">
                    <x-custom-select 
                        name="student_id" 
                        :options="$students->map(fn($s) => ['value' => $s->id, 'label' => $s->nis . ' - ' . $s->name . ' (' . $s->classroom->name . ')'])->toArray()" 
                        :selected="request('student_id', '')" 
                        placeholder="-- Pilih Siswa --"
                        onchange="if(this.selected) { $el.closest('form').submit(); }"
                        :searchable="true"
                    />
                </div>
            </form>
        </div>

        @if($selectedStudent)
        <!-- Score Input Form -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-lg shadow-sm">
            <h3 class="font-h2 text-primary font-bold mb-4">Nilai Semester: {{ $selectedStudent->name }}</h3>

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
        @else
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-10 text-center shadow-sm">
            <span class="material-symbols-outlined text-outline text-[48px]">edit_note</span>
            <p class="text-on-surface-variant mt-2">Pilih siswa terlebih dahulu untuk menginput nilai.</p>
        </div>
        @endif
    </div>
</div>
@endsection
