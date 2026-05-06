<?php
// app/Helpers/SurveyDefaults.php
namespace App\Helpers;

class SurveyDefaults
{
    /**
     * Get default section sebagai object
     */
    public static function getDefaultSection()
    {
        $data = config('survey_defaults.default_section');
        return (object) $data;
    }

    /**
     * Get default questions sebagai collection of objects
     */
    public static function getDefaultQuestions()
    {
        $questions = config('survey_defaults.default_questions');
        return collect($questions)->map(function ($question) {
            return (object) $question;
        });
    }

    /**
     * Get default question by ID
     */
    public static function getDefaultQuestionById($id)
    {
        $questions = self::getDefaultQuestions();
        return $questions->firstWhere('id', $id);
    }

    /**
     * Check if section ID is permanent
     */
    public static function isPermanentSection($sectionId)
    {
        return $sectionId === 'data_diri';
    }

    /**
     * Check if question ID is permanent
     */
    public static function isPermanentQuestion($questionId)
    {
        $permanentIds = ['nama', 'email', 'jenis_kelamin', 'umur', 'jenis_pendidikan', 'pekerjaan'];
        return in_array($questionId, $permanentIds);
    }

    /**
     * Get question type label
     */
    public static function getQuestionTypeLabel($type)
    {
        $labels = [
            'short_text' => 'Teks Pendek',
            'long_text' => 'Teks Panjang',
            'multiple_choice' => 'Pilihan Ganda',
            'checkbox' => 'Checkbox',
            'dropdown' => 'Dropdown',
            'file_upload' => 'Upload File',
            'linear_scale' => 'Skala Linear',
        ];

        return $labels[$type] ?? $type;
    }
}