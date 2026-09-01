<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

    public function show(Student $student)
    {
        $student->load('classroom');
        return view('admin.students.show', compact('student'));
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
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['nis'],
                'email' => $validated['nis'] . '@student.com', // dummy email
                'password' => Hash::make('smpn1sumber'),
                'role' => 'student',
            ]);

            $validated['user_id'] = $user->id;
            Student::create($validated);
        });

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
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        DB::transaction(function () use ($validated, $student) {
            $student->update($validated);

            if ($student->user) {
                $student->user->update([
                    'name' => $validated['name'],
                    'username' => $validated['nis'],
                    'email' => $validated['nis'] . '@student.com',
                ]);
            }
        });

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $user = $student->user;
            $student->delete();
            
            if ($user) {
                $user->delete();
            }
        });
        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }
}
