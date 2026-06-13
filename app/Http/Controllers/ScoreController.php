<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentScore;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        $selectedStudent = null;
        $scores = [];

        // Build student query with search and classroom filter
        $studentQuery = Student::with('classroom')->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $studentQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('classroom')) {
            $studentQuery->whereHas('classroom', fn($q) => $q->where('name', $request->classroom));
        }

        $students = $studentQuery->get();

        if ($request->filled('student_id')) {
            $selectedStudent = Student::with('classroom')->find($request->student_id);
            if ($selectedStudent) {
                $scores = StudentScore::where('student_id', $selectedStudent->id)
                    ->get()
                    ->groupBy('semester')
                    ->map(fn($group) => $group->keyBy('subject_id'));
            }
        }

        return view('admin.scores.index', compact('students', 'classrooms', 'subjects', 'selectedStudent', 'scores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->scores as $semester => $score) {
            if ($score !== null && $score !== '') {
                StudentScore::updateOrCreate(
                    [
                        'student_id' => $request->student_id,
                        'subject_id' => $request->subject_id,
                        'semester' => $semester,
                    ],
                    ['score' => $score]
                );
            } else {
                StudentScore::where([
                    'student_id' => $request->student_id,
                    'subject_id' => $request->subject_id,
                    'semester' => $semester,
                ])->delete();
            }
        }

        // Determine next subject for auto-switch
        $subjects = Subject::orderBy('id')->pluck('id')->toArray();
        $currentIndex = array_search($request->subject_id, $subjects);
        $isLastSubject = ($currentIndex === count($subjects) - 1);
        $nextSubjectId = (isset($subjects[$currentIndex + 1])) ? $subjects[$currentIndex + 1] : $subjects[0];

        return redirect()->route('admin.scores.index', ['student_id' => $request->student_id])
            ->with('success', 'Nilai berhasil disimpan.')
            ->with('next_subject', $nextSubjectId)
            ->with('show_table', $isLastSubject);
    }

    /**
     * Download a pre-filled CSV template for a specific student.
     */
    public function downloadTemplate(Student $student)
    {
        $subjects = Subject::orderBy('id')->get();

        $filename = 'template_nilai_' . str_replace(' ', '_', strtolower($student->name)) . '.csv';
        $filename = "Template_Nilai_{$student->nis}_" . date('Ymd_His') . ".xlsx";

        $scores = StudentScore::where('student_id', $student->id)
            ->get()
            ->groupBy('semester')
            ->map(fn($group) => $group->keyBy('subject_id'));

        $data = [];
        $data[] = ['Mata Pelajaran', 'Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5'];

        foreach ($subjects as $subj) {
            $row = [$subj->name];
            for ($sem = 1; $sem <= 5; $sem++) {
                $row[] = $scores[$sem][$subj->id]->score ?? null;
            }
            $data[] = $row;
        }

        require_once app_path('Services/SimpleXLSXGen.php');
        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        
        return response()->streamDownload(function() use ($xlsx) {
            echo $xlsx->output();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import scores from a CSV or Excel file for a specific student.
     */
    public function importFile(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'import_file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        $student = Student::findOrFail($request->student_id);
        $subjects = Subject::orderBy('id')->get()->keyBy(function ($subj) {
            return mb_strtolower(trim($subj->name));
        });

        $file = $request->file('import_file');
        $extension = $file->getClientOriginalExtension();
        
        $imported = 0;
        $skipped = [];

        if ($extension === 'xlsx') {
            require_once app_path('Services/SimpleXLSX.php');
            if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getRealPath())) {
                $rows = $xlsx->rows();
                // Skip header (first row)
                array_shift($rows);
                
                foreach ($rows as $row) {
                    if (count($row) < 2) continue;

                    $subjectName = mb_strtolower(trim($row[0]));
                    $subject = $subjects[$subjectName] ?? null;

                    if (!$subject) {
                        $skipped[] = $row[0];
                        continue;
                    }

                    for ($sem = 1; $sem <= 5; $sem++) {
                        $score = isset($row[$sem]) ? trim($row[$sem]) : '';

                        if ($score !== '' && is_numeric($score) && $score >= 0 && $score <= 100) {
                            StudentScore::updateOrCreate(
                                [
                                    'student_id' => $student->id,
                                    'subject_id' => $subject->id,
                                    'semester' => $sem,
                                ],
                                ['score' => (float) $score]
                            );
                            $imported++;
                        }
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Gagal mem-parsing file Excel.');
            }
        } else {
            // Process CSV
            $handle = fopen($file->getRealPath(), 'r');
            if (!$handle) {
                return redirect()->back()->with('error', 'Gagal membuka file CSV.');
            }

            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
                rewind($handle);
            }

            // Skip header row
            fgetcsv($handle, 0, ',');

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (count($row) < 2) continue;

                $subjectName = mb_strtolower(trim($row[0]));
                $subject = $subjects[$subjectName] ?? null;

                if (!$subject) {
                    $skipped[] = $row[0];
                    continue;
                }

                for ($sem = 1; $sem <= 5; $sem++) {
                    $score = isset($row[$sem]) ? trim($row[$sem]) : '';

                    if ($score !== '' && is_numeric($score) && $score >= 0 && $score <= 100) {
                        StudentScore::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'subject_id' => $subject->id,
                                'semester' => $sem,
                            ],
                            ['score' => (float) $score]
                        );
                        $imported++;
                    }
                }
            }
            fclose($handle);
        }

        $message = "Berhasil mengimpor {$imported} nilai.";
        if (!empty($skipped)) {
            $message .= ' Mata pelajaran tidak ditemukan: ' . implode(', ', array_unique($skipped)) . '.';
        }

        return redirect()->route('admin.scores.index', ['student_id' => $student->id])
            ->with('success', $message)
            ->with('show_table', true);
    }

    /**
     * Bulk import from the UMC research Excel format.
     * Auto-creates classrooms, subjects, students, and all scores.
     */
    public function importBulkExcel(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx|max:10240',
        ]);

        require_once app_path('Services/SimpleXLSX.php');

        $file = $request->file('bulk_file');
        $xlsx = \Shuchkin\SimpleXLSX::parse($file->getRealPath());

        if (!$xlsx) {
            return redirect()->back()->with('error', 'Gagal mem-parsing file Excel: ' . \Shuchkin\SimpleXLSX::parseError());
        }

        $rows = $xlsx->rows();

        // --- STEP 1: Detect subject columns from header rows ---
        // Row 6 (index 6) contains subject names: PAIBP, PKN, B.INDO, MTK, IPA, IPS, B. INGGRIS, PJOK, INFORMATIKA, SENI BUDAYA, B. CIREBON
        // Row 7 (index 7) contains semester numbers (1-6) under each subject
        // Each subject spans 7 columns: Sem1, Sem2, Sem3, Sem4, Sem5, Sem6, RT(rata-rata)

        $subjectMap = [
            'PAIBP'        => ['name' => 'Pendidikan Agama dan Budi Pekerti', 'code' => 'pendidikan_agama_dan_budi_pekerti', 'start' => 7],
            'PKN'          => ['name' => 'Pendidikan Pancasila',              'code' => 'pendidikan_pancasila',              'start' => 14],
            'B.INDO'       => ['name' => 'Bahasa Indonesia',                  'code' => 'bahasa_indonesia',                  'start' => 21],
            'MTK'          => ['name' => 'Matematika',                        'code' => 'matematika',                        'start' => 28],
            'IPA'          => ['name' => 'Ilmu Pengetahuan Alam',             'code' => 'ipa',                               'start' => 35],
            'IPS'          => ['name' => 'Ilmu Pengetahuan Sosial',           'code' => 'ips',                               'start' => 42],
            'B. INGGRIS'   => ['name' => 'Bahasa Inggris',                    'code' => 'bahasa_inggris',                    'start' => 49],
            'PJOK'         => ['name' => 'Pendidikan Jasmani, Olahraga dan Kesehatan', 'code' => 'pendidikan_jasmani_olahraga_dan_kesehatan', 'start' => 56],
            'INFORMATIKA'  => ['name' => 'Informatika',                       'code' => 'informatika',                       'start' => 63],
            'SENI BUDAYA'  => ['name' => 'Seni dan Budaya',                   'code' => 'seni_budaya',                       'start' => 70],
            'B. CIREBON'   => ['name' => 'Bahasa Cirebon',                    'code' => 'bahasa_cirebon',                    'start' => 77],
        ];

        // --- STEP 2: Create subjects ---
        $subjectModels = [];
        foreach ($subjectMap as $key => $info) {
            $subjectModels[$key] = Subject::firstOrCreate(
                ['code' => $info['code']],
                ['name' => $info['name'], 'weight' => 1.00]
            );
        }

        // --- STEP 3: Parse student rows (start from row index 8) ---
        $studentsImported = 0;
        $scoresImported = 0;
        $maxSemesters = 5; // System only uses semester 1-5

        for ($i = 8; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Skip empty rows
            $name = trim($row[2] ?? '');
            $nis = trim($row[1] ?? '');
            if (empty($name) || empty($nis)) continue;

            $gender = trim($row[6] ?? 'L');
            $kelas = trim($row[5] ?? 'IX A');
            $birthPlace = trim($row[3] ?? '');
            $birthDateRaw = trim($row[4] ?? '');

            // Parse birth date
            $birthDate = null;
            if (!empty($birthDateRaw)) {
                $months = [
                    'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
                    'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
                    'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
                ];
                if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{4})/', $birthDateRaw, $m)) {
                    $monthKey = strtolower($m[2]);
                    if (isset($months[$monthKey])) {
                        $birthDate = $m[3] . '-' . $months[$monthKey] . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT);
                    }
                }
            }

            // Create classroom
            $classroom = Classroom::firstOrCreate(
                ['name' => $kelas],
                ['grade' => 'IX', 'academic_year' => '2025/2026']
            );

            // Create student
            $student = Student::firstOrCreate(
                ['nis' => (string) $nis],
                [
                    'name' => $name,
                    'gender' => in_array($gender, ['L', 'P']) ? $gender : 'L',
                    'classroom_id' => $classroom->id,
                    'birth_date' => $birthDate,
                    'address' => $birthPlace ? "Tempat Lahir: {$birthPlace}" : null,
                ]
            );

            $studentsImported++;

            // --- STEP 4: Import scores for each subject ---
            foreach ($subjectMap as $key => $info) {
                $subjectModel = $subjectModels[$key];
                $startCol = $info['start'];

                // Columns: startCol+0=S1, startCol+1=S2, ..., startCol+4=S5
                for ($sem = 1; $sem <= $maxSemesters; $sem++) {
                    $colIdx = $startCol + ($sem - 1);
                    $score = $row[$colIdx] ?? '';

                    if (is_numeric($score) && $score >= 0 && $score <= 100) {
                        StudentScore::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'subject_id' => $subjectModel->id,
                                'semester' => $sem,
                            ],
                            ['score' => (float) $score]
                        );
                        $scoresImported++;
                    }
                }
            }
        }

        return redirect()->route('admin.scores.index')
            ->with('success', "Import selesai! {$studentsImported} siswa dan {$scoresImported} nilai berhasil diimpor.");
    }
}
