@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
<div class="space-y-space-lg" x-data="{ loading: false }" @submit.document="loading = true">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
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
        <h2 class="font-h1 text-primary text-lg sm:text-xl font-bold">Laporan Rekapitulasi</h2>

        <!-- Filters & Export -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-4 shadow-sm">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3" x-data>
                <x-custom-select 
                    name="classroom" 
                    :options="$classrooms->map(fn($c) => ['value' => $c->name, 'label' => $c->name])->toArray()" 
                    :selected="request('classroom', '')" 
                    placeholder="Semua Kelas"
                />
                <x-custom-select 
                    name="path" 
                    :options="collect(['IPA','IPS','Bahasa','Vokasi','RPL','TKJ','MM','AKL','OTKP','TKR'])->map(fn($p) => ['value' => $p, 'label' => $p])->toArray()" 
                    :selected="request('path', '')" 
                    placeholder="Semua Jurusan"
                />
                <button type="submit" class="bg-primary hover:bg-primary/95 text-white py-2.5 rounded-lg text-xs font-bold cursor-pointer transition-colors">Filter</button>
                <div class="flex gap-2">
                    <a href="{{ route('admin.reports.csv', request()->query()) }}" class="flex-1 py-2.5 border border-primary text-primary text-center rounded-lg text-xs font-bold hover:bg-primary/5 transition-all flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">table_view</span> CSV
                    </a>
                    <a href="{{ route('admin.reports.pdf', request()->query()) }}" class="flex-1 py-2.5 bg-primary text-white text-center rounded-lg text-xs font-bold hover:bg-primary/90 transition-all flex items-center justify-center gap-1">
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
            @endphp
            <div class="mobile-card-item">
                <div class="flex justify-between items-start">
                    <div class="min-w-0">
                        <p class="font-bold text-primary text-sm truncate">{{ $r->student->name }}</p>
                        <p class="text-[11px] text-outline mt-0.5">NIS: {{ $r->student->nis }} • {{ $r->student->classroom->name }}</p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border bg-primary/10 border-primary/20 text-primary">{{ $r->recommended_path }}</span>
                        @if($r->vocational_major)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700">{{ $r->vocational_major }}</span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-1.5 mt-2 pt-2 border-t border-outline-variant/20">
                    @foreach(['IPA' => $r->ipa_probability, 'IPS' => $r->ips_probability, 'BHS' => $r->bahasa_probability, 'VOK' => $r->vokasi_probability] as $label => $prob)
                    <div class="text-center">
                        <span class="text-[9px] text-outline font-bold block">{{ $label }}</span>
                        <span class="text-[11px] font-extrabold text-on-surface block">{{ round($prob*100,1) }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-on-surface-variant">Belum ada data laporan.</div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block dense-table-wrapper shadow-sm">
            <table class="w-full text-left border-collapse dense-table" style="min-width: 700px;">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Rekomendasi</th>
                        <th>Probabilitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse($results as $r)
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="font-bold text-primary">{{ $r->student->nis }}</td>
                        <td class="font-semibold">{{ $r->student->name }}</td>
                        <td>{{ $r->student->classroom->name }}</td>
                        <td><span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border bg-primary/10 border-primary/20 text-primary">{{ $r->recommended_path }}</span>@if($r->vocational_major)<span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700 ml-1">{{ $r->vocational_major }}</span>@endif</td>
                        <td class="text-[11px] font-bold text-outline">
                            <div class="flex flex-wrap gap-x-2 gap-y-0.5">
                                <span>IPA: {{ round($r->ipa_probability*100,1) }}%</span>
                                <span>IPS: {{ round($r->ips_probability*100,1) }}%</span>
                                <span>BHS: {{ round($r->bahasa_probability*100,1) }}%</span>
                                <span>VOK: {{ round($r->vokasi_probability*100,1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-on-surface-variant">Belum ada data laporan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
