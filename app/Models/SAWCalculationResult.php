<?php
// app/Models/SAWCalculationResult.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SAWCalculationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'period_id',
        'criteria_name',
        'criteria_type',
        'criteria_weight',
        'average_score',
        'normalized_score',
        'weight_normalized',
        'weighted_score',
        'total_responses',
        'questions_count',
        'calculated_at',
    ];

    protected $casts = [
        'criteria_weight' => 'decimal:2',
        'average_score' => 'decimal:2',
        'normalized_score' => 'decimal:4',
        'weight_normalized' => 'decimal:4',
        'weighted_score' => 'decimal:4',
        'calculated_at' => 'datetime',
    ];

    /**
     * Relasi ke Period
     */
    public function period()
    {
        return $this->belongsTo(SurveyPeriod::class, 'period_id');
    }

    /**
     * Helper: Interpretasi kualitatif
     */
    public function getInterpretationAttribute()
    {
        $score = $this->normalized_score;
        
        if ($score >= 0.9) return 'Sangat Baik';
        if ($score >= 0.8) return 'Baik';
        if ($score >= 0.6) return 'Cukup';
        if ($score >= 0.4) return 'Kurang';
        return 'Sangat Kurang';
    }
}