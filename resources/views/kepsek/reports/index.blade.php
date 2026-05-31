@extends('layouts.app')
@section('title', 'Laporan Rekapitulasi')
@section('content')
<div class="space-y-space-lg" x-data="{ loading: false }" @submit.document="loading = true">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
        <div class="grid grid-cols-2 gap-3">
            @for($i = 0; $i < 4; $i++)
            <div class="h-20 bg-surface-container-low rounded-xl skeleton"></div>
            @endfor
        </div>
        <div class="h-16 bg-surface-container-low rounded-xl skeleton w-full"></div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-4 shadow-sm">
            <div class="space-y-3">
                @for($i = 0; $i < 5; $i++)
                <div class="h-14 bg-surface-container-low rounded-lg skeleton"></div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-space-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-h1 text-primary text-lg sm:text-xl font-bold">Laporan Rekapitulasi</h2>
                <p class="text-on-surface-variant font-body-sm mt-0.5 text-xs sm:text-sm">Data rekap hasil rekomendasi peminatan siswa</p>
            </div>
        </div>

        <!-- Distribution Summary -->
        @php
            $distrib = $results->groupBy('recommended_path')->map->count();
            $totalResults = $results->count();
            $pathThemes = [
                'IPA' => ['color' => 'bg-blue-50 border-blue-200 text-blue-700', 'icon' => 'science'],
                'IPS' => ['color' => 'bg-amber-50 border-amber-200 text-amber-700', 'icon' => 'public'],
                'Bahasa' => ['color' => 'bg-violet-50 border-violet-200 text-violet-700', 'icon' => 'translate'],
                'Vokasi' => ['color' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'icon' => 'construction'],
            ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
            @foreach(['IPA', 'IPS', 'Bahasa', 'Vokasi'] as $path)
            @php $theme = $pathThemes[$path]; $count = $distrib[$path] ?? 0; @endphp
            <div class="{{ $theme['color'] }} border rounded-xl p-3 text-center">
                <div class="flex items-center justify-center gap-1.5 mb-1">
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider">{{ $path }}</span>
                </div>
                <span class="text-xl sm:text-2xl font-extrabold block">{{ $count }}</span>
                <span class="text-[10px] font-medium opacity-70">{{ $totalResults > 0 ? round($count / $totalResults * 100) : 0 }}%</span>
            </div>
            @endforeach
        </div>

        <!-- Filter & Export -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-4 shadow-sm">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                <select name="classroom" class="border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $c)
                    <option value="{{ $c->name }}" {{ request('classroom') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="path" class="border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface">
                    <option value="">Semua Jalur</option>
                    @foreach(['IPA','IPS','Bahasa','Vokasi','RPL','TKJ','MM','AKL','OTKP','TKR'] as $p)
                    <option value="{{ $p }}" {{ request('path') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white py-2.5 rounded-xl text-xs font-bold cursor-pointer transition-all active:scale-95">Filter</button>
                <div class="flex gap-2">
                    <a href="{{ route('kepsek.reports.csv', request()->query()) }}" class="flex-1 py-2.5 border border-primary text-primary text-center rounded-xl text-xs font-bold hover:bg-primary/5 transition-all flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">table_view</span> CSV
                    </a>
                    <a href="{{ route('kepsek.reports.pdf', request()->query()) }}" class="flex-1 py-2.5 bg-primary text-white text-center rounded-xl text-xs font-bold hover:bg-primary/90 transition-all flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">picture_as_pdf</span> PDF
                    </a>
                </div>
            </form>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-2.5">
            @forelse($results as $r)
            @php
                $maxProb = max($r->ipa_probability, $r->ips_probability, $r->bahasa_probability, $r->vokasi_probability);
                $badgeTheme = match($r->recommended_path) {
                    'IPA' => 'bg-blue-50 border-blue-200 text-blue-700',
                    'IPS' => 'bg-amber-50 border-amber-200 text-amber-700',
                    'Bahasa' => 'bg-violet-50 border-violet-200 text-violet-700',
                    'Vokasi' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                    default => 'bg-gray-50 border-gray-200 text-gray-700',
                };
            @endphp
            <div class="mobile-card-item">
                <div class="flex justify-between items-start">
                    <div class="min-w-0">
                        <p class="font-bold text-primary text-sm truncate">{{ $r->student->name }}</p>
                        <p class="text-[11px] text-outline mt-0.5">NIS: {{ $r->student->nis }} • {{ $r->student->classroom->name }}</p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $badgeTheme }}">{{ $r->recommended_path }}</span>
                        @if($r->vocational_major)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700">{{ $r->vocational_major }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between mt-1.5 pt-1.5 border-t border-outline-variant/20">
                    <span class="text-xs font-extrabold text-primary">{{ round($maxProb * 100, 1) }}% kecocokan</span>
                    <p class="text-[10px] text-on-surface-variant line-clamp-1 max-w-[50%] text-right">{{ $r->dominant_factor }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-on-surface-variant">
                <span class="material-symbols-outlined text-[48px] text-outline/30 block mb-2">assessment</span>
                Belum ada data laporan.
            </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block dense-table-wrapper shadow-sm">
            <table class="w-full text-left border-collapse dense-table" style="min-width: 750px;">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jalur Pendidikan</th>
                        <th>Kecocokan</th>
                        <th>Alasan Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse($results as $r)
                    @php
                        $maxProb = max($r->ipa_probability, $r->ips_probability, $r->bahasa_probability, $r->vokasi_probability);
                        $badgeTheme = match($r->recommended_path) {
                            'IPA' => 'bg-blue-50 border-blue-200 text-blue-700',
                            'IPS' => 'bg-amber-50 border-amber-200 text-amber-700',
                            'Bahasa' => 'bg-violet-50 border-violet-200 text-violet-700',
                            'Vokasi' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                            default => 'bg-gray-50 border-gray-200 text-gray-700',
                        };
                    @endphp
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="font-bold text-primary">{{ $r->student->nis }}</td>
                        <td class="font-semibold">{{ $r->student->name }}</td>
                        <td>{{ $r->student->classroom->name }}</td>
                        <td>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $badgeTheme }}">{{ $r->recommended_path }}</span>
                            @if($r->vocational_major)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700 ml-1">{{ $r->vocational_major }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm font-extrabold text-primary">{{ round($maxProb * 100, 1) }}%</span>
                        </td>
                        <td class="text-[11px] text-on-surface-variant max-w-[200px]">
                            <span class="line-clamp-2">{{ $r->dominant_factor }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] text-outline/30 block mb-2">assessment</span>
                            Belum ada data laporan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
