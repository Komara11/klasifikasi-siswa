<?php

namespace App\Services;

use App\Models\Classification;
use App\Models\NaiveBayesModel;
use App\Models\Student;
use App\Models\Subject;

class NaiveBayesService
{
    private array $classes = ['IPA', 'IPS', 'Bahasa', 'Vokasi'];
    private array $vocationalMajors = ['RPL', 'TKJ', 'MM', 'AKL', 'OTKP', 'TKR'];

    // Default training parameters (can be customized by admin)
    public const DEFAULT_SYNTHETIC_COUNT = 10;
    public const DEFAULT_MIN_VARIANCE = 10.0;
    public const DEFAULT_K_FOLD = 10;

    public function getModelVersion(): string
    {
        $latest = NaiveBayesModel::orderBy('id', 'desc')->first();
        return $latest ? $latest->model_version : 'v0.0';
    }

    public function getModelStats(): array
    {
        $version = $this->getModelVersion();
        $hasModel = NaiveBayesModel::where('model_version', $version)->exists();

        if (!$hasModel) {
            return ['version' => 'v0.0', 'accuracy' => 0, 'precision' => 0, 'recall' => 0, 'f1' => 0, 'trained' => false, 'confusion_matrix' => $this->emptyConfusionMatrix()];
        }

        $eval = $this->evaluate();
        return array_merge($eval, ['version' => $version, 'trained' => true]);
    }

    /**
     * Train the Gaussian Naive Bayes model.
     */
    public function train(int $syntheticCount = self::DEFAULT_SYNTHETIC_COUNT, float $minVariance = self::DEFAULT_MIN_VARIANCE): array
    {
        $students = Student::with(['scores.subject', 'questionnaireAnswers.question', 'classification'])->get();

        // Clear old classifications so the new model starts fresh
        Classification::query()->delete();

        $trainingData = [];

        // Always use heuristic labeling for training data to ensure balanced labels.
        // This prevents the chicken-and-egg problem where bad classifications
        // become "ground truth" and perpetuate errors.
        foreach ($students as $student) {
            if (!$student->is_complete) continue;
            $features = $this->extractFeatures($student);
            if ($features === null) continue;
            $label = $this->determineHeuristicLabel($features);
            $trainingData[] = ['class' => $label, 'features' => $features];
        }

        // Augment with synthetic data if not enough real data
        if (count($trainingData) < 16) {
            $synthetic = $this->generateSyntheticTrainingData($syntheticCount);
            $trainingData = array_merge($trainingData, $synthetic);
        }

        // Increment version
        $currentVersion = $this->getModelVersion();
        $vNum = floatval(str_replace('v', '', $currentVersion)) + 0.1;
        $newVersion = 'v' . number_format($vNum, 1);

        NaiveBayesModel::where('model_version', '!=', $newVersion)->delete();

        // Calculate class priors and feature statistics
        $classCounts = array_fill_keys($this->classes, 0);
        $classFeatures = [];
        foreach ($this->classes as $class) $classFeatures[$class] = [];

        foreach ($trainingData as $sample) {
            $c = $sample['class'];
            if (!isset($classCounts[$c])) continue;
            $classCounts[$c]++;
            foreach ($sample['features'] as $fname => $fval) {
                $classFeatures[$c][$fname][] = $fval;
            }
        }

        $total = max(array_sum($classCounts), 1);

        $allFeatureNames = [];
        foreach ($trainingData as $s) {
            $allFeatureNames = array_merge($allFeatureNames, array_keys($s['features']));
        }
        $allFeatureNames = array_unique($allFeatureNames);

        foreach ($this->classes as $class) {
            $prior = $classCounts[$class] / $total;
            $featureArrays = $classFeatures[$class] ?? [];

            foreach ($allFeatureNames as $fname) {
                $values = $featureArrays[$fname] ?? [50];
                $mean = array_sum($values) / max(count($values), 1);
                $variance = 0;
                foreach ($values as $v) $variance += ($v - $mean) ** 2;
                $variance = $variance / max(count($values), 1);
                // Use higher minimum variance to prevent overfit on small datasets
                $variance = max($variance, $minVariance);

                NaiveBayesModel::create([
                    'class_name' => $class,
                    'feature_name' => $fname,
                    'mean' => $mean,
                    'variance' => $variance,
                    'prior_probability' => $prior,
                    'model_version' => $newVersion,
                ]);
            }
        }

        return ['version' => $newVersion, 'samples' => $total, 'classes' => $classCounts];
    }

