@extends('layouts.house')

@section('title', 'Estimasi Harga Rumah Berbasis Random Forest')

@section('styles')
<style>
    #map {
        height: 450px;
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f5f9;
        z-index: 1;
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<div class="relative overflow-hidden bg-slate-50 border-b border-slate-100 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <!-- Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">psychology</span>
                Analisis Properti Berbasis Kecerdasan Buatan
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Prediksi Harga Rumah <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Majalengka</span> Akurat
            </h1>
            
            <!-- Description -->
            <p class="text-lg text-slate-500 font-medium leading-relaxed">
                Optimalkan keputusan investasi properti Anda dengan estimasi harga real-time menggunakan algoritma Random Forest Machine Learning yang dilatih khusus untuk pasar lokal Majalengka.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-wrap justify-center gap-4 pt-4">
                <a href="{{ route('prediction.create') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-600/10 hover:shadow-lg hover:shadow-blue-600/20 active:scale-[0.98]">
                    Mulai Prediksi
                    <span class="material-symbols-outlined text-[20px]">arrow_right_alt</span>
                </a>
                <a href="{{ route('methodology') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white hover:bg-slate-50 border border-slate-200 text-blue-600 font-bold rounded-xl transition-all shadow-xs hover:shadow-md active:scale-[0.98]">
                    Pelajari Metode
                </a>
            </div>
        </div>
    </div>
    
    <!-- Background grid decoration -->
    <div class="absolute inset-0 z-0 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:16px_16px] [mask-image:radial-gradient(ellipse_50%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-60"></div>
</div>

<!-- Statistics Metrics Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white border border-slate-100 p-8 rounded-2xl shadow-xl shadow-slate-100/50 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-4xl font-extrabold text-blue-600">{{ $accuracyR2 * 100 }}%</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Akurasi Model (R²)</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">percent</span>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white border border-slate-100 p-8 rounded-2xl shadow-xl shadow-slate-100/50 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-4xl font-extrabold text-slate-900">{{ $totalDataset }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Data Properti Latih</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">database</span>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white border border-slate-100 p-8 rounded-2xl shadow-xl shadow-slate-100/50 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-4xl font-extrabold text-slate-900">&lt; 1 Detik</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Waktu Pemrosesan</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">bolt</span>
            </div>
        </div>
    </div>
</div>

<!-- How it works Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center max-w-2xl mx-auto space-y-3 mb-16">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Bagaimana Kami Bekerja</h2>
        <p class="text-slate-500 text-sm font-semibold">Tiga langkah sederhana untuk mendapatkan estimasi nilai properti yang presisi di wilayah Kabupaten Majalengka.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Step 1 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100/80 shadow-xs space-y-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold text-sm">
                01
            </div>
            <h3 class="text-lg font-bold text-slate-900">Input Data</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Masukkan detail properti seperti luas tanah, luas bangunan, jumlah kamar, dan lokasi spesifik di Majalengka.
            </p>
        </div>
        
        <!-- Step 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100/80 shadow-xs space-y-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold text-sm">
                02
            </div>
            <h3 class="text-lg font-bold text-slate-900">Proses AI</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Algoritma Random Forest kami melakukan komputasi regresi terhadap ribuan titik data pasar lokal terkini.
            </p>
        </div>
        
        <!-- Step 3 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100/80 shadow-xs space-y-4 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold text-sm">
                03
            </div>
            <h3 class="text-lg font-bold text-slate-900">Hasil Estimasi</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Dapatkan kisaran harga pasar yang wajar dalam hitungan detik untuk keperluan jual beli atau investasi.
            </p>
        </div>
    </div>
</div>

<!-- Interactive Map Section -->
<div class="bg-slate-50 border-y border-slate-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
            <!-- Map text -->
            <div class="lg:w-1/3 space-y-6">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">Map</div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">Sebaran Lokasi Pasar Properti</h2>
                    <p class="text-slate-500 text-sm font-semibold leading-relaxed">
                        Peta distribusi menampilkan rata-rata harga properti terhitung di setiap kecamatan di Kabupaten Majalengka. Klik marker lokasi untuk melihat info rinci.
                    </p>
                </div>
                
                <div class="space-y-3 bg-white p-4 rounded-xl border border-slate-150">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-500">Harga Tertinggi:</span>
                        <span class="font-extrabold text-blue-600">Majalengka Kota</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-500">Harga Terendah:</span>
                        <span class="font-extrabold text-slate-600">Ligung</span>
                    </div>
                </div>
            </div>
            
            <!-- Map box -->
            <div class="lg:w-2/3 w-full">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

<!-- Research/UMC Info Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <!-- Left Image -->
        <div>
            <img src="{{ asset('images/majalengka_housing.png') }}" alt="Modern Housing Complex" class="rounded-3xl shadow-xl w-full object-cover h-[400px] border border-slate-100">
        </div>
        
        <!-- Right text -->
        <div class="space-y-6">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">Analisis Berbasis Riset</h2>
            <p class="text-slate-500 leading-relaxed text-sm font-medium">
                Dikembangkan oleh tim dari Universitas Muhammadiyah Cirebon, proyek ini menggabungkan keahlian ilmu komputer dengan data spasial wilayah Majalengka untuk menghasilkan transparansi harga properti bagi masyarakat.
            </p>
            
            <ul class="space-y-3">
                <li class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <span class="text-sm font-bold text-slate-700">Model tervalidasi dataset lokal</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <span class="text-sm font-bold text-slate-700">Pembaruan data pasar berkala</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <span class="text-sm font-bold text-slate-700">Open source untuk keperluan akademik</span>
                </li>
            </ul>
            
            <div class="pt-4">
                <a href="{{ route('prediction.create') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-600/10 active:scale-[0.98]">
                    Coba Kalkulator Prediksi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Leaflet Map centered on Majalengka area
        var map = L.map('map', {
            scrollWheelZoom: false
        }).setView([-6.8374, 108.2244], 11);

        // OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Map data from PHP controller
        var subdistricts = @json($mapData);

        subdistricts.forEach(function(s) {
            // Circle representing average price size
            var circle = L.circle([s.lat, s.lng], {
                color: '#2563eb',
                fillColor: '#3b82f6',
                fillOpacity: 0.35,
                radius: 1200 // Size of circle in meters
            }).addTo(map);

            // Pop-up detailing price and sample size
            circle.bindPopup(
                "<div class='space-y-1 py-1 font-sans'>" +
                "<h4 class='font-bold text-sm text-slate-900 border-b border-slate-100 pb-1'>Kecamatan " + s.name + "</h4>" +
                "<p class='text-xs text-slate-500 font-semibold'>Rata-rata Harga:</p>" +
                "<p class='text-sm font-extrabold text-blue-600'>" + s.formatted_price + "</p>" +
                "<p class='text-[10px] text-slate-400 font-bold'>Jumlah Sampel Dataset: " + s.count + "</p>" +
                "</div>"
            );
        });
    });
</script>
@endsection
