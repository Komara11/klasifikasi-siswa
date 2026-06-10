@extends('layouts.public')

@section('title', 'Cek Hasil Peminatan')

@section('content')
<div class="flex-grow flex flex-col items-center justify-start pt-8 sm:pt-16 pb-12 px-4 sm:px-6 min-h-[calc(100vh-8rem)]" x-data="{ loading: false }" @submit.document="loading = true">

    <div class="w-full max-w-2xl relative z-10">

        <!-- Skeleton Loading -->
        <div x-show="loading" class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200/60 animate-pulse max-w-lg mx-auto">
            <div class="space-y-4">
                <div class="h-6 bg-gray-100 rounded-lg w-48 mx-auto"></div>
                <div class="h-4 bg-gray-50 rounded-lg w-64 mx-auto"></div>
                <div class="h-12 bg-gray-100 rounded-xl w-full"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div x-show="!loading" class="space-y-6">

            @if(isset($result))
                {{-- Compact search bar when viewing results --}}
                <div class="no-print max-w-lg mx-auto">
                    <form method="POST" action="{{ route('cek-hasil.search') }}" class="flex gap-2.5">
                        @csrf
                        <input type="text" name="nis" value="{{ old('nis', isset($student) ? $student->nis : '') }}" required placeholder="Cari NIS lain..."
                            class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/15 focus:border-primary outline-none bg-white transition-all font-medium"/>
                        <button type="submit" class="bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-semibold cursor-pointer transition-all hover:bg-primary/90 active:scale-[0.97]">
                            Cari
                        </button>
                    </form>

                    @if(isset($error))
                    <div class="bg-red-50 text-red-700 px-4 py-2.5 rounded-xl text-sm mt-3 font-medium">{{ $error }}</div>
                    @endif
                    @if(isset($warning))
                    <div class="bg-amber-50 text-amber-700 px-4 py-2.5 rounded-xl text-sm mt-3 font-medium">{{ $warning }}</div>
                    @endif
                </div>
            @else
                {{-- Search form (no result) --}}
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200/60 max-w-lg mx-auto">
                    <div class="text-center mb-5">
                        <h2 class="font-h1 text-primary text-xl sm:text-2xl font-extrabold tracking-tight">Cek Hasil Rekomendasi</h2>
                        <p class="text-on-surface-variant text-sm mt-1.5">Masukkan NIS untuk melihat hasil rekomendasi peminatan.</p>
                    </div>

                    @if(isset($error))
                    <div class="bg-red-50 text-red-700 px-4 py-2.5 rounded-xl text-sm mb-4 font-medium">{{ $error }}</div>
                    @endif
                    @if(isset($warning))
                    <div class="bg-amber-50 text-amber-700 px-4 py-2.5 rounded-xl text-sm mb-4 font-medium">{{ $warning }}</div>
                    @endif

                    <form method="POST" action="{{ route('cek-hasil.search') }}" class="flex flex-col sm:flex-row gap-2.5">
                        @csrf
                        <input type="text" name="nis" value="{{ old('nis', isset($student) ? $student->nis : '') }}" required placeholder="Masukkan NIS (Contoh: 12903841)"
                            class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/15 focus:border-primary outline-none bg-white transition-all placeholder-gray-400 font-medium"/>
                        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl text-sm font-semibold cursor-pointer transition-all hover:bg-primary/90 active:scale-[0.97]">
                            Cari
                        </button>
                    </form>
                </div>
            @endif

            {{-- ===== RESULT DISPLAY ===== --}}
            @if(isset($result))
            @php
                $theme = match($result->recommended_path) {
                    'IPA' => [
                        'accent' => 'text-blue-600', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200',
                        'bar' => 'bg-blue-600', 'tagBg' => 'bg-blue-50 text-blue-700', 'ring' => 'ring-blue-100',
                        'gradFrom' => 'from-blue-600', 'gradTo' => 'to-blue-500',
                        'desc' => 'Kamu memiliki potensi yang kuat di bidang Ilmu Pengetahuan Alam. Jalur ini cocok bagi yang menyukai Matematika, Fisika, Kimia, dan Biologi.',
                        'careers' => ['Dokter Sp.', 'Insinyur Sipil', 'Data Scientist', 'Apoteker', 'Arsitek', 'Peneliti Medis'],
                    ],
                    'IPS' => [
                        'accent' => 'text-amber-600', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200',
                        'bar' => 'bg-amber-600', 'tagBg' => 'bg-amber-50 text-amber-700', 'ring' => 'ring-amber-100',
                        'gradFrom' => 'from-amber-600', 'gradTo' => 'to-amber-500',
                        'desc' => 'Kamu memiliki potensi yang kuat di bidang Ilmu Pengetahuan Sosial. Jalur ini cocok bagi yang tertarik pada ekonomi, geografi, sejarah, dan sosiologi.',
                        'careers' => ['Pengacara', 'Ekonom Senior', 'Diplomat RI', 'Jurnalis Investigasi', 'Wirausahawan', 'Sosiolog'],
                    ],
                    'Bahasa' => [
                        'accent' => 'text-violet-600', 'bg' => 'bg-violet-50', 'border' => 'border-violet-200',
                        'bar' => 'bg-violet-600', 'tagBg' => 'bg-violet-50 text-violet-700', 'ring' => 'ring-violet-100',
                        'gradFrom' => 'from-violet-600', 'gradTo' => 'to-violet-500',
                        'desc' => 'Kamu memiliki potensi yang kuat di bidang Bahasa dan Sastra. Jalur ini cocok bagi yang menyukai bahasa asing, menulis, dan komunikasi lintas budaya.',
                        'careers' => ['Penerjemah', 'Penulis Buku', 'Diplomat Budaya', 'Editor Senior', 'Sastrawan', 'Humas Korporat'],
                    ],
                    'Vokasi' => [
                        'accent' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200',
                        'bar' => 'bg-emerald-600', 'tagBg' => 'bg-emerald-50 text-emerald-700', 'ring' => 'ring-emerald-100',
                        'gradFrom' => 'from-emerald-600', 'gradTo' => 'to-emerald-500',
                        'vocationalLabel' => match($result->vocational_major) {
                            'RPL' => 'Rekayasa Perangkat Lunak',
                            'TKJ' => 'Teknik Komputer & Jaringan',
                            'MM' => 'Multimedia',
                            'AKL' => 'Akuntansi & Keuangan Lembaga',
                            'OTKP' => 'Otomatisasi & Tata Kelola Perkantoran',
                            'TKR' => 'Teknik Kendaraan Ringan',
                            default => 'Vokasi'
                        },
                        'desc' => $result->vocational_major
                            ? 'Kamu memiliki potensi kuat di jalur Vokasi (SMK) dengan spesialisasi ' . $result->vocational_major . '. Jalur ini membekali keahlian praktis dan siap kerja.'
                            : 'Kamu memiliki potensi kuat di jalur Vokasi (SMK). Jalur ini cocok bagi yang menyukai pembelajaran praktis dan pengembangan keterampilan teknis.',
                        'careers' => match($result->vocational_major) {
                            'RPL' => ['Software Engineer', 'Web Developer', 'App Developer', 'Database Admin'],
                            'TKJ' => ['Network Engineer', 'IT Support', 'System Admin', 'Cyber Security'],
                            'MM' => ['Graphic Designer', 'Video Editor', 'Animator 3D', 'UI/UX Designer'],
                            'AKL' => ['Akuntan Publik', 'Financial Analyst', 'Auditor Keuangan', 'Konsultan Pajak'],
                            'OTKP' => ['Office Manager', 'Sekretaris Eksekutif', 'HRD Assistant', 'PR Officer'],
                            'TKR' => ['Mekanik Otomotif', 'Technician Service', 'Service Advisor', 'Workshop Owner'],
                            default => ['Teknisi', 'Desainer', 'Programmer', 'Akuntan', 'Mekanik']
                        },
                    ],
                    default => [
                        'accent' => 'text-gray-600', 'bg' => 'bg-gray-50', 'border' => 'border-gray-200',
                        'bar' => 'bg-gray-600', 'tagBg' => 'bg-gray-50 text-gray-700', 'ring' => 'ring-gray-100',
                        'gradFrom' => 'from-gray-600', 'gradTo' => 'to-gray-500',
                        'desc' => 'Rekomendasi jalur pendidikan lanjutan kamu.',
                        'careers' => ['Akademisi', 'Profesional'],
                    ]
                };
                $maxProb = max($result->ipa_probability, $result->ips_probability, $result->bahasa_probability, $result->vokasi_probability);
            @endphp

            <div id="printable-result" class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">

                {{-- Header with gradient accent --}}
                <div class="bg-gradient-to-r {{ $theme['gradFrom'] }} {{ $theme['gradTo'] }} px-5 sm:px-8 py-5 sm:py-6 text-white">
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-white/70 mb-1">Laporan Rekomendasi Peminatan</p>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight leading-tight">{{ $student->name }}</h2>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-xs text-white/80">
                        <span>NIS: <strong class="text-white">{{ $student->nis }}</strong></span>
                        <span class="text-white/40">•</span>
                        <span>Kelas: <strong class="text-white">{{ $student->classroom->name }}</strong></span>
                    </div>
                </div>

                <div class="px-5 sm:px-8 py-5 sm:py-7 space-y-6 sm:space-y-8">

                    {{-- Recommendation Result --}}
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.15em] mb-2">Rekomendasi Jalur Pendidikan</p>
                        <div class="flex flex-wrap items-end gap-3">
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 leading-none">
                                Jalur {{ $result->recommended_path }}
                            </h1>
                            <span class="text-sm font-extrabold {{ $theme['accent'] }} bg-gradient-to-r {{ $theme['bg'] }} border {{ $theme['border'] }} px-3 py-1 rounded-full mb-0.5">
                                {{ round($maxProb * 100, 1) }}%
                            </span>
                        </div>
                        @if($result->recommended_path === 'Vokasi' && $result->vocational_major)
                        <p class="text-xs font-semibold {{ $theme['accent'] }} mt-1.5">
                            Spesialisasi: {{ $theme['vocationalLabel'] }} ({{ $result->vocational_major }})
                        </p>
                        @endif
                        <p class="text-sm text-gray-500 leading-relaxed mt-2.5 max-w-xl">{{ $theme['desc'] }}</p>
                    </div>

                    {{-- Tingkat Kecocokan - Horizontal bars --}}
                    <div class="space-y-3">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.15em]">Tingkat Kecocokan</p>
                        <div class="space-y-2.5">
                            @foreach([
                                'IPA' => ['prob' => $result->ipa_probability, 'color' => 'bg-blue-600', 'light' => 'bg-blue-100'],
                                'IPS' => ['prob' => $result->ips_probability, 'color' => 'bg-amber-600', 'light' => 'bg-amber-100'],
                                'Bahasa' => ['prob' => $result->bahasa_probability, 'color' => 'bg-violet-600', 'light' => 'bg-violet-100'],
                                'Vokasi' => ['prob' => $result->vokasi_probability, 'color' => 'bg-emerald-600', 'light' => 'bg-emerald-100']
                            ] as $key => $item)
                            @php 
                                $percent = round($item['prob'] * 100); 
                                $isActive = $key === $result->recommended_path;
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="w-14 text-right text-xs font-bold {{ $isActive ? 'text-gray-900' : 'text-gray-400' }}">{{ $key }}</span>
                                <div class="flex-1 h-2.5 {{ $isActive ? $item['light'] : 'bg-gray-100' }} rounded-full overflow-hidden">
                                    <div class="h-full {{ $isActive ? $item['color'] : 'bg-gray-300' }} rounded-full transition-all duration-700" style="width: {{ $percent }}%;"></div>
                                </div>
                                <span class="w-12 text-xs font-extrabold {{ $isActive ? 'text-gray-900' : 'text-gray-400' }}">{{ round($item['prob'] * 100, 1) }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Two column: Analisis & Prospek Karir --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-1">
                        {{-- Faktor BK --}}
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.15em] mb-2">Analisis Faktor Utama</p>
                            <div class="pl-3 border-l-2 {{ $theme['border'] }}">
                                <p class="text-sm text-gray-600 italic leading-relaxed">"{{ $result->dominant_factor }}"</p>
                            </div>
                        </div>

                        {{-- Prospek Karir --}}
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.15em] mb-2">Prospek Karir</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($theme['careers'] as $c)
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg {{ $theme['tagBg'] }} border {{ $theme['border'] }}">{{ $c }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Vocational Detail --}}
                    @if($result->recommended_path === 'Vokasi' && $result->vocational_probabilities)
                    <div class="pt-1 space-y-3">
                        <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-[0.15em]">Rincian Program Keahlian (SMK)</p>
                        @php
                            $majorLabels = ['RPL' => 'Rekayasa Perangkat Lunak', 'TKJ' => 'Teknik Komputer & Jaringan', 'MM' => 'Multimedia', 'AKL' => 'Akuntansi Keuangan', 'OTKP' => 'Tata Kelola Perkantoran', 'TKR' => 'Teknik Kendaraan Ringan'];
                            $majorColors = ['RPL' => 'bg-blue-600', 'TKJ' => 'bg-cyan-600', 'MM' => 'bg-pink-600', 'AKL' => 'bg-amber-600', 'OTKP' => 'bg-indigo-600', 'TKR' => 'bg-orange-600'];
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($result->vocational_probabilities as $major => $prob)
                            @php $pct = round($prob * 100); $isTopMajor = $major === $result->vocational_major; @endphp
                            <div class="flex items-center gap-3 p-2.5 rounded-xl {{ $isTopMajor ? 'bg-emerald-50/60 border border-emerald-200' : 'bg-gray-50 border border-gray-100' }}">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold {{ $isTopMajor ? 'text-emerald-700' : 'text-gray-700' }}">{{ $major }}</p>
                                    <p class="text-[10px] text-gray-400 truncate">{{ $majorLabels[$major] ?? '' }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $isTopMajor ? ($majorColors[$major] ?? 'bg-emerald-600') : 'bg-gray-300' }} rounded-full" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <span class="text-xs font-bold {{ $isTopMajor ? 'text-emerald-600' : 'text-gray-500' }} w-10 text-right">{{ round($prob * 100, 1) }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Langkah Selanjutnya --}}
                    <div class="bg-gray-50 -mx-5 sm:-mx-8 px-5 sm:px-8 py-5 mt-2 border-t border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.15em] mb-3">Langkah Selanjutnya</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 mb-0.5">Konsultasi BK & Wali</p>
                                <p class="text-xs text-gray-500 leading-relaxed">Diskusikan bersama orang tua dan Guru BK untuk mematangkan pilihan.</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 mb-0.5">Sifat Rekomendasi</p>
                                <p class="text-xs text-gray-500 leading-relaxed">Ini sarana pendukung pemetaan potensi, bukan keputusan akhir yang mengikat.</p>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 mb-0.5">Cari Info Sekolah</p>
                                <p class="text-xs text-gray-500 leading-relaxed">Mulai cari info pendaftaran SMA/SMK sesuai jalur rekomendasi kamu.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-center gap-3 pt-2 no-print">
                        <button type="button" onclick="window.print()" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-semibold cursor-pointer transition-all hover:bg-primary/90 active:scale-[0.97] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">print</span>
                            Cetak Rekomendasi
                        </button>
                        <a href="{{ route('public.transcripts.pdf', $student) }}" target="_blank" class="bg-red-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold cursor-pointer transition-all hover:bg-red-700 active:scale-[0.97] flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                            Unduh Transkrip
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * { visibility: hidden; }
        #printable-result, #printable-result * { visibility: visible; }
        #printable-result {
            position: absolute;
            left: 0; top: 0;
            width: 100%;
            padding: 0px;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        .no-print { display: none !important; }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection
