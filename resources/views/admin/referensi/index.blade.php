@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- HEADER --}}
    <div class="flex flex-col gap-2">
        <h2 class="font-h2 text-primary font-bold text-2xl flex items-center gap-2">
            <span class="material-symbols-outlined text-3xl">menu_book</span>
            Referensi Teknis Sistem Cerdas
        </h2>
        <p class="text-on-surface-variant text-sm">Dokumentasi komprehensif mengenai cara kerja algoritma Machine Learning (Gaussian Naive Bayes) dalam memberikan rekomendasi peminatan siswa.</p>
    </div>

    {{-- SECTION 1: PENGENALAN --}}
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-primary/5 px-6 py-4 border-b border-outline-variant/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-xl">psychology</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-primary">1. Konsep Dasar Gaussian Naive Bayes</h3>
                <p class="text-xs text-on-surface-variant mt-0.5">Pendekatan Probabilistik Klasifikasi Algoritma</p>
            </div>
        </div>
        <div class="p-6">
            <div class="prose prose-sm max-w-none text-on-surface-variant">
                <p class="mb-4">
                    Sistem ini mengandalkan <strong>Gaussian Naive Bayes</strong>, sebuah varian dari algoritma Naive Bayes yang dirancang khusus untuk menangani fitur berupa angka kontinu (seperti nilai rapor). Algoritma ini memprediksi kelas (jurusan) mana yang paling cocok bagi siswa berdasarkan peluang (*probability*) tertinggi dari historis data sebelumnya.
                </p>
                <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/30 font-mono text-sm flex flex-col gap-2 items-center justify-center my-6">
                    <p class="font-bold text-primary mb-1">Teorema Bayes (Posterior Probability)</p>
                    <div class="bg-white px-4 py-2 rounded shadow-sm border border-outline-variant/20">
                        P(Jurusan | Data Siswa) = <span class="text-emerald-600">P(Jurusan)</span> × <span class="text-blue-600">P(Data Siswa | Jurusan)</span> / P(Data Siswa)
                    </div>
                    <ul class="text-[11px] mt-2 text-left w-full space-y-1">
                        <li><span class="font-bold text-emerald-600">P(Jurusan) / Prior:</span> Peluang dasar sebuah jurusan dipilih secara umum.</li>
                        <li><span class="font-bold text-blue-600">P(Data | Jurusan) / Likelihood:</span> Peluang munculnya nilai akademik tertentu pada siswa di jurusan tersebut (menggunakan Kurva Distribusi Normal/Gaussian).</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: DATA & NORMALISASI --}}
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-primary/5 px-6 py-4 border-b border-outline-variant/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-xl">dataset</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-emerald-800">2. Fitur Data & Proses Normalisasi</h3>
                <p class="text-xs text-emerald-600 mt-0.5">Data Akademik dan Minat Kuesioner</p>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-outline-variant/30 rounded-xl p-5 bg-surface">
                    <h4 class="font-bold text-primary flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-lg">school</span> Nilai Rapor Akademik
                    </h4>
                    <p class="text-sm text-on-surface-variant mb-3">Sistem mengambil data dari 11 mata pelajaran pokok.</p>
                    <ul class="space-y-2 text-sm text-on-surface-variant">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-500 mt-0.5">check_circle</span>
                            <span><strong>Agregasi:</strong> Nilai dihitung dari rata-rata gabungan Semester 1 hingga Semester 5 untuk setiap mata pelajaran.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-500 mt-0.5">check_circle</span>
                            <span><strong>Skala Tetap:</strong> Nilai tidak diberi pembobotan awal agar skala aslinya (0 - 100) tetap konsisten saat diproses kurva Gaussian.</span>
                        </li>
                    </ul>
                </div>
                
                <div class="border border-outline-variant/30 rounded-xl p-5 bg-surface">
                    <h4 class="font-bold text-primary flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-lg">psychology_alt</span> Hasil Kuesioner
                    </h4>
                    <p class="text-sm text-on-surface-variant mb-3">Jawaban kuesioner berskala Likert (1 - 5) dinormalisasi agar seimbang dengan nilai akademik.</p>
                    <ul class="space-y-2 text-sm text-on-surface-variant">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-500 mt-0.5">check_circle</span>
                            <span><strong>Rata-rata Kategori:</strong> Nilai dijumlahkan berdasarkan kategori minat (IPA, IPS, Bahasa, Vokasi), kemudian dirata-ratakan per jumlah pertanyaan agar proporsional.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-emerald-500 mt-0.5">check_circle</span>
                            <span><strong>Scaling:</strong> Rata-rata Likert (1-5) dikalikan dengan 20. Sehingga rentang akhirnya menjadi skala <strong>20 hingga 100</strong>.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: KLASIFIKASI VOKASI KHUSUS --}}
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-primary/5 px-6 py-4 border-b border-outline-variant/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-xl">precision_manufacturing</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-amber-800">3. Penentuan Sub-Klasifikasi Jurusan Vokasi (SMK)</h3>
                <p class="text-xs text-amber-600 mt-0.5">Analisis Lanjutan Spesifik per Program Keahlian</p>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-on-surface-variant mb-5">
                Jika sistem memutuskan bahwa siswa direkomendasikan ke jalur <strong>Vokasi (SMK)</strong>, algoritma tahap kedua (*sub-classification*) dijalankan. Tahap ini menggabungkan skor ketertarikan minat spesifik dari kuesioner (bobot 60%) dengan kecocokan nilai akademik pendukung (bobot 40%). Berikut rumusan rincinya:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- RPL --}}
                <div class="bg-white border border-outline-variant/30 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="font-bold text-primary mb-1">💻 Rekayasa Perangkat Lunak (RPL)</div>
                    <div class="text-xs text-on-surface-variant mb-2">Fokus pada logika komputasional & sains.</div>
                    <div class="bg-surface-container-low text-[11px] p-2 rounded text-on-surface-variant font-mono">
                        (Matematika × 0.5) + <br>
                        (IPA × 0.3) + <br>
                        (Seni Budaya × 0.2)
                    </div>
                </div>

                {{-- TKJ --}}
                <div class="bg-white border border-outline-variant/30 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="font-bold text-primary mb-1">🖧 Teknik Komputer & Jaringan (TKJ)</div>
                    <div class="text-xs text-on-surface-variant mb-2">Fokus perangkat keras & sains dasar.</div>
                    <div class="bg-surface-container-low text-[11px] p-2 rounded text-on-surface-variant font-mono">
                        (IPA × 0.5) + <br>
                        (Matematika × 0.3) + <br>
                        (Seni Budaya × 0.2)
                    </div>
                </div>

                {{-- MM --}}
                <div class="bg-white border border-outline-variant/30 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="font-bold text-primary mb-1">🎨 Multimedia (MM)</div>
                    <div class="text-xs text-on-surface-variant mb-2">Fokus pada kreativitas & linguistik.</div>
                    <div class="bg-surface-container-low text-[11px] p-2 rounded text-on-surface-variant font-mono">
                        (Seni Budaya × 0.5) + <br>
                        (B. Indonesia × 0.3) + <br>
                        (Matematika × 0.2)
                    </div>
                </div>

                {{-- AKL --}}
                <div class="bg-white border border-outline-variant/30 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="font-bold text-primary mb-1">📊 Akuntansi (AKL)</div>
                    <div class="text-xs text-on-surface-variant mb-2">Fokus pada ketelitian & ekonomi.</div>
                    <div class="bg-surface-container-low text-[11px] p-2 rounded text-on-surface-variant font-mono">
                        (Matematika × 0.5) + <br>
                        (IPS × 0.3) + <br>
                        (B. Indonesia × 0.2)
                    </div>
                </div>

                {{-- OTKP --}}
                <div class="bg-white border border-outline-variant/30 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="font-bold text-primary mb-1">📂 Tata Kelola Perkantoran (OTKP)</div>
                    <div class="text-xs text-on-surface-variant mb-2">Fokus pada komunikasi & manajemen.</div>
                    <div class="bg-surface-container-low text-[11px] p-2 rounded text-on-surface-variant font-mono">
                        (B. Indonesia × 0.4) + <br>
                        (IPS × 0.3) + <br>
                        (Matematika × 0.3)
                    </div>
                </div>

                {{-- TKR --}}
                <div class="bg-white border border-outline-variant/30 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="font-bold text-primary mb-1">⚙️ Teknik Kendaraan Ringan (TKR)</div>
                    <div class="text-xs text-on-surface-variant mb-2">Fokus perhitungan mekanis & fisika.</div>
                    <div class="bg-surface-container-low text-[11px] p-2 rounded text-on-surface-variant font-mono">
                        (IPA × 0.4) + <br>
                        (Matematika × 0.4) + <br>
                        (Seni Budaya × 0.2)
                    </div>
                </div>
            </div>
            <div class="mt-4 p-3 bg-blue-50 text-blue-800 text-xs rounded-lg border border-blue-200">
                <strong>💡 Catatan:</strong> Skor akademik di atas akan dikali bobot <strong>40%</strong>, lalu dijumlahkan dengan Nilai Minat Vokasi Spesifik (dari kuesioner) yang dikali <strong>60%</strong>. Vokasi dengan skor total tertinggi akan ditetapkan sebagai rekomendasi akhir.
            </div>
        </div>
    </div>

    {{-- SECTION 4: KONDISI HEURISTIK (COLD START) --}}
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-primary/5 px-6 py-4 border-b border-outline-variant/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-xl">hub</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-blue-800">4. Penanganan Data Kosong (Heuristic Fallback)</h3>
                <p class="text-xs text-blue-600 mt-0.5">Sistem Pengganti Sebelum Model Ditraining</p>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-on-surface-variant mb-4">
                Machine Learning membutuhkan minimal 20-40 siswa untuk dapat memprediksi (*training*) dengan baik. Jika sistem masih benar-benar kosong, sistem menggunakan metode <span class="font-bold">Heuristic Classification</span> (Aturan Pakar Baku) untuk memberikan label klasifikasi sementara agar aplikasi tetap fungsional. Rumus pakar (*Expert Rule*) yang digunakan adalah:
            </p>
            <ul class="space-y-3 text-sm text-on-surface-variant mb-4">
                <li class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-surface-container border border-outline-variant/30 rounded text-xs font-bold w-16 text-center">IPA</span>
                    <span class="font-mono text-[11px] bg-gray-50 px-2 py-1 rounded border border-gray-200">((Mat + IPA)/2 * 0.6) + (Minat IPA * 0.4)</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-surface-container border border-outline-variant/30 rounded text-xs font-bold w-16 text-center">IPS</span>
                    <span class="font-mono text-[11px] bg-gray-50 px-2 py-1 rounded border border-gray-200">((IPS + (B.Indo*0.4 + Mat*0.3 + IPA*0.3))/2 * 0.6) + (Minat IPS * 0.4)</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-surface-container border border-outline-variant/30 rounded text-xs font-bold w-16 text-center">Bahasa</span>
                    <span class="font-mono text-[11px] bg-gray-50 px-2 py-1 rounded border border-gray-200">((B.Indo + B.Inggris)/2 * 0.6) + (Minat Bahasa * 0.4)</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-surface-container border border-outline-variant/30 rounded text-xs font-bold w-16 text-center">Vokasi</span>
                    <span class="font-mono text-[11px] bg-gray-50 px-2 py-1 rounded border border-gray-200">((Seni + (Mat*0.3 + IPA*0.3 + B.Indo*0.4))/2 * 0.6) + (Minat Vokasi * 0.4)</span>
                </li>
            </ul>
            <p class="text-xs text-outline italic">
                Sistem akan secara otomatis berpindah meninggalkan Heuristic dan menggunakan Machine Learning sepenuhnya ketika Anda menekan tombol "Latih Model Baru" di menu Training setelah memiliki cukup data siswa.
            </p>
        </div>
    </div>
</div>
@endsection
