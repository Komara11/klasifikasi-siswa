@extends('layouts.house')

@section('title', 'Metodologi Prediksi Random Forest')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="space-y-3 mb-12 text-center">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Metodologi Algoritma Prediksi</h1>
        <p class="text-slate-500 text-sm font-semibold max-w-lg mx-auto leading-relaxed">
            Rincian metodologis mengenai penerapan algoritma Random Forest untuk estimasi harga properti di Kabupaten Majalengka.
        </p>
    </div>

    <!-- Main Content Grid -->
    <div class="space-y-10">
        
        <!-- Algoritma Random Forest -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-md p-8 space-y-4">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-blue-600 text-[24px]">account_tree</span>
                <h2 class="text-xl font-bold text-slate-900">Random Forest Regressor</h2>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">
                Random Forest adalah algoritma *ensemble learning* berbasis pohon keputusan (*decision trees*). Untuk tugas regresi (seperti memprediksi harga properti), algoritma ini bekerja dengan membangun banyak pohon keputusan selama fase pelatihan dan mengeluarkan rata-rata prediksi dari masing-masing pohon individu sebagai prediksi akhir.
            </p>
            <p class="text-sm text-slate-600 leading-relaxed">
                Metode ini sangat andal dalam menangani hubungan non-linear yang kompleks dan meminimalkan masalah *overfitting* yang sering terjadi pada pohon keputusan tunggal.
            </p>
        </div>

        <!-- Workflow Diagram -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-md p-8 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-blue-600 text-[24px]">flowsheet</span>
                <h2 class="text-xl font-bold text-slate-900">Alur Kerja Sistem Prediksi</h2>
            </div>
            
            <!-- Custom CSS Tree Workflow -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-center">
                    <p class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1">01. Input Data</p>
                    <p class="text-[10px] text-slate-400 font-bold">LT, LB, KT, KM, Jarak, Kondisi, Kecamatan</p>
                </div>
                <div class="hidden md:flex justify-center text-slate-300">
                    <span class="material-symbols-outlined text-[28px]">arrow_forward</span>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center ring-1 ring-blue-300">
                    <p class="text-xs font-extrabold text-blue-800 uppercase tracking-wider mb-1">02. Random Forest</p>
                    <p class="text-[10px] text-blue-500 font-bold">100 Pohon Keputusan Independen</p>
                </div>
                <div class="hidden md:flex justify-center text-slate-300">
                    <span class="material-symbols-outlined text-[28px]">arrow_forward</span>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-center">
                    <p class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-1">03. Hasil Prediksi</p>
                    <p class="text-[10px] text-slate-400 font-bold">Output Estimasi Harga Real-time</p>
                </div>
            </div>
        </div>

        <!-- Hyperparameters -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-md p-8 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-blue-600 text-[24px]">tune</span>
                <h2 class="text-xl font-bold text-slate-900">Parameter Model Utama</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-slate-800">1. n_estimators</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Jumlah pohon keputusan yang akan dibangun di dalam hutan. Semakin banyak pohon, hasil prediksi akan semakin stabil, namun waktu komputasi juga akan meningkat. Default sistem ini adalah 100 pohon.
                    </p>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-slate-800">2. max_depth</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Kedalaman maksimum dari setiap pohon keputusan. Kedalaman yang terlalu tinggi dapat menyebabkan overfitting, sedangkan kedalaman yang rendah menyebabkan underfitting. Default diatur sebesar 15.
                    </p>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-slate-800">3. min_samples_split</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Jumlah minimum sampel data yang diperlukan untuk membagi simpul internal pohon. Membantu mengontrol tingkat pertumbuhan percabangan pohon keputusan. Default diatur sebesar 2.
                    </p>
                </div>
            </div>
        </div>

        <!-- Metrik Evaluasi -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-md p-8 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                <span class="material-symbols-outlined text-blue-600 text-[24px]">calculate</span>
                <h2 class="text-xl font-bold text-slate-900">Metrik Evaluasi Model</h2>
            </div>
            
            <div class="space-y-6">
                <!-- R2 -->
                <div class="flex flex-col md:flex-row gap-4 justify-between items-start border-b border-slate-100 pb-4">
                    <div class="space-y-1 max-w-lg">
                        <h4 class="text-sm font-bold text-slate-800">Coefficient of Determination (R² Score)</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Mengukur seberapa baik model dapat menerangkan variasi dari data aktual. Nilai R² berkisar antara 0 hingga 1. Akurasi R² sistem kami adalah **0.942**, menandakan model sangat andal memproyeksikan harga pasar Majalengka.
                        </p>
                    </div>
                    <div class="bg-slate-50 px-4 py-2.5 rounded-lg border border-slate-200 font-mono text-xs font-bold text-slate-700 whitespace-nowrap self-center">
                        R² = 1 - (SS_res / SS_tot)
                    </div>
                </div>

                <!-- MAE -->
                <div class="flex flex-col md:flex-row gap-4 justify-between items-start border-b border-slate-100 pb-4">
                    <div class="space-y-1 max-w-lg">
                        <h4 class="text-sm font-bold text-slate-800">Mean Absolute Error (MAE)</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Rata-rata dari nilai absolut selisih antara prediksi model dan nilai harga rumah riil. Merepresentasikan rata-rata deviasi prediksi dalam unit mata uang rupiah secara intuitif.
                        </p>
                    </div>
                    <div class="bg-slate-50 px-4 py-2.5 rounded-lg border border-slate-200 font-mono text-xs font-bold text-slate-700 whitespace-nowrap self-center">
                        MAE = (1/n) * Σ|y_i - ŷ_i|
                    </div>
                </div>

                <!-- RMSE -->
                <div class="flex flex-col md:flex-row gap-4 justify-between items-start">
                    <div class="space-y-1 max-w-lg">
                        <h4 class="text-sm font-bold text-slate-800">Root Mean Squared Error (RMSE)</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Akar kuadrat dari rata-rata kuadrat selisih prediksi dengan nilai riil. RMSE memberikan hukuman (penalty) lebih besar pada error yang bernilai besar, sangat baik untuk mendeteksi outlier data harga ekstrim.
                        </p>
                    </div>
                    <div class="bg-slate-50 px-4 py-2.5 rounded-lg border border-slate-200 font-mono text-xs font-bold text-slate-700 whitespace-nowrap self-center">
                        RMSE = √((1/n) * Σ(y_i - ŷ_i)²)
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
