<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Setting;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function adminIndex(Request $request)
    {
        $query = Classification::with(['student.classroom']);

        if ($request->filled('classroom')) {
            $query->whereHas('student.classroom', fn($q) => $q->where('name', $request->classroom));
        }
        if ($request->filled('path')) {
            $smkMajors = ['RPL', 'TKJ', 'MM', 'AKL', 'OTKP', 'TKR'];
            if (in_array($request->path, $smkMajors)) {
                $query->where('vocational_major', $request->path);
            } else {
                $query->where('recommended_path', $request->path);
            }
        }

        $results = $query->orderBy('classified_at', 'desc')->get();
        $classrooms = \App\Models\Classroom::all();

        return view('admin.reports.index', compact('results', 'classrooms'));
    }

    public function kepsekIndex(Request $request)
    {
        $query = Classification::with(['student.classroom']);

        if ($request->filled('classroom')) {
            $query->whereHas('student.classroom', fn($q) => $q->where('name', $request->classroom));
        }
        if ($request->filled('path')) {
            $smkMajors = ['RPL', 'TKJ', 'MM', 'AKL', 'OTKP', 'TKR'];
            if (in_array($request->path, $smkMajors)) {
                $query->where('vocational_major', $request->path);
            } else {
                $query->where('recommended_path', $request->path);
            }
        }

        $results = $query->orderBy('classified_at', 'desc')->get();
        $classrooms = \App\Models\Classroom::all();

        return view('kepsek.reports.index', compact('results', 'classrooms'));
    }

    public function exportCsv(Request $request)
    {
        $query = Classification::with(['student.classroom']);

        if ($request->filled('classroom')) {
            $query->whereHas('student.classroom', fn($q) => $q->where('name', $request->classroom));
        }
        if ($request->filled('path')) {
            $query->where('recommended_path', $request->path);
        }

        $results = $query->get();

        $csvContent = "NIS,Nama Siswa,Kelas,Rekomendasi,Jurusan SMK,Prob. IPA,Prob. IPS,Prob. Bahasa,Prob. Vokasi,Faktor Dominan\n";
        foreach ($results as $r) {
            $csvContent .= "\"{$r->student->nis}\","
                . "\"{$r->student->name}\","
                . "\"{$r->student->classroom->name}\","
                . "\"{$r->recommended_path}\","
                . "\"" . ($r->vocational_major ?? '-') . "\","
                . round($r->ipa_probability * 100, 1) . "%,"
                . round($r->ips_probability * 100, 1) . "%,"
                . round($r->bahasa_probability * 100, 1) . "%,"
                . round($r->vokasi_probability * 100, 1) . "%,"
                . "\"" . str_replace('"', '""', $r->dominant_factor) . "\"\n";
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="Laporan_Rekomendasi_' . date('Y-m-d') . '.csv"');
    }

    public function exportPdf(Request $request)
    {
        $query = Classification::with(['student.classroom']);

        if ($request->filled('classroom')) {
            $query->whereHas('student.classroom', fn($q) => $q->where('name', $request->classroom));
        }

        $results = $query->get();
        $settings = [
            'school_name' => Setting::getValue('school_name', 'SMP Negeri 1 Sumber'),
            'academic_year' => Setting::getValue('academic_year', '2025/2026'),
            'principal_name' => Setting::getValue('principal_name', 'Drs. H. Sudrajat, M.M.'),
            'principal_nip' => Setting::getValue('principal_nip', '19680312 199403 1 005'),
        ];

        $pdf = Pdf::loadView('reports.pdf', compact('results', 'settings'));
        return $pdf->download('Laporan_Rekomendasi_' . date('Y-m-d') . '.pdf');
    }
}
