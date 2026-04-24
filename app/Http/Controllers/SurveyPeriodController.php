<?php
// app/Http/Controllers/SurveyPeriodController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyPeriod;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SAWCalculationResult;
use App\Models\User;
use Illuminate\Support\Collection;

class SurveyPeriodController extends Controller
{
    /**
     * Check admin authentication
     */
    private function checkAdminAuth()
    {
        if (!session('admin_id') && !session('admin_user') && !session('admin')) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }
        return null;
    }

    // ============================================================
    // KELOLA PERIODE (CRUD)
    // ============================================================

    /**
     * HALAMAN UTAMA - List semua periode
     * 
     * Route: GET /admin/periods
     */
    public function index()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $periods = SurveyPeriod::with(['sawResults', 'responses'])
                               ->orderBy('year', 'desc')
                               ->get();
        
        return view('admin.periods.index', compact('periods'));
    }

    /**
     * FORM BUAT PERIODE BARU
     * 
     * Route: GET /admin/periods/create
     */
    public function create()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        return view('admin.periods.create');
    }

    /**
     * SIMPAN PERIODE BARU
     * 
     * Route: POST /admin/periods
     */
    public function store(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $validated = $request->validate([
            'period_name' => 'required|string|max:255',
            'year' => 'required|integer|min:2020|max:2100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = 'draft';
        $validated['is_active'] = false;
        $validated['survey_id'] = 1; // Atau sesuaikan dengan survey_id yang ada

        $period = SurveyPeriod::create($validated);

        return redirect()->route('admin.periods.index')
                         ->with('success', "Periode {$period->period_name} berhasil dibuat.");
    }

    /**
     * AKTIFKAN PERIODE
     * 
     * Route: POST /admin/periods/{id}/activate
     */
    public function activate($id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $period = SurveyPeriod::findOrFail($id);

        // Nonaktifkan semua periode lain
        SurveyPeriod::where('survey_id', $period->survey_id)
                    ->update(['is_active' => false, 'status' => 'closed']);

        // Aktifkan periode ini
        $period->update([
            'status' => 'active',
            'is_active' => true
        ]);

        return redirect()->back()->with('success', 
            "Periode {$period->period_name} telah diaktifkan.");
    }

    /**
     * TUTUP PERIODE
     * 
     * Route: POST /admin/periods/{id}/close
     */
    public function close($id)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $period = SurveyPeriod::findOrFail($id);

        // Hitung dan simpan hasil SAW final sebelum tutup
        $this->calculateAndStoreSAW($period->id);

        // Tutup periode
        $period->update([
            'status' => 'closed',
            'is_active' => false
        ]);

        return redirect()->back()->with('success', 
            "Periode {$period->period_name} telah ditutup. Hasil SAW telah disimpan.");
    }

    // ============================================================
    // DASHBOARD SAW DENGAN FILTER PERIODE
    // ============================================================

    /**
     * DASHBOARD SAW DENGAN PERIODE
     * 
     * Route: GET /admin/periods/{id}/saw
     */
    public function showSAW($periodId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Get selected period
        $selectedPeriod = SurveyPeriod::findOrFail($periodId);

        // Ambil hasil SAW untuk periode yang dipilih
        $sawResults = SAWCalculationResult::where('period_id', $selectedPeriod->id)->get();
        
        // Kalau belum ada hasil, hitung dulu
        if ($sawResults->isEmpty()) {
            $sawResults = $this->calculateAndStoreSAW($selectedPeriod->id);
        }

        // Ambil semua periode untuk dropdown
        $allPeriods = SurveyPeriod::orderBy('year', 'desc')->get();
        
        // Total Vi
        $totalVi = $sawResults->sum('weighted_score');
        
        // Total responden di periode ini
        $totalResponses = $selectedPeriod->total_respondents ?? 0;

        // Format data untuk view (compatibility dengan view lama)
        $criteriaResults = $sawResults->map(function($result) {
            return [
                'criteria' => $result->criteria_name,
                'score' => $result->average_score,
                'weight_normalized' => $result->weight_normalized,
                'normalized' => $result->normalized_score,
                'weighted_score' => $result->weighted_score,
                'interpretation' => $result->interpretation,
                'total_responses' => $result->total_responses,
                'questions_count' => $result->questions_count,
                'criteria_type' => $result->criteria_type,
            ];
        });

        return view('admin.periods.saw-results', [
            'criteriaResults' => $criteriaResults,
            'selectedPeriod' => $selectedPeriod,
            'allPeriods' => $allPeriods,
            'hasSAW' => $sawResults->isNotEmpty(),
            'totalVi' => $totalVi,
            'totalResponses' => $totalResponses,
        ]);
    }

    /**
     * RECALCULATE SAW UNTUK PERIODE TERTENTU
     * 
     * Route: POST /admin/periods/{id}/saw/recalculate
     */
    public function recalculateSAW($periodId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $period = SurveyPeriod::findOrFail($periodId);
        
        // Hitung ulang
        $this->calculateAndStoreSAW($periodId);
        
        return redirect()->back()->with('success', 
            "Hasil SAW untuk periode {$period->period_name} berhasil dihitung ulang.");
    }

    // ============================================================
    // JAWABAN RESPONDEN PER PERIODE
    // ============================================================

    /**
     * HALAMAN JAWABAN SEMUA RESPONDEN (untuk periode tertentu)
     * 
     * Route: GET /admin/periods/{id}/responses
     */
    public function responses($periodId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $period = SurveyPeriod::findOrFail($periodId);
        
        // Ambil unique responden di periode ini
        $responses = SurveyResponse::where('period_id', $periodId)
                                   ->select('user_id', 'created_at')
                                   ->groupBy('user_id')
                                   ->with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(20);
        
        $totalResponses = SurveyResponse::where('period_id', $periodId)
                                        ->distinct('user_id')
                                        ->count('user_id');
        
        $questions = SurveyQuestion::where('enable_saw', true)->get();
        
        return view('admin.periods.responses', [
            'period' => $period,
            'responses' => $responses,
            'totalResponses' => $totalResponses,
            'questions' => $questions,
        ]);
    }

    /**
     * HALAMAN DETAIL JAWABAN 1 RESPONDEN (di periode tertentu)
     * 
     * Route: GET /admin/periods/{periodId}/responses/{userId}
     */
    public function responseDetail($periodId, $userId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $period = SurveyPeriod::findOrFail($periodId);
        $user = User::find($userId);
        
        // Ambil semua jawaban user ini di periode ini
        $responses = SurveyResponse::where('period_id', $periodId)
                                   ->where('user_id', $userId)
                                   ->with(['question.section'])
                                   ->get();
        
        return view('admin.periods.response-detail', [
            'period' => $period,
            'user' => $user,
            'responses' => $responses,
        ]);
    }

    // ============================================================
    // PERBANDINGAN ANTAR PERIODE
    // ============================================================

    /**
     * HALAMAN PERBANDINGAN ANTAR PERIODE
     * 
     * Route: GET /admin/saw/compare
     */
    public function comparePeriods()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $periods = SurveyPeriod::orderBy('year', 'asc')->get();

        if ($periods->count() < 2) {
            return redirect()->route('admin.periods.index')
                             ->with('info', 'Perlu minimal 2 periode untuk melakukan perbandingan.');
        }

        // Ambil semua nama kriteria unik
        $allCriteria = SAWCalculationResult::distinct('criteria_name')
                                          ->pluck('criteria_name');

        // Ambil hasil SAW untuk semua periode
        $comparisonData = [];
        foreach ($periods as $period) {
            $results = SAWCalculationResult::where('period_id', $period->id)->get();
            
            $comparisonData[] = [
                'period' => $period,
                'total_vi' => $results->sum('weighted_score'),
                'results' => $results,
            ];
        }

        return view('admin.saw.compare', [
            'periods' => $periods,
            'allCriteria' => $allCriteria,
            'comparisonData' => $comparisonData,
        ]);
    }

    // ============================================================
    // HELPER METHODS - PERHITUNGAN SAW
    // ============================================================

    /**
     * HITUNG DAN SIMPAN HASIL SAW UNTUK PERIODE TERTENTU
     */
    public function calculateAndStoreSAW($periodId)
    {
        $period = SurveyPeriod::findOrFail($periodId);
        
        // Get all SAW enabled questions
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
                                    ->where('question_type', 'linear_scale')
                                    ->whereNotNull('criteria_name')
                                    ->get();

        if ($sawQuestions->isEmpty()) {
            return collect();
        }

        // Group questions by criteria
        $questionsByCriteria = $sawQuestions->groupBy('criteria_name');
        $criteriaAggregates = collect();

        foreach ($questionsByCriteria as $criteriaName => $questions) {
            // Collect all responses for this criteria IN THIS PERIOD
            $allScores = collect();
            
            foreach ($questions as $question) {
                // PENTING: Filter responses berdasarkan period_id
                $questionResponses = SurveyResponse::where('question_id', $question->id)
                                                   ->where('period_id', $periodId)
                                                   ->get();
                
                foreach ($questionResponses as $response) {
                    // Gunakan answer_value atau answer tergantung field yang ada
                    $answerField = isset($response->answer_value) ? 'answer_value' : 'answer';
                    $allScores->push((float) $response->$answerField);
                }
            }

            if ($allScores->isNotEmpty()) {
                $criteriaAverage = $allScores->avg();
                $firstQuestion = $questions->first();
                
                $settings = $firstQuestion->settings ?? [];
                $scaleMax = $settings['scale_max'] ?? 5;
                $scaleMin = $settings['scale_min'] ?? 1;
                
                $criteriaAggregates->push([
                    'criteria_name' => $criteriaName ?: 'Tidak Dikategorikan',
                    'criteria_weight' => $firstQuestion->criteria_weight ?? 0,
                    'criteria_type' => $firstQuestion->criteria_type ?? 'benefit',
                    'average_score' => $criteriaAverage,
                    'total_responses' => $allScores->count(),
                    'questions_count' => $questions->count(),
                    'scale_max' => $scaleMax,
                    'scale_min' => $scaleMin,
                ]);
            }
        }

        // STEP 1: NORMALISASI BOBOT KRITERIA
        $totalWeight = $criteriaAggregates->sum('criteria_weight');
        if ($totalWeight == 0) {
            return collect();
        }

        // Hapus hasil lama untuk periode ini (kalau ada)
        SAWCalculationResult::where('period_id', $periodId)->delete();

        // STEP 2: NORMALISASI SAW DAN SIMPAN KE DATABASE
        $results = collect();
        
        foreach ($criteriaAggregates as $criteria) {
            // Bobot ternormalisasi
            $weightNormalized = $criteria['criteria_weight'] / $totalWeight;
            
            // Normalisasi SAW
            if ($criteria['criteria_type'] === 'benefit') {
                $maxScore = $criteria['scale_max'];
                $normalized = $maxScore > 0 ? ($criteria['average_score'] / $maxScore) : 0;
            } else {
                $minScore = $criteria['scale_min'];
                $normalized = $criteria['average_score'] > 0 ? ($minScore / $criteria['average_score']) : 0;
            }
            
            // Ensure normalized score is between 0 and 1
            $normalized = max(0, min(1, $normalized));
            
            // Nilai Terbobot (wj × rij)
            $weightedScore = $weightNormalized * $normalized;
            
            // SIMPAN KE DATABASE
            $sawResult = SAWCalculationResult::create([
                'survey_id' => null,
                'period_id' => $periodId,
                'criteria_name' => $criteria['criteria_name'],
                'criteria_type' => $criteria['criteria_type'],
                'criteria_weight' => $criteria['criteria_weight'],
                'average_score' => round($criteria['average_score'], 2),
                'normalized_score' => round($normalized, 4),
                'weight_normalized' => round($weightNormalized, 4),
                'weighted_score' => round($weightedScore, 4),
                'total_responses' => $criteria['total_responses'],
                'questions_count' => $criteria['questions_count'],
                'calculated_at' => now(),
            ]);
            
            $results->push($sawResult);
        }

        return $results;
    }

    /**
     * INTERPRETASI KUALITATIF UNTUK SAW
     */
    private function getSAWInterpretation($normalizedScore)
    {
        if ($normalizedScore >= 0.9) return 'Sangat Baik';
        if ($normalizedScore >= 0.8) return 'Baik';
        if ($normalizedScore >= 0.6) return 'Cukup';
        if ($normalizedScore >= 0.4) return 'Kurang';
        return 'Sangat Kurang';
    }
}