    /**
     * Classify a single student.
     */
    public function classify(Student $student): ?array
    {
        $student->load(['scores.subject', 'questionnaireAnswers.question']);

        if (!$student->is_complete) return null;

        $features = $this->extractFeatures($student);
        if ($features === null) return null;

        $version = $this->getModelVersion();
        $modelParams = NaiveBayesModel::where('model_version', $version)->get();

        if ($modelParams->isEmpty()) {
            return $this->heuristicClassify($student, $features);
        }

        $posteriors = [];
        foreach ($this->classes as $class) {
            $params = $modelParams->where('class_name', $class);
            $prior = $params->first()?->prior_probability ?? 0.25;
            $logPosterior = log(max($prior, 1e-10));

            foreach ($features as $fname => $fval) {
                $param = $params->where('feature_name', $fname)->first();
                if ($param) {
                    $likelihood = $this->gaussianPDF($fval, $param->mean, $param->variance);
                    $logPosterior += log(max($likelihood, 1e-10));
                }
            }
            $posteriors[$class] = $logPosterior;
        }

        // Softmax
        $maxLog = max($posteriors);
        $expSum = 0;
        foreach ($posteriors as $val) $expSum += exp($val - $maxLog);

        $probabilities = [];
        foreach ($posteriors as $class => $val) {
            $probabilities[$class] = exp($val - $maxLog) / $expSum;
        }

        $recommendedPath = array_keys($probabilities, max($probabilities))[0];

        // Vocational sub-classification
        $vocationalMajor = null;
        $vocationalProbs = null;
        if ($recommendedPath === 'Vokasi') {
            $vocResult = $this->classifyVocationalMajor($features);
            $vocationalMajor = $vocResult['major'];
            $vocationalProbs = $vocResult['probabilities'];
        }

        $dominantFactor = $this->generateFactorExplanation($student, $recommendedPath, $features, $vocationalMajor);

        Classification::updateOrCreate(
            ['student_id' => $student->id],
            [
                'recommended_path' => $recommendedPath,
                'vocational_major' => $vocationalMajor,
                'ipa_probability' => $probabilities['IPA'],
                'ips_probability' => $probabilities['IPS'],
                'bahasa_probability' => $probabilities['Bahasa'],
                'vokasi_probability' => $probabilities['Vokasi'],
                'vocational_probabilities' => $vocationalProbs,
                'dominant_factor' => $dominantFactor,
                'model_version' => $version,
                'classified_at' => now(),
            ]
        );

        return [
            'student_id' => $student->id,
            'recommended_path' => $recommendedPath,
            'vocational_major' => $vocationalMajor,
            'probabilities' => $probabilities,
            'vocational_probabilities' => $vocationalProbs,
            'dominant_factor' => $dominantFactor,
        ];
    }

    /**
     * Batch classify all complete students.
     */
    public function classifyAll(): int
    {
        $students = Student::with(['scores.subject', 'questionnaireAnswers.question'])->get();
        $count = 0;
        foreach ($students as $student) {
            if ($student->is_complete) {
                $result = $this->classify($student);
                if ($result) $count++;
            }
        }
        return $count;
    }

