<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function cekHasil()
    {
        $user = Auth::user();
        if (!$user || !$user->isStudent()) {
            return redirect('/login');
        }

        $student = $user->student()->with(['classroom', 'classification'])->first();

        if (!$student) {
            return view('cek-hasil', ['error' => 'Data profil siswa Anda tidak ditemukan. Hubungi Guru BK.']);
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
