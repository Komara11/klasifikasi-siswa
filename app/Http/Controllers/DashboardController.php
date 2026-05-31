<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Student;
use App\Services\NaiveBayesService;

class DashboardController extends Controller
{
    public function admin(NaiveBayesService $nb)
    {
        $totalStudents = Student::count();
        $completeCount = Student::all()->filter(fn($s) => $s->is_complete)->count();
        $classifiedCount = Classification::count();
        $modelStats = $nb->getModelStats();

        $distributions = Classification::selectRaw('recommended_path, count(*) as total')
            ->groupBy('recommended_path')->pluck('total', 'recommended_path')->toArray();

        return view('admin.dashboard', compact('totalStudents', 'completeCount', 'classifiedCount', 'modelStats', 'distributions'));
    }

    public function kepsek(NaiveBayesService $nb)
    {
        $totalStudents = Student::count();
        $classifiedCount = Classification::count();
        $modelStats = $nb->getModelStats();

        $distributions = Classification::selectRaw('recommended_path, count(*) as total')
            ->groupBy('recommended_path')->pluck('total', 'recommended_path')->toArray();

        return view('kepsek.dashboard', compact('totalStudents', 'classifiedCount', 'modelStats', 'distributions'));
    }
}