    /**
     * Evaluate model using K-Fold Cross Validation (k=5).
     */
    public function evaluate(): array
    {
        $students = Student::with(['scores.subject', 'questionnaireAnswers.question', 'classification'])->get();

        $dataset = [];
        foreach ($students as $student) {
            if (!$student->is_complete) continue;
            $features = $this->extractFeatures($student);
            if ($features === null) continue;

            $label = $student->classification
                ? $student->classification->recommended_path
                : $this->determineHeuristicLabel($features);

            $dataset[] = ['features' => $features, 'label' => $label];
        }

        // Augment with synthetic data if not enough for meaningful evaluation
        if (count($dataset) < 20) {
            $synth = $this->generateSyntheticTrainingData();
            foreach ($synth as $s) {
                $dataset[] = ['features' => $s['features'], 'label' => $s['class']];
            }
        }

        if (count($dataset) < 4) {
            return ['accuracy' => 0, 'precision' => 0, 'recall' => 0, 'f1' => 0, 'confusion_matrix' => $this->emptyConfusionMatrix()];
        }

        shuffle($dataset);

        $k = min(10, count($dataset));
        $foldSize = intval(ceil(count($dataset) / $k));
        $allPredictions = [];
        $allActuals = [];

        for ($fold = 0; $fold < $k; $fold++) {
            $testStart = $fold * $foldSize;
            $testEnd = min($testStart + $foldSize, count($dataset));

            $trainSet = [];
            $testSet = [];

            for ($i = 0; $i < count($dataset); $i++) {
                if ($i >= $testStart && $i < $testEnd) {
                    $testSet[] = $dataset[$i];
                } else {
                    $trainSet[] = $dataset[$i];
                }
            }

            if (empty($trainSet) || empty($testSet)) continue;

            $tempModel = $this->trainTemporaryModel($trainSet);

            foreach ($testSet as $sample) {
                $predicted = $this->predictWithModel($sample['features'], $tempModel);
                $allPredictions[] = $predicted;
                $allActuals[] = $sample['label'];
            }
        }

        // Build confusion matrix
        $matrix = $this->emptyConfusionMatrix();
        $correct = 0;
        $total = count($allPredictions);

        for ($i = 0; $i < $total; $i++) {
            $actual = strtolower($allActuals[$i]);
            $predicted = strtolower($allPredictions[$i]);
            if (isset($matrix[$actual][$predicted])) $matrix[$actual][$predicted]++;
            if ($actual === $predicted) $correct++;
        }

        $accuracy = $total > 0 ? round(($correct / $total) * 100, 1) : 0;

        $precisions = [];
        $recalls = [];

        foreach ($this->classes as $class) {
            $lc = strtolower($class);
            $tp = $matrix[$lc][$lc] ?? 0;
            $fp = 0; $fn = 0;
            foreach ($this->classes as $other) {
                $lo = strtolower($other);
                if ($lo !== $lc) {
                    $fp += $matrix[$lo][$lc] ?? 0;
                    $fn += $matrix[$lc][$lo] ?? 0;
                }
            }
            $precisions[] = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0;
            $recalls[] = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0;
        }

        $avgPrecision = count($precisions) > 0 ? round(array_sum($precisions) / count($precisions) * 100, 1) : 0;
        $avgRecall = count($recalls) > 0 ? round(array_sum($recalls) / count($recalls) * 100, 1) : 0;
        $f1 = ($avgPrecision + $avgRecall) > 0
            ? round(2 * ($avgPrecision * $avgRecall) / ($avgPrecision + $avgRecall), 1)
            : 0;

        return [
            'accuracy' => $accuracy,
            'precision' => $avgPrecision,
            'recall' => $avgRecall,
            'f1' => $f1,
            'confusion_matrix' => $matrix,
        ];
    }

