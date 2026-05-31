<?php

namespace App\Http\Controllers;

use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use App\Models\Student;
use Illuminate\Http\Request;

class QuestionnaireController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('classroom')->orderBy('name')->get();
        $questions = QuestionnaireQuestion::all();
        $selectedStudent = null;
        $answers = [];

        if ($request->filled('student_id')) {
            $selectedStudent = Student::find($request->student_id);
            if ($selectedStudent) {
                $answers = QuestionnaireAnswer::where('student_id', $selectedStudent->id)
                    ->pluck('score', 'question_id')
                    ->toArray();
            }
        }

        return view('admin.questionnaires.index', compact('students', 'questions', 'selectedStudent', 'answers'));
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
