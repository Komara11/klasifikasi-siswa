<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Student;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function cekHasil()
    {
        return view('cek-hasil');
    }

    public function cekHasilSearch(Request $request)
    {
        $request->validate(['nis' => 'required|string']);

        $student = Student::where('nis', $request->nis)->with(['classroom', 'classification'])->first();

        if (!$student) {
            return view('cek-hasil', ['error' => 'NIS tidak ditemukan dalam sistem. Pastikan NIS yang Anda masukkan benar.']);
        }

        if (!$student->classification) {
            return view('cek-hasil', [
                'student' => $student,
                'warning' => 'Hasil rekomendasi belum tersedia. Data Anda sedang dalam proses klasifikasi oleh Guru BK.',
            ]);
        }

        return view('cek-hasil', [
            'student' => $student,
            'result' => $student->classification,
        ]);
    }
}
