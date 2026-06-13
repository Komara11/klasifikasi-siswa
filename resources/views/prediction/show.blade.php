@extends('layouts.house')

@section('title', 'Analisis Nilai Properti')

@section('styles')
<style>
    @media print {
        nav, footer, .no-print {
            display: none !important;
        }
        body {
            background-color: white !important;
        }
        .print-container {
            border: 2px solid #e2e8f0;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: none !important;
        }
    }
</style>
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 print-container">
    <div class="text-center space-y-3 mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Analisis Nilai Properti</h1>
        <p class="text-slate-500 text-sm font-semibold max-w-lg mx-auto leading-relaxed">
            Berdasarkan data pasar terbaru dan parameter yang Anda masukkan ke dalam algoritma Random Forest.
        </p>
    </div>

    <!-- Main Price Card -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-100/50 p-8 sm:p-10 text-center space-y-6 mb-8">
        <div>
            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider">
                Kategori: {{ $prediction->kategori }}
            </span>
        </div>
        
        <div class="space-y-1">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">ESTIMASI HARGA</p>
            <p class="text-4xl sm:text-5xl font-extrabold text-blue-600">
                Rp {{ number_format($prediction->harga_prediksi, 0, ',', '.') }}
            </p>
        </div>
        
        <!-- Confidence Interval -->
        <div class="max-w-md mx-auto space-y-2 pt-2">
            <div class="flex justify-between items-center text-xs font-bold">
                <span class="text-slate-400">Interval Kepercayaan</span>
                <span class="text-emerald-600">Tinggi (95%)</span>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full w-[92%]"></div>
            </div>
            
            <p class="text-[11px] font-bold text-slate-400 italic">
                *Rentang estimasi: Rp {{ number_format($intervalMin, 0, ',', '.') }} - Rp {{ number_format($intervalMax, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Details Columns -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
        <!-- Left Column: Faktor Penentu Harga -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-md p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-blue-600 text-[22px]">bar_chart</span>
                <h3 class="text-md font-bold text-slate-900">Faktor Penentu Harga</h3>
            </div>
            
            <div class="space-y-5">
                @php
                    $colors = ['bg-blue-600', 'bg-teal-500', 'bg-amber-500', 'bg-indigo-600'];
                    $index = 0;
                @endphp
                @foreach($factors as $factor => $percent)
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs font-bold text-slate-600">
                            <span>{{ $factor }}</span>
                            <span class="text-slate-900">{{ $percent }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="{{ $colors[$index % 4] }} h-full rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @php $index++; @endphp
                @endforeach
            </div>
        </div>

        <!-- Right Column: Perbandingan Pasar -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-md p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-blue-600 text-[22px]">balance</span>
                <h3 class="text-md font-bold text-slate-900">Perbandingan Pasar (Kec. {{ $prediction->kecamatan }})</h3>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-150 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50">
                            <th class="py-2 px-3">Fitur</th>
                            <th class="py-2 px-3">Properti Anda</th>
                            <th class="py-2 px-3">Rata-rata Area</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs font-semibold text-slate-600">
                        <tr class="border-b border-slate-100">
                            <td class="py-3 px-3">Luas Tanah</td>
                            <td class="py-3 px-3 text-slate-900 font-bold">{{ $prediction->luas_tanah }} m²</td>
                            <td class="py-3 px-3 text-slate-400">{{ $areaAvgLt }} m²</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-3 px-3">Luas Bangunan</td>
                            <td class="py-3 px-3 text-slate-900 font-bold">{{ $prediction->luas_bangunan }} m²</td>
                            <td class="py-3 px-3 text-slate-400">{{ $areaAvgLb }} m²</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-3 px-3">Kamar Tidur / Mandi</td>
                            <td class="py-3 px-3 text-slate-900 font-bold">{{ $prediction->kamar_tidur }} / {{ $prediction->kamar_mandi }}</td>
                            <td class="py-3 px-3 text-slate-400">{{ $areaAvgKt }} / {{ $areaAvgKm }}</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-3 px-3">Fasilitas Tambahan</td>
                            <td class="py-3 px-3 text-slate-900 font-bold">
                                {{ ($prediction->garasi ? 'Garasi' : '') . ($prediction->garasi && $prediction->taman ? ', ' : '') . ($prediction->taman ? 'Taman' : '') ?: '-' }}
                            </td>
                            <td class="py-3 px-3 text-slate-400">Umum</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3">Harga Rata-rata</td>
                            <td class="py-3 px-3 text-blue-600 font-extrabold">Rp {{ number_format($prediction->harga_prediksi, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-slate-400">Rp {{ number_format($areaAvgPrice, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap justify-center gap-4 no-print border-t border-slate-100 pt-8">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-slate-50 border border-slate-200 text-blue-600 font-bold rounded-xl transition-all shadow-xs hover:shadow-md cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">download</span>
            Cetak Laporan PDF
        </button>
        <a href="{{ route('prediction.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-600/10 hover:shadow-lg hover:shadow-blue-600/20 cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">replay</span>
            Mulai Lagi
        </a>
    </div>
</div>
@endsection
