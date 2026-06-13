<?php
// app/Models/SurveyQuestion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'question_text',
        'question_description',
        'question_type',
        'options',
        'settings',
        'order_index',
        'is_required',
        'is_active',
        'enable_saw',
        'criteria_id',   // <-- ganti dari criteria_name/weight/type
    ];

    protected $casts = [
        'options'    => 'array',
        'settings'   => 'array',
        'is_required'=> 'boolean',
        'is_active'  => 'boolean',
        'enable_saw' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(SurveySection::class);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'question_id');
    }

    /**
     * Relasi ke tabel criterias
     */
    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }

    // ─── Helper accessor agar kode lama tidak perlu banyak diubah ────────────

    public function getCriteriaNameAttribute(): ?string
    {
        return $this->criteria?->criteria_name;
    }

    public function getCriteriaWeightAttribute(): ?float
    {
        return $this->criteria?->criteria_weight;
    }

    public function getCriteriaTypeAttribute(): ?string
    {
        return $this->criteria?->criteria_type;
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function getQuestionTypeLabel(): string
    {
        $types = [
            'short_text'      => 'Jawaban Singkat',
            'long_text'       => 'Paragraf',
            'multiple_choice' => 'Pilihan Ganda',
            'checkbox'        => 'Kotak Centang',
            'dropdown'        => 'Drop-down',
            'file_upload'     => 'Upload File',
            'linear_scale'    => 'Skala Linier',
        ];

        return $types[$this->question_type] ?? $this->question_type;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}