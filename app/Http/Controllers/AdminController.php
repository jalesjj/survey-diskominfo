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
            
            return redirect()->route('admin.jawaban');
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
        return view('admin.jawaban', compact('totalSurveys', 'questions'));
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
                // PERBAIKAN: Untuk tipe lainnya (text, textarea, dll) - tampilkan SEMUA jawaban
                $stats['sample_responses'] = $validResponses->map(function($response) {
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
                // PERBAIKAN: Untuk tipe lainnya (text, textarea, dll) - tampilkan SEMUA jawaban
                $stats['sample_responses'] = $validResponses->map(function($response) {
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
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        try {
            $response = SurveyResponse::findOrFail($responseId);
            
            if ($response->question->question_type !== 'file_upload' || !$response->answer_data) {
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
        $surveys = Survey::with(['responses' => function($query) {
                $query->with('question');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
            // Ambil semua pertanyaan yang aktif, diurutkan
            $questions = SurveyQuestion::active()->ordered()->get();
            
            if ($questions->isEmpty()) {
                return back()->with('error', 'Tidak ada pertanyaan yang tersedia untuk diekspor.');
            }

            // Ambil semua survey dengan responses
            $surveys = Survey::with(['responses.question'])->orderBy('created_at', 'asc')->get();
            
            if ($surveys->isEmpty()) {
                return back()->with('error', 'Tidak ada data survey untuk diekspor.');
            }

            // Buat Spreadsheet baru
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set judul sheet
            $sheet->setTitle('Data Survey');
            
            // Header Row
            $headerRow = ['ID Survey', 'Tanggal Pengisian'];
            foreach ($questions as $question) {
                $headerRow[] = $question->question_text;
            }
            
            // Tulis header
            $sheet->fromArray($headerRow, null, 'A1');
            
            // Style header
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
            
            // Set tinggi baris header
            $sheet->getRowDimension(1)->setRowHeight(30);
            
            // Data Rows
            $rowNumber = 2;
            foreach ($surveys as $survey) {
                $rowData = [
                    $survey->id,
                    $survey->created_at->format('m/d/Y h:i:s A')
                ];
                
                // Buat mapping response berdasarkan question_id
                $responseMap = [];
                foreach ($survey->responses as $response) {
                    $responseMap[$response->question_id] = $response;
                }
                
                // Isi jawaban untuk setiap pertanyaan
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
                                if ($response->answer_data && isset($response->answer_data['filename'])) {
                                    $answer = $response->answer_data['filename'];
                                } else {
                                    $answer = $response->answer ?: '-';
                                }
                                break;
                                
                            default:
                                $answer = $response->answer ?: '-';
                                break;
                        }
                    }
                    
                    $rowData[] = $answer;
                }
                
                // Tulis data row
                $sheet->fromArray($rowData, null, 'A' . $rowNumber);
                
                // Style data row
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
            
            // Auto-size columns
            $sheet->getColumnDimension('A')->setWidth(12); // ID Survey
            $sheet->getColumnDimension('B')->setWidth(22); // Tanggal Pengisian
            
            // Set width untuk kolom pertanyaan
            for ($col = 3; $col <= count($headerRow); $col++) {
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->getColumnDimension($columnLetter)->setWidth(35);
            }
            
            // Freeze header row
            $sheet->freezePane('A2');
            
            // Generate filename
            $filename = 'survey_export_' . date('Y-m-d_His') . '.xlsx';
            
            // Set header untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Write file ke output
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

    // Method untuk melihat semua file yang diupload
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

    // Method untuk menghapus survey individual
    public function deleteSurvey($id)
    {
        // Cek apakah admin sudah login
        if (!session('admin_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            DB::beginTransaction();

            $survey = Survey::findOrFail($id);
            
            // Hapus semua file yang terkait dengan survey ini
            $fileResponses = SurveyResponse::where('survey_id', $id)
                ->whereHas('question', function($query) {
                    $query->where('question_type', 'file_upload');
                })
                ->whereNotNull('answer_data')
                ->get();

            foreach ($fileResponses as $response) {
                if ($response->answer_data && isset($response->answer_data['file_path'])) {
                    $filePath = $response->answer_data['file_path'];
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }

            // Hapus semua responses terkait
            SurveyResponse::where('survey_id', $id)->delete();
            
            // Hapus survey
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