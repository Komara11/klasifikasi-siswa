@extends('layouts.house')

@section('title', 'Riwayat Prediksi')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="space-y-2 mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Riwayat Prediksi Anda</h1>
        <p class="text-slate-500 text-sm font-semibold">
            Berikut adalah daftar riwayat estimasi harga rumah yang pernah Anda lakukan di sistem kami.
        </p>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-100/50 overflow-hidden">
        @if($predictions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-150 text-xs font-bold text-slate-400 uppercase bg-slate-50 tracking-wider">
                            <th class="py-4 px-6">Tanggal</th>
                            <th class="py-4 px-6">Spesifikasi</th>
                            <th class="py-4 px-6">Lokasi / Jarak</th>
                            <th class="py-4 px-6">Estimasi Harga</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs font-semibold text-slate-600">
                        @foreach($predictions as $pred)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-6 text-slate-500">
                                    {{ $pred->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-0.5 text-slate-800">
                                        <p class="font-bold">LT: {{ $pred->luas_tanah }}m² | LB: {{ $pred->luas_bangunan }}m²</p>
                                        <p class="text-[10px] text-slate-400">KT: {{ $pred->kamar_tidur }} | KM: {{ $pred->kamar_mandi }} | Kondisi: {{ $pred->kondisi }}/5</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="space-y-0.5">
                                        <p class="text-slate-800 font-bold">Kec. {{ $pred->kecamatan }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $pred->jarak_kota }} km ke pusat kota</p>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-extrabold text-blue-600">Rp {{ number_format($pred->harga_prediksi, 0, ',', '.') }}</span>
                                        <span>
                                            <span class="inline-flex px-2 py-0.5 rounded-full bg-blue-50 text-[9px] font-bold text-blue-600 border border-blue-100 uppercase">
                                                {{ $pred->kategori }}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a href="{{ route('prediction.show', $pred->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors font-bold">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 border-t border-slate-100 bg-slate-50">
                {{ $predictions->links() }}
            </div>
        @else
            <div class="p-12 text-center space-y-4">
                <div class="w-16 h-16 mx-auto bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-[36px]">history</span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-slate-800">Belum ada riwayat prediksi</h3>
                    <p class="text-xs text-slate-400 font-medium max-w-xs mx-auto">Silakan gunakan kalkulator prediksi harga untuk melihat riwayat analisis Anda.</p>
                </div>
                <div>
                    <a href="{{ route('prediction.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-600/10">
                        Buat Prediksi Pertama
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
