<?php
// app/Http/Controllers/AdminController.php
// FIXED VERSION - Total Responden Mengikuti Periode

namespace App\Http\Controllers;

use App\Helpers\SurveyDefaults;
use App\Models\AdminUser;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveySection;
use App\Models\SurveyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = AdminUser::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            session([
                'admin_id' => $admin->id, 
                'admin_name' => $admin->name,
                'admin_role' => $admin->role
            ]);
            
            $admin->update(['last_login_at' => now()]);
            
            return redirect()->route('admin.jawaban');
        }

        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    public function dashboard(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $tab = $request->get('tab', 'questions');
        
        // TAMBAHAN: Ambil parameter period_id dari request
        $periodId = $request->get('period_id');
        
        // TAMBAHAN: Ambil semua periode untuk dropdown
        $allPeriods = SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->get();
        
        // TAMBAHAN: Tentukan periode yang dipilih
        if ($periodId) {
            $selectedPeriod = SurveyPeriod::find($periodId);
        } else {
            // Default: ambil periode yang aktif, atau periode terbaru
            $selectedPeriod = SurveyPeriod::where('is_active', true)->first() 
                           ?? SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->first();
        }
        
        // TAMBAHAN: Filter berdasarkan periode
        if ($selectedPeriod) {
            $totalSurveys = Survey::whereHas('responses', function($query) use ($selectedPeriod) {
                $query->where('period_id', $selectedPeriod->id);
            })->count();
            
            $questions = SurveyQuestion::active()
                ->with(['section', 'responses' => function($query) use ($selectedPeriod) {
                    $query->where('period_id', $selectedPeriod->id);
                }])
                ->ordered()
                ->get();
        } else {
            // Jika belum ada periode sama sekali
            $totalSurveys = Survey::count();
            $questions = SurveyQuestion::active()->with(['section', 'responses'])->ordered()->get();
        }
        
        switch ($tab) {
            case 'individual':
                return $this->getIndividualData($totalSurveys, $questions, $selectedPeriod, $allPeriods);
            case 'sections':
                return $this->getSectionData($totalSurveys, $questions, $selectedPeriod, $allPeriods);
            default:
                return $this->getQuestionsData($totalSurveys, $questions, $selectedPeriod, $allPeriods);
        }
    }

    public function jawaban(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $tab = $request->get('tab', 'questions');
        
        // TAMBAHAN: Ambil parameter period_id dari request
        $periodId = $request->get('period_id');
        
        // TAMBAHAN: Ambil semua periode untuk dropdown
        $allPeriods = SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->get();
        
        // TAMBAHAN: Tentukan periode yang dipilih
        if ($periodId) {
            $selectedPeriod = SurveyPeriod::find($periodId);
        } else {
            // Default: ambil periode yang aktif, atau periode terbaru
            $selectedPeriod = SurveyPeriod::where('is_active', true)->first() 
                           ?? SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->first();
        }
        
        // TAMBAHAN: Filter berdasarkan periode
        if ($selectedPeriod) {
            $totalSurveys = Survey::whereHas('responses', function($query) use ($selectedPeriod) {
                $query->where('period_id', $selectedPeriod->id);
            })->count();
            
            $questions = SurveyQuestion::active()
                ->with(['section', 'responses' => function($query) use ($selectedPeriod) {
                    $query->where('period_id', $selectedPeriod->id);
                }])
                ->ordered()
                ->get();
        } else {
            // Jika belum ada periode sama sekali
            $totalSurveys = Survey::count();
            $questions = SurveyQuestion::active()->with(['section', 'responses'])->ordered()->get();
        }
        
        switch ($tab) {
            case 'individual':
                return $this->getIndividualData($totalSurveys, $questions, $selectedPeriod, $allPeriods);
            case 'sections':
                return $this->getSectionData($totalSurveys, $questions, $selectedPeriod, $allPeriods);
            default:
                return $this->getQuestionsData($totalSurveys, $questions, $selectedPeriod, $allPeriods);
        }
    }

    public function jawabanSections(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $totalSurveys = Survey::count();
        $questions = SurveyQuestion::active()->with(['section', 'responses'])->ordered()->get();
        
        return $this->getSectionData($totalSurveys, $questions);
    }

    public function jawabanIndividual(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $totalSurveys = Survey::count();
        $questions = SurveyQuestion::active()->with(['section', 'responses'])->ordered()->get();
        
        return $this->getIndividualData($totalSurveys, $questions);
    }

    public function getSurveyDetail($id)
    {
        if (!session('admin_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
 
        try {
            // ✅ FIX: Ambil period_id dari request untuk filter responses
            $periodId = request('period_id');
            
            Log::info('getSurveyDetail called', [
                'survey_id' => $id,
                'period_id' => $periodId
            ]);
            
            // Load survey dengan filter responses berdasarkan period_id
            $survey = Survey::with(['responses' => function($query) use ($periodId) {
                if ($periodId) {
                    $query->where('period_id', $periodId);
                }
            }])->findOrFail($id);
            
            Log::info('Survey loaded', [
                'survey_id' => $survey->id,
                'responses_count' => $survey->responses->count()
            ]);
            
            // ✅ FIX: Cek jika tidak ada responses setelah filter
            if ($survey->responses->isEmpty()) {
                Log::warning('No responses found', [
                    'survey_id' => $id,
                    'period_id' => $periodId
                ]);
                
                return response()->json([
                    'error' => 'Tidak ada jawaban untuk responden ini di periode yang dipilih.'
                ], 404);
            }
            
            // ✅ FIX: Load questions manually karena SurveyResponse punya custom accessor
            // Yang perlu handle both database questions dan default questions
            $questionsLoaded = 0;
            foreach ($survey->responses as $response) {
                // Trigger accessor untuk load question
                $question = $response->question;
                
                Log::info('Question loaded for response', [
                    'response_id' => $response->id,
                    'question_id' => $response->question_id,
                    'question_exists' => $question ? 'yes' : 'no',
                    'question_type' => $question ? get_class($question) : 'null'
                ]);
                
                if ($question) {
                    $questionsLoaded++;
                }
                
                // Jika question dari database, load section-nya
                if ($question && is_object($question) && method_exists($question, 'getAttribute')) {
                    if (!isset($question->section) && $question->section_id) {
                        $question->load('section');
                    }
                }
            }
            
            Log::info('Questions loaded summary', [
                'total_responses' => $survey->responses->count(),
                'questions_loaded' => $questionsLoaded
            ]);
 
            $responsesBySection = $survey->responses->groupBy(function($response) {
                $question = $response->question;
                
                // ✅ FIX: Handle permanent questions (from SurveyDefaults)
                if (!$question) {
                    return 'default_section';
                }
                
                if (is_object($question) && !($question instanceof \App\Models\SurveyQuestion)) {
                    return 'default_section';
                }
                
                return $question->section_id ?? 'default_section';
            });
            
            Log::info('Responses grouped by section', [
                'sections_count' => $responsesBySection->count(),
                'section_ids' => $responsesBySection->keys()->toArray()
            ]);
 
            $detailData = [
                'survey' => [
                    'id' => $survey->id,
                    'created_at' => $survey->created_at->format('d/m/Y H:i:s'),
                    'ip_address' => $survey->ip_address ?: 'Tidak diketahui',
                    'user_agent' => $survey->user_agent ?: 'Tidak diketahui'
                ],
                'sections' => []
            ];
 
            foreach ($responsesBySection as $sectionId => $responses) {
                $firstResponse = $responses->first();
                if (!$firstResponse) continue;
                
                $firstQuestion = $firstResponse->question;
                
                if ($sectionId === 'default_section') {
                    $section = (object) [
                        'title' => 'Data Diri',
                        'description' => null
                    ];
                } else {
                    // ✅ FIX: Safe access to section
                    $section = null;
                    if ($firstQuestion && is_object($firstQuestion)) {
                        $section = $firstQuestion->section;
                    }
                }
                
                $sectionData = [
                    'title' => $section ? $section->title : 'Tanpa Bagian',
                    'description' => $section ? ($section->description ?? '') : '',
                    'responses' => []
                ];
 
                foreach ($responses as $response) {
                    $question = $response->question;
                    
                    Log::info('Processing response in section', [
                        'response_id' => $response->id,
                        'section_id' => $sectionId,
                        'question_exists' => $question ? 'yes' : 'no'
                    ]);
                    
                    // ✅ Skip if question is null
                    if (!$question) {
                        Log::warning('Question is null, skipping response', [
                            'response_id' => $response->id,
                            'question_id' => $response->question_id
                        ]);
                        continue;
                    }
                    
                    $responseData = [
                        'response_id' => $response->id,
                        'question_text' => is_object($question) ? ($question->question_text ?? 'Unknown') : 'Unknown',
                        'question_type' => is_object($question) ? ($question->question_type ?? 'short_text') : 'short_text',
                        'question_type_label' => $this->getQuestionTypeLabel($question),
                        'is_required' => is_object($question) ? ($question->is_required ?? false) : false,
                        'answer' => $response->answer ?? '',
                        'answer_data' => $response->answer_data
                    ];
 
                    // ✅ FIX: Safe access ke answer_data dengan isset checks
                    if (is_object($question)) {
                        if ($question->question_type === 'checkbox' && $response->answer_data) {
                            $responseData['formatted_answer'] = is_array($response->answer_data) 
                                ? implode(', ', $response->answer_data) 
                                : $response->answer;
                        } elseif ($question->question_type === 'file_upload') {
                            // ✅ FIX: Check if answer_data exists and has filename key
                            if ($response->answer_data && is_array($response->answer_data) && isset($response->answer_data['filename'])) {
                                $responseData['formatted_answer'] = $response->answer_data['filename'];
                                $responseData['file_info'] = $response->answer_data;
                            } else {
                                // Fallback jika answer_data tidak lengkap
                                $responseData['formatted_answer'] = $response->answer ?: 'File tidak tersedia';
                                $responseData['file_info'] = null;
                            }
                        } elseif ($question->question_type === 'linear_scale') {
                            $responseData['formatted_answer'] = $response->answer;
                            
                            $settings = is_object($question) && isset($question->settings) ? $question->settings : [];
                            $responseData['scale_info'] = [
                                'min' => $settings['scale_min'] ?? 1,
                                'max' => $settings['scale_max'] ?? 5,
                                'min_label' => $settings['scale_min_label'] ?? '',
                                'max_label' => $settings['scale_max_label'] ?? ''
                            ];
                        } else {
                            $responseData['formatted_answer'] = $response->answer;
                        }
                    } else {
                        $responseData['formatted_answer'] = $response->answer;
                    }
 
                    $sectionData['responses'][] = $responseData;
                }
                
                // ✅ Hanya tambahkan section jika ada responses
                if (count($sectionData['responses']) > 0) {
                    $detailData['sections'][] = $sectionData;
                    Log::info('Section added to detail', [
                        'section_title' => $sectionData['title'],
                        'responses_count' => count($sectionData['responses'])
                    ]);
                } else {
                    Log::warning('Section skipped - no responses', [
                        'section_id' => $sectionId,
                        'section_title' => $sectionData['title']
                    ]);
                }
            }
            
            Log::info('Final detail data', [
                'sections_count' => count($detailData['sections']),
                'total_responses' => collect($detailData['sections'])->sum(function($section) {
                    return count($section['responses']);
                })
            ]);
 
            return response()->json($detailData);
 
        } catch (\Exception $e) {
            Log::error('Get survey detail error', [
                'survey_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }

    private function getQuestionTypeLabel($question)
    {
        if (!is_object($question)) {
            return 'Unknown';
        }
        
        if (method_exists($question, 'getQuestionTypeLabel')) {
            return $question->getQuestionTypeLabel();
        }
        
        $labels = [
            'short_text' => 'Teks Pendek',
            'long_text' => 'Teks Panjang',
            'multiple_choice' => 'Pilihan Ganda',
            'checkbox' => 'Checkbox',
            'dropdown' => 'Dropdown',
            'file_upload' => 'Upload File',
            'linear_scale' => 'Skala Linear',
        ];
        
        return $labels[$question->question_type] ?? $question->question_type;
    }

    private function getQuestionsData($totalSurveys, $questions, $selectedPeriod = null, $allPeriods = [])
    {
        $defaultSection = SurveyDefaults::getDefaultSection();
        $defaultQuestions = SurveyDefaults::getDefaultQuestions();
        $defaultSection->questions = $defaultQuestions;
        
        $dbSections = SurveySection::active()
                                ->ordered()
                                ->with(['questions' => function($query) {
                                    $query->active()->ordered();
                                }])
                                ->get();
     
        $dbSections = $dbSections->filter(function($section) {
            return $section->questions->count() > 0;
        });
        
        $sections = collect([$defaultSection])->merge($dbSections);
     
        $sectionStats = [];
        
        foreach ($sections as $section) {
            $sectionQuestionStats = [];
            
            foreach ($section->questions as $question) {
                // ✅ FIX: TAMBAHKAN FILTER PERIOD_ID
                if (isset($question->is_permanent) && $question->is_permanent) {
                    $query = SurveyResponse::where('question_id', $question->id)
                        ->whereHas('survey');
                    
                    // Filter berdasarkan periode jika ada
                    if ($selectedPeriod) {
                        $query->where('period_id', $selectedPeriod->id);
                    }
                    
                    $validResponses = $query->get();
                } else {
                    $query = $question->responses()->whereHas('survey');
                    
                    // Filter berdasarkan periode jika ada
                    if ($selectedPeriod) {
                        $query->where('period_id', $selectedPeriod->id);
                    }
                    
                    $validResponses = $query->get();
                }
                
                $stats = [
                    'question' => $question,
                    'question_type_label' => $this->getQuestionTypeLabel($question),
                    'total_responses' => $validResponses->count(),
                    'response_rate' => $totalSurveys > 0 ?
                        round(($validResponses->count() / $totalSurveys) * 100, 1) : 0
                ];
     
                if ($question->question_type === 'multiple_choice' && $validResponses->count() > 0) {
                    $answers = $validResponses->pluck('answer')->filter();
                    $distribution = $answers->countBy();
                    
                    $chartData = collect();
                    foreach ($distribution as $answer => $count) {
                        $chartData->push((object)[
                            'answer' => $answer,
                            'count' => $count
                        ]);
                    }
                    $stats['response_data'] = $chartData;
                    $stats['chart_enabled'] = true;
                    $stats['data'] = $distribution->toArray();
                    $stats['most_popular'] = $distribution->keys()->first();
                    
                } elseif ($question->question_type === 'checkbox' && $validResponses->count() > 0) {
                    $allAnswers = [];
                    foreach ($validResponses as $response) {
                        if ($response->answer_data && is_array($response->answer_data)) {
                            $allAnswers = array_merge($allAnswers, $response->answer_data);
                        }
                    }
                    $distribution = array_count_values($allAnswers);
                    
                    $chartData = collect();
                    foreach ($distribution as $answer => $count) {
                        $chartData->push((object)[
                            'answer' => $answer,
                            'count' => $count
                        ]);
                    }
                    $stats['response_data'] = $chartData;
                    $stats['chart_enabled'] = true;
                    $stats['data'] = $distribution;
                    
                } elseif ($question->question_type === 'dropdown' && $validResponses->count() > 0) {
                    $answers = $validResponses->pluck('answer')->filter();
                    $distribution = $answers->countBy();
                    
                    $chartData = collect();
                    foreach ($distribution as $answer => $count) {
                        $chartData->push((object)[
                            'answer' => $answer,
                            'count' => $count
                        ]);
                    }
                    $stats['response_data'] = $chartData;
                    $stats['chart_enabled'] = true;
                    $stats['data'] = $distribution->toArray();
                    
                } elseif ($question->question_type === 'linear_scale') {
                    if ($validResponses->count() > 0) {
                        $responses = $validResponses->pluck('answer')->filter()->map(function($item) {
                            return (int) $item;
                        });
                        $average = $responses->avg();
                        $distribution = $responses->countBy();
                        
                        $chartData = collect();
                        foreach ($distribution as $answer => $count) {
                            $chartData->push((object)[
                                'answer' => $answer,
                                'count' => $count
                            ]);
                        }
                        // ✅ Format khusus untuk linear_scale
                        $stats['response_data'] = [
                            'distribution' => $distribution->toArray(),
                            'total_responses' => $validResponses->count(),
                            'average' => round($average, 1)
                        ];
                        $stats['chart_enabled'] = true;
                        $stats['data'] = $distribution->toArray();
                        $stats['average'] = round($average, 1);
                    } else {
                        $stats['response_data'] = [];
                        $stats['chart_enabled'] = false;
                    }
                } elseif ($question->question_type === 'file_upload') {
                    // ✅ FIX: Tangani file upload dengan aman
                    $stats['uploaded_files'] = $validResponses->map(function($response) {
                        // Cek apakah answer_data ada dan valid
                        $answerData = $response->answer_data;
                        $isValidData = $answerData && is_array($answerData) && isset($answerData['filename']);
                        
                        return [
                            'response_id' => $response->id,
                            'filename' => $isValidData 
                                ? $answerData['filename'] 
                                : ($response->answer ?: 'File tidak tersedia'),
                            'upload_date' => $response->created_at,
                            'file_data' => $isValidData ? [
                                'size' => $answerData['size'] ?? null,
                                'mime_type' => $answerData['mime_type'] ?? null,
                                'extension' => $answerData['extension'] ?? null,
                                'path' => $answerData['path'] ?? null,
                            ] : []
                        ];
                    })->toArray();
                    $stats['chart_enabled'] = false;
                } else {
                    // ✅ FIX: Untuk text questions, gunakan 'sample_responses'
                    $stats['sample_responses'] = $validResponses->take(5)->map(function($response) {
                        return [
                            'answer' => $response->answer,
                            'created_at' => $response->created_at->format('d/m/Y H:i')
                        ];
                    })->toArray();
                    $stats['chart_enabled'] = false;
                }
     
                $sectionQuestionStats[] = $stats;
            }
            
            $sectionStats[] = [
                'section' => $section,
                'questions_stats' => $sectionQuestionStats,
                'total_questions' => $section->questions->count(),
                'total_responses' => collect($sectionQuestionStats)->sum('total_responses')
            ];
        }
     
        return view('admin.jawaban', compact(
            'totalSurveys',
            'questions',
            'sectionStats',
            'selectedPeriod',
            'allPeriods'
        ));
    }

    private function getSectionData($totalSurveys, $questions, $selectedPeriod = null, $allPeriods = [])
    {
        $defaultSection = SurveyDefaults::getDefaultSection();
        $defaultQuestions = SurveyDefaults::getDefaultQuestions();
        $defaultSection->questions = $defaultQuestions;
        
        $dbSections = SurveySection::active()
                                ->ordered()
                                ->with(['questions' => function($query) {
                                    $query->active()->ordered();
                                }])
                                ->get();
     
        $dbSections = $dbSections->filter(function($section) {
            return $section->questions->count() > 0;
        });
        
        $sections = collect([$defaultSection])->merge($dbSections);
     
        $sectionStats = [];
        
        foreach ($sections as $section) {
            $sectionQuestionStats = [];
            
            foreach ($section->questions as $question) {
                // ✅ FIX: TAMBAHKAN FILTER PERIOD_ID (sama seperti di getQuestionsData)
                if (isset($question->is_permanent) && $question->is_permanent) {
                    $query = SurveyResponse::where('question_id', $question->id)
                        ->whereHas('survey');
                    
                    if ($selectedPeriod) {
                        $query->where('period_id', $selectedPeriod->id);
                    }
                    
                    $validResponses = $query->get();
                } else {
                    $query = $question->responses()->whereHas('survey');
                    
                    if ($selectedPeriod) {
                        $query->where('period_id', $selectedPeriod->id);
                    }
                    
                    $validResponses = $query->get();
                }
                
                $stats = [
                    'question' => $question,
                    'question_type_label' => $this->getQuestionTypeLabel($question),
                    'total_responses' => $validResponses->count(),
                    'response_rate' => $totalSurveys > 0 ?
                        round(($validResponses->count() / $totalSurveys) * 100, 1) : 0
                ];
     
                if ($question->question_type === 'multiple_choice' && $validResponses->count() > 0) {
                    $answers = $validResponses->pluck('answer')->filter();
                    $distribution = $answers->countBy();
                    
                    $chartData = collect();
                    foreach ($distribution as $answer => $count) {
                        $chartData->push((object)[
                            'answer' => $answer,
                            'count' => $count
                        ]);
                    }
                    $stats['response_data'] = $chartData;
                    $stats['chart_enabled'] = true;
                    $stats['data'] = $distribution->toArray();
                    $stats['most_popular'] = $distribution->keys()->first();
                    
                } elseif ($question->question_type === 'checkbox' && $validResponses->count() > 0) {
                    $allAnswers = [];
                    foreach ($validResponses as $response) {
                        if ($response->answer_data && is_array($response->answer_data)) {
                            $allAnswers = array_merge($allAnswers, $response->answer_data);
                        }
                    }
                    $distribution = array_count_values($allAnswers);
                    
                    $chartData = collect();
                    foreach ($distribution as $answer => $count) {
                        $chartData->push((object)[
                            'answer' => $answer,
                            'count' => $count
                        ]);
                    }
                    $stats['response_data'] = $chartData;
                    $stats['chart_enabled'] = true;
                    $stats['data'] = $distribution;
                    
                } elseif ($question->question_type === 'dropdown' && $validResponses->count() > 0) {
                    $answers = $validResponses->pluck('answer')->filter();
                    $distribution = $answers->countBy();
                    
                    $chartData = collect();
                    foreach ($distribution as $answer => $count) {
                        $chartData->push((object)[
                            'answer' => $answer,
                            'count' => $count
                        ]);
                    }
                    $stats['response_data'] = $chartData;
                    $stats['chart_enabled'] = true;
                    $stats['data'] = $distribution->toArray();
                    
                } elseif ($question->question_type === 'linear_scale') {
                    if ($validResponses->count() > 0) {
                        $responses = $validResponses->pluck('answer')->filter()->map(function($item) {
                            return (int) $item;
                        });
                        $average = $responses->avg();
                        $distribution = $responses->countBy();
                        
                        $chartData = collect();
                        foreach ($distribution as $answer => $count) {
                            $chartData->push((object)[
                                'answer' => $answer,
                                'count' => $count
                            ]);
                        }
                        $stats['response_data'] = [
                            'distribution' => $distribution->toArray(),
                            'total_responses' => $validResponses->count(),
                            'average' => round($average, 1)
                        ];
                        $stats['chart_enabled'] = true;
                        $stats['data'] = $distribution->toArray();
                        $stats['average'] = round($average, 1);
                    } else {
                        $stats['response_data'] = [];
                        $stats['chart_enabled'] = false;
                    }
                } elseif ($question->question_type === 'file_upload') {
                    $stats['uploaded_files'] = $validResponses->map(function($response) {
                        $answerData = $response->answer_data;
                        $isValidData = $answerData && is_array($answerData) && isset($answerData['filename']);
                        
                        return [
                            'response_id' => $response->id,
                            'filename' => $isValidData 
                                ? $answerData['filename'] 
                                : ($response->answer ?: 'File tidak tersedia'),
                            'upload_date' => $response->created_at,
                            'file_data' => $isValidData ? [
                                'size' => $answerData['size'] ?? null,
                                'mime_type' => $answerData['mime_type'] ?? null,
                                'extension' => $answerData['extension'] ?? null,
                                'path' => $answerData['path'] ?? null,
                            ] : []
                        ];
                    })->toArray();
                    $stats['chart_enabled'] = false;
                } else {
                    $stats['sample_responses'] = $validResponses->take(5)->map(function($response) {
                        return [
                            'answer' => $response->answer,
                            'created_at' => $response->created_at->format('d/m/Y H:i')
                        ];
                    })->toArray();
                    $stats['chart_enabled'] = false;
                }
     
                $sectionQuestionStats[] = $stats;
            }
            
            $sectionStats[] = [
                'section' => $section,
                'questions_stats' => $sectionQuestionStats,
                'total_questions' => $section->questions->count(),
                'total_responses' => collect($sectionQuestionStats)->sum('total_responses')
            ];
        }
     
        return view('admin.jawaban-sections', compact(
            'totalSurveys',
            'questions',
            'sectionStats',
            'selectedPeriod',
            'allPeriods'
        ));
    }

    private function getIndividualData($totalSurveys, $questions, $selectedPeriod = null, $allPeriods = [])
    {
        // ✅ FIX: FILTER SURVEY DAN RESPONSES BERDASARKAN PERIODE
        if ($selectedPeriod) {
            // Filter survey yang punya responses di periode ini
            // DAN load hanya responses yang ada di periode ini
            $query = Survey::with(['responses' => function($q) use ($selectedPeriod) {
                $q->where('period_id', $selectedPeriod->id);
            }])
            ->whereHas('responses', function($q) use ($selectedPeriod) {
                $q->where('period_id', $selectedPeriod->id);
            });
        } else {
            // Jika tidak ada periode dipilih, ambil semua
            $query = Survey::with('responses');
        }
        
        $surveys = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.jawaban-individual', compact(
            'surveys',
            'totalSurveys',
            'questions',
            'selectedPeriod',
            'allPeriods'
        ));
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_name', 'admin_role']);
        return redirect()->route('admin.login')->with('success', 'Anda berhasil logout.');
    }

    public function export(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        try {
            $questions = SurveyQuestion::active()->ordered()->get();
            
            if ($questions->isEmpty()) {
                return back()->with('error', 'Tidak ada pertanyaan yang tersedia untuk diekspor.');
            }

            $surveys = Survey::with(['responses.question'])->orderBy('created_at', 'asc')->get();
            
            if ($surveys->isEmpty()) {
                return back()->with('error', 'Tidak ada data survey untuk diekspor.');
            }

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Survey');
            
            $headerRow = ['ID Survey', 'Tanggal Pengisian'];
            foreach ($questions as $question) {
                $headerRow[] = $question->question_text;
            }
            
            $sheet->fromArray($headerRow, null, 'A1');
            
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
            
            $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headerRow));
            $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);
            
            $rowNumber = 2;
            foreach ($surveys as $survey) {
                $rowData = [
                    $survey->id,
                    $survey->created_at->format('m/d/Y h:i:s A')
                ];
                
                $responseMap = [];
                foreach ($survey->responses as $response) {
                    $responseMap[$response->question_id] = $response;
                }
                
                foreach ($questions as $question) {
                    $answer = '-';
                    
                    if (isset($responseMap[$question->id])) {
                        $response = $responseMap[$question->id];
                        
                        switch ($question->question_type) {
                            case 'checkbox':
                                if ($response->answer_data && is_array($response->answer_data)) {
                                    $answer = implode('; ', $response->answer_data);
                                } else {
                                    $answer = $response->answer ?: '-';
                                }
                                break;
                                
                            case 'file_upload':
                                // ✅ FIX: Safe access dengan isset
                                if ($response->answer_data && is_array($response->answer_data) && isset($response->answer_data['filename'])) {
                                    $answer = $response->answer_data['filename'];
                                } else {
                                    $answer = $response->answer ?: 'File tidak tersedia';
                                }
                                break;
                                
                            default:
                                $answer = $response->answer ?: '-';
                                break;
                        }
                    }
                    
                    $rowData[] = $answer;
                }
                
                $sheet->fromArray($rowData, null, 'A' . $rowNumber);
                
                $dataStyle = [
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ];
                
                $sheet->getStyle('A' . $rowNumber . ':' . $lastColumn . $rowNumber)->applyFromArray($dataStyle);
                
                $rowNumber++;
            }
            
            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(22);
            
            for ($col = 3; $col <= count($headerRow); $col++) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($columnLetter)->setWidth(35);
            }
            
            $sheet->freezePane('A2');
            
            $filename = 'survey_export_' . date('Y-m-d_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            
            exit;
            
        } catch (\Exception $e) {
            Log::error('Export Excel Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat mengekspor data: ' . $e->getMessage());
        }
    }

    public function uploadedFiles()
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $fileResponses = SurveyResponse::whereHas('question', function($query) {
            $query->where('question_type', 'file_upload');
        })
        ->whereNotNull('answer_data')
        ->with(['survey', 'question'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('admin.uploaded-files', compact('fileResponses'));
    }

    public function deleteSurvey($id)
    {
        if (!session('admin_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            DB::beginTransaction();

            $survey = Survey::findOrFail($id);
            
            $fileResponses = SurveyResponse::where('survey_id', $id)
                ->whereHas('question', function($query) {
                    $query->where('question_type', 'file_upload');
                })
                ->whereNotNull('answer_data')
                ->get();

            foreach ($fileResponses as $response) {
                // ✅ FIX: Safe check sebelum akses array
                if ($response->answer_data && is_array($response->answer_data) && isset($response->answer_data['path'])) {
                    $filePath = $response->answer_data['path'];
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }

            SurveyResponse::where('survey_id', $id)->delete();
            $survey->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Survey berhasil dihapus',
                'show_toast' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Survey Error', [
                'survey_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat menghapus survey: ' . $e->getMessage()
            ], 500);
        }
    }
}