    /**
     * Train a temporary in-memory model for K-Fold evaluation.
     */
    private function trainTemporaryModel(array $trainSet): array
    {
        $classCounts = array_fill_keys($this->classes, 0);
        $classFeatures = [];
        foreach ($this->classes as $c) $classFeatures[$c] = [];

        $allFeatureNames = [];
        foreach ($trainSet as $s) {
            $allFeatureNames = array_merge($allFeatureNames, array_keys($s['features']));
        }
        $allFeatureNames = array_unique($allFeatureNames);

        foreach ($trainSet as $sample) {
            $c = $sample['class'] ?? $sample['label'] ?? 'IPA';
            if (!isset($classCounts[$c])) continue;
            $classCounts[$c]++;
            foreach ($sample['features'] as $fname => $fval) {
                $classFeatures[$c][$fname][] = $fval;
            }
        }

        $total = max(array_sum($classCounts), 1);
        $model = [];

        foreach ($this->classes as $class) {
            $prior = $classCounts[$class] / $total;
            $featureArrays = $classFeatures[$class] ?? [];
            $model[$class] = ['prior' => $prior, 'features' => []];

            foreach ($allFeatureNames as $fname) {
                $values = $featureArrays[$fname] ?? [50];
                $mean = array_sum($values) / max(count($values), 1);
                $variance = 0;
                foreach ($values as $v) $variance += ($v - $mean) ** 2;
                $variance = max($variance / max(count($values), 1), self::DEFAULT_MIN_VARIANCE);

                $model[$class]['features'][$fname] = ['mean' => $mean, 'variance' => $variance];
            }
        }

        return $model;
    }

    /**
     * Predict class using a temporary in-memory model.
     */
    private function predictWithModel(array $features, array $model): string
    {
        $posteriors = [];
        foreach ($this->classes as $class) {
            $prior = $model[$class]['prior'] ?? 0.25;
            $logP = log(max($prior, 1e-10));

            foreach ($features as $fname => $fval) {
                if (isset($model[$class]['features'][$fname])) {
                    $m = $model[$class]['features'][$fname]['mean'];
                    $v = $model[$class]['features'][$fname]['variance'];
                    $logP += log(max($this->gaussianPDF($fval, $m, $v), 1e-10));
                }
            }
            $posteriors[$class] = $logP;
        }

        return array_keys($posteriors, max($posteriors))[0];
    }

    /**
     * Gaussian Probability Density Function.
     */
    private function gaussianPDF(float $x, float $mean, float $variance): float
    {
        $variance = max($variance, self::DEFAULT_MIN_VARIANCE);
        $coeff = 1.0 / sqrt(2.0 * M_PI * $variance);
        $exponent = exp(-pow($x - $mean, 2) / (2.0 * $variance));
        return $coeff * $exponent;
    }

    /**
     * Extract NORMALIZED feature vector from student data.
     * 
     * KEY FIXES for accuracy:
     * 1. Uses ALL available semesters (averaged) instead of just semester 1
     * 2. Does NOT apply subject weights (raw scores keep consistent scale)
     * 3. Normalizes questionnaire scores to PER-QUESTION AVERAGE per category
     *    so categories with different question counts are comparable
     */
    private function extractFeatures(Student $student): ?array
    {
        // Use all semesters, averaged per subject
        $scoresBySubject = [];
        foreach ($student->scores as $score) {
            $code = $score->subject->code;
            $scoresBySubject[$code][] = $score->score;
        }

        // Need at least 6 subject codes
        if (count($scoresBySubject) < 6) return null;

        $features = [];
        foreach ($scoresBySubject as $code => $scores) {
            // Average across all semesters for this subject
            $features[$code] = array_sum($scores) / count($scores);
        }

        // Questionnaire features: AVERAGE score per category (not sum)
        // This normalizes categories with different question counts
        $categoryScores = [];
        $categoryCounts = [];
        foreach ($student->questionnaireAnswers as $answer) {
            $cat = strtolower($answer->question->category);
            $key = 'minat_' . $cat;
            $categoryScores[$key] = ($categoryScores[$key] ?? 0) + $answer->score;
            $categoryCounts[$key] = ($categoryCounts[$key] ?? 0) + 1;
        }

        foreach ($categoryScores as $key => $total) {
            // Average per question, then scale to 0-100 range for consistency with academic scores
            $avg = $total / max($categoryCounts[$key], 1); // 1-5 scale
            $features[$key] = $avg * 20; // Scale to 20-100 range
        }

        return $features;
    }

