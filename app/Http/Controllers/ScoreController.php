<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentScore;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classroom')->orderBy('name')->get();
        $subjects = Subject::all();
        $selectedStudent = null;
        $scores = [];

        if ($request->filled('student_id')) {
            $selectedStudent = Student::find($request->student_id);
            if ($selectedStudent) {
                $scores = StudentScore::where('student_id', $selectedStudent->id)
                    ->get()
                    ->groupBy('semester')
                    ->map(fn($group) => $group->keyBy('subject_id'));
            }
        }

        return view('admin.scores.index', compact('students', 'subjects', 'selectedStudent', 'scores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|integer|min:1|max:5',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->scores as $subjectId => $score) {
            StudentScore::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'subject_id' => $subjectId,
                    'semester' => $request->semester,
                ],
                ['score' => $score]
            );
        }

        return redirect()->route('admin.scores.index', ['student_id' => $request->student_id])
            ->with('success', 'Nilai berhasil disimpan.');
    }
}
