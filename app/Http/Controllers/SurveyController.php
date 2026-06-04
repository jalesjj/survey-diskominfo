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
        // ============================================================
        // CEK APAKAH ADA PERIODE AKTIF
        // Jika tidak ada periode aktif (belum di-lock/submit atau sudah di-stop),
        // maka halaman responden tidak bisa diakses
        // ============================================================
        $activePeriod = SurveyPeriod::getActivePeriod();
        
        if (!$activePeriod) {
            // Tampilkan halaman kosong atau pesan bahwa survey belum dibuka
            return view('survey.no-active-period');
        }
        
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
            // ============================================================
            // CEK PERIODE AKTIF SEBELUM MENYIMPAN RESPONSE
            // ============================================================
            $activePeriod = SurveyPeriod::getActivePeriod();
            
            if (!$activePeriod) {
                // PERBAIKAN: Return JSON untuk AJAX request
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Survey sedang tidak aktif. Silakan coba lagi nanti.'
                    ], 400);
                }
                
                return redirect()->route('survey.index')
                    ->with('error', 'Survey sedang tidak aktif. Silakan coba lagi nanti.');
            }
            
            // Log request data untuk debugging
            Log::info('Survey submission started', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'request_data_keys' => array_keys($request->all()),
                'files' => array_keys($request->allFiles()),
                'period_id' => $activePeriod->id,
                'period_name' => $activePeriod->period_name
            ]);

            DB::beginTransaction();

            // ============================================================
            // PERBAIKAN: Ambil nama dan email dari question_nama dan question_email
            // Karena form mengirim sebagai question_nama dan question_email, bukan nama dan email
            // ============================================================
            $nama = $request->input('question_nama');
            $email = $request->input('question_email');

            // Validasi basic menggunakan Validator facade
            $validator = \Illuminate\Support\Facades\Validator::make([
                'nama' => $nama,
                'email' => $email,
            ], [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ], [
                'nama.required' => 'Nama harus diisi.',
                'email.required' => 'Email harus diisi.',
                'email.email' => 'Format email tidak valid.',
            ]);

            if ($validator->fails()) {
                // Return JSON untuk AJAX
                if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                return back()->withErrors($validator->errors())->withInput();
            }

            // Create survey record
            $survey = Survey::create([
                'nama' => $nama,
                'email' => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'submitted_at' => now(),
            ]);

            Log::info('Survey record created', ['survey_id' => $survey->id]);

            // Get all questions (default + database)
            $defaultQuestions = SurveyDefaults::getDefaultQuestions();
            $dbQuestions = SurveyQuestion::active()->get();
            $allQuestions = collect($defaultQuestions)->merge($dbQuestions);

            Log::info('Total questions to process', [
                'default_count' => count($defaultQuestions),
                'db_count' => $dbQuestions->count(),
                'total' => $allQuestions->count()
            ]);

            $totalResponses = 0;
            $totalFiles = 0;

            // Process each question
            foreach ($allQuestions as $question) {
                $questionId = is_object($question) ? $question->id : $question['id'];
                $questionType = is_object($question) ? $question->question_type : $question['question_type'];
                $isRequired = is_object($question) ? $question->is_required : $question['is_required'];
                $isPermanent = is_object($question) ? ($question->is_permanent ?? false) : ($question['is_permanent'] ?? false);

                Log::info('Processing question', [
                    'id' => $questionId,
                    'type' => $questionType,
                    'is_permanent' => $isPermanent
                ]);

                // Ambil jawaban dari request
                $answer = $request->input('question_' . $questionId);
                $answerData = null;

                // Handle file uploads
                if ($questionType === 'file_upload') {
                    Log::info('Processing file upload for question', ['question_id' => $questionId]);
                    
                    if ($request->hasFile('question_' . $questionId)) {
                        try {
                            $file = $request->file('question_' . $questionId);
                            
                            Log::info('File details', [
                                'original_name' => $file->getClientOriginalName(),
                                'size' => $file->getSize(),
                                'mime' => $file->getMimeType()
                            ]);

                            // Validasi file
                            $maxSize = 10 * 1024 * 1024; // 10 MB
                            if ($file->getSize() > $maxSize) {
                                throw new \Exception('File terlalu besar. Maksimal 10 MB.');
                            }

                            $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
                            $extension = strtolower($file->getClientOriginalExtension());
                            if (!in_array($extension, $allowedTypes)) {
                                throw new \Exception('Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowedTypes));
                            }

                            // Generate filename
                            $filename = time() . '_' . $questionId . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());
                            $path = $file->storeAs('uploads', $filename, 'public');

                            Log::info('File uploaded successfully', ['path' => $path]);

                            // Simpan data file sebagai JSON
                            $answerData = [
                                'filename' => $file->getClientOriginalName(),
                                'stored_filename' => $filename,
                                'path' => $path,
                                'size' => $file->getSize(),
                                'mime_type' => $file->getMimeType(),
                                'extension' => $extension,
                                'uploaded_at' => now()->toDateTimeString()
                            ];

                            $answer = json_encode($answerData);
                            $totalFiles++;
                            
                        } catch (\Exception $fileError) {
                            Log::error('File upload error', [
                                'question_id' => $questionId,
                                'error' => $fileError->getMessage()
                            ]);
                            throw new \Exception('Gagal upload file: ' . $fileError->getMessage());
                        }
                    } elseif ($isRequired) {
                        Log::warning('Required file upload missing', ['question_id' => $questionId]);
                    }
                } 
                // Handle non-file questions
                elseif ($answer !== null && $answer !== '') {
                    if ($questionType === 'checkbox' && is_array($answer)) {
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
                        'question_id' => $questionId,
                        'answer' => $answer,
                        'has_answer_data' => !empty($answerData),
                        'answer_data_type' => gettype($answerData)
                    ]);

                    try {
                        $response = SurveyResponse::create([
                            'survey_id' => $survey->id,
                            'period_id' => $activePeriod->id, // Gunakan periode aktif
                            'question_id' => $questionId,
                            'answer' => $answer ?? '',
                            'answer_data' => $answerData ? json_encode($answerData) : null
                        ]);

                        Log::info('Response saved successfully', [
                            'response_id' => $response->id,
                            'question_id' => $questionId
                        ]);

                        $totalResponses++;
                    } catch (\Exception $e) {
                        Log::error('Failed to save response', [
                            'question_id' => $questionId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                } else {
                    Log::info('Skipping question - no answer provided', ['question_id' => $questionId]);
                }
            }

            DB::commit();

            Log::info('Survey submission completed', [
                'survey_id' => $survey->id,
                'total_responses' => $totalResponses,
                'total_files' => $totalFiles
            ]);

            // PERBAIKAN: Return JSON untuk AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Terima kasih! Survei Anda telah berhasil dikirim.',
                    'data' => [
                        'survey_id' => $survey->id,
                        'total_responses' => $totalResponses,
                        'total_files' => $totalFiles
                    ]
                ]);
            }

            return redirect()->route('survey.index')
                           ->with('success', 'Terima kasih! Survei Anda telah berhasil dikirim.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error', ['errors' => $e->errors()]);
            
            // PERBAIKAN: Return JSON untuk AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Survey submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // PERBAIKAN: Return JSON untuk AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengirim survei: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat mengirim survei: ' . $e->getMessage())->withInput();
        }
    }
}