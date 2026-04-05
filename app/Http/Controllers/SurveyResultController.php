<?php
// app/Http/Controllers/SurveyResultController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveySection;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class SurveyResultController extends Controller
{
    /**
     * Check admin authentication - FLEXIBLE
     */
    private function checkAdminAuth()
    {
        // Support multiple session keys
        if (!session('admin_id') && !session('admin_user') && !session('admin')) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }
        return null;
    }

    /**
     * DASHBOARD LANGSUNG TABEL SAW
     * 
     * Halaman utama yang langsung menampilkan tabel:
     * | Kriteria | Skor (x) | Bobot Normalisasi (wᵢ) | Normalisasi (rᵢ) | Nilai Terbobot (wᵢ×rᵢ) | Keterangan |
     * 
     * Tanpa perlu pilih survey ID, langsung agregasi semua data
     */
    public function dashboard()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Get all SAW enabled questions
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
                                    ->where('question_type', 'linear_scale')
                                    ->whereNotNull('criteria_name')
                                    ->with('responses')
                                    ->get();

        if ($sawQuestions->isEmpty()) {
            return view('admin.hasil-survey.dashboard', [
                'criteriaResults' => collect(),
                'hasSAW' => false,
                'totalVi' => 0,
                'totalResponses' => 0,
                'message' => 'Tidak ada pertanyaan dengan pengaturan SAW yang aktif. 
                             Silakan aktifkan fitur SAW pada pertanyaan dengan tipe skala linier.'
            ]);
        }

        // Calculate aggregate SAW results from all surveys
        $criteriaResults = $this->calculateAggregateSAWResults($sawQuestions);
        $totalVi = $criteriaResults->sum('weighted_score');

        // Count total responses
        $totalResponses = SurveyResponse::whereHas('question', function($query) {
            $query->where('enable_saw', true);
        })->distinct('survey_id')->count();

        return view('admin.hasil-survey.dashboard', compact('criteriaResults', 'totalVi', 'totalResponses') + ['hasSAW' => true]);
    }

    /**
     * PERHITUNGAN AGREGAT SAW DARI SEMUA SURVEY
     * 
     * Menghitung nilai rata-rata per kriteria dari seluruh survey,
     * kemudian menerapkan rumus SAW untuk mendapatkan nilai akhir
     */
    private function calculateAggregateSAWResults($sawQuestions)
    {
        $results = collect();
        
        // # agregasi per kriteria Group questions by criteria
        $questionsByCriteria = $sawQuestions->groupBy('criteria_name');
        $criteriaAggregates = collect();

        foreach ($questionsByCriteria as $criteriaName => $questions) {
            // Collect all responses for this criteria from all surveys
            $allScores = collect();
            
            foreach ($questions as $question) {
                // Get all responses for this question
                $questionResponses = $question->responses;
                
                foreach ($questionResponses as $response) {
                    $allScores->push((float) $response->answer);
                }
            }

            if ($allScores->isNotEmpty()) {
                // Calculate average score for this criteria across all responses
                $criteriaAverage = $allScores->avg();
                
                $firstQuestion = $questions->first();
                
                $criteriaAggregates->push([
                    'criteria_name' => $criteriaName ?: 'Tidak Dikategorikan',
                    'criteria_weight' => $firstQuestion->criteria_weight ?? 0,
                    'criteria_type' => $firstQuestion->criteria_type ?? 'benefit',
                    'average_score' => $criteriaAverage,
                    'total_responses' => $allScores->count(),
                    'questions_count' => $questions->count()
                ]);
            }
        }

        // # STEP 1: NORMALISASI BOBOT KRITERIA
        $totalWeight = $criteriaAggregates->sum('criteria_weight');
        if ($totalWeight == 0) {
            return $results;
        }

        // STEP 2: NORMALISASI SAW DAN PERHITUNGAN NILAI TERBOBOT
        foreach ($criteriaAggregates as $criteria) {
            // Bobot ternormalisasi
            $weightNormalized = $criteria['criteria_weight'] / $totalWeight;
            
            // # Normalisasi SAW benefit dan cost - menggunakan skor rata-rata sebagai Xij
            if ($criteria['criteria_type'] === 'benefit') {
                // Untuk benefit: rij = Xij / Max{Xij}
                $maxScore = $criteriaAggregates->max('average_score');
                $normalized = $maxScore > 0 ? ($criteria['average_score'] / $maxScore) : 0;
            } else {
                // Untuk cost: rij = Min{Xij} / Xij
                $minScore = $criteriaAggregates->min('average_score');
                $normalized = $criteria['average_score'] > 0 ? ($minScore / $criteria['average_score']) : 0;
            }
            
            // Ensure normalized score is between 0 and 1
            $normalized = max(0, min(1, $normalized));
            
            // # perhitungan Nilai Terbobot (wj × rij)
            $weightedScore = $weightNormalized * $normalized;
            
            // Keterangan interpretasi
            $interpretation = $this->getSAWInterpretation($normalized);
            
            // Build result untuk tabel
            $results->push([
                'criteria' => $criteria['criteria_name'],
                'score' => round($criteria['average_score'], 2), // Skor (x) - rata-rata
                'weight_normalized' => round($weightNormalized, 3), // Bobot Normalisasi (wᵢ)
                'normalized' => round($normalized, 3), // Normalisasi (rᵢ)
                'weighted_score' => round($weightedScore, 4), // Nilai Terbobot (wᵢ×rᵢ)
                'interpretation' => $interpretation, // Keterangan
                'total_responses' => $criteria['total_responses'],
                'questions_count' => $criteria['questions_count'],
                'criteria_type' => $criteria['criteria_type']
            ]);
        }

        return $results;
    }

    /**
     * # INTERPRETASI KUALITITATIF UNTUK SAW
     */
    private function getSAWInterpretation($normalizedScore)
    {
        if ($normalizedScore >= 0.9) return 'Sangat Baik';
        if ($normalizedScore >= 0.8) return 'Baik';
        if ($normalizedScore >= 0.6) return 'Cukup';
        if ($normalizedScore >= 0.4) return 'Kurang';
        return 'Sangat Kurang';
    }

    /**
     * EXPORT PDF - DATA MENTAH + HASIL SAW
     * 
     * Generate PDF berisi:
     * 1. Cover Page
     * 2. Hasil Perhitungan SAW (Tabel Rangking + Grafik)
     * 3. Data Mentah Responden (Detail jawaban per responden)
     * 4. Ringkasan Statistik per Pertanyaan
     * 5. Lampiran (Penjelasan Metode SAW)
     */
    public function exportPDF()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Get SAW questions
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
                                    ->where('question_type', 'linear_scale')
                                    ->whereNotNull('criteria_name')
                                    ->with('responses')
                                    ->get();

        // Calculate SAW results
        $criteriaResults = $this->calculateAggregateSAWResults($sawQuestions);
        $totalVi = $criteriaResults->sum('weighted_score');

        // Get all surveys with responses
        $surveys = Survey::with(['responses.question.section'])
                        ->whereHas('responses')
                        ->get();

        // Calculate SAW score for each survey
        $surveysWithSAW = $surveys->map(function($survey) use ($sawQuestions) {
            $sawScore = $this->calculateIndividualSAWScore($survey, $sawQuestions);
            $survey->saw_score = $sawScore;
            return $survey;
        })->sortByDesc('saw_score')->values();

        // Get all questions with sections
        $sections = SurveySection::where('is_active', true)
                                ->with(['allQuestions' => function($q) {
                                    $q->where('is_active', true);
                                }])
                                ->orderBy('order_index')
                                ->get();
        
        // Filter sections yang memiliki questions aktif dan rename relation
        $sections->transform(function($section) {
            $section->questions = $section->allQuestions;
            unset($section->allQuestions);
            return $section;
        });

        // Statistics per question
        $questionStats = $this->calculateQuestionStatistics($sections);

        // SAW configuration
        $sawConfig = $sawQuestions->map(function($q) {
            return [
                'code' => 'Q' . $q->id,
                'question' => $q->question_text,
                'type' => $q->criteria_type,
                'weight' => $q->criteria_weight
            ];
        });

        // Prepare data for PDF
        $data = [
            'title' => 'LAPORAN DATA SURVEI',
            'generated_at' => now()->format('d F Y H:i:s'),
            'total_responses' => $surveysWithSAW->count(),
            'period_start' => $surveys->min('created_at')?->format('d F Y') ?? '-',
            'period_end' => $surveys->max('created_at')?->format('d F Y') ?? '-',
            
            // SAW Results
            'criteriaResults' => $criteriaResults,
            'totalVi' => $totalVi,
            'surveysWithSAW' => $surveysWithSAW,
            'sawConfig' => $sawConfig,
            
            // Raw Data
            'sections' => $sections,
            'questionStats' => $questionStats,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.hasil-survey.export-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Laporan_Survey_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Calculate SAW score for individual survey
     */
    private function calculateIndividualSAWScore($survey, $sawQuestions)
    {
        if ($sawQuestions->isEmpty()) {
            return 0;
        }

        $totalScore = 0;
        $totalWeight = $sawQuestions->sum('criteria_weight');
        
        if ($totalWeight == 0) {
            return 0;
        }

        // Get all SAW responses for this survey
        $surveyResponses = $survey->responses()
            ->whereIn('question_id', $sawQuestions->pluck('id'))
            ->get();

        // Group responses by criteria
        $criteriaScores = [];
        foreach ($sawQuestions as $question) {
            $response = $surveyResponses->firstWhere('question_id', $question->id);
            
            if ($response) {
                $criteriaName = $question->criteria_name ?: 'Uncategorized';
                
                if (!isset($criteriaScores[$criteriaName])) {
                    $criteriaScores[$criteriaName] = [
                        'scores' => [],
                        'type' => $question->criteria_type,
                        'weight' => $question->criteria_weight
                    ];
                }
                
                $criteriaScores[$criteriaName]['scores'][] = (float) $response->answer;
            }
        }

        // Calculate normalized scores
        foreach ($criteriaScores as $criteriaName => $data) {
            $avgScore = collect($data['scores'])->avg();
            
            // Normalization
            if ($data['type'] === 'benefit') {
                $maxScore = $sawQuestions->max(function($q) use ($surveyResponses) {
                    $r = $surveyResponses->firstWhere('question_id', $q->id);
                    return $r ? (float) $r->answer : 0;
                });
                $normalized = $maxScore > 0 ? ($avgScore / $maxScore) : 0;
            } else {
                $minScore = $sawQuestions->min(function($q) use ($surveyResponses) {
                    $r = $surveyResponses->firstWhere('question_id', $q->id);
                    return $r ? (float) $r->answer : 1;
                });
                $normalized = $avgScore > 0 ? ($minScore / $avgScore) : 0;
            }
            
            $weightNormalized = $data['weight'] / $totalWeight;
            $totalScore += ($normalized * $weightNormalized);
        }

        return round($totalScore, 4);
    }

    /**
     * Calculate statistics for each question
     */
    private function calculateQuestionStatistics($sections)
    {
        $stats = [];
        
        foreach ($sections as $section) {
            foreach ($section->questions as $question) {
                $responses = $question->responses;
                
                if ($responses->isEmpty()) {
                    continue;
                }

                $stat = [
                    'question_id' => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'section_name' => $section->title,
                    'total_responses' => $responses->count(),
                    'distribution' => []
                ];

                // For questions with options
                if (in_array($question->question_type, ['radio', 'select', 'checkbox'])) {
                    $distribution = $responses->groupBy('answer')->map(function($group) use ($responses) {
                        return [
                            'count' => $group->count(),
                            'percentage' => round(($group->count() / $responses->count()) * 100, 1)
                        ];
                    });
                    
                    $stat['distribution'] = $distribution->toArray();
                }
                
                // For linear scale
                if ($question->question_type === 'linear_scale') {
                    $stat['average'] = round($responses->avg('answer'), 2);
                    $stat['min'] = $responses->min('answer');
                    $stat['max'] = $responses->max('answer');
                }

                $stats[] = $stat;
            }
        }
        
        return collect($stats);
    }

    /**
     * CONTOH OUTPUT YANG DIHASILKAN:
     * 
     * | Kriteria     | Skor (x) | Bobot Normalisasi (wᵢ) | Normalisasi (rᵢ) | Nilai Terbobot (wᵢ×rᵢ) | Keterangan |
     * |--------------|----------|------------------------|-------------------|------------------------|------------|
     * | Afektif      | 78.50    | 0.300                  | 0.870             | 0.261                  | Baik       |
     * | Kognitif     | 82.10    | 0.400                  | 0.910             | 0.364                  | Sangat Baik|
     * | Psikomotorik | 75.30    | 0.300                  | 0.834             | 0.250                  | Baik       |
     */
}