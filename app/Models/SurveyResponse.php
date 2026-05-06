<?php
// app/Models/SurveyResponse.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\SurveyDefaults;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'period_id', 
        'question_id',
        'answer',
        'answer_data',
    ];

    protected $casts = [
        'answer_data' => 'array',
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
     * Relasi ke question - support both integer (database) and string (default questions)
     * NOTE: Ini bukan relasi Eloquent standard karena question_id bisa string atau integer
     */
    public function question()
    {
        // Cek apakah question_id adalah string (default question)
        if (is_string($this->question_id) && !is_numeric($this->question_id)) {
            // Ambil dari default questions
            $defaultQuestion = SurveyDefaults::getDefaultQuestionById($this->question_id);
            return $defaultQuestion;
        }
        
        // Jika integer, gunakan relasi normal
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    /**
     * Get question object (works for both database and default questions)
     */
    public function getQuestionAttribute()
    {
        // Cek apakah sudah ada di relations cache
        if (array_key_exists('question', $this->relations)) {
            return $this->relations['question'];
        }

        // Cek apakah question_id adalah string (default question)
        if (is_string($this->attributes['question_id']) && !is_numeric($this->attributes['question_id'])) {
            // Ambil dari default questions
            $defaultQuestion = SurveyDefaults::getDefaultQuestionById($this->attributes['question_id']);
            
            // Cache hasilnya
            $this->setRelation('question', $defaultQuestion);
            
            return $defaultQuestion;
        }
        
        // Jika integer, load dari database
        if (!isset($this->relations['question'])) {
            $this->load('questionFromDb');
            $this->setRelation('question', $this->relations['questionFromDb']);
        }
        
        return $this->relations['question'];
    }

    /**
     * Relasi database question (hanya untuk integer IDs)
     */
    public function questionFromDb()
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }
}