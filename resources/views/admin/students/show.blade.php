@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $student->name)

@section('content')
<div class="space-y-space-lg">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:text-primary/80 transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Data Siswa
        </a>
    </div>

    <!-- Student Profile Card -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm overflow-hidden">
        <!-- Header with gradient -->
        <div class="bg-gradient-to-r from-primary to-primary-container h-28 sm:h-36 relative"></div>

        <div class="px-5 sm:px-8 pb-6 sm:pb-8 -mt-14 sm:-mt-16 relative">
            <!-- Photo -->
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 sm:gap-6">
                <div class="shrink-0">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" 
                             class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl object-cover border-4 border-white shadow-md bg-white">
                    @else
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl border-4 border-white shadow-md flex items-center justify-center text-2xl sm:text-3xl font-bold {{ $student->gender === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                            {{ strtoupper(substr($student->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="sm:pb-1">
                    <h2 class="font-h1 text-primary text-xl sm:text-2xl font-bold">{{ $student->name }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ $student->gender === 'L' ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-pink-50 border-pink-200 text-pink-600' }}">
                            {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border bg-primary/5 border-primary/20 text-primary">
                            {{ $student->classroom->name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail Info -->
            <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-surface rounded-xl p-4 border border-outline-variant/30">
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider">NIS</span>
                    <p class="text-sm font-bold text-on-surface mt-1">{{ $student->nis }}</p>
                </div>
                <div class="bg-surface rounded-xl p-4 border border-outline-variant/30">
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider">Kelas</span>
                    <p class="text-sm font-bold text-on-surface mt-1">{{ $student->classroom->name }}</p>
                </div>
                <div class="bg-surface rounded-xl p-4 border border-outline-variant/30">
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider">Jenis Kelamin</span>
                    <p class="text-sm font-bold text-on-surface mt-1">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div class="bg-surface rounded-xl p-4 border border-outline-variant/30">
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider">Tanggal Lahir</span>
                    <p class="text-sm font-bold text-on-surface mt-1">{{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d F Y') : '-' }}</p>
                </div>
                <div class="bg-surface rounded-xl p-4 border border-outline-variant/30 sm:col-span-2">
                    <span class="text-[10px] font-bold text-outline uppercase tracking-wider">Alamat</span>
                    <p class="text-sm font-bold text-on-surface mt-1">{{ $student->address ?: '-' }}</p>
                </div>
            </div>

            <!-- Status -->
            <div class="mt-4 bg-surface rounded-xl p-4 border border-outline-variant/30">
                <span class="text-[10px] font-bold text-outline uppercase tracking-wider">Status Data</span>
                <p class="text-sm font-bold mt-1 {{ $student->status === 'Lengkap' ? 'text-green-700' : 'text-amber-700' }}">
                    {{ $student->status }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
