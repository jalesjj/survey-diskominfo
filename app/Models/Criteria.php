<?php
// app/Models/Criteria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'criteria_name',
        'criteria_weight',
        'criteria_type',
    ];

    protected $casts = [
        'criteria_weight' => 'float',
    ];

    /**
     * Relasi ke pertanyaan yang memakai kriteria ini
     */
    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class, 'criteria_id');
    }

    /**
     * Jumlah pertanyaan yang memakai kriteria ini
     */
    public function getUsedCountAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Apakah kriteria ini masih dipakai pertanyaan?
     */
    public function isInUse(): bool
    {
        return $this->questions()->exists();
    }

    public function getCriteriaTypeLabelAttribute(): string
    {
        return $this->criteria_type === 'benefit' ? 'Benefit' : 'Cost';
    }
}