    /**
     * Determine class label heuristically from features.
     */
    private function determineHeuristicLabel(array $features): string
    {
        $scores = $this->calculatePathScores($features);
        arsort($scores);
        return array_key_first($scores);
    }

    /**
     * Calculate balanced path scores from features.
     * Features are now all on ~same scale (academic: 0-100, interest: 20-100).
     */
    private function calculatePathScores(array $features): array
    {
        $mat = $features['matematika'] ?? 70;
        $ipaVal = $features['ipa'] ?? 70;
        $ipsVal = $features['ips'] ?? 70;
        $bindoVal = $features['bahasa_indonesia'] ?? 70;
        $bingVal = $features['bahasa_inggris'] ?? 70;
        $seniVal = $features['seni_budaya'] ?? 70;

        $minatIpa = $features['minat_ipa'] ?? 60;
        $minatIps = $features['minat_ips'] ?? 60;
        $minatBahasa = $features['minat_bahasa'] ?? 60;
        $minatVokasi = $features['minat_vokasi'] ?? 60;

        // Academic component (60%) + Interest component (40%)
        return [
            'IPA'    => (($mat + $ipaVal) / 2) * 0.6 + $minatIpa * 0.4,
            'IPS'    => (($ipsVal + ($bindoVal * 0.4 + $mat * 0.3 + $ipaVal * 0.3)) / 2) * 0.6 + $minatIps * 0.4,
            'Bahasa' => (($bindoVal + $bingVal) / 2) * 0.6 + $minatBahasa * 0.4,
            'Vokasi' => (($seniVal + ($mat * 0.3 + $ipaVal * 0.3 + $bindoVal * 0.4)) / 2) * 0.6 + $minatVokasi * 0.4,
        ];
    }

    /**
     * Heuristic fallback classification when no trained model exists.
     */
    private function heuristicClassify(Student $student, array $features): array
    {
        $scores = $this->calculatePathScores($features);
        $total = array_sum($scores);
        if ($total == 0) $total = 1;

        $probabilities = [];
        foreach ($scores as $class => $score) {
            $probabilities[$class] = max(0.01, $score / $total);
        }

        $sum = array_sum($probabilities);
        foreach ($probabilities as $class => $p) {
            $probabilities[$class] = $p / $sum;
        }

        $recommendedPath = array_keys($probabilities, max($probabilities))[0];

        $vocationalMajor = null;
        $vocationalProbs = null;
        if ($recommendedPath === 'Vokasi') {
            $vocResult = $this->classifyVocationalMajor($features);
            $vocationalMajor = $vocResult['major'];
            $vocationalProbs = $vocResult['probabilities'];
        }

        $dominantFactor = $this->generateFactorExplanation($student, $recommendedPath, $features, $vocationalMajor);

        Classification::updateOrCreate(
            ['student_id' => $student->id],
            [
                'recommended_path' => $recommendedPath,
                'vocational_major' => $vocationalMajor,
                'ipa_probability' => $probabilities['IPA'],
                'ips_probability' => $probabilities['IPS'],
                'bahasa_probability' => $probabilities['Bahasa'],
                'vokasi_probability' => $probabilities['Vokasi'],
                'vocational_probabilities' => $vocationalProbs,
                'dominant_factor' => $dominantFactor,
                'model_version' => $this->getModelVersion() ?: 'v1.0',
                'classified_at' => now(),
            ]
        );

        return [
            'student_id' => $student->id,
            'recommended_path' => $recommendedPath,
            'vocational_major' => $vocationalMajor,
            'probabilities' => $probabilities,
            'vocational_probabilities' => $vocationalProbs,
            'dominant_factor' => $dominantFactor,
        ];
    }

