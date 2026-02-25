<?php
// app/Http/Controllers/SurveyQuestionController.php - FINAL FIXED VERSION
namespace App\Http\Controllers;

use App\Models\SurveySection;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;

class SurveyQuestionController extends Controller
{
    // Middleware untuk cek login admin
    public function __construct()
    {
        // Cek session admin di setiap method
    }

    private function checkAdminAuth()
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }
        return null;
    }

    // Halaman utama manajemen pertanyaan
    public function index()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $sections = SurveySection::with(['allQuestions' => function($query) {
            $query->orderBy('order_index');
        }])->ordered()->get();

        return view('admin.questions.index', compact('sections'));
    }

    // Form tambah bagian baru
    public function createSection()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        return view('admin.questions.create-section');
    }

    // Simpan bagian baru
    public function storeSection(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $maxOrder = SurveySection::max('order_index') ?? 0;

        SurveySection::create([
            'title' => $request->title,
            'description' => $request->description,
            'order_index' => $maxOrder + 1
        ]);

        return redirect()->route('admin.questions.index')
                        ->with('success', 'Bagian baru berhasil ditambahkan.');
    }

    // Form tambah pertanyaan
    public function createQuestion($sectionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $section = SurveySection::findOrFail($sectionId);
        
        return view('admin.questions.create-question', compact('section'));
    }

    // Simpan pertanyaan baru
    public function storeQuestion(Request $request, $sectionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $section = SurveySection::findOrFail($sectionId);

        // Validasi dasar
        $validationRules = [
            'question_text' => 'required|string',
            'question_description' => 'nullable|string|max:1000',
            'question_type' => 'required|in:short_text,long_text,multiple_choice,checkbox,dropdown,file_upload,linear_scale',
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'scale_min' => 'nullable|integer|min:1',
            'scale_max' => 'nullable|integer|max:10',
            'scale_min_label' => 'nullable|string',
            'scale_max_label' => 'nullable|string',
            // SAW fields validation
            'enable_saw' => 'boolean',
            'criteria_selection' => 'nullable|string',
            'criteria_name' => 'nullable|string|max:255',
            'criteria_weight' => 'nullable|numeric|min:0.1|max:10',
            'criteria_type' => 'nullable|in:benefit,cost'
        ];

        // Conditional validation untuk SAW
        if ($request->boolean('enable_saw') && $request->question_type === 'linear_scale') {
            $validationRules['criteria_selection'] = 'required|string';
            $validationRules['criteria_weight'] = 'required|numeric|min:0.1|max:10';
            $validationRules['criteria_type'] = 'required|in:benefit,cost';
            
            if ($request->criteria_selection === 'new') {
                $validationRules['criteria_name'] = 'required|string|max:255';
            }
        }

        $request->validate($validationRules);

        $maxOrder = SurveyQuestion::where('section_id', $sectionId)->max('order_index') ?? 0;

        $options = null;
        $settings = [];

        // Handle options untuk question type tertentu
        if (in_array($request->question_type, ['multiple_choice', 'checkbox', 'dropdown'])) {
            $options = array_filter($request->options ?? []);
        }

        // Handle settings untuk linear scale
        if ($request->question_type === 'linear_scale') {
            $settings = [
                'scale_min' => $request->scale_min ?? 1,
                'scale_max' => $request->scale_max ?? 5,
                'scale_min_label' => $request->scale_min_label,
                'scale_max_label' => $request->scale_max_label
            ];
        }

        // Prepare SAW fields
        $sawFields = [];
        
        if ($request->boolean('enable_saw') && $request->question_type === 'linear_scale') {
            $sawFields['enable_saw'] = true;
            $sawFields['criteria_weight'] = $request->criteria_weight;
            $sawFields['criteria_type'] = $request->criteria_type;
            
            if ($request->criteria_selection === 'new') {
                // Gunakan kriteria baru
                $sawFields['criteria_name'] = $request->criteria_name;
            } else {
                // Gunakan kriteria yang sudah ada
                $sawFields['criteria_name'] = $request->criteria_selection;
            }
        } else {
            $sawFields['enable_saw'] = false;
            $sawFields['criteria_name'] = null;
            $sawFields['criteria_weight'] = null;
            $sawFields['criteria_type'] = null;
        }

        // Create question dengan SAW fields
        SurveyQuestion::create(array_merge([
            'section_id' => $sectionId,
            'question_text' => $request->question_text,
            'question_description' => $request->question_description,
            'question_type' => $request->question_type,
            'options' => $options,
            'settings' => $settings,
            'order_index' => $maxOrder + 1,
            'is_required' => $request->boolean('is_required')
        ], $sawFields));

        return redirect()->route('admin.questions.index')
                        ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    // Edit pertanyaan
    public function editQuestion($questionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $question = SurveyQuestion::with('section')->findOrFail($questionId);
        
        return view('admin.questions.edit-question', compact('question'));
    }

    // Update pertanyaan - FINAL FIXED VERSION
    public function updateQuestion(Request $request, $questionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $question = SurveyQuestion::findOrFail($questionId);

        // SOLUSI: Handle criteria_type dari backup field jika field utama disabled
        if ($request->has('criteria_type_backup') && !$request->filled('criteria_type')) {
            $request->merge(['criteria_type' => $request->criteria_type_backup]);
        }

        // Validasi dasar
        $validationRules = [
            'question_text' => 'required|string',
            'question_description' => 'nullable|string|max:1000',
            'question_type' => 'required|in:short_text,long_text,multiple_choice,checkbox,dropdown,file_upload,linear_scale',
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'scale_min' => 'nullable|integer|min:1',
            'scale_max' => 'nullable|integer|max:10',
            'scale_min_label' => 'nullable|string',
            'scale_max_label' => 'nullable|string',
            // SAW fields validation
            'enable_saw' => 'boolean',
            'criteria_selection' => 'nullable|string',
            'criteria_name' => 'nullable|string|max:255',
            'criteria_weight' => 'nullable|numeric|min:0.1|max:10',
            'criteria_type' => 'nullable|in:benefit,cost'
        ];

        // Conditional validation untuk SAW
        if ($request->boolean('enable_saw') && $request->question_type === 'linear_scale') {
            $validationRules['criteria_selection'] = 'required|string';
            $validationRules['criteria_weight'] = 'required|numeric|min:0.1|max:10';
            $validationRules['criteria_type'] = 'required|in:benefit,cost';
            
            if ($request->criteria_selection === 'new') {
                $validationRules['criteria_name'] = 'required|string|max:255';
            }
        }

        $request->validate($validationRules);

        $options = null;
        $settings = [];

        // Handle options
        if (in_array($request->question_type, ['multiple_choice', 'checkbox', 'dropdown'])) {
            $options = array_filter($request->options ?? []);
        }

        // Handle linear scale settings
        if ($request->question_type === 'linear_scale') {
            $settings = [
                'scale_min' => $request->scale_min ?? 1,
                'scale_max' => $request->scale_max ?? 5,
                'scale_min_label' => $request->scale_min_label,
                'scale_max_label' => $request->scale_max_label
            ];
        }

        // Prepare SAW fields
        $sawFields = [];
        
        if ($request->boolean('enable_saw') && $request->question_type === 'linear_scale') {
            $sawFields['enable_saw'] = true;
            $sawFields['criteria_weight'] = $request->criteria_weight;
            $sawFields['criteria_type'] = $request->criteria_type; // Sudah dihandle dari backup
            
            if ($request->criteria_selection === 'new') {
                // Gunakan kriteria baru
                $sawFields['criteria_name'] = $request->criteria_name;
            } else {
                // Gunakan kriteria yang sudah ada
                $sawFields['criteria_name'] = $request->criteria_selection;
            }
        } else {
            $sawFields['enable_saw'] = false;
            $sawFields['criteria_name'] = null;
            $sawFields['criteria_weight'] = null;
            $sawFields['criteria_type'] = null;
        }

        // Update question dengan SAW fields
        $question->update(array_merge([
            'question_text' => $request->question_text,
            'question_description' => $request->question_description,
            'question_type' => $request->question_type,
            'options' => $options,
            'settings' => $settings,
            'is_required' => $request->boolean('is_required')
        ], $sawFields));

        return redirect()->route('admin.questions.index')
                        ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    // Hapus pertanyaan
    public function deleteQuestion($questionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $question = SurveyQuestion::findOrFail($questionId);
        $question->delete();

        return redirect()->route('admin.questions.index')
                        ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    // Toggle status pertanyaan
    public function toggleQuestion($questionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $question = SurveyQuestion::findOrFail($questionId);
        $question->update(['is_active' => !$question->is_active]);

        $status = $question->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.questions.index')
                        ->with('success', "Pertanyaan berhasil {$status}.");
    }

    // Hapus section
    public function deleteSection($sectionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $section = SurveySection::findOrFail($sectionId);
        $section->delete(); // Akan menghapus pertanyaan juga karena foreign key cascade

        return redirect()->route('admin.questions.index')
                        ->with('success', 'Bagian dan semua pertanyaannya berhasil dihapus.');
    }

    // Toggle status section
    public function toggleSection($sectionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $section = SurveySection::findOrFail($sectionId);
        $section->update(['is_active' => !$section->is_active]);

        $status = $section->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.questions.index')
                        ->with('success', "Bagian berhasil {$status}.");
    }

    // Update urutan section
    public function updateSectionOrder(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $request->validate([
            'sections' => 'required|array',
            'sections.*' => 'required|integer|exists:survey_sections,id'
        ]);

        foreach ($request->sections as $index => $sectionId) {
            SurveySection::where('id', $sectionId)->update(['order_index' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    // Update urutan pertanyaan dalam section
    public function updateQuestionOrder(Request $request, $sectionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'required|integer|exists:survey_questions,id'
        ]);

        foreach ($request->questions as $index => $questionId) {
            SurveyQuestion::where('id', $questionId)
                        ->where('section_id', $sectionId)
                        ->update(['order_index' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}