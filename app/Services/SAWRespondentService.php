<?php
// app/Services/SAWRespondentService.php

namespace App\Services;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;

class SAWRespondentService
{
    /**
     * Hitung SAW score untuk satu survey (satu responden).
     *
     * Normalisasi menggunakan scale_max / scale_min dari settings pertanyaan,
     * sama persis dengan logika calculateAggregateSAWResults di SurveyResultController.
     *
     * @param  Survey  $survey
     * @return array   ['score' => float, 'has_saw' => bool, 'interpretation' => string]
     */
    public function calculateForSurvey(Survey $survey): array
    {
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
            ->where('question_type', 'linear_scale')
            ->whereNotNull('criteria_name')
            ->get();

        if ($sawQuestions->isEmpty()) {
            return ['score' => 0, 'has_saw' => false, 'interpretation' => '-'];
        }

        $totalWeight = $sawQuestions->sum('criteria_weight');

        if ($totalWeight == 0) {
            return ['score' => 0, 'has_saw' => false, 'interpretation' => '-'];
        }

        $surveyResponses = SurveyResponse::where('survey_id', $survey->id)
            ->whereIn('question_id', $sawQuestions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        if ($surveyResponses->isEmpty()) {
            return ['score' => 0, 'has_saw' => false, 'interpretation' => '-'];
        }

        $criteriaGroups = $sawQuestions->groupBy('criteria_name');
        $totalVi        = 0;

        foreach ($criteriaGroups as $criteriaName => $questions) {
            $firstQuestion    = $questions->first();
            $criteriaWeight   = $firstQuestion->criteria_weight ?? 0;
            $weightNormalized = $criteriaWeight / $totalWeight;

            $scores = [];
            foreach ($questions as $question) {
                $response = $surveyResponses->get($question->id);
                if ($response) {
                    $scores[] = (float) $response->answer;
                }
            }

            if (empty($scores)) {
                continue;
            }

            $avgScore = array_sum($scores) / count($scores);

            // Ambil skala dari settings pertanyaan — SAMA dengan per-kriteria
            $settings = $firstQuestion->settings ?? [];
            $scaleMax = $settings['scale_max'] ?? 5;
            $scaleMin = $settings['scale_min'] ?? 1;

            $criteriaType = $firstQuestion->criteria_type ?? 'benefit';

            if ($criteriaType === 'benefit') {
                $normalized = $scaleMax > 0 ? ($avgScore / $scaleMax) : 0;
            } else {
                $normalized = $avgScore > 0 ? ($scaleMin / $avgScore) : 0;
            }

            $normalized = max(0, min(1, $normalized));
            $totalVi   += $weightNormalized * $normalized;
        }

        return [
            'score'          => round($totalVi, 4),
            'has_saw'        => true,
            'interpretation' => $this->interpret($totalVi),
        ];
    }

    /**
     * Hitung SAW score untuk banyak survey sekaligus (efisien, hanya 1 query responses).
     *
     * @param  \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator  $surveys
     * @return array  keyed by survey->id => ['score', 'has_saw', 'interpretation']
     */
    public function calculateForSurveys($surveys): array
    {
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
            ->where('question_type', 'linear_scale')
            ->whereNotNull('criteria_name')
            ->get();

        $empty = fn($s) => [$s->id => ['score' => 0, 'has_saw' => false, 'interpretation' => '-']];

        if ($sawQuestions->isEmpty()) {
            return collect($surveys->items())->mapWithKeys($empty)->toArray();
        }

        $totalWeight = $sawQuestions->sum('criteria_weight');

        if ($totalWeight == 0) {
            return collect($surveys->items())->mapWithKeys($empty)->toArray();
        }

        // Gunakan ->items() agar kompatibel dengan paginator maupun collection biasa
        $items          = method_exists($surveys, 'items') ? collect($surveys->items()) : collect($surveys);
        $surveyIds      = $items->pluck('id');
        $questionIds    = $sawQuestions->pluck('id');
        $criteriaGroups = $sawQuestions->groupBy('criteria_name');

        // 1 query untuk semua jawaban SAW semua survey di halaman ini
        $allResponses = SurveyResponse::whereIn('survey_id', $surveyIds)
            ->whereIn('question_id', $questionIds)
            ->get()
            ->groupBy('survey_id');

        $results = [];

        foreach ($items as $survey) {
            $surveyResponses = $allResponses->get($survey->id, collect())->keyBy('question_id');

            if ($surveyResponses->isEmpty()) {
                $results[$survey->id] = ['score' => 0, 'has_saw' => false, 'interpretation' => '-'];
                continue;
            }

            $totalVi = 0;

            foreach ($criteriaGroups as $criteriaName => $questions) {
                $firstQuestion    = $questions->first();
                $criteriaWeight   = $firstQuestion->criteria_weight ?? 0;
                $weightNormalized = $criteriaWeight / $totalWeight;

                $scores = [];
                foreach ($questions as $question) {
                    $response = $surveyResponses->get($question->id);
                    if ($response) {
                        $scores[] = (float) $response->answer;
                    }
                }

                if (empty($scores)) {
                    continue;
                }

                $avgScore = array_sum($scores) / count($scores);

                $settings = $firstQuestion->settings ?? [];
                $scaleMax = $settings['scale_max'] ?? 5;
                $scaleMin = $settings['scale_min'] ?? 1;

                $criteriaType = $firstQuestion->criteria_type ?? 'benefit';

                if ($criteriaType === 'benefit') {
                    $normalized = $scaleMax > 0 ? ($avgScore / $scaleMax) : 0;
                } else {
                    $normalized = $avgScore > 0 ? ($scaleMin / $avgScore) : 0;
                }

                $normalized = max(0, min(1, $normalized));
                $totalVi   += $weightNormalized * $normalized;
            }

            $results[$survey->id] = [
                'score'          => round($totalVi, 4),
                'has_saw'        => true,
                'interpretation' => $this->interpret($totalVi),
            ];
        }

        return $results;
    }

    /**
     * Interpretasi kualitatif (sama dengan SurveyResultController).
     */
    private function interpret(float $score): string
    {
        if ($score >= 0.9) return 'Sangat Baik';
        if ($score >= 0.8) return 'Baik';
        if ($score >= 0.6) return 'Cukup';
        if ($score >= 0.4) return 'Kurang';
        return 'Sangat Kurang';
    }
}