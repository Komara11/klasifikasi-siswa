# PRODUCT REQUIREMENTS DOCUMENT (PRD) vFINAL

# Sistem Rekomendasi Peminatan Pendidikan Lanjutan Siswa SMP

## Berbasis Laravel 12 + Naive Bayes

### SMP Negeri 1 Sumber

---

# 1. INFORMASI DOKUMEN

| Item               | Detail                                                 |
| ------------------ | ------------------------------------------------------ |
| Nama Sistem        | Sistem Rekomendasi Peminatan Pendidikan Lanjutan Siswa |
| Versi              | PRD vFINAL                                             |
| Platform           | Web Application                                        |
| Framework          | Laravel 12                                             |
| Arsitektur         | Monolith Multi-Role System                             |
| Studi Kasus        | SMP Negeri 1 Sumber                                    |
| Jenis Sistem       | Sistem Informasi Akademik + Machine Learning           |
| Metode Klasifikasi | Gaussian Naive Bayes                                   |
| Frontend           | Blade + TailwindCSS                                    |
| Database           | MySQL                                                  |
| Status             | FINAL                                                  |

---

# 2. LATAR BELAKANG

Proses rekomendasi peminatan pendidikan lanjutan siswa SMP masih dilakukan secara manual oleh Guru BK berdasarkan observasi dan penilaian subjektif.

Hal tersebut menyebabkan:

* proses lama,
* hasil kurang konsisten,
* sulit didokumentasikan,
* dan sulit dianalisis secara akademik.

Sistem ini dikembangkan untuk membantu Guru BK memberikan rekomendasi jalur pendidikan lanjutan berdasarkan:

* nilai akademik,
* minat siswa,
* dan data historis siswa sebelumnya.

Sistem juga menyediakan:

* akses mandiri hasil siswa menggunakan NIS,
* dashboard statistik,
* laporan PDF dan Excel,
* serta evaluasi model machine learning.

---

# 3. TUJUAN SISTEM

## Tujuan Utama

Menghasilkan rekomendasi jalur pendidikan lanjutan siswa SMP menggunakan algoritma Naive Bayes berdasarkan:

* nilai akademik,
* minat siswa,
* dan data historis.

---

# Kategori Rekomendasi

| Kategori | Jalur Pendidikan          |
| -------- | ------------------------- |
| IPA      | SMA Jalur Sains           |
| IPS      | SMA Jalur Sosial          |
| Bahasa   | SMA Jalur Bahasa          |
| Vokasi   | SMK / Pendidikan Kejuruan |

---

# Target Sistem

| Target        | Nilai     |
| ------------- | --------- |
| Akurasi Model | ≥ 80%     |
| Response Time | < 3 detik |
| Multi-role    | Ya        |
| Export PDF    | Ya        |
| Export Excel  | Ya        |

---

# 4. RUANG LINGKUP SISTEM

---

# DALAM RUANG LINGKUP

## Modul Akademik

* CRUD siswa
* CRUD kelas
* CRUD mata pelajaran
* input nilai
* import Excel

---

## Modul Kuisioner

* kuisioner minat
* skala Likert 1–5
* pengelompokan minat

---

## Modul Machine Learning

* training model
* klasifikasi Naive Bayes
* confusion matrix
* evaluasi model
* K-Fold Validation

---

## Modul Hasil

* rekomendasi peminatan
* probabilitas kelas
* faktor dominan

---

## Modul Laporan

* export PDF
* export Excel
* rekap kelas
* surat rekomendasi siswa

---

## Modul Multi Role

* Admin / Guru BK
* Siswa
* Kepala Sekolah

---

# DI LUAR RUANG LINGKUP

* aplikasi mobile
* integrasi Dapodik
* multi sekolah
* chatbot AI
* realtime system
* pembayaran sekolah
* absensi
* algoritma selain Naive Bayes

---

# 5. ARSITEKTUR SISTEM

# Jenis Arsitektur

## Monolith Web Application

```text id="5yc7fh"
Browser
↓
Laravel 12 Application
├── Authentication
├── Student Management
├── Questionnaire Module
├── Naive Bayes Engine
├── Reporting System
└── Dashboard
↓
MySQL Database
```

---

# Alasan Pemilihan

