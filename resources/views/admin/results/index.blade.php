@extends('layouts.app')
@section('title', 'Hasil Rekomendasi Peminatan')
@section('content')
<div class="space-y-space-lg" x-data="{ loading: false, expandedRow: null }" @submit.document="loading = true">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
        <div class="grid grid-cols-2 gap-3">
            @for($i = 0; $i < 5; $i++)
            <div class="h-20 bg-surface-container-low rounded-xl skeleton"></div>
            @endfor
        </div>
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
                <h2 class="font-h1 text-primary text-lg sm:text-xl font-bold">Hasil Rekomendasi Peminatan</h2>
                <p class="text-on-surface-variant font-body-sm mt-0.5 text-xs sm:text-sm">Daftar siswa beserta rekomendasi jalur pendidikan lanjutan</p>
            </div>
        </div>

        <!-- Distribution Summary Cards -->
        @php
            $pathThemes = [
                'IPA' => ['color' => 'bg-blue-50 border-blue-200 text-blue-700', 'icon' => 'science', 'iconBg' => 'bg-blue-100 text-blue-600'],
                'IPS' => ['color' => 'bg-amber-50 border-amber-200 text-amber-700', 'icon' => 'public', 'iconBg' => 'bg-amber-100 text-amber-600'],
                'Bahasa' => ['color' => 'bg-violet-50 border-violet-200 text-violet-700', 'icon' => 'translate', 'iconBg' => 'bg-violet-100 text-violet-600'],
                'Vokasi' => ['color' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'icon' => 'construction', 'iconBg' => 'bg-emerald-100 text-emerald-600'],
            ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-4 shadow-sm col-span-2 sm:col-span-1">
                <span class="text-[10px] text-outline font-bold block uppercase tracking-wider">Total</span>
                <span class="text-xl sm:text-2xl font-extrabold text-primary block mt-1">{{ $stats['total'] }}</span>
                <span class="text-[10px] text-outline font-medium">siswa terklasifikasi</span>
            </div>
            @foreach(['IPA', 'IPS', 'Bahasa', 'Vokasi'] as $path)
            <div class="{{ $pathThemes[$path]['color'] }} border rounded-xl p-3 sm:p-4 shadow-sm">
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="material-symbols-outlined text-[14px] sm:text-[16px]">{{ $pathThemes[$path]['icon'] }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider">{{ $path }}</span>
                </div>
                <span class="text-xl sm:text-2xl font-extrabold block">{{ $stats[$path] ?? 0 }}</span>
                <span class="text-[10px] font-medium opacity-75">{{ $stats['total'] > 0 ? round(($stats[$path] ?? 0) / $stats['total'] * 100) : 0 }}% dari total</span>
            </div>
            @endforeach
        </div>

        <!-- Filters -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-4 shadow-sm">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline/50 text-[20px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..."
                        class="w-full border border-outline-variant rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                </div>
                <select name="classroom" class="border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ request('classroom') == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                    @endforeach
                </select>
                <select name="path" class="border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface">
                    <option value="">Semua Jalur</option>
                    @foreach(['IPA', 'IPS', 'Bahasa', 'Vokasi'] as $p)
                    <option value="{{ $p }}" {{ request('path') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-primary hover:bg-primary/90 text-white py-2.5 rounded-xl text-xs font-bold cursor-pointer transition-all active:scale-95">Filter</button>
                    <a href="{{ route('admin.results.index') }}" class="px-4 py-2.5 border border-outline-variant text-on-surface-variant rounded-xl text-xs font-bold hover:bg-surface-container-low transition-all flex items-center justify-center">Reset</a>
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
            <div class="mobile-card-item" x-data="{ open: false }">
                <div class="flex justify-between items-start cursor-pointer" @click="open = !open">
                    <div class="min-w-0">
                        <p class="font-bold text-primary text-sm truncate">{{ $r->student->name }}</p>
                        <p class="text-[11px] text-outline mt-0.5">NIS: {{ $r->student->nis }} • {{ $r->student->classroom->name ?? '-' }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $badgeTheme }}">{{ $r->recommended_path }}</span>
                        <span class="material-symbols-outlined text-primary/60 text-[18px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-xs font-extrabold text-primary">{{ round($maxProb * 100, 1) }}% kecocokan</span>
                    @if($r->vocational_major)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700">{{ $r->vocational_major }}</span>
                    @endif
                </div>
                <!-- Expandable Detail -->
                <div x-show="open" x-transition class="mt-3 pt-3 border-t border-outline-variant/30 space-y-3">
                    <div class="space-y-2">
                        <h4 class="text-[10px] font-extrabold text-primary uppercase tracking-wider">Kecocokan Tiap Jalur</h4>
                        @foreach([
                            'IPA' => ['prob' => $r->ipa_probability, 'color' => 'bg-blue-500', 'text' => 'text-blue-700'],
                            'IPS' => ['prob' => $r->ips_probability, 'color' => 'bg-amber-500', 'text' => 'text-amber-700'],
                            'Bahasa' => ['prob' => $r->bahasa_probability, 'color' => 'bg-violet-500', 'text' => 'text-violet-700'],
                            'Vokasi' => ['prob' => $r->vokasi_probability, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
                        ] as $label => $item)
                        <div>
                            <div class="flex justify-between items-center text-[11px] mb-0.5">
                                <span class="font-bold {{ $item['text'] }}">{{ $label }}</span>
                                <span class="font-extrabold {{ $item['text'] }}">{{ round($item['prob'] * 100, 1) }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                                <div class="h-full {{ $item['color'] }} rounded-full" style="width: {{ round($item['prob'] * 100) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div>
                        <h4 class="text-[10px] font-extrabold text-primary uppercase tracking-wider mb-1">Penjelasan</h4>
                        <p class="text-[11px] text-on-surface-variant leading-relaxed">{{ $r->dominant_factor }}</p>
                    </div>
                    @if($r->recommended_path === 'Vokasi' && $r->vocational_probabilities)
                    <div>
                        <h4 class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-wider mb-1">Detail Jurusan SMK</h4>
                        @php $majorLabels = ['RPL' => 'Rekayasa Perangkat Lunak', 'TKJ' => 'Teknik Komputer & Jaringan', 'MM' => 'Multimedia', 'AKL' => 'Akuntansi & Keuangan', 'OTKP' => 'Tata Kelola Perkantoran', 'TKR' => 'Teknik Kendaraan Ringan']; @endphp
                        <div class="space-y-1">
                            @foreach($r->vocational_probabilities as $major => $prob)
                            <div class="flex items-center justify-between text-[11px] {{ $major === $r->vocational_major ? 'font-extrabold text-emerald-700' : 'font-medium text-on-surface-variant' }}">
                                <span>{{ $major }} <span class="text-[10px] opacity-60">{{ $majorLabels[$major] ?? '' }}</span></span>
                                <span>{{ round($prob * 100, 1) }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-on-surface-variant">
                <span class="material-symbols-outlined text-[48px] text-outline/30 block mb-2">assignment</span>
                Belum ada hasil klasifikasi.
            </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block dense-table-wrapper shadow-sm">
            <table class="w-full text-left border-collapse dense-table" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jalur Pendidikan</th>
                        <th>Kecocokan</th>
                        <th>Alasan Rekomendasi</th>
                        <th class="text-center w-10">Detail</th>
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
                    <tr class="hover:bg-surface-container-low transition-colors cursor-pointer" @click="expandedRow = expandedRow === {{ $r->id }} ? null : {{ $r->id }}">
                        <td class="font-bold text-primary">{{ $r->student->nis }}</td>
                        <td class="font-semibold">{{ $r->student->name }}</td>
                        <td>{{ $r->student->classroom->name ?? '-' }}</td>
                        <td>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $badgeTheme }}">
                                {{ $r->recommended_path }}
                            </span>
                            @if($r->vocational_major)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700 ml-1">{{ $r->vocational_major }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm font-extrabold text-primary">{{ round($maxProb * 100, 1) }}%</span>
                        </td>
                        <td class="text-[11px] text-on-surface-variant max-w-[220px]">
                            <span class="line-clamp-2">{{ $r->dominant_factor }}</span>
                        </td>
                        <td class="text-center">
                            <span class="material-symbols-outlined text-primary/60 text-[18px] transition-transform duration-200" 
                                :class="expandedRow === {{ $r->id }} ? 'rotate-180' : ''">expand_more</span>
                        </td>
                    </tr>
                    <!-- Expandable Detail Row -->
                    <tr x-show="expandedRow === {{ $r->id }}" x-transition class="bg-surface-container-low/30">
                        <td colspan="7" class="!p-4 sm:!p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                <!-- Probability Bars -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-extrabold text-primary flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                                        Persentase Kecocokan Tiap Jalur
                                    </h4>
                                    @foreach([
                                        'IPA' => ['prob' => $r->ipa_probability, 'color' => 'bg-blue-500', 'text' => 'text-blue-700'],
                                        'IPS' => ['prob' => $r->ips_probability, 'color' => 'bg-amber-500', 'text' => 'text-amber-700'],
                                        'Bahasa' => ['prob' => $r->bahasa_probability, 'color' => 'bg-violet-500', 'text' => 'text-violet-700'],
                                        'Vokasi' => ['prob' => $r->vokasi_probability, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
                                    ] as $label => $item)
                                    <div>
                                        <div class="flex justify-between items-center text-[11px] mb-1">
                                            <span class="font-bold {{ $item['text'] }}">{{ $label }}</span>
                                            <span class="font-extrabold {{ $item['text'] }}">{{ round($item['prob'] * 100, 1) }}%</span>
                                        </div>
                                        <div class="w-full h-2 bg-surface-container-low rounded-full overflow-hidden">
                                            <div class="h-full {{ $item['color'] }} rounded-full transition-all duration-500" style="width: {{ round($item['prob'] * 100) }}%"></div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <!-- Factor Explanation -->
                                <div class="space-y-3">
                                    <h4 class="text-xs font-extrabold text-primary flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]">info</span>
                                        Penjelasan Rekomendasi
                                    </h4>
                                    <div class="bg-surface-container-lowest border border-outline-variant/40 rounded-xl p-4">
                                        <p class="text-xs text-on-surface-variant leading-relaxed font-medium">{{ $r->dominant_factor }}</p>
                                    </div>
                                    @if($r->recommended_path === 'Vokasi' && $r->vocational_probabilities)
                                    <h4 class="text-xs font-extrabold text-emerald-700 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px]">school</span>
                                        Detail Jurusan SMK
                                    </h4>
                                    @php
                                        $majorLabels = ['RPL' => 'Rekayasa Perangkat Lunak', 'TKJ' => 'Teknik Komputer & Jaringan', 'MM' => 'Multimedia', 'AKL' => 'Akuntansi & Keuangan', 'OTKP' => 'Tata Kelola Perkantoran', 'TKR' => 'Teknik Kendaraan Ringan'];
                                    @endphp
                                    <div class="space-y-1.5">
                                        @foreach($r->vocational_probabilities as $major => $prob)
                                        <div class="flex items-center justify-between text-[11px] {{ $major === $r->vocational_major ? 'font-extrabold text-emerald-700' : 'font-medium text-on-surface-variant' }}">
                                            <span>{{ $major }} <span class="text-[10px] opacity-60">{{ $majorLabels[$major] ?? '' }}</span></span>
                                            <span>{{ round($prob * 100, 1) }}%</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] text-outline/30 block mb-2">assignment</span>
                            Belum ada hasil klasifikasi. Silakan proses klasifikasi terlebih dahulu di halaman Klasifikasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
