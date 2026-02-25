<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

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
            // Set session untuk admin dengan role
            session([
                'admin_id' => $admin->id, 
                'admin_name' => $admin->name,
                'admin_role' => $admin->role
            ]);
            
            // Update last login
            $admin->update(['last_login_at' => now()]);
            
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    public function dashboard(Request $request)
{
    // Cek apakah admin sudah login
    if (!session('admin_id')) {
        return redirect()->route('admin.login');
    }

    $tab = $request->get('tab', 'questions'); // Default ke tab questions

    // Data dasar yang dibutuhkan semua tab
    $totalSurveys = Survey::count();
    $questions = SurveyQuestion::active()->with(['section', 'responses'])->ordered()->get();
    
    // Data berdasarkan tab yang dipilih
    switch ($tab) {
        case 'individual':
            return $this->getIndividualData($totalSurveys, $questions);
        case 'sections':
            return $this->getSectionData($totalSurveys, $questions);
        default: // questions
            return $this->getQuestionsData($totalSurveys, $questions);
    }
}

    // Method baru untuk mengambil detail survei individual
    public function getSurveyDetail($id)
    {
        // Cek apakah admin sudah login
        if (!session('admin_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $survey = Survey::with([
                'responses' => function($query) {
                    $query->with(['question' => function($q) {
                        $q->with('section');
                    }]);
                }
            ])->findOrFail($id);

            // Group responses by section
            $responsesBySection = $survey->responses->groupBy(function($response) {
                return $response->question->section_id ?? 'no_section';
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
                $section = $responses->first()->question->section ?? null;
                
                $sectionData = [
                    'title' => $section ? $section->title : 'Tanpa Bagian',
                    'description' => $section ? $section->description : '',
                    'responses' => []
                ];

                foreach ($responses as $response) {
                    $responseData = [
                        'response_id' => $response->id,
                        'question_text' => $response->question->question_text,
                        'question_type' => $response->question->question_type,
                        'question_type_label' => $response->question->getQuestionTypeLabel(),
                        'is_required' => $response->question->is_required,
                        'answer' => $response->answer,
                        'answer_data' => $response->answer_data
                    ];

                    // Format jawaban berdasarkan tipe pertanyaan
                    if ($response->question->question_type === 'checkbox' && $response->answer_data) {
                        $responseData['formatted_answer'] = is_array($response->answer_data) 
                            ? implode(', ', $response->answer_data) 
                            : $response->answer;
                    } elseif ($response->question->question_type === 'file_upload' && $response->answer_data) {
                        $responseData['formatted_answer'] = $response->answer_data['filename'] ?? $response->answer;
                        $responseData['file_info'] = $response->answer_data;
                    } elseif ($response->question->question_type === 'linear_scale') {
                        $responseData['formatted_answer'] = $response->answer;
                        $responseData['scale_info'] = [
                            'min' => $response->question->settings['scale_min'] ?? 1,
                            'max' => $response->question->settings['scale_max'] ?? 5,
                            'min_label' => $response->question->settings['scale_min_label'] ?? '',
                            'max_label' => $response->question->settings['scale_max_label'] ?? ''
                        ];
                    } else {
                        $responseData['formatted_answer'] = $response->answer;
                    }

                    $sectionData['responses'][] = $responseData;
                }

                $detailData['sections'][] = $sectionData;
            }

            return response()->json($detailData);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }

    private function getQuestionsData($totalSurveys, $questions)
{
    // Jika tidak ada questions, return view sederhana
    if ($questions->isEmpty()) {
        return view('admin.dashboard', compact('totalSurveys', 'questions'));
    }

    // Ambil semua sections beserta pertanyaannya
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

    $sectionStats = [];
    
    foreach ($sections as $section) {
        $sectionQuestionStats = [];
        
        foreach ($section->questions as $question) {
            // Pastikan hanya menghitung responses dari survey yang masih ada
            $validResponses = $question->responses()->whereHas('survey')->get();
            
            $stats = [
                'question' => $question,
                'total_responses' => $validResponses->count(),
                'response_rate' => $totalSurveys > 0 ? round(($validResponses->count() / $totalSurveys) * 100, 1) : 0
            ];

            // Analisis jawaban berdasarkan tipe pertanyaan - SAMA dengan getSectionData
            if ($question->question_type === 'multiple_choice' && $validResponses->count() > 0) {
                $answers = $validResponses->pluck('answer')->filter();
                $distribution = $answers->countBy();
                
                // Format data untuk chart
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
                
                // Format data untuk chart
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
                
                // Format data untuk chart
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
                
            } elseif ($question->question_type === 'linear_scale' && $validResponses->count() > 0) {
                $responses = $validResponses->pluck('answer')->filter()->map(function($item) {
                    return (int) $item;
                });
                
                $distribution = $responses->countBy();
                
                $stats['data'] = [
                    'average' => round($responses->avg(), 2),
                    'min' => $responses->min(),
                    'max' => $responses->max(),
                    'distribution' => $distribution->toArray()
                ];
                
                $stats['response_data'] = [
                    'average' => round($responses->avg(), 2),
                    'min' => $responses->min(),
                    'max' => $responses->max(),
                    'distribution' => $distribution->toArray(),
                    'total_responses' => $responses->count()
                ];
                $stats['chart_enabled'] = true;
                
            } elseif ($question->question_type === 'file_upload' && $validResponses->count() > 0) {
                $fileResponses = $validResponses->filter(function($response) {
                    return $response->answer_data !== null;
                });
                
                $stats['response_data'] = $fileResponses->map(function($response) {
                    return [
                        'response_id' => $response->id,
                        'filename' => $response->answer,
                        'upload_date' => $response->created_at,
                        'file_data' => $response->answer_data
                    ];
                })->toArray();
                $stats['chart_enabled'] = false;
                
            } else {
                // Untuk tipe lainnya (text, textarea, dll)
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

    return view('admin.dashboard', compact(
        'totalSurveys',
        'questions',
        'sectionStats'
    ));
}

private function getSectionData($totalSurveys, $questions)
{
    $sections = SurveySection::active()
                            ->ordered()
                            ->with(['questions' => function($query) {
                                $query->active()->ordered();
                            }])
                            ->get();

    $sections = $sections->filter(function($section) {
        return $section->questions->count() > 0;
    });

    $sectionStats = [];
    
    foreach ($sections as $section) {
        $sectionQuestionStats = [];
        
        foreach ($section->questions as $question) {
            $validResponses = $question->responses()->whereHas('survey')->get();
            
            $stats = [
                'question' => $question,
                'total_responses' => $validResponses->count(),
                'response_rate' => $totalSurveys > 0 ? round(($validResponses->count() / $totalSurveys) * 100, 1) : 0
            ];

            // Analisis jawaban berdasarkan tipe pertanyaan untuk chart
            if ($question->question_type === 'multiple_choice' && $validResponses->count() > 0) {
                $answers = $validResponses->pluck('answer')->filter();
                $distribution = $answers->countBy();
                
                // Format data untuk chart
                $chartData = collect();
                foreach ($distribution as $answer => $count) {
                    $chartData->push((object)[
                        'answer' => $answer,
                        'count' => $count
                    ]);
                }
                $stats['response_data'] = $chartData;
                $stats['chart_enabled'] = true;
                
            } elseif ($question->question_type === 'checkbox' && $validResponses->count() > 0) {
                $allAnswers = [];
                foreach ($validResponses as $response) {
                    if ($response->answer_data && is_array($response->answer_data)) {
                        $allAnswers = array_merge($allAnswers, $response->answer_data);
                    }
                }
                $distribution = array_count_values($allAnswers);
                
                // Format data untuk chart
                $chartData = collect();
                foreach ($distribution as $answer => $count) {
                    $chartData->push((object)[
                        'answer' => $answer,
                        'count' => $count
                    ]);
                }
                $stats['response_data'] = $chartData;
                $stats['chart_enabled'] = true;
                
            } elseif ($question->question_type === 'dropdown' && $validResponses->count() > 0) {
                $answers = $validResponses->pluck('answer')->filter();
                $distribution = $answers->countBy();
                
                // Format data untuk chart
                $chartData = collect();
                foreach ($distribution as $answer => $count) {
                    $chartData->push((object)[
                        'answer' => $answer,
                        'count' => $count
                    ]);
                }
                $stats['response_data'] = $chartData;
                $stats['chart_enabled'] = true;
                
            } elseif ($question->question_type === 'linear_scale' && $validResponses->count() > 0) {
                $responses = $validResponses->pluck('answer')->filter()->map(function($item) {
                    return (int) $item;
                });
                
                $distribution = $responses->countBy();
                
                $stats['response_data'] = [
                    'average' => round($responses->avg(), 2),
                    'min' => $responses->min(),
                    'max' => $responses->max(),
                    'distribution' => $distribution->toArray(),
                    'total_responses' => $responses->count()
                ];
                $stats['chart_enabled'] = true;
                
            } elseif ($question->question_type === 'file_upload' && $validResponses->count() > 0) {
                $fileResponses = $validResponses->filter(function($response) {
                    return $response->answer_data !== null;
                });
                
                $stats['response_data'] = $fileResponses->map(function($response) {
                    return [
                        'response_id' => $response->id,
                        'filename' => $response->answer,
                        'upload_date' => $response->created_at,
                        'file_data' => $response->answer_data
                    ];
                })->toArray();
                $stats['chart_enabled'] = false;
                
            } else {
                // Untuk tipe lainnya (text, textarea, dll) - tidak mendukung chart
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

    return view('admin.dashboard-sections', compact(
        'totalSurveys',
        'questions', 
        'sectionStats'
    ));
}

    // Method untuk download file yang diupload responden
    public function downloadFile($responseId)
    {
        // Cek apakah admin sudah login
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        try {
            $response = SurveyResponse::findOrFail($responseId);
            
            // Pastikan ini adalah response file upload
            if ($response->question->question_type !== 'file_upload' || !$response->answer_data) {
                abort(404, 'File tidak ditemukan');
            }

            $filePath = $response->answer_data['path'];
            $originalFilename = $response->answer_data['filename'] ?? basename($filePath);
            
            // Cek apakah file exists di storage
            if (!Storage::disk('public')->exists($filePath)) {
                return redirect()->back()->with('error', 'File tidak ditemukan di server');
            }

            // Log download activity
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

    // Method untuk preview gambar (opsional)
    public function viewFile($responseId)
    {
        // Cek apakah admin sudah login
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        try {
            $response = SurveyResponse::findOrFail($responseId);
            
            if ($response->question->question_type !== 'file_upload' || !$response->answer_data) {
                abort(404, 'File tidak ditemukan');
            }

            $filePath = $response->answer_data['path'];
            
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, 'File tidak ditemukan di server');
            }

            $mimeType = $response->answer_data['mime_type'] ?? 'application/octet-stream';
            
            return Storage::disk('public')->response($filePath, null, [
                'Content-Type' => $mimeType
            ]);

        } catch (\Exception $e) {
            abort(404, 'File tidak dapat ditampilkan');
        }
    }

    // Method untuk mendapatkan daftar semua file yang diupload
    public function getUploadedFiles()
    {
        // Cek apakah admin sudah login
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $fileResponses = SurveyResponse::whereHas('question', function($query) {
                $query->where('question_type', 'file_upload');
            })
            ->whereNotNull('answer_data')
            ->with(['question', 'survey'])
            ->latest()
            ->paginate(20);

        return view('admin.uploaded-files', compact('fileResponses'));
    }

    private function getIndividualData($totalSurveys, $questions)
    {
        // Data individual responden
        $surveys = Survey::with(['responses.question.section'])
            ->latest()
            ->paginate(20);

        return view('admin.dashboard-individual', compact(
            'totalSurveys',
            'questions',
            'surveys'
        ));
    }

    public function export()
{
    // Cek apakah admin sudah login
    if (!session('admin_id')) {
        return redirect()->route('admin.login');
    }

    // Ambil data dengan relasi yang diperlukan, diurutkan berdasarkan tanggal terbaru
    $surveys = Survey::with(['responses.question'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    // PERBAIKAN: Ambil pertanyaan yang aktif dan diurutkan berdasarkan bagian terlebih dahulu, 
    // kemudian berdasarkan urutan dalam bagian
    $questions = SurveyQuestion::with('section')
        ->join('survey_sections', 'survey_questions.section_id', '=', 'survey_sections.id')
        ->where('survey_questions.is_active', true)  // Spesifik tabel survey_questions
        ->where('survey_sections.is_active', true)   // Spesifik tabel survey_sections
        ->orderBy('survey_sections.order_index', 'asc')  // Urutkan bagian dulu
        ->orderBy('survey_questions.order_index', 'asc') // Kemudian urutkan pertanyaan dalam bagian
        ->select('survey_questions.*') // Pilih hanya kolom dari survey_questions
        ->get();
    
    // Header CSV yang konsisten
    $headers = [
        'ID Survei',
        'Tanggal Pengisian'
    ];
    
    // Tambahkan header untuk setiap pertanyaan berdasarkan urutan yang benar
    foreach ($questions as $question) {
        // Bersihkan teks pertanyaan untuk header
        $questionText = strip_tags($question->question_text);
        $questionText = str_replace(["\r", "\n", "\t"], ' ', $questionText);
        $questionText = trim($questionText);
        
        // OPSIONAL: Tambahkan nama bagian di depan pertanyaan untuk kejelasan
        if ($question->section) {
            $sectionName = strip_tags($question->section->title);
            $sectionName = str_replace(["\r", "\n", "\t"], ' ', $sectionName);
            $sectionName = trim($sectionName);
            $questionText = "[$sectionName] $questionText";
        }
        
        $headers[] = $questionText;
    }

    // Buat header CSV dengan encoding dan separator yang benar
    $csvData = "\xEF\xBB\xBF"; // BOM untuk UTF-8
    
    // Gunakan semicolon sebagai delimiter untuk kompatibilitas Excel yang lebih baik
    $csvData .= implode(';', array_map(function($header) {
        // Escape tanda kutip dan semicolon
        $header = str_replace('"', '""', $header);
        $header = str_replace(';', ',', $header); // Ganti semicolon dalam data dengan koma
        return '"' . $header . '"';
    }, $headers)) . "\n";
    
    // Buat baris data
    foreach ($surveys as $survey) {
        $row = [
            $survey->id,
            $survey->created_at->format('Y-m-d H:i:s')
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
                        } else {
                            $answer = $response->answer ?: '-';
                        }
                        break;
                        
                    case 'checkbox':
                        // Untuk checkbox multiple, gabungkan jawaban
                        if ($response->answer_data && is_array($response->answer_data)) {
                            $answer = implode(' | ', $response->answer_data); // Gunakan | sebagai separator
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

        // Escape semua field untuk CSV dengan semicolon delimiter
        $csvRow = implode(';', array_map(function($field) {
            // Escape tanda kutip dan semicolon
            $field = str_replace('"', '""', $field);
            $field = str_replace(';', ',', $field); // Ganti semicolon dalam data dengan koma
            return '"' . $field . '"';
        }, $row));
        
        $csvData .= $csvRow . "\n";
    }

    // Generate filename yang unik
    $filename = 'survei-kepuasan-admin-' . date('Y-m-d_H-i-s') . '.csv';

    return response($csvData)
        ->header('Content-Type', 'text/csv; charset=UTF-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Cache-Control', 'no-cache, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}

    public function logout()
    {
        session()->forget(['admin_id', 'admin_name', 'admin_role']);
        return redirect()->route('admin.login')->with('message', 'Berhasil logout.');
    }

    public function deleteSurvey($id)
    {
        // Cek apakah admin sudah login
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        $survey = Survey::findOrFail($id);
        $surveyName = $survey->nama; // Akan menggunakan accessor untuk mendapatkan nama dari response
        $survey->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Data survei ' . $surveyName . ' berhasil dihapus.');
    }
}