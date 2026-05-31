<?php

namespace App\Http\Controllers;

use App\Services\NaiveBayesService;
use Illuminate\Http\Request;

class MLController extends Controller
{
    public function training(NaiveBayesService $nb)
    {
        $modelStats = $nb->getModelStats();
        $dataQuality = $nb->analyzeDataQuality();
        $defaults = $nb->getDefaultSettings();
        $hasCustomData = $nb->getCustomTrainingData() !== null;
        return view('admin.training.index', compact('modelStats', 'dataQuality', 'defaults', 'hasCustomData'));
    }

    public function train(Request $request, NaiveBayesService $nb)
    {
        $syntheticCount = (int) $request->input('synthetic_count', NaiveBayesService::DEFAULT_SYNTHETIC_COUNT);
        $minVariance = (float) $request->input('min_variance', NaiveBayesService::DEFAULT_MIN_VARIANCE);

        $result = $nb->train($syntheticCount, $minVariance);
        return redirect()->route('admin.training.index')
            ->with('success', "Model {$result['version']} berhasil dilatih dengan {$result['samples']} sampel data.");
    }

    public function importTrainingData(Request $request, NaiveBayesService $nb)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:2048']);
        $content = file_get_contents($request->file('csv_file')->getRealPath());
        $result = $nb->importTrainingCSV($content);

        if ($result['success']) {
            return redirect()->route('admin.training.index')->with('success', $result['message']);
        }
        return redirect()->route('admin.training.index')->with('error', $result['message']);
    }

    public function clearCustomData(NaiveBayesService $nb)
    {
        \App\Models\Setting::where('key', 'custom_training_data')->delete();
        return redirect()->route('admin.training.index')->with('success', 'Data training custom berhasil dihapus. Sistem akan menggunakan data default.');
    }

    public function downloadTemplate()
    {
        $header = "label,matematika,ipa,ips,bahasa_indonesia,bahasa_inggris,seni_budaya,minat_ipa,minat_ips,minat_bahasa,minat_vokasi\n";
        $sample = "IPA,90,88,72,78,80,65,90,40,30,35\n";
        $sample .= "IPS,74,70,92,78,72,75,35,90,50,30\n";
        $sample .= "Bahasa,68,65,76,92,90,78,30,45,90,30\n";
        $sample .= "Vokasi,76,74,68,70,65,92,50,30,30,90\n";

        return response($header . $sample)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_training_data.csv"');
    }

    public function classifications(NaiveBayesService $nb)
    {
        $students = \App\Models\Student::with(['classroom', 'classification'])->orderBy('name')->get();
        $modelStats = $nb->getModelStats();
        return view('admin.classifications.index', compact('students', 'modelStats'));
    }

    public function classify(NaiveBayesService $nb)
    {
        $count = $nb->classifyAll();
        return redirect()->route('admin.classifications.index')
            ->with('success', "Berhasil mengklasifikasikan {$count} siswa.");
    }
}
