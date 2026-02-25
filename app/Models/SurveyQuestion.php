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
        'question_description', // Field baru untuk deskripsi pertanyaan
        'question_type',
        'options',
        'settings',
        'order_index',
        'is_required',
        'is_active',
        'enable_saw',
        'criteria_name',
        'criteria_weight',
        'criteria_type'
    ];

    protected $casts = [
        'options' => 'array',
        'settings' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'enable_saw' => 'boolean'
    ];

    public function section()
    {
        return $this->belongsTo(SurveySection::class);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'question_id');
    }

    public function getQuestionTypeLabel()
    {
        $types = [
            'short_text' => 'Jawaban Singkat',
            'long_text' => 'Paragraf',
            'multiple_choice' => 'Pilihan Ganda',
            'checkbox' => 'Kotak Centang',
            'dropdown' => 'Drop-down',
            'file_upload' => 'Upload File',
            'linear_scale' => 'Skala Linier'
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