* lebih sederhana,
* mudah maintenance,
* cocok untuk sistem sekolah,
* cocok untuk skripsi,
* tidak membutuhkan microservice,
* biaya development nol.

---

# 6. TECHNOLOGY STACK

| Layer             | Teknologi              |
| ----------------- | ---------------------- |
| Backend           | Laravel 12             |
| Frontend          | Blade                  |
| CSS               | TailwindCSS            |
| UI Interaction    | AlpineJS               |
| Database          | MySQL                  |
| Authentication    | Laravel Breeze         |
| Role Permission   | Spatie Permission      |
| PDF Export        | DomPDF                 |
| Excel Export      | Laravel Excel          |
| Charts            | Chart.js               |
| Icons             | Heroicons              |
| ML Engine         | Native PHP Naive Bayes |
| Local Development | Laragon                |
| Version Control   | GitHub                 |

---

# 7. USER ROLE SYSTEM

---

# ROLE 1 — ADMIN / GURU BK

## Deskripsi

Operator utama sistem.

---

## Hak Akses

### Dashboard

* melihat statistik
* melihat akurasi model

### Data Siswa

* tambah siswa
* edit siswa
* hapus siswa
* import Excel

### Nilai Akademik

* input nilai
* edit nilai
* import bulk

### Kuisioner

* input kuisioner
* edit kuisioner

### Machine Learning

* training model
* klasifikasi siswa
* evaluasi model

### Laporan

* export PDF
* export Excel

### Pengaturan

* tahun ajaran
* bobot mapel
* akun kepala sekolah

---

# ROLE 2 — SISWA

## Deskripsi

Siswa tidak memiliki akun.

Akses menggunakan NIS.

---

## Hak Akses

### Halaman Publik

* input NIS
* melihat hasil rekomendasi
* melihat probabilitas
* melihat faktor dominan

---

## Tidak Dapat

* edit data
* export
* login dashboard

---

# ROLE 3 — KEPALA SEKOLAH

## Hak Akses

### Dashboard

* statistik peminatan
* distribusi kelas
* akurasi model

### Laporan

* export PDF
* export Excel

---

## Tidak Dapat

* input data
* training model
* konfigurasi sistem

---

# 8. FLOW SISTEM

---

# FLOW ADMIN

```text id="jlwmvi"
Login
↓
Dashboard
↓
Kelola Data Siswa
↓
Input Nilai
↓
Input Kuisioner
↓
Training Model
↓
Klasifikasi
↓
Lihat Hasil
↓
Export PDF/Excel
```

---

# FLOW SISWA

```text id="nmrth6"
Buka Website
↓
Input NIS
↓
Validasi NIS
↓
Tampilkan Hasil
```

---

# FLOW KEPALA SEKOLAH

```text id="vs1fkw"
Login
↓
Dashboard Statistik
↓
Lihat Rekap
↓
Export Laporan
```

---

# 9. STRUKTUR HALAMAN

---

# PUBLIC PAGE

## /cek-hasil

### Komponen

* logo sekolah
* input NIS
* tombol cek hasil
* hasil rekomendasi
* chart probabilitas

---

# LOGIN PAGE

## /login

### Komponen

* username
* password
* remember me
* login button

---

# ADMIN PAGES

| Page           | URL                    |
| -------------- | ---------------------- |
| Dashboard      | /admin/dashboard       |
| Data Siswa     | /admin/students        |
| Input Nilai    | /admin/scores          |
| Kuisioner      | /admin/questionnaires  |
| Training Model | /admin/training        |
| Klasifikasi    | /admin/classifications |
| Hasil          | /admin/results         |
| Laporan        | /admin/reports         |
| Pengaturan     | /admin/settings        |

---

# KEPALA SEKOLAH PAGES

| Page      | URL               |
| --------- | ----------------- |
| Dashboard | /kepsek/dashboard |
| Laporan   | /kepsek/reports   |

---

# 10. FRONTEND DESIGN SYSTEM

# Design Direction

## Academic Administrative System

Modern tetapi realistis seperti sistem sekolah sungguhan.

---

# Karakter UI

| Element    | Style         |
| ---------- | ------------- |
| Sidebar    | Solid navy    |
| Layout     | Compact clean |
| Table      | Dominan       |
| Card       | Secukupnya    |
| Animation  | Minimal       |
| Chart      | Simple        |
| Typography | Formal modern |

