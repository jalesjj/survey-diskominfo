<?php
// app/Http/Controllers/SurveyResultController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveySection;
use App\Models\SurveyPeriod;
use App\Models\SAWCalculationResult; // TAMBAHAN: Import SAWCalculationResult
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\SAWRespondentService;

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
     * DASHBOARD LANGSUNG TABEL SAW - DENGAN FILTER PERIODE
     * 
     * Halaman utama yang langsung menampilkan tabel:
     * | Kriteria | Skor (x) | Bobot Normalisasi (wᵢ) | Normalisasi (rᵢ) | Nilai Terbobot (wᵢ×rᵢ) | Keterangan |
     * 
     * Tanpa perlu pilih survey ID, langsung agregasi semua data
     */
    public function dashboard(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;
 
        $periodId   = $request->get('period_id');
        $allPeriods = SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->get();
 
        if ($periodId) {
            $selectedPeriod = SurveyPeriod::find($periodId);
        } else {
            $selectedPeriod = SurveyPeriod::where('is_active', true)->first()
                           ?? SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->first();
        }
 
        // ─── Helper: ambil data responden + skor SAW per responden ───────────
        $sawService = new SAWRespondentService();
 
        if ($selectedPeriod) {
            // Ambil surveys yang punya respons di periode ini
            $surveys = Survey::with(['responses' => function ($q) use ($selectedPeriod) {
                            $q->where('period_id', $selectedPeriod->id);
                        }])
                        ->whereHas('responses', function ($q) use ($selectedPeriod) {
                            $q->where('period_id', $selectedPeriod->id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
        } else {
            $surveys = Survey::with('responses')->orderBy('created_at', 'desc')->get();
        }
 
        // Hitung skor SAW per responden (1 query, efisien)
        $respondentSawScores = $sawService->calculateForSurveys($surveys);
        // ────────────────────────────────────────────────────────────────────
 
        if ($selectedPeriod) {
            // Selalu recalculate agar data yang tersimpan selalu akurat
            // (menghindari pakai data lama yang dihitung dengan bobot global yang salah)
            $sawResults = $this->calculateSAWForPeriod($selectedPeriod->id);
 
            if ($sawResults->isEmpty()) {
                return view('admin.hasil-survey.dashboard', [
                    'criteriaResults'     => collect(),
                    'hasSAW'              => false,
                    'totalVi'             => 0,
                    'totalResponses'      => 0,
                    'selectedPeriod'      => $selectedPeriod,
                    'allPeriods'          => $allPeriods,
                    'message'             => 'Tidak ada data SAW untuk periode ' . $selectedPeriod->period_name . '. Silakan aktifkan fitur SAW pada pertanyaan dengan tipe skala linier.',
                    'surveys'             => $surveys,
                    'respondentSawScores' => $respondentSawScores,
                ]);
            }
 
            $totalVi        = $sawResults->sum('weighted_score');
            $totalResponses = \DB::table('survey_responses')
                ->where('period_id', $selectedPeriod->id)
                ->distinct('survey_id')
                ->count('survey_id');
 
            $criteriaResults = $sawResults->map(function ($result) {
                return [
                    'criteria'        => $result->criteria_name,
                    'score'           => $result->average_score,
                    'weight_normalized' => $result->weight_normalized,
                    'normalized'      => $result->normalized_score,
                    'weighted_score'  => $result->weighted_score,
                    'interpretation'  => $result->interpretation,
                    'total_responses' => $result->total_responses,
                    'questions_count' => $result->questions_count,
                    'criteria_type'   => $result->criteria_type,
                ];
            });
 
            return view('admin.hasil-survey.dashboard', [
                'criteriaResults'     => $criteriaResults,
                'hasSAW'              => true,
                'totalVi'             => $totalVi,
                'totalResponses'      => $totalResponses,
                'selectedPeriod'      => $selectedPeriod,
                'allPeriods'          => $allPeriods,
                'surveys'             => $surveys,             // ← TAMBAHAN
                'respondentSawScores' => $respondentSawScores, // ← TAMBAHAN
            ]);
 
        } else {
            // Tidak ada periode sama sekali
            $sawQuestions = SurveyQuestion::where('enable_saw', true)
                                ->where('question_type', 'linear_scale')
                                ->whereNotNull('criteria_id')
                                ->with('responses')
                                ->get();
 
            if ($sawQuestions->isEmpty()) {
                return view('admin.hasil-survey.dashboard', [
                    'criteriaResults'     => collect(),
                    'hasSAW'              => false,
                    'totalVi'             => 0,
                    'totalResponses'      => 0,
                    'selectedPeriod'      => null,
                    'allPeriods'          => $allPeriods,
                    'message'             => 'Tidak ada pertanyaan dengan pengaturan SAW yang aktif. Silakan aktifkan fitur SAW pada pertanyaan dengan tipe skala linier.',
                    'surveys'             => $surveys,
                    'respondentSawScores' => $respondentSawScores,
                ]);
            }
 
            $criteriaResults = $this->calculateAggregateSAWResults($sawQuestions);
            $totalVi         = $criteriaResults->sum('weighted_score');
            $totalResponses  = SurveyResponse::whereHas('question', function ($query) {
                $query->where('enable_saw', true);
            })->distinct('survey_id')->count();
 
            return view('admin.hasil-survey.dashboard', [
                'criteriaResults'     => $criteriaResults,
                'hasSAW'              => true,
                'totalVi'             => $totalVi,
                'totalResponses'      => $totalResponses,
                'selectedPeriod'      => null,
                'allPeriods'          => $allPeriods,
                'surveys'             => $surveys,             // ← TAMBAHAN
                'respondentSawScores' => $respondentSawScores, // ← TAMBAHAN
            ]);
        }
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
                
                // Get scale_max and scale_min from settings (for linear_scale questions)
                $settings = $firstQuestion->settings ?? [];
                $scaleMax = $settings['scale_max'] ?? 5; // Default 5
                $scaleMin = $settings['scale_min'] ?? 1; // Default 1
                
                $criteriaAggregates->push([
                    'criteria_name' => $criteriaName ?: 'Tidak Dikategorikan',
                    'criteria_weight' => $firstQuestion->criteria_weight ?? 0,
                    'criteria_type' => $firstQuestion->criteria_type ?? 'benefit',
                    'average_score' => $criteriaAverage,
                    'total_responses' => $allScores->count(),
                    'questions_count' => $questions->count(),
                    'scale_max' => $scaleMax,
                    'scale_min' => $scaleMin
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
                // Untuk benefit: rij = Xij / Max{Scale}
                // Menggunakan nilai maksimal dari skala (scale_max), bukan max dari average_score
                $maxScore = $criteria['scale_max'];
                $normalized = $maxScore > 0 ? ($criteria['average_score'] / $maxScore) : 0;
            } else {
                // Untuk cost: rij = Min{Scale} / Xij
                // Menggunakan nilai minimal dari skala (scale_min), bukan min dari average_score
                $minScore = $criteria['scale_min'];
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
     * HITUNG SAW UNTUK PERIODE TERTENTU
     *
     * Perbaikan:
     * 1. Normalisasi bobot HANYA dari kriteria yang benar-benar dijawab di periode ini
     *    (bukan dari semua pertanyaan SAW global) agar total Vi bisa mencapai 1.0
     * 2. scale_max / scale_min diambil dari settings tiap pertanyaan, bukan hard-coded 5/1
     * 3. Selalu recalculate (updateOrCreate) agar data lama yang salah ikut diperbaiki
     */
    private function calculateSAWForPeriod($periodId)
    {
        // Ambil semua pertanyaan dengan enable_saw
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
            ->where('question_type', 'linear_scale')
            ->whereNotNull('criteria_id')
            ->get();

        if ($sawQuestions->isEmpty()) {
            return collect();
        }

        // Group by criteria_name
        $criteriaGroups = $sawQuestions->groupBy('criteria_name');

        // PASS 1: kumpulkan kriteria yang benar-benar ada jawabannya di periode ini
        $answeredCriteria = [];

        foreach ($criteriaGroups as $criteriaName => $questions) {
            $firstQuestion  = $questions->first();
            $criteriaWeight = $firstQuestion->criteria_weight ?? 0;
            $criteriaType   = $firstQuestion->criteria_type ?? 'benefit';
            $settings       = $firstQuestion->settings ?? [];

            // Ambil scale_max / scale_min dari settings pertanyaan (bukan hard-code)
            $scaleMax = isset($settings['scale_max']) ? (float) $settings['scale_max'] : 5;
            $scaleMin = isset($settings['scale_min']) ? (float) $settings['scale_min'] : 1;

            $totalScore = 0;
            $totalCount = 0;

            foreach ($questions as $question) {
                $responses = SurveyResponse::where('question_id', $question->id)
                    ->where('period_id', $periodId)
                    ->get();

                foreach ($responses as $response) {
                    $totalScore += (float) $response->answer;
                    $totalCount++;
                }
            }

            // Lewati kriteria yang tidak ada jawabannya
            if ($totalCount == 0) {
                continue;
            }

            $answeredCriteria[] = [
                'criteria_name'   => $criteriaName,
                'criteria_type'   => $criteriaType,
                'criteria_weight' => $criteriaWeight,
                'average_score'   => $totalScore / $totalCount,
                'total_responses' => $totalCount,
                'questions_count' => $questions->count(),
                'scale_max'       => $scaleMax,
                'scale_min'       => $scaleMin,
            ];
        }

        if (empty($answeredCriteria)) {
            return collect();
        }

        // PASS 2: total bobot HANYA dari kriteria yang dijawab
        $answeredTotalWeight = array_sum(array_column($answeredCriteria, 'criteria_weight'));

        if ($answeredTotalWeight == 0) {
            return collect();
        }

        // PASS 3: normalisasi, hitung weighted score, simpan ke DB
        $results = [];

        foreach ($answeredCriteria as $criteria) {
            // Bobot ternormalisasi (dari kriteria yang dijawab saja)
            $weightNormalized = $criteria['criteria_weight'] / $answeredTotalWeight;

            // Normalisasi SAW menggunakan scale dari settings pertanyaan
            if ($criteria['criteria_type'] === 'benefit') {
                $normalizedScore = $criteria['scale_max'] > 0
                    ? ($criteria['average_score'] / $criteria['scale_max'])
                    : 0;
            } else {
                // cost: nilai rendah = bagus
                $normalizedScore = $criteria['average_score'] > 0
                    ? ($criteria['scale_min'] / $criteria['average_score'])
                    : 0;
            }

            $normalizedScore = max(0.0, min(1.0, $normalizedScore));
            $weightedScore   = $weightNormalized * $normalizedScore;
            $interpretation  = $this->getSAWInterpretation($normalizedScore);

            // Selalu updateOrCreate agar data lama yang salah ikut diperbarui
            $sawResult = SAWCalculationResult::updateOrCreate(
                [
                    'period_id'     => $periodId,
                    'criteria_name' => $criteria['criteria_name'],
                ],
                [
                    'criteria_type'   => $criteria['criteria_type'],
                    'criteria_weight' => $criteria['criteria_weight'],
                    'weight_normalized' => round($weightNormalized, 4),
                    'average_score'   => round($criteria['average_score'], 4),
                    'normalized_score' => round($normalizedScore, 4),
                    'weighted_score'  => round($weightedScore, 4),
                    'interpretation'  => $interpretation,
                    'total_responses' => $criteria['total_responses'],
                    'questions_count' => $criteria['questions_count'],
                    'calculated_at'   => now(),
                ]
            );

            $results[] = $sawResult;
        }

        return collect($results);
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
     * EXPORT PDF - DATA MENTAH + HASIL SAW
     * 
     * Generate PDF berisi:
     * 1. Cover Page
     * 2. Hasil Perhitungan SAW (Tabel Rangking + Grafik)
     * 3. Data Mentah Responden (Detail jawaban per responden)
     * 4. Ringkasan Statistik per Pertanyaan
     * 5. Lampiran (Penjelasan Metode SAW)
     */
    public function exportPDF(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Ambil periode dari request
        $periodId = $request->get('period_id');
        $selectedPeriod = $periodId ? SurveyPeriod::find($periodId) : null;

        // Get SAW questions
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
                                    ->where('question_type', 'linear_scale')
                                    ->whereNotNull('criteria_id')
                                    ->with(['responses' => function($q) use ($periodId) {
                                        if ($periodId) $q->where('period_id', $periodId);
                                    }])
                                    ->get();

        // Calculate SAW results
        $criteriaResults = $this->calculateAggregateSAWResults($sawQuestions);
        $totalVi = $criteriaResults->sum('weighted_score');

        // Get surveys - filter berdasarkan periode jika dipilih
        $surveysQuery = Survey::with(['responses.question.section'])
                        ->whereHas('responses', function($q) use ($periodId) {
                            if ($periodId) $q->where('period_id', $periodId);
                        });

        $surveys = $surveysQuery->get();

        // Calculate SAW score for each survey
        $surveysWithSAW = $surveys->map(function($survey) use ($sawQuestions) {
            $sawScore = $this->calculateIndividualSAWScore($survey, $sawQuestions);
            $survey->saw_score = $sawScore;
            return $survey;
        })->sortByDesc('saw_score')->values();

        // Get all questions with sections - filter responses by periode
        $sections = SurveySection::where('is_active', true)
                                ->with(['allQuestions' => function($q) use ($periodId) {
                                    $q->where('is_active', true)
                                      ->with(['responses' => function($r) use ($periodId) {
                                          if ($periodId) $r->where('period_id', $periodId);
                                      }])
                                      ->orderBy('order_index');
                                }])
                                ->orderBy('order_index')
                                ->get();
        
        // Filter sections yang memiliki questions aktif dan rename relation
        $sections = $sections->filter(function($section) {
            return $section->allQuestions->count() > 0;
        })->map(function($section) {
            $section->questions = $section->allQuestions;
            unset($section->allQuestions);
            return $section;
        })->values();

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

        // Label periode untuk judul PDF
        $periodLabel = $selectedPeriod
            ? $selectedPeriod->period_name . ' ' . $selectedPeriod->year
            : 'Semua Periode';

        // Prepare data for PDF
        $data = [
            'title' => 'LAPORAN DATA SURVEI',
            'generated_at' => now()->format('d F Y H:i:s'),
            'total_responses' => $surveysWithSAW->count(),
            'period_start' => $surveys->min('created_at')?->format('d F Y') ?? '-',
            'period_end' => $surveys->max('created_at')?->format('d F Y') ?? '-',
            'period_label' => $periodLabel,
            'selected_period' => $selectedPeriod,
            
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
        
        $periodSuffix = $selectedPeriod
            ? '_' . $selectedPeriod->year . '_' . str_replace(' ', '-', $selectedPeriod->period_name)
            : '';
        $filename = 'Laporan_Survey' . $periodSuffix . '_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * EXPORT PDF — PERHITUNGAN SAW PER RESPONDEN
     *
     * Menampilkan detail rumus SAW lengkap untuk setiap responden:
     *   wj    = Wj / ΣWj
     *   xij   = rata-rata jawaban responden i pada kriteria j
     *   rij   = xij / scale_max  (benefit)  |  scale_min / xij  (cost)
     *   Vij   = wj × rij
     *   Vi    = Σ Vij
     */
    public function exportPDFRespondent(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;
 
        $periodId       = $request->get('period_id');
        $selectedPeriod = $periodId ? SurveyPeriod::find($periodId) : null;
 
        // ── 1. Ambil semua pertanyaan SAW ──────────────────────────────────────
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
            ->where('question_type', 'linear_scale')
            ->whereNotNull('criteria_id')
            ->get();
 
        // ── 2. Kelompokkan per kriteria ────────────────────────────────────────
        $criteriaGroups = $sawQuestions->groupBy('criteria_name');
 
        // ── 3. Hitung total bobot ──────────────────────────────────────────────
        $totalWeight = 0;
        foreach ($criteriaGroups as $cName => $cQuestions) {
            $totalWeight += $cQuestions->first()->criteria_weight ?? 0;
        }
 
        // ── 4. Bangun konfigurasi kriteria (untuk cover & tabel header) ────────
        $criteriaConfig = [];
        foreach ($criteriaGroups as $cName => $cQuestions) {
            $firstQ = $cQuestions->first();
            $wj     = $firstQ->criteria_weight ?? 0;
            $settings = $firstQ->settings ?? [];
            $criteriaConfig[] = [
                'name'  => $cName,
                'type'  => $firstQ->criteria_type ?? 'benefit',
                'weight'=> $wj,
                'wNorm' => $totalWeight > 0 ? round($wj / $totalWeight, 4) : 0,
                'sMax'  => $settings['scale_max'] ?? 5,
                'sMin'  => $settings['scale_min'] ?? 1,
            ];
        }
 
        // ── 5. Ambil surveys (filter periode) ─────────────────────────────────
        $questionIds  = $sawQuestions->pluck('id');
        $surveysQuery = Survey::with(['responses' => function ($q) use ($questionIds, $periodId) {
            $q->whereIn('question_id', $questionIds);
            if ($periodId) $q->where('period_id', $periodId);
        }]);
 
        if ($selectedPeriod) {
            $surveysQuery->whereHas('responses', function ($q) use ($periodId) {
                $q->where('period_id', $periodId);
            });
        }
 
        $surveys = $surveysQuery->orderBy('created_at', 'desc')->get();
 
        // ── 6. Hitung SAW per responden ────────────────────────────────────────
        $respondentRows = [];
 
        foreach ($surveys as $survey) {
            // Jawaban responden ini (keyed by question_id)
            $surveyResponses = $survey->responses->keyBy('question_id');
 
            // Kumpulkan kriteria yang dijawab
            $answeredCriteria = [];
            foreach ($criteriaGroups as $cName => $cQuestions) {
                $firstQ = $cQuestions->first();
                $scores = [];
                foreach ($cQuestions as $cQ) {
                    $resp = $surveyResponses->get((string) $cQ->id);
                    if ($resp) {
                        $scores[] = (float) $resp->answer;
                    }
                }
                if (empty($scores)) continue;
 
                $settings = $firstQ->settings ?? [];
                $answeredCriteria[] = [
                    'name'     => $cName,
                    'weight'   => $firstQ->criteria_weight ?? 0,
                    'avgScore' => array_sum($scores) / count($scores),
                    'settings' => $settings,
                    'type'     => $firstQ->criteria_type ?? 'benefit',
                ];
            }
 
            if (empty($answeredCriteria)) continue;
 
            // Total bobot hanya dari kriteria yang dijawab
            $answeredTotalWeight = array_sum(array_column($answeredCriteria, 'weight'));
            if ($answeredTotalWeight == 0) continue;
 
            // Hitung tiap kriteria dan total Vi
            $details = [];
            $totalVi = 0;
 
            foreach ($answeredCriteria as $ac) {
                $wNorm    = $ac['weight'] / $answeredTotalWeight;
                $sMax     = $ac['settings']['scale_max'] ?? 5;
                $sMin     = $ac['settings']['scale_min'] ?? 1;
                $xij      = round($ac['avgScore'], 4);
 
                if ($ac['type'] === 'benefit') {
                    $rij = $sMax > 0 ? $xij / $sMax : 0;
                } else {
                    $rij = $xij > 0 ? $sMin / $xij : 0;
                }
 
                $rij  = max(0, min(1, $rij));
                $vij  = $wNorm * $rij;
                $totalVi += $vij;
 
                $details[] = [
                    'name'  => $ac['name'],
                    'type'  => $ac['type'],
                    'xij'   => $xij,
                    'wj'    => $ac['weight'],
                    'wNorm' => round($wNorm, 4),
                    'sMax'  => $sMax,
                    'sMin'  => $sMin,
                    'rij'   => round($rij, 4),
                    'vij'   => round($vij, 4),
                ];
            }
 
            // Interpretasi
            $vi = round($totalVi, 4);
            if ($vi >= 0.9)      $interpretation = 'Sangat Baik';
            elseif ($vi >= 0.8)  $interpretation = 'Baik';
            elseif ($vi >= 0.6)  $interpretation = 'Cukup';
            elseif ($vi >= 0.4)  $interpretation = 'Kurang';
            else                  $interpretation = 'Sangat Kurang';
 
            // Ambil data identitas responden
            $responses = $survey->responses->keyBy('question_id');
            $nama     = optional($survey->responses->where('question_id', 'nama')->first())->answer ?? 'Responden #' . $survey->id;
            $email    = optional($survey->responses->where('question_id', 'email')->first())->answer ?? '-';
            $gender   = optional($survey->responses->where('question_id', 'jenis_kelamin')->first())->answer ?? '-';
            $umur     = optional($survey->responses->where('question_id', 'umur')->first())->answer ?? '-';
            $pendidik = optional($survey->responses->where('question_id', 'jenis_pendidikan')->first())->answer ?? '-';
            $pekerjaan= optional($survey->responses->where('question_id', 'pekerjaan')->first())->answer ?? '-';
 
            $respondentRows[] = [
                'survey_id'        => $survey->id,
                'nama'             => $nama,
                'email'            => $email,
                'jenis_kelamin'    => $gender,
                'umur'             => $umur,
                'jenis_pendidikan' => $pendidik,
                'pekerjaan'        => $pekerjaan,
                'details'          => $details,
                'vi'               => $vi,
                'interpretation'   => $interpretation,
            ];
        }
 
        // Urutkan dari Vi tertinggi
        usort($respondentRows, fn($a, $b) => $b['vi'] <=> $a['vi']);
 
        // ── 7. Susun data dan generate PDF ────────────────────────────────────
        $periodLabel = $selectedPeriod
            ? $selectedPeriod->period_name . ' ' . $selectedPeriod->year
            : 'Semua Periode';
 
        $data = [
            'title'          => 'LAPORAN PERHITUNGAN SAW PER RESPONDEN',
            'periodLabel'    => $periodLabel,
            'selectedPeriod' => $selectedPeriod,
            'generatedAt'    => now()->format('d F Y H:i:s'),
            'criteriaConfig' => $criteriaConfig,
            'totalCriteria'  => count($criteriaConfig),
            'respondentRows' => $respondentRows,
        ];
 
        $pdf = Pdf::loadView('admin.hasil-survey.export-pdf-respondent', $data);
        $pdf->setPaper('a4', 'landscape');   // landscape karena kolom banyak
 
        $periodSuffix = $selectedPeriod
            ? '_' . $selectedPeriod->year . '_' . str_replace(' ', '-', $selectedPeriod->period_name)
            : '';
        $filename = 'SAW_Per_Responden' . $periodSuffix . '_' . now()->format('Y-m-d_His') . '.pdf';
 
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

    public function exportLaporan(Request $request)
{
    $authCheck = $this->checkAdminAuth();
    if ($authCheck) return $authCheck;
 
    $periodId = $request->get('period_id');
 
    // ── Periode ──────────────────────────────────────────────────────────────
    $selectedPeriod = $periodId
        ? SurveyPeriod::find($periodId)
        : (SurveyPeriod::where('is_active', true)->first()
           ?? SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->first());
 
    // ── SAW Results ──────────────────────────────────────────────────────────
    if ($selectedPeriod) {
        $sawResults = $this->calculateSAWForPeriod($selectedPeriod->id);
    } else {
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
            ->where('question_type', 'linear_scale')
            ->whereNotNull('criteria_id')
            ->with('responses')
            ->get();
        $sawResults = $this->calculateAggregateSAWResults($sawQuestions);
    }
 
    $criteriaResults = $sawResults->map(function ($r) {
        if (is_array($r)) return $r;
        return [
            'criteria'          => $r->criteria_name,
            'score'             => $r->average_score,
            'weight_normalized' => $r->weight_normalized,
            'normalized'        => $r->normalized_score,
            'weighted_score'    => $r->weighted_score,
            'interpretation'    => $r->interpretation,
            'criteria_type'     => $r->criteria_type,
        ];
    });
 
    $totalVi = $criteriaResults->sum('weighted_score');
 
    // ── Total Responden ───────────────────────────────────────────────────────
    $total_responses = $selectedPeriod
        ? DB::table('survey_responses')
            ->where('period_id', $selectedPeriod->id)
            ->distinct('survey_id')
            ->count('survey_id')
        : Survey::count();
 
    // ── Sections (DB saja, default section diambil di view) ───────────────────
    $sections = SurveySection::where('is_active', true)
        ->with(['allQuestions' => function ($q) {
            $q->where('is_active', true)->orderBy('order_index');
        }])
        ->orderBy('order_index')
        ->get()
        ->filter(fn($s) => $s->allQuestions->count() > 0)
        ->map(function ($s) {
            $s->questions = $s->allQuestions;
            return $s;
        })
        ->values();
 
    // ── Data ke view ──────────────────────────────────────────────────────────
    $period_label = $selectedPeriod
        ? $selectedPeriod->period_name . ' ' . $selectedPeriod->year
        : 'Semua Periode';
 
    $tahun = $selectedPeriod ? $selectedPeriod->year : date('Y');
 
    $data = [
        'period_label'    => $period_label,
        'tahun'           => $tahun,
        'generated_at'    => now()->format('d F Y H:i:s'),
        'total_responses' => $total_responses,
        'selected_period' => $selectedPeriod,
        'periodId'        => $periodId,
        'criteriaResults' => $criteriaResults,
        'totalVi'         => $totalVi,
        'sections'        => $sections,
    ];
 
    $pdf = Pdf::loadView('admin.hasil-survey.laporan-kominfo', $data);
    $pdf->setPaper('a4', 'portrait');
 
    $suffix   = $selectedPeriod
        ? '_' . $selectedPeriod->year . '_' . str_replace(' ', '-', $selectedPeriod->period_name)
        : '';
    $filename = 'Laporan_SKM_Kominfo' . $suffix . '_' . now()->format('Y-m-d') . '.pdf';
 
    return $pdf->download($filename);
}
 
/**
     * EXPORT PDF — LAPORAN SAW PER RESPONDEN (RINGKASAN)
     *
     * Kolom: No | Nama | Email | Jenis Kelamin | Umur | Pendidikan | Pekerjaan | Total Skor (Vi) | Keterangan
     * Diurutkan dari skor tertinggi.
     * Dilengkapi area tanda tangan di bagian bawah.
     *
     * Route: GET /admin/hasil-survey/export-pdf-saw-respondent
     * Name:  admin.hasil-survey.export-pdf-saw-respondent
     */
    public function exportPDFSAWRespondent(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;
 
        $periodId       = $request->get('period_id');
        $selectedPeriod = $periodId ? SurveyPeriod::find($periodId) : null;
 
        // ── 1. Ambil semua pertanyaan SAW ──────────────────────────────────────
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
            ->where('question_type', 'linear_scale')
            ->whereNotNull('criteria_id')
            ->get();
 
        // ── 2. Kelompokkan per kriteria ────────────────────────────────────────
        $criteriaGroups = $sawQuestions->groupBy('criteria_name');
 
        // ── 3. Hitung total bobot ──────────────────────────────────────────────
        $totalWeight = 0;
        foreach ($criteriaGroups as $cName => $cQuestions) {
            $totalWeight += $cQuestions->first()->criteria_weight ?? 0;
        }
 
        // ── 4. Susun criteriaConfig ────────────────────────────────────────────
        $criteriaConfig = [];
        foreach ($criteriaGroups as $cName => $cQuestions) {
            $first = $cQuestions->first();
            $criteriaConfig[] = [
                'name'     => $cName,
                'weight'   => $first->criteria_weight ?? 0,
                'wNorm'    => $totalWeight > 0 ? (($first->criteria_weight ?? 0) / $totalWeight) : 0,
                'type'     => $first->criteria_type  ?? 'benefit',
                'scaleMax' => $first->settings['scale_max'] ?? 5,
                'scaleMin' => $first->settings['scale_min'] ?? 1,
                'questions'=> $cQuestions,
            ];
        }
 
        // ── 5. Ambil surveys ───────────────────────────────────────────────────
        if ($selectedPeriod) {
            $surveys = Survey::whereHas('responses', function ($q) use ($selectedPeriod) {
                $q->where('period_id', $selectedPeriod->id);
            })->get();
        } else {
            $surveys = Survey::all();
        }
 
        // ── 6. Hitung Vi per responden ─────────────────────────────────────────
        $respondentRows = [];
 
        foreach ($surveys as $survey) {
            // Ambil info identitas dari responses
            $responses = SurveyResponse::where('survey_id', $survey->id)
                ->when($selectedPeriod, fn($q) => $q->where('period_id', $selectedPeriod->id))
                ->get()
                ->keyBy('question_id');
 
            $nama      = $responses->get('nama')?->answer
                      ?? $responses->get('name')?->answer
                      ?? 'Responden #' . $survey->id;
            $email     = $responses->get('email')?->answer          ?? '-';
            $gender    = $responses->get('jenis_kelamin')?->answer   ?? '-';
            $umur      = $responses->get('umur')?->answer            ?? '-';
            $pendidik  = $responses->get('jenis_pendidikan')?->answer ?? '-';
            $pekerjaan = $responses->get('pekerjaan')?->answer       ?? '-';
 
            // Hitung Vi
            $sawResponses = SurveyResponse::where('survey_id', $survey->id)
                ->when($selectedPeriod, fn($q) => $q->where('period_id', $selectedPeriod->id))
                ->whereIn('question_id', $sawQuestions->pluck('id'))
                ->get()
                ->keyBy('question_id');
 
            if ($sawResponses->isEmpty()) {
                continue; // skip responden tanpa jawaban SAW
            }
 
            $answeredCriteria = [];
            foreach ($criteriaConfig as $cc) {
                $scores = [];
                foreach ($cc['questions'] as $q) {
                    $ans = $sawResponses->get((string) $q->id);
                    if ($ans) $scores[] = (float) $ans->answer;
                }
                if (empty($scores)) continue;
 
                $answeredCriteria[] = [
                    'weight'   => $cc['weight'],
                    'avgScore' => array_sum($scores) / count($scores),
                    'type'     => $cc['type'],
                    'scaleMax' => $cc['scaleMax'],
                    'scaleMin' => $cc['scaleMin'],
                ];
            }
 
            if (empty($answeredCriteria)) continue;
 
            $answeredTotalWeight = array_sum(array_column($answeredCriteria, 'weight'));
            if ($answeredTotalWeight == 0) continue;
 
            $vi = 0;
            foreach ($answeredCriteria as $ac) {
                $wNorm = $ac['weight'] / $answeredTotalWeight;
                if ($ac['type'] === 'benefit') {
                    $rij = $ac['scaleMax'] > 0 ? ($ac['avgScore'] / $ac['scaleMax']) : 0;
                } else {
                    $rij = $ac['avgScore']  > 0 ? ($ac['scaleMin'] / $ac['avgScore']) : 0;
                }
                $rij  = max(0, min(1, $rij));
                $vi  += $wNorm * $rij;
            }
 
            $vi             = round($vi, 4);
            $interpretation = $this->getSAWInterpretation($vi / 1); // reuse existing method
 
            $respondentRows[] = [
                'survey_id'        => $survey->id,
                'nama'             => $nama,
                'email'            => $email,
                'jenis_kelamin'    => $gender,
                'umur'             => $umur,
                'jenis_pendidikan' => $pendidik,
                'pekerjaan'        => $pekerjaan,
                'vi'               => $vi,
                'interpretation'   => $interpretation,
            ];
        }
 
        // Urutkan dari Vi tertinggi
        usort($respondentRows, fn($a, $b) => $b['vi'] <=> $a['vi']);
 
        // ── 7. Susun data dan generate PDF ────────────────────────────────────
        $periodLabel = $selectedPeriod
            ? $selectedPeriod->period_name . ' ' . $selectedPeriod->year
            : 'Semua Periode';
 
        $data = [
            'title'          => 'LAPORAN SAW PER RESPONDEN',
            'periodLabel'    => $periodLabel,
            'selectedPeriod' => $selectedPeriod,
            'generatedAt'    => now()->format('d F Y H:i:s'),
            'criteriaConfig' => $criteriaConfig,
            'totalCriteria'  => count($criteriaConfig),
            'respondentRows' => $respondentRows,
        ];
 
        $pdf = Pdf::loadView('admin.hasil-survey.export-pdf-saw-respondent', $data);
        $pdf->setPaper('a4', 'landscape');
 
        $periodSuffix = $selectedPeriod
            ? '_' . $selectedPeriod->year . '_' . str_replace(' ', '-', $selectedPeriod->period_name)
            : '';
        $filename = 'SAW_Responden_Ringkasan' . $periodSuffix . '_' . now()->format('Y-m-d_His') . '.pdf';
 
        return $pdf->download($filename);
    }
}