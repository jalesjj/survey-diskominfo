<?php
// app/Models/SurveyResponse.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $fillable = [
        'survey_id',
        'period_id', 
        'question_id',
        'user_id',
        'answer_value',
        'answer_text',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    /**
     * Relasi ke Period
     */
    public function period()
    {
        return $this->belongsTo(SurveyPeriod::class, 'period_id');
    }

    /**
     * Relasi existing
     */
    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}