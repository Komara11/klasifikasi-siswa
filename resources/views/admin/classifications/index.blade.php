@extends('layouts.app')
@section('title', 'Klasifikasi Siswa')
@section('content')
<div class="space-y-space-lg" x-data="{ 
    loading: false, 
    filterStatus: 'all',
    search: '',
    get filteredStudents() {
        let students = document.querySelectorAll('[data-student-row]');
        students.forEach(row => {
            let matchStatus = this.filterStatus === 'all' || row.dataset.status === this.filterStatus;
            let matchSearch = this.search === '' || 
                row.dataset.name.toLowerCase().includes(this.search.toLowerCase()) ||
                row.dataset.nis.includes(this.search);
            row.style.display = (matchStatus && matchSearch) ? '' : 'none';
        });
        return true;
    }
}" @submit.document="loading = true">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="flex justify-between items-center">
            <div class="space-y-2">
                <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
                <div class="h-4 bg-surface-container-high rounded skeleton w-32"></div>
            </div>
            <div class="h-10 bg-surface-container-high rounded skeleton w-32"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            @for($i = 0; $i < 4; $i++)
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
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-h1 text-primary text-lg sm:text-xl font-bold">Proses Klasifikasi Peminatan</h2>
                <p class="text-on-surface-variant font-body-sm mt-0.5 text-xs sm:text-sm">Model aktif: <strong>{{ $modelStats['version'] }}</strong> (Ketepatan: {{ $modelStats['accuracy'] }}%)</p>
            </div>
            <form method="POST" action="{{ route('admin.classifications.classify') }}" class="w-full sm:w-auto"
                x-data="{ showConfirm: false }">
                @csrf
                <button type="button" @click="showConfirm = true" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                    Proses Klasifikasi
                </button>
                <!-- Confirmation overlay -->
                <div x-show="showConfirm" x-transition.opacity class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-[999] flex items-center justify-center p-4" @click="showConfirm = false" x-cloak>
                    <div @click.stop class="bg-surface-container-lowest border border-outline-variant/50 max-w-sm w-full rounded-3xl p-6 shadow-2xl space-y-5 text-center">
                        <div class="w-14 h-14 mx-auto bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center ring-1 ring-blue-100">
                            <span class="material-symbols-outlined text-[30px]">settings_suggest</span>
                        </div>
                        <div class="space-y-2">
                            <h4 class="font-h1 text-primary text-lg font-extrabold tracking-tight">Konfirmasi Klasifikasi</h4>
                            <p class="text-on-surface-variant text-xs font-medium leading-relaxed">
                                Sistem akan memproses semua siswa yang datanya lengkap dan memberikan rekomendasi jalur pendidikan. Hasil klasifikasi sebelumnya akan diperbarui.
                            </p>
                            @php
                                $readyCount = $students->filter(fn($s) => $s->data_status === 'Siap Diklasifikasi')->count();
                                $totalComplete = $students->filter(fn($s) => $s->is_complete)->count();
                            @endphp
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-800 font-bold">
                                {{ $totalComplete }} siswa dengan data lengkap akan diproses
                            </div>
                        </div>
                        <div class="flex gap-3 justify-center">
                            <button type="button" @click="showConfirm = false" class="flex-1 px-4 py-3 bg-surface-container-low border border-outline-variant/30 text-on-surface hover:bg-surface-container-high text-xs font-bold rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 px-4 py-3 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-xl transition-all active:scale-[0.98] shadow-md shadow-primary/10 cursor-pointer">
                                Ya, Proses Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Status Summary Cards -->
        @php
            $statusCounts = [
                'classified' => $students->filter(fn($s) => $s->data_status === 'Sudah Diklasifikasi')->count(),
                'ready' => $students->filter(fn($s) => $s->data_status === 'Siap Diklasifikasi')->count(),
                'no_questionnaire' => $students->filter(fn($s) => $s->data_status === 'Kuesioner Belum Lengkap')->count(),
                'no_scores' => $students->filter(fn($s) => $s->data_status === 'Nilai Belum Lengkap')->count(),
            ];
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-space-md">
            <button type="button" @click="filterStatus = filterStatus === 'classified' ? 'all' : 'classified'" 
                class="bg-surface-container-lowest border rounded-xl p-3 sm:p-4 shadow-sm text-left transition-all cursor-pointer"
                :class="filterStatus === 'classified' ? 'border-green-400 ring-2 ring-green-200' : 'border-outline-variant hover:border-outline-variant/80'">
                <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                    <span class="text-[9px] sm:text-[10px] text-outline font-bold uppercase tracking-wider truncate">Sudah Diklasifikasi</span>
                </div>
                <span class="text-xl sm:text-2xl font-extrabold text-green-700">{{ $statusCounts['classified'] }}</span>
                <span class="text-[10px] text-outline font-medium block">siswa</span>
            </button>
            <button type="button" @click="filterStatus = filterStatus === 'ready' ? 'all' : 'ready'"
                class="bg-surface-container-lowest border rounded-xl p-3 sm:p-4 shadow-sm text-left transition-all cursor-pointer"
                :class="filterStatus === 'ready' ? 'border-blue-400 ring-2 ring-blue-200' : 'border-outline-variant hover:border-outline-variant/80'">
                <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                    <span class="text-[9px] sm:text-[10px] text-outline font-bold uppercase tracking-wider truncate">Siap Diklasifikasi</span>
                </div>
                <span class="text-xl sm:text-2xl font-extrabold text-blue-700">{{ $statusCounts['ready'] }}</span>
                <span class="text-[10px] text-outline font-medium block">siswa</span>
            </button>
            <button type="button" @click="filterStatus = filterStatus === 'no_questionnaire' ? 'all' : 'no_questionnaire'"
                class="bg-surface-container-lowest border rounded-xl p-3 sm:p-4 shadow-sm text-left transition-all cursor-pointer"
                :class="filterStatus === 'no_questionnaire' ? 'border-amber-400 ring-2 ring-amber-200' : 'border-outline-variant hover:border-outline-variant/80'">
                <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-[9px] sm:text-[10px] text-outline font-bold uppercase tracking-wider truncate">Kuesioner Belum</span>
                </div>
                <span class="text-xl sm:text-2xl font-extrabold text-amber-700">{{ $statusCounts['no_questionnaire'] }}</span>
                <span class="text-[10px] text-outline font-medium block">siswa</span>
            </button>
            <button type="button" @click="filterStatus = filterStatus === 'no_scores' ? 'all' : 'no_scores'"
                class="bg-surface-container-lowest border rounded-xl p-3 sm:p-4 shadow-sm text-left transition-all cursor-pointer"
                :class="filterStatus === 'no_scores' ? 'border-red-400 ring-2 ring-red-200' : 'border-outline-variant hover:border-outline-variant/80'">
                <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                    <span class="text-[9px] sm:text-[10px] text-outline font-bold uppercase tracking-wider truncate">Nilai Belum</span>
                </div>
                <span class="text-xl sm:text-2xl font-extrabold text-red-700">{{ $statusCounts['no_scores'] }}</span>
                <span class="text-[10px] text-outline font-medium block">siswa</span>
            </button>
        </div>

        <!-- Search -->
        <div class="flex items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline/50 text-[20px]">search</span>
                <input type="text" x-model="search" @input="filteredStudents" placeholder="Cari nama atau NIS..." 
                    class="w-full border border-outline-variant rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface-container-lowest"/>
            </div>
            <button type="button" x-show="filterStatus !== 'all' || search !== ''" @click="filterStatus = 'all'; search = ''; filteredStudents" 
                class="text-xs font-bold text-primary hover:text-primary/80 flex items-center gap-1 cursor-pointer shrink-0">
                <span class="material-symbols-outlined text-[16px]">close</span> Reset
            </button>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden mobile-card-list">
            @foreach($students as $student)
            @php
                $statusConfig = match($student->data_status_code) {
                    'classified' => ['bg' => 'bg-green-50 border-green-200 text-green-700', 'icon' => '✓'],
                    'ready' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-700', 'icon' => '✓'],
                    'no_questionnaire' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'icon' => '⚠'],
                    'no_scores' => ['bg' => 'bg-red-50 border-red-200 text-red-700', 'icon' => '⚠'],
                    default => ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'icon' => '?'],
                };
            @endphp
            <div class="mobile-card-item" data-student-row data-status="{{ $student->data_status_code }}" data-name="{{ $student->name }}" data-nis="{{ $student->nis }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-bold text-primary text-sm">{{ $student->name }}</p>
                        <p class="text-[11px] text-outline mt-0.5">NIS: {{ $student->nis }} • {{ $student->classroom->name }}</p>
                    </div>
                    @if($student->classification)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border bg-primary/10 border-primary/20 text-primary shrink-0">{{ $student->classification->recommended_path }}</span>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-1">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $statusConfig['bg'] }}">
                        {{ $statusConfig['icon'] }} {{ $student->data_status }}
                    </span>
                    @if($student->classification && $student->classification->vocational_major)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700">{{ $student->classification->vocational_major }}</span>
                    @elseif(!$student->classification)
                        <span class="text-outline text-[10px]">Belum diproses</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block dense-table-wrapper shadow-sm">
            <table class="w-full text-left border-collapse dense-table" style="min-width: 650px;">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Status Kelengkapan</th>
                        <th>Hasil Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @foreach($students as $student)
                    @php
                        $statusConfig = match($student->data_status_code) {
                            'classified' => ['bg' => 'bg-green-50 border-green-200 text-green-700', 'icon' => '✓'],
                            'ready' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-700', 'icon' => '✓'],
                            'no_questionnaire' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'icon' => '⚠'],
                            'no_scores' => ['bg' => 'bg-red-50 border-red-200 text-red-700', 'icon' => '⚠'],
                            default => ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'icon' => '?'],
                        };
                    @endphp
                    <tr class="hover:bg-surface-container-low transition-colors" 
                        data-student-row 
                        data-status="{{ $student->data_status_code }}"
                        data-name="{{ $student->name }}"
                        data-nis="{{ $student->nis }}">
                        <td class="font-bold text-primary">{{ $student->nis }}</td>
                        <td class="font-semibold">{{ $student->name }}</td>
                        <td>{{ $student->classroom->name }}</td>
                        <td>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $statusConfig['bg'] }}">
                                {{ $statusConfig['icon'] }} {{ $student->data_status }}
                            </span>
                        </td>
                        <td>
                            @if($student->classification)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border bg-primary/10 border-primary/20 text-primary">{{ $student->classification->recommended_path }}</span>
                                @if($student->classification->vocational_major)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700 ml-1">{{ $student->classification->vocational_major }}</span>
                                @endif
                            @else
                                <span class="text-outline text-[10px]">Belum diproses</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