    private function generateFactorExplanation(Student $student, string $path, array $features, ?string $vocMajor = null): string
    {
        $mat = round($features['matematika'] ?? 0, 1);
        $ipaVal = round($features['ipa'] ?? 0, 1);
        $ipsVal = round($features['ips'] ?? 0, 1);
        $bindoVal = round($features['bahasa_indonesia'] ?? 0, 1);
        $bingVal = round($features['bahasa_inggris'] ?? 0, 1);
        $seniVal = round($features['seni_budaya'] ?? 0, 1);

        $majorLabels = [
            'RPL' => 'Rekayasa Perangkat Lunak', 'TKJ' => 'Teknik Komputer & Jaringan',
            'MM' => 'Multimedia', 'AKL' => 'Akuntansi & Keuangan Lembaga',
            'OTKP' => 'Otomatisasi & Tata Kelola Perkantoran', 'TKR' => 'Teknik Kendaraan Ringan',
        ];

        $vocDesc = $vocMajor ? ' Jurusan yang paling cocok: ' . ($majorLabels[$vocMajor] ?? $vocMajor) . '.' : '';

        return match($path) {
            'IPA' => "Nilai eksakta (Matematika: {$mat}, IPA: {$ipaVal}) konsisten di atas KKM, dipadu minat sains yang kuat dari kuesioner.",
            'IPS' => "Performansi sosial-humaniora (IPS: {$ipsVal}) menonjol dikombinasikan minat interaksi kemasyarakatan yang tinggi.",
            'Bahasa' => "Kemampuan linguistik (B.Indo: {$bindoVal}, B.Ing: {$bingVal}) di atas rata-rata dengan kecenderungan minat sastra kuat.",
            'Vokasi' => "Pencapaian seni budaya ({$seniVal}) serta ketrampilan praktis tinggi pada kuesioner.{$vocDesc}",
            default => "Berdasarkan analisis gabungan nilai akademik dan minat kuesioner.",
        };
    }

    /**
     * Generate balanced synthetic training data.
     * 10 samples per class = 40 total, with clearly distinct patterns.
     * All features use the SAME SCALE as extractFeatures():
     *   - Academic scores: 55-95 range
     *   - Interest scores: 20-100 range (Likert avg * 20)
     */
    private function generateSyntheticTrainingData(int $samplesPerClass = self::DEFAULT_SYNTHETIC_COUNT): array
    {
        $data = [];
        for ($i = 0; $i < $samplesPerClass; $i++) {
            foreach ($this->classes as $class) {
                $data[] = ['class' => $class, 'features' => $this->generateSyntheticFeatures($class)];
            }
        }
        return $data;
    }

    private function generateSyntheticFeatures(string $class): array
    {
        $high = rand(85, 95);
        $mid = rand(72, 82);
        $low = rand(58, 70);
        $highInterest = rand(80, 100);
        $lowInterest = rand(20, 50);
        $midInterest = rand(45, 65);

        // Base vocational sub-interests (all low by default)
        $vocSub = array_fill_keys(array_map(fn($m) => 'minat_' . strtolower($m), $this->vocationalMajors), $lowInterest);

        $base = match($class) {
            'IPA' => [
                'matematika' => $high, 'ipa' => $high, 'ips' => $low,
                'bahasa_indonesia' => $mid, 'bahasa_inggris' => $mid, 'seni_budaya' => $low,
                'minat_ipa' => $highInterest, 'minat_ips' => $lowInterest,
                'minat_bahasa' => $lowInterest, 'minat_vokasi' => $lowInterest,
            ],
            'IPS' => [
                'matematika' => $mid, 'ipa' => $low, 'ips' => $high,
                'bahasa_indonesia' => $mid, 'bahasa_inggris' => $mid, 'seni_budaya' => $mid,
                'minat_ipa' => $lowInterest, 'minat_ips' => $highInterest,
                'minat_bahasa' => $midInterest, 'minat_vokasi' => $lowInterest,
            ],
            'Bahasa' => [
                'matematika' => $low, 'ipa' => $low, 'ips' => $mid,
                'bahasa_indonesia' => $high, 'bahasa_inggris' => $high, 'seni_budaya' => $mid,
                'minat_ipa' => $lowInterest, 'minat_ips' => $midInterest,
                'minat_bahasa' => $highInterest, 'minat_vokasi' => $lowInterest,
            ],
            'Vokasi' => [
                'matematika' => $mid, 'ipa' => $mid, 'ips' => $low,
                'bahasa_indonesia' => $low, 'bahasa_inggris' => $low, 'seni_budaya' => $high,
                'minat_ipa' => $midInterest, 'minat_ips' => $lowInterest,
                'minat_bahasa' => $lowInterest, 'minat_vokasi' => $highInterest,
            ],
        };

        // For Vokasi, randomly boost one sub-major interest
        if ($class === 'Vokasi') {
            $randomMajor = $this->vocationalMajors[array_rand($this->vocationalMajors)];
            $vocSub['minat_' . strtolower($randomMajor)] = $highInterest;
        }

        return array_merge($base, $vocSub);
    }

