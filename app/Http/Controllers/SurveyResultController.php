<?php
// app/Http/Controllers/SurveyResultController.php - LANGSUNG TABEL SAW

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveySection;
use Illuminate\Support\Collection;

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
        
        // Group questions by criteria
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

        // STEP 1: NORMALISASI BOBOT KRITERIA
        $totalWeight = $criteriaAggregates->sum('criteria_weight');
        if ($totalWeight == 0) {
            return $results;
        }

        // STEP 2: NORMALISASI SAW DAN PERHITUNGAN NILAI TERBOBOT
        foreach ($criteriaAggregates as $criteria) {
            // Bobot ternormalisasi
            $weightNormalized = $criteria['criteria_weight'] / $totalWeight;
            
            // Normalisasi SAW - menggunakan skor rata-rata sebagai Xij
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
            
            // Nilai Terbobot (wj × rij)
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
     * INTERPRETASI KUALITITATIF UNTUK SAW
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
     * CONTOH OUTPUT YANG DIHASILKAN:
     * 
     * | Kriteria     | Skor (x) | Bobot Normalisasi (wᵢ) | Normalisasi (rᵢ) | Nilai Terbobot (wᵢ×rᵢ) | Keterangan |
     * |--------------|----------|------------------------|-------------------|------------------------|------------|
     * | Afektif      | 78.50    | 0.300                  | 0.870             | 0.261                  | Baik       |
     * | Kognitif     | 82.10    | 0.400                  | 0.910             | 0.364                  | Sangat Baik|
     * | Psikomotorik | 75.30    | 0.300                  | 0.834             | 0.250                  | Baik       |
     */
}