---

# Warna Utama

| Usage      | Color   |
| ---------- | ------- |
| Primary    | #1E3A5F |
| Sidebar    | #243B53 |
| Background | #F5F7FA |
| Border     | #D9E2EC |
| Text       | #102A43 |
| Success    | #2F855A |
| Warning    | #D69E2E |
| Danger     | #C53030 |

---

# Typography

## Font

* Inter
* Plus Jakarta Sans

---

# UI Guidelines

## Gunakan

* compact layout
* realistic tables
* subtle shadow
* medium radius
* clean chart

---

## Hindari

* glassmorphism
* neon
* futuristic dashboard
* crypto style
* startup SaaS look
* giant floating cards

---

# 11. DATABASE DESIGN

---

# TABEL USERS

| Field      | Type      |
| ---------- | --------- |
| id         | bigint    |
| name       | varchar   |
| username   | varchar   |
| password   | varchar   |
| role       | enum      |
| created_at | timestamp |

---

# TABEL STUDENTS

| Field        | Type    |
| ------------ | ------- |
| id           | bigint  |
| nis          | varchar |
| name         | varchar |
| gender       | enum    |
| classroom_id | bigint  |
| birth_date   | date    |
| address      | text    |

---

# TABEL CLASSROOMS

| Field         | Type    |
| ------------- | ------- |
| id            | bigint  |
| name          | varchar |
| grade         | varchar |
| academic_year | varchar |

---

# TABEL SUBJECTS

| Field  | Type    |
| ------ | ------- |
| id     | bigint  |
| name   | varchar |
| weight | decimal |

---

# TABEL STUDENT_SCORES

| Field      | Type    |
| ---------- | ------- |
| id         | bigint  |
| student_id | bigint  |
| subject_id | bigint  |
| semester   | integer |
| score      | decimal |

---

# TABEL QUESTIONNAIRE_QUESTIONS

| Field    | Type    |
| -------- | ------- |
| id       | bigint  |
| question | text    |
| category | varchar |
| weight   | decimal |

---

# TABEL QUESTIONNAIRE_ANSWERS

| Field       | Type    |
| ----------- | ------- |
| id          | bigint  |
| student_id  | bigint  |
| question_id | bigint  |
| score       | integer |

---

# TABEL CLASSIFICATIONS

| Field              | Type      |
| ------------------ | --------- |
| id                 | bigint    |
| student_id         | bigint    |
| recommended_path   | varchar   |
| ipa_probability    | decimal   |
| ips_probability    | decimal   |
| bahasa_probability | decimal   |
| vokasi_probability | decimal   |
| dominant_factor    | text      |
| classified_at      | timestamp |

---

# TABEL NAIVE_BAYES_MODELS

| Field             | Type    |
| ----------------- | ------- |
| id                | bigint  |
| class_name        | varchar |
| feature_name      | varchar |
| mean              | decimal |
| variance          | decimal |
| prior_probability | decimal |
| model_version     | varchar |

---

# 12. MACHINE LEARNING DESIGN

# Algoritma

Gaussian Naive Bayes.

---

# FLOW TRAINING

```text id="ydjlwm"
Ambil Data Training
↓
Kelompokkan per kelas
↓
Hitung Mean
↓
Hitung Variance
↓
Hitung Prior Probability
↓
Simpan Model
```

---

# FLOW KLASIFIKASI

```text id="74pjxv"
Input Data Siswa
↓
Normalisasi Data
↓
Hitung Likelihood
↓
Hitung Posterior
↓
Ambil Probabilitas Tertinggi
↓
Simpan Hasil
```

---

# Formula Prior Probability

P(C_k)=\frac{N_k}{N}

---

# Formula Gaussian Likelihood

P(x_i|C_k)=\frac{1}{\sqrt{2\pi\sigma_k^2}}e^{-\frac{(x_i-\mu_k)^2}{2\sigma_k^2}}

---

# Formula Posterior

P(C_k|X)=P(C_k)\prod_{i=1}^{n}P(x_i|C_k)

---

# Evaluasi Model

## Metrics

* Accuracy
* Precision
* Recall
* F1-Score

---

# Validation

## K-Fold Cross Validation

