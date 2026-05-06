<?php
// app/Http/Controllers/AdminController.php
// FULL FILE - FIXED VERSION

namespace App\Http\Controllers;

use App\Helpers\SurveyDefaults;
use App\Models\AdminUser;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveySection;
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
        $totalSurveys = Survey::count();
        $questions = SurveyQuestion::active()->with(['section', 'responses'])->ordered()->get();
        
        switch ($tab) {
            case 'individual':
                return $this->getIndividualData($totalSurveys, $questions);
            case 'sections':
                return $this->getSectionData($totalSurveys, $questions);
            default:
                return $this->getQuestionsData($totalSurveys, $questions);
        }
    }

    public function jawaban(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $tab = $request->get('tab', 'questions');
        $totalSurveys = Survey::count();
        $questions = SurveyQuestion::active()->with(['section', 'responses'])->ordered()->get();
        
        switch ($tab) {
            case 'individual':
                return $this->getIndividualData($totalSurveys, $questions);
            case 'sections':
                return $this->getSectionData($totalSurveys, $questions);
            default:
                return $this->getQuestionsData($totalSurveys, $questions);
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
            $survey = Survey::with(['responses'])->findOrFail($id);
 
            $responsesBySection = $survey->responses->groupBy(function($response) {
                $question = $response->question;
                
                if (is_object($question) && !($question instanceof \App\Models\SurveyQuestion)) {
                    return 'default_section';
                }
                
                return $question->section_id ?? 'no_section';
            });
 
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
                $firstQuestion = $responses->first()->question;
                
                if ($sectionId === 'default_section') {
                    $section = (object) [
                        'title' => 'Data Diri',
                        'description' => null
                    ];
                } else {
                    $section = is_object($firstQuestion) && method_exists($firstQuestion, 'getAttribute') 
                        ? $firstQuestion->section 
                        : null;
                }
                
                $sectionData = [
                    'title' => $section ? $section->title : 'Tanpa Bagian',
                    'description' => $section ? $section->description : '',
                    'responses' => []
                ];
 
                foreach ($responses as $response) {
                    $question = $response->question;
                    
                    $responseData = [
                        'response_id' => $response->id,
                        'question_text' => is_object($question) ? $question->question_text : 'Unknown',
                        'question_type' => is_object($question) ? $question->question_type : 'short_text',
                        'question_type_label' => $this->getQuestionTypeLabel($question),
                        'is_required' => is_object($question) ? $question->is_required : false,
                        'answer' => $response->answer,
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
 
                $detailData['sections'][] = $sectionData;
            }
 
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

    private function getQuestionsData($totalSurveys, $questions)
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
                if (isset($question->is_permanent) && $question->is_permanent) {
                    $validResponses = SurveyResponse::where('question_id', $question->id)
                        ->whereHas('survey')
                        ->get();
                } else {
                    $validResponses = $question->responses()->whereHas('survey')->get();
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
                        $stats['data']['average'] = round($average, 1);
                        $stats['data']['min'] = $responses->min();
                        $stats['data']['max'] = $responses->max();
                    } else {
                        // Linear scale tanpa jawaban
                        $stats['response_data'] = [];
                        $stats['chart_enabled'] = true;
                        $stats['data'] = [];
                        $stats['average'] = 0;
                    }
                    
                } else {
    // Handling khusus untuk file_upload
    if ($question->question_type === 'file_upload') {
        $stats['response_data'] = $validResponses->map(function($response) {
            $answerData = $response->answer_data;
            $isValidData = $answerData && is_array($answerData);
            
            return [
                'response_id' => $response->id,
                'filename' => $isValidData && isset($answerData['filename']) 
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
    } else {
        // ✅ FIX: Untuk text questions, gunakan 'sample_responses'
        $stats['sample_responses'] = $validResponses->take(5)->map(function($response) {
            return [
                'answer' => $response->answer,
                'created_at' => $response->created_at->format('d/m/Y H:i')
            ];
        })->toArray();
    }
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
            'sectionStats'
        ));
    }

    private function getSectionData($totalSurveys, $questions)
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
                if (isset($question->is_permanent) && $question->is_permanent) {
                    $validResponses = SurveyResponse::where('question_id', $question->id)
                        ->whereHas('survey')
                        ->get();
                } else {
                    $validResponses = $question->responses()->whereHas('survey')->get();
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
                        $stats['data']['average'] = round($average, 1);
                        $stats['data']['min'] = $responses->min();
                        $stats['data']['max'] = $responses->max();
                    } else {
                        // Linear scale tanpa jawaban
                        $stats['response_data'] = [];
                        $stats['chart_enabled'] = true;
                        $stats['data'] = [];
                        $stats['average'] = 0;
                    }
                    
                } else {
    // Handling khusus untuk file_upload
    if ($question->question_type === 'file_upload') {
        $stats['response_data'] = $validResponses->map(function($response) {
            $answerData = $response->answer_data;
            $isValidData = $answerData && is_array($answerData);
            
            return [
                'response_id' => $response->id,
                'filename' => $isValidData && isset($answerData['filename']) 
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
    } else {
        // ✅ FIX: Untuk text questions, gunakan 'sample_responses'
        $stats['sample_responses'] = $validResponses->take(5)->map(function($response) {
            return [
                'answer' => $response->answer,
                'created_at' => $response->created_at->format('d/m/Y H:i')
            ];
        })->toArray();
    }
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
            'sectionStats'
        ));
    }

    public function downloadFile($responseId)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        try {
            $response = SurveyResponse::findOrFail($responseId);
            
            // ✅ FIX: Safe check untuk question dan answer_data
            $question = $response->question;
            
            if (!$question || $question->question_type !== 'file_upload' || !$response->answer_data) {
                abort(404, 'File tidak ditemukan');
            }

            // ✅ FIX: Safe access ke array keys
            if (!is_array($response->answer_data) || !isset($response->answer_data['path'])) {
                return redirect()->back()->with('error', 'Data file tidak valid');
            }

            $filePath = $response->answer_data['path'];
            $originalFilename = $response->answer_data['filename'] ?? basename($filePath);
            
            if (!Storage::disk('public')->exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan di server');
            }

            Log::info('File downloaded by admin', [
                'admin_id' => session('admin_id'),
                'response_id' => $responseId,
                'filename' => $originalFilename,
                'survey_id' => $response->survey_id
            ]);

            return Storage::disk('public')->download($filePath, $originalFilename);

        } catch (\Exception $e) {
            Log::error('File download error', [
                'response_id' => $responseId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mendownload file');
        }
    }

    public function viewFile($responseId)
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        try {
            $response = SurveyResponse::findOrFail($responseId);
            
            $question = $response->question;
            
            if (!$question || $question->question_type !== 'file_upload' || !$response->answer_data) {
                abort(404);
            }

            if (!is_array($response->answer_data) || !isset($response->answer_data['path'])) {
                abort(404);
            }

            $filePath = $response->answer_data['path'];
            
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404);
            }

            return Storage::disk('public')->response($filePath);

        } catch (\Exception $e) {
            abort(404);
        }
    }

    private function getIndividualData($totalSurveys, $questions)
    {
        $surveys = Survey::with(['responses'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $surveys->each(function($survey) {
            $survey->responses->each(function($response) {
                $question = $response->question;
            });
        });

        return view('admin.jawaban-individual', compact(
            'totalSurveys',
            'questions',
            'surveys'
        ));
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_name', 'admin_role']);
        return redirect()->route('admin.login');
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