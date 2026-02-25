<?php
// app/Http/Controllers/SurveyResultController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Support\Collection;

class SurveyResultController extends Controller
{
    private function checkAdminAuth()
    {
        if (!session('admin_user')) {
            return redirect()->route('admin.login')
                           ->with('error', 'Silakan login terlebih dahulu.');
        }
        return null;
    }

    public function index()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Ambil semua survey yang sudah submit
        $surveys = Survey::with(['responses.question'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('admin.survey-results.index', compact('surveys'));
    }

    public function show($surveyId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $survey = Survey::with(['responses.question'])->findOrFail($surveyId);
        
        // Get only linear scale questions with SAW enabled
        $sawQuestions = SurveyQuestion::where('question_type', 'linear_scale')
                                    ->where('enable_saw', true)
                                    ->whereNotNull('criteria_name')
                                    ->with('responses')
                                    ->get();

        if ($sawQuestions->isEmpty()) {
            return view('admin.survey-results.show', [
                'survey' => $survey,
                'sawResults' => collect(),
                'hasSAW' => false,
                'message' => 'Survey ini tidak menggunakan perhitungan SAW'
            ]);
        }

        // Calculate SAW results
        $sawResults = $this->calculateSAWResults($survey, $sawQuestions);

        return view('admin.survey-results.show', compact('survey', 'sawResults') + ['hasSAW' => true]);
    }

    private function calculateSAWResults($survey, $sawQuestions)
    {
        $results = collect();
        $criteriaData = collect();

        // Group questions by criteria
        $questionsByCriteria = $sawQuestions->groupBy('criteria_name');

        foreach ($questionsByCriteria as $criteriaName => $questions) {
            $criteriaWeight = $questions->first()->criteria_weight;
            $criteriaType = $questions->first()->criteria_type;
            
            // Calculate aggregated score for this criteria (average of all sub-criteria)
            $subCriteriaScores = [];
            $subCriteriaData = [];

            foreach ($questions as $question) {
                $response = $survey->responses()->where('question_id', $question->id)->first();
                $score = $response ? (float) $response->answer : 0;
                
                $scaleMax = $question->settings['scale_max'] ?? 5;
                $normalized = $scaleMax > 0 ? ($score / $scaleMax) : 0;

                $subCriteriaScores[] = $score;
                $subCriteriaData[] = [
                    'sub_criteria' => $question->question_text,
                    'score' => $score,
                    'normalized' => $normalized,
                    'scale_max' => $scaleMax
                ];
            }

            // Calculate average score for criteria
            $criteriaScore = count($subCriteriaScores) > 0 ? array_sum($subCriteriaScores) / count($subCriteriaScores) : 0;
            
            $criteriaData->push([
                'name' => $criteriaName,
                'weight' => $criteriaWeight,
                'type' => $criteriaType,
                'score' => $criteriaScore,
                'sub_criteria' => $subCriteriaData
            ]);
        }

        // Normalize weights
        $totalWeight = $criteriaData->sum('weight');
        $criteriaData = $criteriaData->map(function ($criteria) use ($totalWeight) {
            $criteria['weight_normalized'] = $totalWeight > 0 ? ($criteria['weight'] / $totalWeight) : 0;
            return $criteria;
        });

        // Calculate SAW normalization and final scores
        foreach ($criteriaData as &$criteria) {
            foreach ($criteria['sub_criteria'] as &$subCriteria) {
                // SAW Normalization based on criteria type
                if ($criteria['type'] === 'benefit') {
                    // For benefit: normalize = score / max_score_in_criteria
                    $maxScore = $criteriaData->where('name', $criteria['name'])->first()['score'];
                    $subCriteria['saw_normalized'] = $maxScore > 0 ? ($subCriteria['score'] / $maxScore) : 0;
                } else {
                    // For cost: normalize = min_score_in_criteria / score
                    $minScore = max(0.1, $criteriaData->where('name', $criteria['name'])->first()['score']);
                    $subCriteria['saw_normalized'] = $subCriteria['score'] > 0 ? ($minScore / $subCriteria['score']) : 0;
                }
                
                // Calculate weighted score
                $subCriteria['weight_normalized'] = $criteria['weight_normalized'];
                $subCriteria['weighted_score'] = $subCriteria['saw_normalized'] * $criteria['weight_normalized'];
                
                // Add interpretation
                $subCriteria['interpretation'] = $this->getScoreInterpretation($subCriteria['saw_normalized']);
                
                $results->push([
                    'criteria' => $criteria['name'],
                    'sub_criteria' => $subCriteria['sub_criteria'],
                    'score' => $subCriteria['score'],
                    'weight_normalized' => $subCriteria['weight_normalized'],
                    'normalized' => $subCriteria['saw_normalized'],
                    'weighted_score' => $subCriteria['weighted_score'],
                    'interpretation' => $subCriteria['interpretation']
                ]);
            }
        }

        return $results->sortBy('criteria');
    }

    private function getScoreInterpretation($normalizedScore)
    {
        if ($normalizedScore >= 0.8) return 'Sangat Baik';
        if ($normalizedScore >= 0.6) return 'Baik';
        if ($normalizedScore >= 0.4) return 'Cukup';
        if ($normalizedScore >= 0.2) return 'Kurang';
        return 'Sangat Kurang';
    }

    public function exportResults(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // TODO: Implement export functionality (Excel, PDF)
        return response()->json(['message' => 'Export feature will be implemented']);
    }

    public function ranking()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Get all surveys and calculate ranking
        $surveys = Survey::with(['responses.question'])->get();
        $rankings = collect();

        foreach ($surveys as $survey) {
            $sawQuestions = SurveyQuestion::where('question_type', 'linear_scale')
                                        ->where('enable_saw', true)
                                        ->whereNotNull('criteria_name')
                                        ->get();

            if ($sawQuestions->isNotEmpty()) {
                $sawResults = $this->calculateSAWResults($survey, $sawQuestions);
                $totalScore = $sawResults->sum('weighted_score');
                
                $rankings->push([
                    'survey_id' => $survey->id,
                    'survey_date' => $survey->created_at,
                    'total_score' => round($totalScore, 4),
                    'rank' => 0 // Will be calculated after sorting
                ]);
            }
        }

        // Sort by total_score descending and assign ranks
        $rankings = $rankings->sortByDesc('total_score')->values();
        $rankings = $rankings->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        return view('admin.survey-results.ranking', compact('rankings'));
    }
}
