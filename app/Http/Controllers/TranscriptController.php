<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Student;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    public function show(Student $student)
    {
        $student->load(['classroom', 'scores.subject']);
        $subjects = Subject::orderBy('id')->get();
        $scores = $student->scores->groupBy('semester')->map(fn($g) => $g->keyBy('subject_id'));

        $settings = [
            'school_name' => Setting::getValue('school_name', 'SMP Negeri 1 Sumber'),
            'school_address' => Setting::getValue('school_address', 'Jl. Nyi Mas Gandasari No.2, Sumber, Kec. Sumber, Kab. Cirebon, Jawa Barat 45611'),
            'academic_year' => Setting::getValue('academic_year', '2025/2026'),
            'school_logo' => Setting::getValue('school_logo'),
            'principal_name' => Setting::getValue('principal_name', 'Drs. H. Sudrajat, M.M.'),
            'principal_nip' => Setting::getValue('principal_nip', '19680312 199403 1 005'),
        ];

        return view('admin.transcripts.show', compact('student', 'subjects', 'scores', 'settings'));
    }

    public function downloadPdf(Student $student)
    {
        $student->load(['classroom', 'scores.subject']);
        $subjects = Subject::orderBy('id')->get();
        $scores = $student->scores->groupBy('semester')->map(fn($g) => $g->keyBy('subject_id'));

        $settings = [
            'school_name' => Setting::getValue('school_name', 'SMP Negeri 1 Sumber'),
            'school_address' => Setting::getValue('school_address', 'Jl. Nyi Mas Gandasari No.2, Sumber, Kec. Sumber, Kab. Cirebon, Jawa Barat 45611'),
            'academic_year' => Setting::getValue('academic_year', '2025/2026'),
            'school_logo' => Setting::getValue('school_logo'),
            'principal_name' => Setting::getValue('principal_name', 'Drs. H. Sudrajat, M.M.'),
            'principal_nip' => Setting::getValue('principal_nip', '19680312 199403 1 005'),
        ];

        // Compute logo path for PDF embedding
        $logoPath = null;
        if ($settings['school_logo']) {
            $fullPath = storage_path('app/public/' . $settings['school_logo']);
            if (file_exists($fullPath)) {
                $logoPath = $fullPath;
            }
        }

        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('student', 'subjects', 'scores', 'settings', 'logoPath'))
            ->setPaper('a4', 'portrait');

        $filename = 'transkrip-nilai-' . \Illuminate\Support\Str::slug($student->name) . '.pdf';

        return $pdf->download($filename);
    }
}
