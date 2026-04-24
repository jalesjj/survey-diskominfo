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
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Survey (kalau ada tabel surveys)
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Relasi ke Responses
     */
    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'period_id');
    }

    /**
     * Relasi ke SAW Results
     */
    public function sawResults()
    {
        return $this->hasMany(SAWCalculationResult::class, 'period_id');
    }

    /**
     * Scope: Periode aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Periode closed
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Helper: Cek apakah periode ini sudah punya responden
     */
    public function hasResponses()
    {
        return $this->responses()->exists();
    }

    /**
     * Helper: Total responden unik di periode ini
     */
    public function getTotalRespondentsAttribute()
    {
        return $this->responses()->distinct('user_id')->count('user_id');
    }
}