```text id="3oy16g"
k = 10
```

---

# 13. DETAIL FITUR

---

# FITUR IMPORT EXCEL

## Fungsi

* import siswa
* import nilai
* import data training

---

# FITUR EXPORT PDF

## Output

* surat rekomendasi
* laporan rekap

---

# FITUR EXPORT EXCEL

## Output

* data siswa
* hasil klasifikasi
* statistik kelas

---

# FITUR SEARCH & FILTER

## Filter

* kelas
* tahun ajaran
* status data
* kategori rekomendasi

---

# FITUR ACTIVITY LOG

## Aktivitas

* login
* tambah data
* edit data
* delete data
* training model
* export laporan

---

# 14. BUSINESS RULES

---

# RULE NILAI

* nilai 0–100
* tidak boleh kosong

---

# RULE KUISIONER

* skala 1–5
* wajib lengkap

---

# RULE KLASIFIKASI

Klasifikasi hanya dapat dilakukan jika:

* nilai lengkap
* kuisioner lengkap
* model aktif tersedia

---

# RULE SISWA

Siswa hanya dapat melihat hasil.

---

# RULE KEPALA SEKOLAH

Read-only access.

---

# 15. SECURITY SYSTEM

---

# Authentication

* session auth
* hashed password
* middleware role

---

# Protection

* CSRF
* XSS Protection
* Rate Limiting
* Session Timeout

---

# Student Public Access

* tanpa login
* validasi NIS
* read-only

---

# 16. VALIDATION RULES

| Field | Rule     |
| ----- | -------- |
| NIS   | unique   |
| Nama  | required |
| Nilai | numeric  |
| Nilai | min 0    |
| Nilai | max 100  |

---

# 17. ERROR HANDLING

| Error               | Handling        |
| ------------------- | --------------- |
| NIS tidak ditemukan | alert           |
| Data belum lengkap  | warning         |
| Training gagal      | error           |
| File invalid        | validasi upload |
| Session expired     | redirect login  |

---

# 18. RESPONSIVE DESIGN

---

# Desktop

Prioritas utama admin dan kepala sekolah.

---

# Tablet

Support dashboard dan laporan.

---

# Mobile

Prioritas halaman siswa input NIS.

---

# 19. PERFORMANCE TARGET

| Item           | Target     |
| -------------- | ---------- |
| Login          | < 2 detik  |
| Dashboard      | < 3 detik  |
| Export PDF     | < 10 detik |
| Training Model | < 30 detik |

---

# 20. LARAVEL PACKAGE

| Package                   | Fungsi          |
| ------------------------- | --------------- |
| laravel/breeze            | authentication  |
| spatie/laravel-permission | role permission |
| barryvdh/laravel-dompdf   | PDF export      |
| maatwebsite/excel         | Excel export    |

---

# 21. TESTING

---

# Functional Testing

* login
* CRUD
* klasifikasi
* export

---

# Validation Testing

* validasi form
* validasi NIS
* validasi nilai

---

# Machine Learning Testing

* accuracy
* confusion matrix
* precision
* recall

---

# 22. DEPLOYMENT

---

# Development

* Laragon
* GitHub

---

# Production

* Railway free tier
* Render free tier
* localhost sidang

---

# Minimum Requirement

| Resource | Minimum |
| -------- | ------- |
| RAM      | 2GB     |
| CPU      | 2 Core  |
| PHP      | 8.3     |
| Database | MySQL 8 |

---

# 23. FUTURE DEVELOPMENT

* aplikasi mobile
* integrasi Dapodik
* multi sekolah
* rekomendasi jurusan SMK spesifik
* AI analytics

---

# 24. KESIMPULAN

Sistem dirancang sebagai aplikasi web akademik berbasis Laravel 12 dengan arsitektur monolith modern yang fokus pada:

* kemudahan penggunaan,
* maintainability,
* kebutuhan sekolah,
* dan implementasi machine learning yang realistis.

Sistem menggunakan pendekatan multi-role:

* Admin/Guru BK
* Siswa
* Kepala Sekolah

UI menggunakan pendekatan:

# Academic Administrative System

agar:

* realistis,
* profesional,
* tidak terlihat AI-generated,
* dan cocok digunakan pada lingkungan sekolah maupun kebutuhan skripsi.
