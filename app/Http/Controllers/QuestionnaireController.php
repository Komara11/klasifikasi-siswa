<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use App\Models\Student;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::all();
        $questions = QuestionnaireQuestion::all();
        $selectedStudent = null;
        $answers = [];

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
                $answers = QuestionnaireAnswer::where('student_id', $selectedStudent->id)
                    ->pluck('score', 'question_id')
                    ->toArray();
            }
        }

        return view('admin.questionnaires.index', compact('students', 'classrooms', 'questions', 'selectedStudent', 'answers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'answers' => 'required|array',
            'answers.*' => 'required|integer|min:1|max:5',
        ]);

        foreach ($request->answers as $questionId => $score) {
            QuestionnaireAnswer::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'question_id' => $questionId,
                ],
                ['score' => $score]
            );
        }

        return redirect()->route('admin.questionnaires.index', ['student_id' => $request->student_id])
            ->with('success', 'Jawaban kuesioner berhasil disimpan.');
    }
}
