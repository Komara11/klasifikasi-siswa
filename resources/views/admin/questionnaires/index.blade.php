@extends('layouts.app')
@section('title', 'Kuesioner Minat')
@section('content')
<div class="space-y-space-lg" x-data="{ loading: false }">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
        <div class="h-16 bg-surface-container-low rounded-xl skeleton w-full"></div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-4 shadow-sm">
            <div class="h-6 bg-surface-container-high rounded skeleton w-1/4"></div>
            @for($s = 0; $s < 3; $s++)
            <div class="border border-outline-variant/30 rounded-lg p-4 space-y-3">
                <div class="h-4 bg-surface-container-high rounded skeleton w-3/4"></div>
                <div class="h-3 bg-surface-container-low rounded skeleton w-16"></div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-surface-container-low skeleton"></div>
                    <div class="w-8 h-8 rounded-full bg-surface-container-low skeleton"></div>
                    <div class="w-8 h-8 rounded-full bg-surface-container-low skeleton"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-space-lg">
        <h2 class="font-h1 text-primary text-xl font-bold">Input Kuesioner Minat Siswa</h2>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.questionnaires.index') }}" class="flex gap-3" x-data>
                <div class="flex-1">
                    <x-custom-select 
                        name="student_id" 
                        :options="$students->map(fn($s) => ['value' => $s->id, 'label' => $s->nis . ' - ' . $s->name])->toArray()" 
                        :selected="request('student_id', '')" 
                        placeholder="-- Pilih Siswa --"
                        onchange="if(this.selected) { $el.closest('form').submit(); }"
                        :searchable="true"
                    />
                </div>
            </form>
        </div>

        @if($selectedStudent)
        <form method="POST" action="{{ route('admin.questionnaires.store') }}" class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-lg shadow-sm" @submit="loading = true">
            @csrf
            <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
            <h3 class="font-h2 text-primary font-bold mb-4">Kuesioner: {{ $selectedStudent->name }}</h3>
            <p class="text-on-surface-variant text-xs mb-6">Berikan penilaian 1 (Sangat Tidak Setuju) hingga 5 (Sangat Setuju)</p>

            <div class="space-y-4">
                @foreach($questions as $q)
                <div class="border border-outline-variant/50 rounded-lg p-4">
                    <p class="text-sm font-semibold mb-3">{{ $loop->iteration }}. {{ $q->question }}</p>
                    <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full mb-2 inline-block">Kategori: {{ $q->category }}</span>
                    <div class="flex items-center gap-4 mt-2">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $i }}" {{ ($answers[$q->id] ?? 0) == $i ? 'checked' : '' }}
                                class="text-primary focus:ring-primary" required>
                            <span class="text-xs font-bold">{{ $i }}</span>
                        </label>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl text-xs font-bold cursor-pointer transition-all active:scale-95">
                    Simpan Jawaban Kuesioner
                </button>
            </div>
        </form>
        @else
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-10 text-center shadow-sm">
            <span class="material-symbols-outlined text-outline text-[48px]">quiz</span>
            <p class="text-on-surface-variant mt-2">Pilih siswa terlebih dahulu.</p>
        </div>
        @endif
    </div>
</div>
@endsection
