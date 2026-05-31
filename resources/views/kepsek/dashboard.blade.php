@extends('layouts.app')
@section('title', 'Dashboard Kepala Sekolah')
@section('content')
<div class="space-y-space-lg" x-data="{ loading: false }">
    
    <!-- Skeleton screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
            @for($i = 0; $i < 3; $i++)
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-md shadow-sm h-28 space-y-3">
                <div class="h-3 bg-surface-container-high rounded skeleton w-1/2"></div>
                <div class="h-8 bg-surface-container-high rounded skeleton w-1/3"></div>
            </div>
            @endfor
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-4 shadow-sm h-80">
            <div class="h-6 bg-surface-container-high rounded skeleton w-1/4"></div>
            <div class="h-56 bg-surface-container-low rounded skeleton w-full"></div>
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-space-lg">
        <div>
            <h2 class="font-h1 text-primary text-lg sm:text-xl font-bold">Dashboard Kepala Sekolah</h2>
            <p class="text-on-surface-variant font-body-sm mt-0.5 text-xs sm:text-sm">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan data peminatan siswa.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
            <div class="bg-surface-container-lowest border border-outline-variant/50 hover:border-outline-variant/80 rounded-2xl p-5 flex justify-between items-center shadow-xs transition-all duration-200">
                <div>
                    <span class="block text-on-surface-variant/80 font-label-caps text-[10px] uppercase font-extrabold tracking-widest">Total Siswa</span>
                    <span class="font-h1 text-primary text-3xl font-extrabold block mt-2.5 tracking-tight">{{ $totalStudents }}</span>
                    <span class="text-on-surface-variant text-[11px] font-medium block mt-1">Siswa terdaftar</span>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl ring-1 ring-blue-100 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">group</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest border border-outline-variant/50 hover:border-outline-variant/80 rounded-2xl p-5 flex justify-between items-center shadow-xs transition-all duration-200">
                <div>
                    <span class="block text-on-surface-variant/80 font-label-caps text-[10px] uppercase font-extrabold tracking-widest">Terklasifikasi</span>
                    <span class="font-h1 text-primary text-3xl font-extrabold block mt-2.5 tracking-tight">{{ $classifiedCount }}</span>
                    <span class="text-on-surface-variant text-[11px] font-medium block mt-1">{{ $totalStudents > 0 ? round($classifiedCount / $totalStudents * 100) : 0 }}% selesai diproses</span>
                </div>
                <div class="p-3 bg-violet-50 text-violet-600 rounded-2xl ring-1 ring-violet-100 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">settings_suggest</span>
                </div>
            </div>

            <!-- Accuracy Card (Premium) -->
            <div class="bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl p-5 flex justify-between items-center shadow-md relative overflow-hidden ring-1 ring-black/[0.05]">
                <div class="absolute right-0 bottom-0 w-24 h-24 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                <div class="z-10">
                    <span class="block text-white/80 font-label-caps text-[10px] uppercase font-extrabold tracking-widest">Ketepatan Sistem</span>
                    <span class="font-h1 text-white text-3xl font-extrabold block mt-2.5 tracking-tight">{{ $modelStats['accuracy'] }}%</span>
                    <span class="text-white/70 text-[11px] font-medium block mt-1">
                        @php
                            $acc = $modelStats['accuracy'];
                            $status = match(true) {
                                $acc >= 90 => 'Sangat Baik',
                                $acc >= 80 => 'Baik',
                                $acc >= 60 => 'Cukup',
                                default => 'Perlu Peningkatan'
                            };
                        @endphp
                        Status: {{ $status }} • Versi {{ $modelStats['version'] }}
                    </span>
                </div>
                <div class="p-3 bg-white/15 text-white rounded-2xl ring-1 ring-white/10 flex items-center justify-center shrink-0 z-10">
                    <span class="material-symbols-outlined text-[26px]">psychology</span>
                </div>
            </div>
        </div>

        <!-- Distribution Bar Chart -->
        @php
            $total = array_sum($distributions);
            $maxCount = max(1, max($distributions['IPA'] ?? 0, $distributions['IPS'] ?? 0, $distributions['Bahasa'] ?? 0, $distributions['Vokasi'] ?? 0));
            $pathThemes = [
                'IPA' => ['color' => 'bg-blue-500 hover:bg-blue-600', 'text' => 'text-blue-600', 'bg' => 'bg-blue-50/50'],
                'IPS' => ['color' => 'bg-amber-500 hover:bg-amber-600', 'text' => 'text-amber-600', 'bg' => 'bg-amber-50/50'],
                'Bahasa' => ['color' => 'bg-violet-500 hover:bg-violet-600', 'text' => 'text-violet-600', 'bg' => 'bg-violet-50/50'],
                'Vokasi' => ['color' => 'bg-emerald-500 hover:bg-emerald-600', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50/50'],
            ];
        @endphp
        <div class="bg-surface-container-lowest border border-outline-variant/50 rounded-2xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-5 gap-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">bar_chart</span>
                    <h3 class="font-h2 font-bold text-sm sm:text-base text-primary">Distribusi Peminatan Siswa</h3>
                </div>
                <span class="text-[10px] text-on-surface-variant/80 font-extrabold uppercase tracking-wider bg-surface-container-low border border-outline-variant/30 px-3 py-1 rounded-full">
                    {{ $classifiedCount }} siswa
                </span>
            </div>

            <div class="w-full h-52 sm:h-64 border-b border-l border-outline-variant/50 flex items-end justify-around pb-1 relative mt-6 sm:mt-8">
                <!-- Helper dashed grid lines -->
                <div class="absolute inset-x-0 top-0 border-t border-dashed border-outline-variant/20"></div>
                <div class="absolute inset-x-0 top-1/3 border-t border-dashed border-outline-variant/20"></div>
                <div class="absolute inset-x-0 top-2/3 border-t border-dashed border-outline-variant/20"></div>

                @foreach(['IPA', 'IPS', 'Bahasa', 'Vokasi'] as $cat)
                    @php 
                        $count = $distributions[$cat] ?? 0; 
                        $height = max(12, ($count / $maxCount) * 80); 
                        $pct = $total > 0 ? round($count / $total * 100) : 0;
                        $theme = $pathThemes[$cat];
                    @endphp
                    <div class="flex-1 max-w-[60px] sm:max-w-[100px] flex flex-col items-center z-10 group mx-1 sm:mx-2">
                        <div class="w-full {{ $theme['color'] }} rounded-t-xl transition-all duration-300 flex items-end justify-center shadow-sm relative group-hover:scale-y-[1.02] origin-bottom cursor-pointer" style="height: {{ $height }}%">
                            <div class="absolute -top-7 text-[10px] sm:text-xs font-extrabold {{ $theme['text'] }} bg-white border border-outline-variant/40 rounded-md px-1.5 sm:px-2 py-0.5 shadow-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                                {{ $count }} ({{ $pct }}%)
                            </div>
                            <div class="text-[10px] sm:text-[11px] font-extrabold text-white text-center pb-2 tracking-wide">{{ $count }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="flex justify-around text-center pt-3 sm:pt-4 px-1 sm:px-2">
                @foreach(['IPA', 'IPS', 'Bahasa', 'Vokasi'] as $cat)
                    @php $theme = $pathThemes[$cat]; @endphp
                    <div class="flex-1 max-w-[60px] sm:max-w-[100px] mx-1 sm:mx-2 flex flex-col items-center">
                        <span class="text-[10px] sm:text-xs font-extrabold {{ $theme['text'] }} uppercase tracking-wider">{{ $cat }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-surface-container-lowest border border-outline-variant/50 rounded-2xl p-4 sm:p-6 shadow-sm">
            <h3 class="font-h2 text-primary font-bold mb-4 flex items-center gap-2 text-sm sm:text-base">
                <span class="material-symbols-outlined text-[20px]">quick_reference_all</span>
                Aksi Cepat
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <a href="{{ route('kepsek.reports.index') }}" class="flex items-center gap-3 p-3 sm:p-4 border border-outline-variant/50 hover:border-primary/30 rounded-xl transition-all hover:bg-primary/5 group">
                    <div class="p-2 bg-primary/5 text-primary rounded-xl group-hover:bg-primary/10 transition-colors shrink-0">
                        <span class="material-symbols-outlined text-[20px] sm:text-[22px]">assessment</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-sm font-bold text-primary block">Lihat Laporan Rekap</span>
                        <span class="text-[11px] text-on-surface-variant font-medium truncate block">Lihat dan export laporan peminatan siswa</span>
                    </div>
                </a>
                <a href="{{ route('kepsek.reports.pdf', request()->query()) }}" class="flex items-center gap-3 p-3 sm:p-4 border border-outline-variant/50 hover:border-primary/30 rounded-xl transition-all hover:bg-primary/5 group">
                    <div class="p-2 bg-primary/5 text-primary rounded-xl group-hover:bg-primary/10 transition-colors shrink-0">
                        <span class="material-symbols-outlined text-[20px] sm:text-[22px]">picture_as_pdf</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-sm font-bold text-primary block">Unduh Laporan PDF</span>
                        <span class="text-[11px] text-on-surface-variant font-medium truncate block">Download rekap lengkap dalam format PDF</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
