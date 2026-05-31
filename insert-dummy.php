<?php

use App\Models\Student;
use App\Models\Classroom;
use App\Models\Classification;

$class = Classroom::first();

if (!$class) {
    echo "No class found.\n";
    exit;
}

$student = Student::create([
    'nis' => '12345678',
    'name' => 'Demo Siswa (Cek Hasil)',
    'gender' => 'L',
    'classroom_id' => $class->id
]);

Classification::create([
    'student_id' => $student->id,
    'recommended_path' => 'Vokasi',
    'vocational_major' => 'RPL',
    'ipa_probability' => 0.15,
    'ips_probability' => 0.10,
    'bahasa_probability' => 0.05,
    'vokasi_probability' => 0.70,
    'dominant_factor' => 'Siswa memiliki minat yang sangat tinggi pada bidang teknologi dan praktikum.',
    'vocational_probabilities' => [
        'RPL' => 0.85,
        'TKJ' => 0.60,
        'MM' => 0.50,
        'AKL' => 0.10,
        'OTKP' => 0.05,
        'TKR' => 0.20
    ]
]);

echo "Dummy student added! NIS: 12345678\n";
