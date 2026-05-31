<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Classification::with(['student.classroom'])->orderBy('classified_at', 'desc');

        // Filter by search (name or NIS)
        if ($search = $request->get('search')) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filter by classroom
        if ($classroomId = $request->get('classroom')) {
            $query->whereHas('student', function ($q) use ($classroomId) {
                $q->where('classroom_id', $classroomId);
            });
        }

        // Filter by recommended path
        if ($path = $request->get('path')) {
            $query->where('recommended_path', $path);
        }

        $results = $query->get();

        // Statistics
        $allClassifications = Classification::selectRaw('recommended_path, count(*) as total')
            ->groupBy('recommended_path')->pluck('total', 'recommended_path')->toArray();

        $stats = [
            'total' => Classification::count(),
            'IPA' => $allClassifications['IPA'] ?? 0,
            'IPS' => $allClassifications['IPS'] ?? 0,
            'Bahasa' => $allClassifications['Bahasa'] ?? 0,
            'Vokasi' => $allClassifications['Vokasi'] ?? 0,
        ];

        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.results.index', compact('results', 'stats', 'classrooms'));
    }
}
