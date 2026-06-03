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
