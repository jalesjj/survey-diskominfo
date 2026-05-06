<?php
// app/Models/SurveyPeriod.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'period_name',
        'year',
        'start_date',
        'end_date',
        'status',
        'is_active',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'year' => 'integer'
    ];

    /**
     * Get active period
     */
    public static function getActivePeriod()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Check if system is locked (has active period)
     */
    public static function isLocked()
    {
        return self::where('is_active', true)->exists();
    }

    /**
     * Relasi ke responses
     */
    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'period_id');
    }

    /**
     * Relasi ke SAW calculation results
     */
    public function sawResults()
    {
        return $this->hasMany(SAWCalculationResult::class, 'period_id');
    }

    /**
     * Scope active periods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }
}