@extends('layouts.app')
@section('title', 'Evaluasi & Pelatihan Sistem')
@section('content')
<div class="space-y-4 sm:space-y-space-lg" x-data="{ loading: false, showHelp: false }" @submit.document="loading = true">
    
    <!-- Skeleton Screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-space-md">
            @for($i = 0; $i < 4; $i++)
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-space-md shadow-sm h-20 space-y-2">
                <div class="h-3 bg-surface-container-high rounded skeleton w-1/3"></div>
                <div class="h-6 bg-surface-container-high rounded skeleton w-1/2"></div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-4 sm:space-y-space-lg">
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-h1 text-primary text-lg sm:text-xl font-bold">Evaluasi & Pelatihan Sistem</h2>
                <p class="text-on-surface-variant text-xs sm:font-body-sm mt-0.5">Pantau performa sistem rekomendasi peminatan siswa</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1.5 rounded-xl text-[10px] sm:text-xs font-extrabold border {{ $modelStats['trained'] ? 'bg-green-50 border-green-200 text-green-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                    {{ $modelStats['trained'] ? '● Sistem Aktif' : '● Belum Dilatih' }}
                </span>
                <span class="px-3 py-1.5 rounded-xl text-[10px] sm:text-xs font-bold bg-primary/5 border border-primary/20 text-primary">
                    Versi {{ $modelStats['version'] }}
                </span>
            </div>
        </div>

        <!-- Model Performance Summary -->
        @php
            $accuracy = $modelStats['accuracy'] ?? 0;
            $performanceLevel = match(true) {
                $accuracy >= 90 => ['label' => 'Sangat Baik', 'desc' => 'Sistem bekerja dengan sangat baik! Rekomendasi yang diberikan memiliki tingkat ketepatan tinggi.', 'color' => 'bg-green-50 border-green-200 text-green-800', 'icon' => 'verified', 'iconColor' => 'text-green-600'],
                $accuracy >= 80 => ['label' => 'Baik', 'desc' => 'Sistem bekerja dengan baik. Rekomendasi cukup dapat diandalkan untuk membantu pengambilan keputusan.', 'color' => 'bg-blue-50 border-blue-200 text-blue-800', 'icon' => 'check_circle', 'iconColor' => 'text-blue-600'],
                $accuracy >= 60 => ['label' => 'Cukup', 'desc' => 'Sistem masih perlu peningkatan. Pertimbangkan untuk melengkapi data siswa lalu latih ulang sistem.', 'color' => 'bg-amber-50 border-amber-200 text-amber-800', 'icon' => 'info', 'iconColor' => 'text-amber-600'],
                default => ['label' => 'Perlu Ditingkatkan', 'desc' => 'Sistem belum optimal. Pastikan data siswa sudah lengkap, lalu latih ulang sistem.', 'color' => 'bg-red-50 border-red-200 text-red-800', 'icon' => 'warning', 'iconColor' => 'text-red-600'],
            };
        @endphp

        <div class="{{ $performanceLevel['color'] }} border rounded-2xl p-4 sm:p-5 flex items-start gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white flex items-center justify-center shadow-xs ring-1 ring-black/[0.03] shrink-0">
                <span class="material-symbols-outlined {{ $performanceLevel['iconColor'] }} text-[20px] sm:text-[24px]">{{ $performanceLevel['icon'] }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mb-1">
                    <span class="font-extrabold text-sm">Status: {{ $performanceLevel['label'] }}</span>
                    <span class="text-xs font-bold opacity-75">(Ketepatan {{ $accuracy }}%)</span>
                </div>
                <p class="text-xs font-medium leading-relaxed opacity-90">{{ $performanceLevel['desc'] }}</p>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-space-md">
            @php
                $metrics = [
                    ['label' => 'Ketepatan', 'value' => $modelStats['accuracy'], 'help' => 'Dari semua prediksi yang dibuat, seberapa banyak yang tepat?', 'icon' => 'target', 'iconBg' => 'bg-blue-50 text-blue-600 ring-blue-100'],
                    ['label' => 'Ketelitian', 'value' => $modelStats['precision'] ?? 0, 'help' => 'Jika sistem merekomendasikan suatu jalur, seberapa yakin bahwa itu benar?', 'icon' => 'precision_manufacturing', 'iconBg' => 'bg-emerald-50 text-emerald-600 ring-emerald-100'],
                    ['label' => 'Jangkauan', 'value' => $modelStats['recall'] ?? 0, 'help' => 'Dari semua siswa yang seharusnya masuk suatu jalur, berapa persen yang terdeteksi?', 'icon' => 'radar', 'iconBg' => 'bg-violet-50 text-violet-600 ring-violet-100'],
                    ['label' => 'Keseimbangan', 'value' => $modelStats['f1'] ?? 0, 'help' => 'Gabungan ketelitian dan jangkauan. Nilai tinggi = sistem akurat dan tidak melewatkan siswa.', 'icon' => 'balance', 'iconBg' => 'bg-amber-50 text-amber-600 ring-amber-100'],
                ];
            @endphp

            @foreach($metrics as $metric)
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-space-md shadow-sm relative group" x-data="{ showTip: false }" :class="showTip ? 'z-50' : 'z-10'">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-outline text-[9px] sm:text-[10px] font-bold uppercase tracking-wider">{{ $metric['label'] }}</span>
                    <button type="button" @click="showTip = !showTip" class="text-outline/50 hover:text-primary transition-colors cursor-pointer relative z-20">
                        <span class="material-symbols-outlined text-[14px] sm:text-[16px]">help</span>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <div class="p-1 sm:p-1.5 {{ $metric['iconBg'] }} rounded-lg ring-1 shrink-0">
                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">{{ $metric['icon'] }}</span>
                    </div>
                    <span class="text-xl sm:text-2xl font-extrabold text-primary">{{ $metric['value'] }}%</span>
                </div>
                <!-- Tooltip -->
                <div x-cloak x-show="showTip" x-transition @click.away="showTip = false" class="absolute left-0 right-0 top-full mt-2 z-[60] bg-on-surface text-white text-[11px] font-medium p-3 rounded-xl shadow-xl leading-relaxed min-w-[200px]">
                    {{ $metric['help'] }}
                    <div class="absolute -top-1.5 left-4 w-3 h-3 bg-on-surface rotate-45 rounded-sm"></div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Data Quality -->
        @if(!empty($dataQuality['recommendations']))
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 sm:p-space-lg shadow-sm">
            <h3 class="font-h2 text-primary font-bold mb-3 flex items-center gap-2 text-sm sm:text-base">
                <span class="material-symbols-outlined text-[18px] sm:text-[20px]">tips_and_updates</span> Informasi Data
            </h3>
            <div class="space-y-2 mb-4">
                @foreach($dataQuality['recommendations'] as $rec)
                <div class="flex items-start gap-2 text-xs sm:text-sm px-3 py-2 rounded-lg {{ $rec['type'] === 'warning' ? 'bg-amber-50 text-amber-800' : ($rec['type'] === 'success' ? 'bg-green-50 text-green-800' : 'bg-blue-50 text-blue-800') }}">
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px] mt-0.5 shrink-0">{{ $rec['type'] === 'warning' ? 'warning' : ($rec['type'] === 'success' ? 'check_circle' : 'info') }}</span>
                    <span class="font-medium">{{ $rec['msg'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-surface-container-low rounded-lg p-2">
                    <span class="text-[9px] sm:text-[10px] text-outline font-bold block uppercase">Total Siswa</span>
                    <span class="text-base sm:text-lg font-extrabold text-primary">{{ $dataQuality['total_students'] }}</span>
                </div>
                <div class="bg-surface-container-low rounded-lg p-2">
                    <span class="text-[9px] sm:text-[10px] text-outline font-bold block uppercase">Data Lengkap</span>
                    <span class="text-base sm:text-lg font-extrabold text-primary">{{ $dataQuality['complete_students'] }}</span>
                </div>
                @foreach($dataQuality['class_distribution'] as $class => $count)
                <div class="bg-surface-container-low rounded-lg p-2">
                    <span class="text-[9px] sm:text-[10px] text-outline font-bold block uppercase">{{ $class }}</span>
                    <span class="text-base sm:text-lg font-extrabold text-primary">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Confusion Matrix -->
        @if(isset($modelStats['confusion_matrix']))
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 sm:p-space-lg shadow-sm">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h3 class="font-h2 text-primary font-bold flex items-center gap-2 text-sm sm:text-base">
                    <span class="material-symbols-outlined text-[18px] sm:text-[20px]">grid_on</span>
                    Tabel Ketepatan Prediksi
                </h3>
                <button type="button" @click="showHelp = !showHelp" class="text-[10px] sm:text-xs font-bold text-primary/60 hover:text-primary flex items-center gap-1 cursor-pointer transition-colors">
                    <span class="material-symbols-outlined text-[14px] sm:text-[16px]">help</span>
                    Cara baca
                </button>
            </div>

            <div x-show="showHelp" x-transition class="bg-blue-50 border border-blue-200 rounded-xl p-3 sm:p-4 mb-4 text-[11px] sm:text-xs text-blue-800 leading-relaxed font-medium">
                <strong>Cara membaca:</strong> Baris = jalur sebenarnya, kolom = prediksi sistem. Diagonal hijau = prediksi tepat. Di luar diagonal = kurang tepat.
            </div>

            <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
                <table class="w-full max-w-lg mx-auto text-center border-collapse">
                    <thead>
                        <tr>
                            <th class="p-1.5 sm:p-2 text-[9px] sm:text-[10px] text-outline font-bold" rowspan="2"></th>
                            <th class="p-1.5 sm:p-2 text-[9px] sm:text-[10px] text-primary font-extrabold uppercase tracking-wider" colspan="4">← Prediksi Sistem →</th>
                        </tr>
                        <tr>
                            @foreach(['IPA', 'IPS', 'Bhs', 'Vok'] as $h)
                            <th class="p-1.5 sm:p-2 text-[10px] sm:text-xs font-bold text-primary">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['ipa', 'ips', 'bahasa', 'vokasi'] as $idx => $row)
                        @php $shortLabels = ['ipa' => 'IPA', 'ips' => 'IPS', 'bahasa' => 'Bhs', 'vokasi' => 'Vok']; @endphp
                        <tr>
                            <td class="p-1.5 sm:p-2 text-[10px] sm:text-xs font-bold text-primary">
                                {{ $shortLabels[$row] }}
                            </td>
                            @foreach(['ipa', 'ips', 'bahasa', 'vokasi'] as $col)
                            <td class="p-1.5 sm:p-2 text-xs sm:text-sm font-bold border border-outline-variant/30 {{ $row === $col ? 'bg-green-100/50 text-green-800' : (($modelStats['confusion_matrix'][$row][$col] ?? 0) > 0 ? 'bg-red-50 text-red-600' : 'text-outline') }}">
                                {{ $modelStats['confusion_matrix'][$row][$col] ?? 0 }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-center text-[9px] sm:text-[10px] text-outline mt-3 font-medium">
                <span class="inline-block w-3 h-3 bg-green-100 border border-green-200 rounded-sm align-middle mr-1"></span> Tepat
                <span class="inline-block w-3 h-3 bg-red-50 border border-red-200 rounded-sm align-middle mr-1 ml-3"></span> Kurang Tepat
            </p>
        </div>
        @endif

        <!-- Simple Training Button -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 sm:p-space-lg shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-h2 text-primary font-bold flex items-center gap-2 text-sm sm:text-base">
                        <span class="material-symbols-outlined text-[18px] sm:text-[20px]">refresh</span>
                        Latih Ulang Sistem
                    </h3>
                    <p class="text-on-surface-variant text-[11px] sm:text-xs mt-1">Perbarui sistem agar rekomendasi lebih akurat berdasarkan data siswa terbaru.</p>
                </div>
                <form method="POST" action="{{ route('admin.training.train') }}" x-data="{ confirmTrain: false }">
                    @csrf
                    <input type="hidden" name="synthetic_count" value="{{ $dataQuality['optimal_settings']['synthetic_count'] ?? $defaults['synthetic_count'] }}">
                    <input type="hidden" name="min_variance" value="{{ $dataQuality['optimal_settings']['min_variance'] ?? $defaults['min_variance'] }}">
                    
                    <button type="button" @click="confirmTrain = true" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-6 py-2.5 sm:py-3 rounded-xl font-bold text-xs sm:text-sm transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                        Latih Ulang
                    </button>

                    <!-- Confirmation -->
                    <div x-show="confirmTrain" x-transition.opacity class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-[999] flex items-center justify-center p-4" @click="confirmTrain = false" x-cloak>
                        <div @click.stop class="bg-surface-container-lowest border border-outline-variant/50 max-w-sm w-full rounded-3xl p-6 shadow-2xl space-y-5 text-center">
                            <div class="w-14 h-14 mx-auto bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center ring-1 ring-blue-100">
                                <span class="material-symbols-outlined text-[30px]">model_training</span>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-h1 text-primary text-lg font-extrabold tracking-tight">Latih Ulang Sistem?</h4>
                                <p class="text-on-surface-variant text-xs font-medium leading-relaxed">
                                    Sistem akan mempelajari ulang data siswa dan memperbarui model rekomendasi. Proses ini membutuhkan waktu beberapa detik.
                                </p>
                            </div>
                            <div class="flex gap-3 justify-center">
                                <button type="button" @click="confirmTrain = false" class="flex-1 px-4 py-3 bg-surface-container-low border border-outline-variant/30 text-on-surface hover:bg-surface-container-high text-xs font-bold rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 px-4 py-3 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-xl transition-all active:scale-[0.98] shadow-md shadow-primary/10 cursor-pointer">
                                    Ya, Latih Ulang
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
