<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('classroom');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('classroom')) {
            $query->whereHas('classroom', fn($q) => $q->where('name', $request->classroom));
        }

        $students = $query->orderBy('name')->get()->map(function ($s) {
            $s->computed_status = $s->status;
            return $s;
        });

        $classrooms = Classroom::all();

        return view('admin.students.index', compact('students', 'classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'classroom_id' => 'required|exists:classrooms,id',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        Student::create($validated);

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'classroom_id' => 'required|exists:classrooms,id',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }
}