    /**
     * Classify which specific vocational major suits the student.
     */
    private function classifyVocationalMajor(array $features): array
    {
        $scores = [];
        $mat = $features['matematika'] ?? 70;
        $ipaVal = $features['ipa'] ?? 70;
        $seniVal = $features['seni_budaya'] ?? 70;

        foreach ($this->vocationalMajors as $major) {
            $minatKey = 'minat_' . strtolower($major);
            $minat = $features[$minatKey] ?? 50;

            // Each major has unique academic affinity
            $academic = match($major) {
                'RPL' => ($mat * 0.5 + $ipaVal * 0.3 + $seniVal * 0.2),
                'TKJ' => ($mat * 0.3 + $ipaVal * 0.5 + $seniVal * 0.2),
                'MM' => ($seniVal * 0.5 + ($features['bahasa_indonesia'] ?? 70) * 0.3 + $mat * 0.2),
                'AKL' => ($mat * 0.5 + ($features['ips'] ?? 70) * 0.3 + ($features['bahasa_indonesia'] ?? 70) * 0.2),
                'OTKP' => (($features['bahasa_indonesia'] ?? 70) * 0.4 + ($features['ips'] ?? 70) * 0.3 + $mat * 0.3),
                'TKR' => ($ipaVal * 0.4 + $mat * 0.4 + $seniVal * 0.2),
                default => 70,
            };

            $scores[$major] = $academic * 0.4 + $minat * 0.6;
        }

        $total = max(array_sum($scores), 1);
        $probs = [];
        foreach ($scores as $m => $s) {
            $probs[$m] = round($s / $total, 4);
        }

        arsort($probs);
        return ['major' => array_key_first($probs), 'probabilities' => $probs];
    }

    /**
     * Analyze data quality and provide training recommendations.
     */
    public function analyzeDataQuality(): array
    {
        $students = Student::with(['scores.subject', 'questionnaireAnswers.question'])->get();
        $complete = $students->filter(fn($s) => $s->is_complete)->count();
        $total = $students->count();

        $classDistribution = [];
        foreach ($students as $student) {
            if (!$student->is_complete) continue;
            $features = $this->extractFeatures($student);
            if (!$features) continue;
            $label = $this->determineHeuristicLabel($features);
            $classDistribution[$label] = ($classDistribution[$label] ?? 0) + 1;
        }

        $recommendations = [];
        $optimalSynthetic = self::DEFAULT_SYNTHETIC_COUNT;
        $optimalVariance = self::DEFAULT_MIN_VARIANCE;
        $optimalKFold = self::DEFAULT_K_FOLD;

        if ($complete < 20) {
            $recommendations[] = ['type' => 'warning', 'msg' => "Hanya {$complete} siswa dengan data lengkap. Disarankan minimal 20 siswa untuk hasil optimal. Data sintetis akan ditambahkan otomatis."];
            $optimalSynthetic = 15;
        }

        if ($complete >= 40) {
            $optimalSynthetic = 5;
            $optimalVariance = 8.0;
            $recommendations[] = ['type' => 'success', 'msg' => "Data cukup ({$complete} siswa). Model dapat dilatih dengan percaya diri."];
        }

        $minClass = !empty($classDistribution) ? min($classDistribution) : 0;
        $maxClass = !empty($classDistribution) ? max($classDistribution) : 0;
        if ($maxClass > 0 && $minClass / $maxClass < 0.5) {
            $recommendations[] = ['type' => 'warning', 'msg' => 'Distribusi kelas tidak seimbang. Pertimbangkan menambah data untuk kelas yang kurang.'];
        }

        if (empty($recommendations)) {
            $recommendations[] = ['type' => 'info', 'msg' => 'Data dalam kondisi baik. Gunakan parameter default untuk hasil optimal.'];
        }

        return [
            'total_students' => $total,
            'complete_students' => $complete,
            'class_distribution' => $classDistribution,
            'recommendations' => $recommendations,
            'optimal_settings' => [
                'synthetic_count' => $optimalSynthetic,
                'min_variance' => $optimalVariance,
                'k_fold' => $optimalKFold,
            ],
        ];
    }

