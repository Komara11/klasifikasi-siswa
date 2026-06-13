<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\QuestionnaireQuestion;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === USERS ===
        User::create([
            'name' => 'Guru BK (Admin)',
            'username' => 'admin',
            'email' => 'admin@clovercode.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Drs. H. Sudrajat, M.M.',
            'username' => 'kepsek',
            'email' => 'kepsek@clovercode.com',
            'password' => Hash::make('kepsek123'),
            'role' => 'kepsek',
        ]);

        // === CLASSROOMS ===
        $ixA = Classroom::create(['name' => 'IX-A', 'grade' => 'IX', 'academic_year' => '2025/2026']);
        $ixB = Classroom::create(['name' => 'IX-B', 'grade' => 'IX', 'academic_year' => '2025/2026']);
        $ixC = Classroom::create(['name' => 'IX-C', 'grade' => 'IX', 'academic_year' => '2025/2026']);
        $ixD = Classroom::create(['name' => 'IX-D', 'grade' => 'IX', 'academic_year' => '2025/2026']);

        // === SUBJECTS (11 Mata Pelajaran SMP) ===
        Subject::create(['name' => 'Pendidikan Agama dan Budi Pekerti', 'code' => 'agama', 'weight' => 0.80]);
        Subject::create(['name' => 'Pendidikan Pancasila', 'code' => 'ppkn', 'weight' => 0.80]);
        Subject::create(['name' => 'Bahasa Indonesia', 'code' => 'bahasa_indonesia', 'weight' => 1.00]);
        Subject::create(['name' => 'Matematika', 'code' => 'matematika', 'weight' => 1.20]);
        Subject::create(['name' => 'Ilmu Pengetahuan Alam', 'code' => 'ipa', 'weight' => 1.20]);
        Subject::create(['name' => 'Ilmu Pengetahuan Sosial', 'code' => 'ips', 'weight' => 1.10]);
        Subject::create(['name' => 'Bahasa Inggris', 'code' => 'bahasa_inggris', 'weight' => 1.10]);
        Subject::create(['name' => 'Pendidikan Jasmani, Olahraga dan Kesehatan', 'code' => 'pjok', 'weight' => 0.70]);
        Subject::create(['name' => 'Informatika', 'code' => 'informatika', 'weight' => 0.90]);
        Subject::create(['name' => 'Seni dan Budaya', 'code' => 'seni_budaya', 'weight' => 0.80]);
        Subject::create(['name' => 'Bahasa Cirebon', 'code' => 'bahasa_cirebon', 'weight' => 0.70]);

        // === QUESTIONNAIRE QUESTIONS ===
        QuestionnaireQuestion::create(['question' => 'Saya senang memecahkan soal matematika atau teka-teki logika.', 'category' => 'IPA', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya tertarik melakukan eksperimen sains atau mempelajari gejala alam.', 'category' => 'IPA', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya menikmati diskusi masalah sosial, sejarah, dan politik.', 'category' => 'IPS', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya senang membaca novel, menulis puisi, atau mempelajari bahasa asing.', 'category' => 'Bahasa', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya suka merakit alat, memperbaiki sirkuit elektronik, atau coding.', 'category' => 'Vokasi', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya senang menggambar, mendesain produk, atau membuat kerajinan tangan.', 'category' => 'Vokasi', 'weight' => 0.60]);

        // Vocational sub-major questions
        QuestionnaireQuestion::create(['question' => 'Saya tertarik membuat aplikasi, website, atau belajar pemrograman komputer.', 'category' => 'RPL', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya suka memasang jaringan internet, mengkonfigurasi server, atau troubleshooting komputer.', 'category' => 'TKJ', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya tertarik mengedit video, membuat animasi, atau desain grafis.', 'category' => 'MM', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya suka menghitung keuangan, membuat pembukuan, atau belajar akuntansi.', 'category' => 'AKL', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya tertarik mengatur administrasi, mengetik dokumen, atau mengelola arsip kantor.', 'category' => 'OTKP', 'weight' => 0.80]);
        QuestionnaireQuestion::create(['question' => 'Saya suka memperbaiki mesin motor/mobil, belajar otomotif, atau bengkel.', 'category' => 'TKR', 'weight' => 0.80]);

        // === SETTINGS ===
        Setting::setValue('school_name', 'SMP Negeri 1 Sumber');
        Setting::setValue('school_address', 'Jl. Nyi Mas Gandasari No.2, Sumber, Kec. Sumber, Kab. Cirebon, Jawa Barat 45611');
        Setting::setValue('academic_year', '2025/2026');
        Setting::setValue('school_logo', null);
        Setting::setValue('principal_name', 'Drs. H. Sudrajat, M.M.');
        Setting::setValue('principal_nip', '19680312 199403 1 005');


    }
}
