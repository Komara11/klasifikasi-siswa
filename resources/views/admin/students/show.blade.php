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

    <!-- Page Title -->
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-[22px]">person</span>
        <div>
            <h2 class="font-h1 text-primary text-lg font-bold leading-tight">Profil Lengkap Siswa</h2>
            <p class="text-on-surface-variant text-xs mt-0.5">Rincian identitas siswa terdaftar di {{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm overflow-hidden">
        <!-- Compact Header -->
        <div class="bg-gradient-to-r from-primary to-primary-container h-16 sm:h-20"></div>

        <div class="px-5 sm:px-6 pb-5 sm:pb-6 -mt-10 sm:-mt-12 relative">
            <!-- Photo + Name Row -->
            <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-5">
                <!-- Photo -->
                <div class="shrink-0">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}"
                             class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border-[3px] border-white shadow-md bg-white">
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl border-[3px] border-white shadow-md flex items-center justify-center text-xl sm:text-2xl font-bold {{ $student->gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                            <span class="material-symbols-outlined text-[36px] sm:text-[42px]">person</span>
                        </div>
                    @endif
                </div>
                <!-- Name + Badges -->
                <div class="sm:pb-0.5 flex-1 min-w-0">
                    <h3 class="font-h1 text-primary text-lg sm:text-xl font-bold truncate">{{ $student->name }}</h3>
                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $student->gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                            {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary/5 text-primary">
                            {{ $student->classroom->name }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $student->status === 'Lengkap' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $student->status === 'Lengkap' ? 'Aktif' : 'Data Belum Lengkap' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="mt-5">
                <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px]">badge</span>
                    Informasi Identitas
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-3 bg-surface rounded-xl p-4 border border-outline-variant/20">
                    <!-- NIS -->
                    <div>
                        <span class="text-[9px] font-bold text-outline uppercase tracking-wider block">Nomor Induk Siswa (NIS)</span>
                        <p class="text-sm font-bold text-on-surface mt-0.5">{{ $student->nis }}</p>
                    </div>
                    <!-- Kelas -->
                    <div>
                        <span class="text-[9px] font-bold text-outline uppercase tracking-wider block">Kelas</span>
                        <p class="text-sm font-bold text-on-surface mt-0.5">{{ $student->classroom->name }}</p>
                    </div>
                    <!-- Jenis Kelamin -->
                    <div>
                        <span class="text-[9px] font-bold text-outline uppercase tracking-wider block">Jenis Kelamin</span>
                        <p class="text-sm font-bold text-on-surface mt-0.5">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <!-- Tanggal Lahir -->
                    <div>
                        <span class="text-[9px] font-bold text-outline uppercase tracking-wider block">Tanggal Lahir</span>
                        <p class="text-sm font-bold text-on-surface mt-0.5">{{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d F Y') : '-' }}</p>
                    </div>
                </div>

                <!-- Alamat (full width) -->
                <div class="mt-3 bg-surface rounded-xl p-4 border border-outline-variant/20">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-outline mt-0.5">location_on</span>
                        <div>
                            <span class="text-[9px] font-bold text-outline uppercase tracking-wider block">Alamat Domisili</span>
                            <p class="text-sm font-bold text-on-surface mt-0.5">{{ $student->address ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-5 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2">
                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 border border-outline-variant rounded-lg text-xs font-bold text-on-surface-variant hover:bg-surface transition-colors">
                    Kembali
                </a>
                <a href="{{ route('admin.students.index', ['edit' => $student->id]) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-xs font-bold transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[14px]">edit</span>
                    Edit Profil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
