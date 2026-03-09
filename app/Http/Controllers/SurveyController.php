<?php
// app/Http/Controllers/SurveyController.php
namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveySection;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    public function index()
    {
        // Ambil pertanyaan dinamis
        $sections = SurveySection::active()
                                ->ordered()
                                ->with(['questions' => function($query) {
                                    $query->active()->ordered();
                                }])
                                ->get();

        // Filter sections yang memiliki pertanyaan aktif
        $sections = $sections->filter(function($section) {
            return $section->questions->count() > 0;
        });

        // Jika tidak ada pertanyaan dinamis, redirect ke admin untuk setup
        if ($sections->count() === 0) {
            return view('survey.no-questions');
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

        // Ambil semua pertanyaan aktif
        $questions = SurveyQuestion::active()->get();
        Log::info('Found active questions', ['count' => $questions->count()]);

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
                    $response = SurveyResponse::create([
                        'survey_id' => $survey->id,
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
            'message' => 'Survei berhasil disimpan.',
            'survey_id' => $survey->id,
            'responses_count' => $responseCount,
            'db_responses_count' => $dbResponseCount
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error('Validation error', ['errors' => $e->errors()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Data tidak valid.',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Survey submission error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            'error_details' => [
                'file' => basename($e->getFile()),
                'line' => $e->getLine()
            ]
        ], 500);
    }
}

    private function validateDynamicSurvey(Request $request)
    {
        $rules = [];
        $messages = [];

        // Get all active questions untuk validasi
        $questions = SurveyQuestion::active()->get();
        
        Log::info('Validating survey', ['questions_count' => $questions->count()]);

        foreach ($questions as $question) {
            $fieldName = 'question_' . $question->id;
            
            if ($question->is_required) {
                if ($question->question_type === 'file_upload') {
                    $rules[$fieldName] = 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt'; // Max 10MB + allowed types
                } elseif ($question->question_type === 'checkbox') {
                    $rules[$fieldName] = 'required|array|min:1';
                } else {
                    $rules[$fieldName] = 'required';
                }

                $messages[$fieldName . '.required'] = 'Pertanyaan "' . $question->question_text . '" wajib dijawab.';
                $messages[$fieldName . '.file'] = 'File untuk "' . $question->question_text . '" harus berupa file yang valid.';
                $messages[$fieldName . '.max'] = 'Ukuran file maksimal 10MB.';
                $messages[$fieldName . '.mimes'] = 'File harus berformat: jpg, jpeg, png, pdf, doc, docx, txt.';
                
                Log::info('Added validation rule', [
                    'field' => $fieldName,
                    'required' => true,
                    'type' => $question->question_type
                ]);
            }

            // Additional validation rules berdasarkan jenis pertanyaan
            if ($question->question_type === 'linear_scale' && isset($question->settings['scale_min'], $question->settings['scale_max'])) {
                $rules[$fieldName] = 'nullable|integer|between:' . $question->settings['scale_min'] . ',' . $question->settings['scale_max'];
            } elseif ($question->question_type === 'file_upload' && !$question->is_required) {
                // Optional file validation
                $rules[$fieldName] = 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt';
            }
        }

        Log::info('Validation rules prepared', ['rules_count' => count($rules)]);
        $request->validate($rules, $messages);
    }

    public function dashboard()
    {
        $totalSurveys = Survey::count();
        
        // Hitung gender berdasarkan responses (bukan field langsung)
        $maleCount = Survey::byGender('laki')->count();
        $femaleCount = Survey::byGender('perempuan')->count();
        
        // Statistik usia berdasarkan responses
        $surveys = Survey::with(['responses.question'])->get();
        $ageGroups = [
            '15-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '55+' => 0
        ];

        foreach ($surveys as $survey) {
            $usia = $survey->usia; // Menggunakan accessor
            if ($usia >= 15 && $usia <= 25) {
                $ageGroups['15-25']++;
            } elseif ($usia >= 26 && $usia <= 35) {
                $ageGroups['26-35']++;
            } elseif ($usia >= 36 && $usia <= 45) {
                $ageGroups['36-45']++;
            } elseif ($usia >= 46 && $usia <= 55) {
                $ageGroups['46-55']++;
            } elseif ($usia > 55) {
                $ageGroups['55+']++;
            }
        }

        // Convert to collection format
        $ageStats = collect($ageGroups)->map(function($count, $group) {
            return (object)[
                'age_group' => $group,
                'count' => $count
            ];
        });

        // Data survei terbaru
        $recentSurveys = Survey::with(['responses.question'])->latest()->take(10)->get();

        return view('survey.dashboard', compact(
            'totalSurveys', 
            'maleCount', 
            'femaleCount', 
            'ageStats', 
            'recentSurveys'
        ));
    }

    public function export()
{
    // Ambil data dengan relasi yang diperlukan, diurutkan berdasarkan tanggal terbaru
    $surveys = Survey::with(['responses.question'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Ambil pertanyaan yang aktif dan diurutkan berdasarkan order
    $questions = SurveyQuestion::active()->ordered()->get();

    // Header CSV yang konsisten
    $headers = [
        'ID Survei',
        'Tanggal Pengisian',
        'Nama Responden', 
        'Jenis Kelamin',
        'Usia',
        'IP Address'
    ];
    
    // Tambahkan header untuk setiap pertanyaan berdasarkan urutan yang benar
    foreach ($questions as $question) {
        // Bersihkan teks pertanyaan untuk header
        $questionText = strip_tags($question->question_text);
        $questionText = str_replace(["\r", "\n", "\t"], ' ', $questionText);
        $questionText = trim($questionText);
        $headers[] = $questionText;
    }

    // Buat header CSV dengan encoding yang benar
    $csvData = "\xEF\xBB\xBF"; // BOM untuk UTF-8
    $csvData .= implode(',', array_map(function($header) {
        return '"' . str_replace('"', '""', $header) . '"';
    }, $headers)) . "\n";

    // Buat baris data
    foreach ($surveys as $survey) {
        $row = [
            $survey->id,
            $survey->created_at->format('Y-m-d H:i:s'),
            $survey->nama ?: '-', // Menggunakan accessor nama
            $survey->jenis_kelamin_label ?: '-', // Menggunakan accessor jenis kelamin
            $survey->usia ?: '-', // Menggunakan accessor usia
            $survey->ip_address ?: '-'
        ];

        // Tambahkan jawaban untuk setiap pertanyaan sesuai urutan yang benar
        foreach ($questions as $question) {
            $response = $survey->responses->firstWhere('question_id', $question->id);
            $answer = '';
            
            if ($response) {
                // Handle berbagai tipe jawaban
                switch ($question->question_type) {
                    case 'file_upload':
                        if ($response->answer_data && isset($response->answer_data['filename'])) {
                            $answer = $response->answer_data['filename'];
                            // Tambahkan URL file jika diperlukan
                            if (isset($response->answer_data['path'])) {
                                $answer .= ' (' . asset('storage/' . $response->answer_data['path']) . ')';
                            }
                        } else {
                            $answer = $response->answer ?: '-';
                        }
                        break;
                        
                    case 'checkbox':
                        // Untuk checkbox multiple, gabungkan jawaban
                        if ($response->answer_data && is_array($response->answer_data)) {
                            $answer = implode('; ', $response->answer_data);
                        } else {
                            $answer = $response->answer ?: '-';
                        }
                        break;
                        
                    case 'linear_scale':
                        $answer = $response->answer ?: '-';
                        break;
                        
                    default:
                        $answer = $response->answer ?: '-';
                }
            } else {
                $answer = '-'; // Tidak ada jawaban
            }
            
            // Bersihkan jawaban dari karakter yang bermasalah
            $answer = str_replace(["\r", "\n", "\t"], ' ', $answer);
            $answer = trim($answer);
            
            $row[] = $answer;
        }

        // Escape semua field untuk CSV
        $csvRow = implode(',', array_map(function($field) {
            return '"' . str_replace('"', '""', $field) . '"';
        }, $row));
        
        $csvData .= $csvRow . "\n";
    }

    // Generate filename yang unik
    $filename = 'survei-dinamis-' . date('Y-m-d_H-i-s') . '.csv';

    return response($csvData)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Cache-Control', 'no-cache, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

    public function downloadFile($responseId)
{
    $response = SurveyResponse::findOrFail($responseId);
    
    // Pastikan ini adalah response file upload
    if ($response->question->question_type !== 'file_upload' || !$response->answer_data) {
        abort(404, 'File tidak ditemukan');
    }

    $filePath = $response->answer_data['path'];
    
    // Cek apakah file exists
    if (!Storage::disk('public')->exists($filePath)) {
        abort(404, 'File tidak ditemukan di server');
    }

    return Storage::disk('public')->download(
        $filePath, 
        $response->answer_data['filename'] ?? basename($filePath)
    );
}
}