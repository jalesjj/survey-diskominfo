<?php
// app/Http/Controllers/SurveyController.php
namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveySection;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveyPeriod;
use App\Helpers\SurveyDefaults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class SurveyController extends Controller
{
    public function index()
    {
        // Ambil default section dan questions
        $defaultSection = SurveyDefaults::getDefaultSection();
        $defaultQuestions = SurveyDefaults::getDefaultQuestions();
        $defaultSection->questions = $defaultQuestions;

        // Ambil sections dari database
        $dbSections = SurveySection::active()
                                ->ordered()
                                ->with(['questions' => function($query) {
                                    $query->active()->ordered();
                                }])
                                ->get();

        // Filter sections yang memiliki pertanyaan aktif
        $dbSections = $dbSections->filter(function($section) {
            return $section->questions->count() > 0;
        });

        // Gabungkan default section dengan sections dari database
        $sections = collect([$defaultSection])->merge($dbSections);

        // Jika hanya ada default section dan tidak ada section lain
        if ($sections->count() === 1 && $dbSections->count() === 0) {
            // Tetap tampilkan survey dengan default section
        }

        return view('survey.index', compact('sections'));
    }

    public function store(Request $request)
{
    try {
        // Log request data untuk debugging
        Log::info('Survey submission started', [
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'request_data_keys' => array_keys($request->all()),
            'files' => array_keys($request->allFiles())
        ]);

        DB::beginTransaction();

        // Validasi form dinamis
        $this->validateDynamicSurvey($request);
        
        Log::info('Validation passed');

        // Buat record survei utama
        $survey = Survey::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        Log::info('Survey record created', ['survey_id' => $survey->id]);

        // Ambil semua pertanyaan aktif dari database
        $dbQuestions = SurveyQuestion::active()->get();
        
        // Ambil default questions
        $defaultQuestions = SurveyDefaults::getDefaultQuestions();
        
        // Gabungkan kedua collection
        $questions = $defaultQuestions->merge($dbQuestions);
        
        Log::info('Found active questions', [
            'default_count' => $defaultQuestions->count(),
            'db_count' => $dbQuestions->count(),
            'total_count' => $questions->count()
        ]);

        $responseCount = 0;
        foreach ($questions as $question) {
            $fieldName = 'question_' . $question->id;
            $answer = $request->input($fieldName);
            $answerData = null;

            Log::info('Processing question', [
                'question_id' => $question->id,
                'field_name' => $fieldName,
                'question_type' => $question->question_type,
                'has_input' => !empty($answer),
                'has_file' => $request->hasFile($fieldName),
                'is_required' => $question->is_required
            ]);

            // Handle file upload khusus
            if ($question->question_type === 'file_upload') {
                if ($request->hasFile($fieldName)) {
                    try {
                        $file = $request->file($fieldName);
                        
                        // Validasi file
                        if ($file->isValid()) {
                            // Pastikan direktori exists
                            $uploadPath = 'uploads/survey';
                            if (!Storage::disk('public')->exists($uploadPath)) {
                                Storage::disk('public')->makeDirectory($uploadPath);
                            }
                            
                            // Generate filename yang aman
                            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                            
                            // Store file
                            $path = $file->storeAs($uploadPath, $filename, 'public');
                            
                            $answerData = [
                                'filename' => $file->getClientOriginalName(),
                                'stored_filename' => $filename,
                                'path' => $path,
                                'size' => $file->getSize(),
                                'mime_type' => $file->getMimeType(),
                                'extension' => $file->getClientOriginalExtension()
                            ];
                            
                            $answer = $file->getClientOriginalName();
                            
                            Log::info('File uploaded successfully', [
                                'original_filename' => $file->getClientOriginalName(),
                                'stored_filename' => $filename,
                                'path' => $path,
                                'size' => $file->getSize()
                            ]);

                            // CRITICAL: Pastikan file response tersimpan
                            Log::info('About to save file response', [
                                'survey_id' => $survey->id,
                                'question_id' => $question->id,
                                'answer' => $answer,
                                'answer_data_keys' => array_keys($answerData)
                            ]);

                        } else {
                            Log::error('File upload validation failed', [
                                'question_id' => $question->id,
                                'error' => 'File is not valid'
                            ]);
                            throw new \Exception('File yang diupload tidak valid');
                        }
                    } catch (\Exception $fileError) {
                        Log::error('File upload error', [
                            'question_id' => $question->id,
                            'error' => $fileError->getMessage()
                        ]);
                        throw new \Exception('Gagal mengupload file: ' . $fileError->getMessage());
                    }
                } elseif ($question->is_required) {
                    Log::warning('Required file upload missing', ['question_id' => $question->id]);
                }
            } 
            // Handle non-file questions
            elseif ($answer !== null && $answer !== '') {
                if ($question->question_type === 'checkbox' && is_array($answer)) {
                    $answerData = $answer;
                    $answer = implode(', ', $answer);
                    Log::info('Processed checkbox answer', ['options' => $answerData]);
                } elseif (is_array($answer)) {
                    $answer = implode(', ', $answer);
                    Log::info('Processed array answer', ['answer' => $answer]);
                }
            }

            // CRITICAL DEBUG: Simpan response jika ada jawaban atau file
            if (($answer !== null && $answer !== '') || $answerData !== null) {
                
                Log::info('BEFORE saving response', [
                    'survey_id' => $survey->id,
                    'question_id' => $question->id,
                    'answer' => $answer,
                    'has_answer_data' => !empty($answerData),
                    'answer_data_type' => gettype($answerData)
                ]);

                try {
                    // Get active period
$activePeriod = SurveyPeriod::getActivePeriod();
 
$response = SurveyResponse::create([
    'survey_id' => $survey->id,
    'period_id' => $activePeriod ? $activePeriod->id : null,
    'question_id' => $question->id,
    'answer' => $answer ?? '',
    'answer_data' => $answerData
]);

                    $responseCount++;
                    
                    Log::info('AFTER saving response - SUCCESS', [
                        'response_id' => $response->id,
                        'survey_id' => $response->survey_id,
                        'question_id' => $response->question_id,
                        'answer' => $response->answer,
                        'has_answer_data' => !empty($response->answer_data),
                        'response_count' => $responseCount
                    ]);

                    // Double check: Query back the saved response
                    $savedResponse = SurveyResponse::find($response->id);
                    if ($savedResponse) {
                        Log::info('Response verification - FOUND in DB', [
                            'response_id' => $savedResponse->id,
                            'answer_data_exists' => !empty($savedResponse->answer_data)
                        ]);
                    } else {
                        Log::error('Response verification - NOT FOUND in DB', [
                            'expected_response_id' => $response->id
                        ]);
                    }

                } catch (\Exception $dbError) {
                    Log::error('Database save error', [
                        'question_id' => $question->id,
                        'error' => $dbError->getMessage(),
                        'survey_id' => $survey->id
                    ]);
                    throw $dbError;
                }
            } else {
                Log::info('Skipping response - no answer or file', [
                    'question_id' => $question->id,
                    'answer_empty' => empty($answer),
                    'answer_data_empty' => empty($answerData)
                ]);
            }
        }

        Log::info('All responses processed', [
            'total_responses' => $responseCount,
            'survey_id' => $survey->id
        ]);

        // FINAL VERIFICATION: Count responses in DB
        $dbResponseCount = SurveyResponse::where('survey_id', $survey->id)->count();
        Log::info('Final DB verification', [
            'expected_responses' => $responseCount,
            'actual_db_responses' => $dbResponseCount,
            'match' => $responseCount === $dbResponseCount
        ]);

        DB::commit();
        Log::info('Transaction committed successfully');

        return response()->json([
            'success' => true,
            'message' => 'Survei berhasil disimpan. Terima kasih atas partisipasinya!',
            'survey_id' => $survey->id
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error('Validation failed', ['errors' => $e->errors()]);
        
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Survey submission error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menyimpan survei: ' . $e->getMessage()
        ], 500);
    }
}

    // Validasi dinamis berdasarkan pertanyaan yang aktif
    private function validateDynamicSurvey(Request $request)
    {
        // Ambil pertanyaan dari database
        $dbQuestions = SurveyQuestion::active()->get();
        
        // Ambil default questions
        $defaultQuestions = SurveyDefaults::getDefaultQuestions();
        
        // Gabungkan
        $questions = $defaultQuestions->merge($dbQuestions);
        
        $rules = [];
        $messages = [];

        foreach ($questions as $question) {
            $fieldName = 'question_' . $question->id;
            
            if ($question->is_required) {
                if ($question->question_type === 'file_upload') {
                    $rules[$fieldName] = 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240';
                    $messages[$fieldName . '.required'] = "Pertanyaan '{$question->question_text}' harus diisi.";
                    $messages[$fieldName . '.file'] = "File untuk pertanyaan '{$question->question_text}' tidak valid.";
                    $messages[$fieldName . '.mimes'] = "File harus berformat: jpg, jpeg, png, pdf, doc, docx.";
                    $messages[$fieldName . '.max'] = "Ukuran file maksimal 10MB.";
                } else {
                    $rules[$fieldName] = 'required';
                    $messages[$fieldName . '.required'] = "Pertanyaan '{$question->question_text}' harus diisi.";
                }
            }
        }

        $request->validate($rules, $messages);
    }
}