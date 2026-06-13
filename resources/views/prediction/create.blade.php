@extends('layouts.house')

@section('title', 'Kalkulator Estimasi Harga Rumah')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center space-y-3 mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Estimasi Harga Rumah</h1>
        <p class="text-slate-500 text-sm font-semibold max-w-lg mx-auto leading-relaxed">
            Lengkapi detail properti Anda untuk mendapatkan prediksi harga pasar yang akurat berdasarkan analisis machine learning.
        </p>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-100/50 overflow-hidden">
        <form method="POST" action="{{ route('prediction.store') }}" class="p-8 sm:p-10 space-y-10">
            @csrf

            <!-- Section 1: Detail Properti -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">home</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Detail Properti</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="luas_tanah" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas Tanah (m²)</label>
                        <input type="number" name="luas_tanah" id="luas_tanah" value="{{ old('luas_tanah') }}" required placeholder="Contoh: 120"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label for="luas_bangunan" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Luas Bangunan (m²)</label>
                        <input type="number" name="luas_bangunan" id="luas_bangunan" value="{{ old('luas_bangunan') }}" required placeholder="Contoh: 80"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label for="kamar_tidur" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kamar Tidur</label>
                        <input type="number" name="kamar_tidur" id="kamar_tidur" value="{{ old('kamar_tidur', 2) }}" required placeholder="Jumlah" min="1"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label for="kamar_mandi" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kamar Mandi</label>
                        <input type="number" name="kamar_mandi" id="kamar_mandi" value="{{ old('kamar_mandi', 1) }}" required placeholder="Jumlah" min="1"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Lokasi & Akses -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">distance</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Lokasi & Akses</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="kecamatan" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                        <select name="kecamatan" id="kecamatan" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all custom-select">
                            <option value="">Pilih Kecamatan</option>
                            @foreach($kecamatans as $kec)
                                <option value="{{ $kec }}" {{ old('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="jarak_kota" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jarak ke Pusat Kota (km)</label>
                        <input type="number" step="0.1" name="jarak_kota" id="jarak_kota" value="{{ old('jarak_kota') }}" required placeholder="Contoh: 2.5"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 3: Informasi Tambahan -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">award_star</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Informasi Tambahan</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 items-start">
                    <!-- Condition Slider -->
                    <div class="space-y-3" x-data="{ condition: 3 }">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Kondisi Bangunan (1-5)</label>
                            <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md" x-text="condition"></span>
                        </div>
                        <input type="range" name="kondisi" min="1" max="5" step="1" x-model="condition"
                            class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <div class="flex justify-between text-[10px] font-bold text-slate-400">
                            <span>BURUK</span>
                            <span>SEDANG</span>
                            <span>SANGAT BAGUS</span>
                        </div>
                    </div>
                    
                    <!-- Checkboxes -->
                    <div class="flex flex-col gap-4 sm:pt-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="garasi" value="1" {{ old('garasi') ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-slate-50">
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800 transition-colors">Tersedia Garasi</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="taman" value="1" {{ old('taman') ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 bg-slate-50">
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-800 transition-colors">Tersedia Taman</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-slate-100">
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 bg-blue-50 hover:bg-blue-100/80 border border-blue-200 text-blue-700 font-bold rounded-2xl transition-all shadow-xs active:scale-[0.99] cursor-pointer">
                    <span class="material-symbols-outlined text-[22px]">calculate</span>
                    Hitung Estimasi Harga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