    /**
     * Get default training settings.
     */
    public function getDefaultSettings(): array
    {
        return [
            'synthetic_count' => self::DEFAULT_SYNTHETIC_COUNT,
            'min_variance' => self::DEFAULT_MIN_VARIANCE,
            'k_fold' => self::DEFAULT_K_FOLD,
        ];
    }

    /**
     * Import training data from CSV.
     * Expected columns: label, matematika, ipa, ips, bahasa_indonesia, bahasa_inggris, seni_budaya, minat_ipa, minat_ips, minat_bahasa, minat_vokasi
     */
    public function importTrainingCSV(string $csvContent): array
    {
        $lines = array_filter(explode("\n", trim($csvContent)));
        if (count($lines) < 2) return ['success' => false, 'message' => 'CSV harus memiliki header dan minimal 1 baris data.'];

        $header = str_getcsv(array_shift($lines));
        $header = array_map('trim', array_map('strtolower', $header));

        if (!in_array('label', $header)) {
            return ['success' => false, 'message' => 'Kolom "label" wajib ada di CSV.'];
        }

        $imported = 0;
        $trainingData = [];
        foreach ($lines as $line) {
            $row = str_getcsv($line);
            if (count($row) !== count($header)) continue;
            $data = array_combine($header, $row);
            $label = ucfirst(strtolower(trim($data['label'])));
            if (!in_array($label, $this->classes)) continue;

            $features = [];
            foreach ($data as $key => $val) {
                if ($key !== 'label' && is_numeric($val)) {
                    $features[$key] = floatval($val);
                }
            }

            if (!empty($features)) {
                $trainingData[] = ['class' => $label, 'features' => $features];
                $imported++;
            }
        }

        if ($imported === 0) {
            return ['success' => false, 'message' => 'Tidak ada data valid yang dapat diimport.'];
        }

        // Store imported training data as a setting (JSON)
        \App\Models\Setting::setValue('custom_training_data', json_encode($trainingData));

        return ['success' => true, 'message' => "Berhasil mengimport {$imported} data training.", 'count' => $imported];
    }

    /**
     * Get custom training data if exists.
     */
    public function getCustomTrainingData(): ?array
    {
        $data = \App\Models\Setting::getValue('custom_training_data');
        return $data ? json_decode($data, true) : null;
    }

    private function emptyConfusionMatrix(): array
    {
        return [
            'ipa' => ['ipa' => 0, 'ips' => 0, 'bahasa' => 0, 'vokasi' => 0],
            'ips' => ['ipa' => 0, 'ips' => 0, 'bahasa' => 0, 'vokasi' => 0],
            'bahasa' => ['ipa' => 0, 'ips' => 0, 'bahasa' => 0, 'vokasi' => 0],
            'vokasi' => ['ipa' => 0, 'ips' => 0, 'bahasa' => 0, 'vokasi' => 0],
        ];
    }
}
