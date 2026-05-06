<?php
// app/Http/Controllers/SurveyQuestionController.php
namespace App\Http\Controllers;

use App\Models\SurveyPeriod;
use App\Models\SurveySection;
use App\Models\SurveyQuestion;
use App\Helpers\SurveyDefaults;
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

    // Ambil sections dari database
    $dbSections = SurveySection::with(['allQuestions' => function($query) {
        $query->orderBy('order_index');
    }])->ordered()->get();

    // Tambahkan default section di awal
    $defaultSection = SurveyDefaults::getDefaultSection();
    $defaultQuestions = SurveyDefaults::getDefaultQuestions();
    
    // Set property allQuestions untuk default section
    $defaultSection->allQuestions = $defaultQuestions;
    
    // Gabungkan default section dengan sections dari database
    $sections = collect([$defaultSection])->merge($dbSections);

    // ✅ TAMBAHKAN INI: Cek status lock
    $isLocked = SurveyPeriod::isLocked();
    $activePeriod = SurveyPeriod::getActivePeriod();

    return view('admin.questions.index', compact('sections', 'isLocked', 'activePeriod'));
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

        // Cek jika section adalah default section
        if (SurveyDefaults::isPermanentSection($sectionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat menambah pertanyaan ke bagian Data Diri karena bagian ini permanen.');
        }

        $section = SurveySection::findOrFail($sectionId);
        
        return view('admin.questions.create-question', compact('section'));
    }

    // Simpan pertanyaan baru
    public function storeQuestion(Request $request, $sectionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Cek jika section adalah default section
        if (SurveyDefaults::isPermanentSection($sectionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat menambah pertanyaan ke bagian Data Diri karena bagian ini permanen.');
        }

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

        // Cek jika pertanyaan adalah default question
        if (SurveyDefaults::isPermanentQuestion($questionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat mengedit pertanyaan di bagian Data Diri karena pertanyaan ini permanen.');
        }

        $question = SurveyQuestion::with('section')->findOrFail($questionId);
        
        return view('admin.questions.edit-question', compact('question'));
    }

    // Update pertanyaan - FINAL FIXED VERSION
    public function updateQuestion(Request $request, $questionId)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Cek jika pertanyaan adalah default question
        if (SurveyDefaults::isPermanentQuestion($questionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat mengedit pertanyaan di bagian Data Diri karena pertanyaan ini permanen.');
        }

        $question = SurveyQuestion::findOrFail($questionId);

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

        // Cek jika pertanyaan adalah default question
        if (SurveyDefaults::isPermanentQuestion($questionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat menghapus pertanyaan di bagian Data Diri karena pertanyaan ini permanen.');
        }

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

        // Cek jika pertanyaan adalah default question
        if (SurveyDefaults::isPermanentQuestion($questionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat mengubah status pertanyaan di bagian Data Diri karena pertanyaan ini permanen.');
        }

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

        // Cek jika section adalah default section
        if (SurveyDefaults::isPermanentSection($sectionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat menghapus bagian Data Diri karena bagian ini permanen.');
        }

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

        // Cek jika section adalah default section
        if (SurveyDefaults::isPermanentSection($sectionId)) {
            return redirect()->route('admin.questions.index')
                            ->with('error', 'Tidak dapat mengubah status bagian Data Diri karena bagian ini permanen.');
        }

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

        // Cek jika section adalah default section
        if (SurveyDefaults::isPermanentSection($sectionId)) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mengubah urutan pertanyaan di bagian Data Diri.'], 403);
        }

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

    public function editSection($sectionId)
{
    $authCheck = $this->checkAdminAuth();
    if ($authCheck) return $authCheck;
 
    // Cek jika section adalah default section
    if (SurveyDefaults::isPermanentSection($sectionId)) {
        return redirect()->route('admin.questions.index')
                        ->with('error', 'Tidak dapat mengedit bagian Data Diri karena bagian ini permanen.');
    }
 
    $section = SurveySection::findOrFail($sectionId);
    
    return view('admin.questions.edit-section', compact('section'));
}
 
/**
 * Update bagian
 */
public function updateSection(Request $request, $sectionId)
{
    $authCheck = $this->checkAdminAuth();
    if ($authCheck) return $authCheck;
 
    // Cek jika section adalah default section
    if (SurveyDefaults::isPermanentSection($sectionId)) {
        return redirect()->route('admin.questions.index')
                        ->with('error', 'Tidak dapat mengedit bagian Data Diri karena bagian ini permanen.');
    }
 
    $section = SurveySection::findOrFail($sectionId);
 
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000'
    ]);
 
    $section->update([
        'title' => $request->title,
        'description' => $request->description
    ]);
 
    return redirect()->route('admin.questions.index')
                    ->with('success', 'Bagian berhasil diperbarui.');
}

public function showLockForm()
{
    $authCheck = $this->checkAdminAuth();
    if ($authCheck) return $authCheck;
 
    // Check if already locked
    if (SurveyPeriod::isLocked()) {
        $activePeriod = SurveyPeriod::getActivePeriod();
        return redirect()->route('admin.questions.index')
                        ->with('error', 'Sistem sudah terkunci untuk periode: ' . $activePeriod->period_name);
    }
 
    // Get total questions and sections for confirmation
    $totalSections = SurveySection::count();
    $totalQuestions = SurveyQuestion::count();
    $totalDefaultQuestions = count(SurveyDefaults::getDefaultQuestions());
    $totalAllQuestions = $totalQuestions + $totalDefaultQuestions;
 
    return view('admin.questions.lock-confirm', compact('totalSections', 'totalQuestions', 'totalDefaultQuestions', 'totalAllQuestions'));
}
 
/**
 * Lock the system with period
 */
public function lockSystem(Request $request)
{
    $authCheck = $this->checkAdminAuth();
    if ($authCheck) return $authCheck;
 
    // Check if already locked
    if (SurveyPeriod::isLocked()) {
        return redirect()->route('admin.questions.index')
                        ->with('error', 'Sistem sudah terkunci.');
    }
 
    // Validate
    $request->validate([
        'period_name' => 'required|string|max:255',
        'year' => 'required|integer|min:2020|max:2100',
        'description' => 'nullable|string|max:1000'
    ], [
        'period_name.required' => 'Nama periode harus diisi',
        'year.required' => 'Tahun harus diisi',
        'year.integer' => 'Tahun harus berupa angka',
        'year.min' => 'Tahun minimal 2020',
        'year.max' => 'Tahun maksimal 2100'
    ]);
 
    // Create period
    $period = SurveyPeriod::create([
        'survey_id' => 1, // Default survey ID
        'period_name' => $request->period_name,
        'year' => $request->year,
        'start_date' => now(),
        'end_date' => now()->addYear(), // Default 1 year
        'status' => 'active',
        'is_active' => true,
        'description' => $request->description
    ]);
 
    return redirect()->route('admin.questions.index')
                    ->with('success', 'Sistem berhasil dikunci untuk periode: ' . $period->period_name . '. Pertanyaan tidak dapat diubah sampai periode dibuka kembali.');
}
 
/**
 * Check if system is locked
 */
private function isSystemLocked()
{
    return SurveyPeriod::isLocked();
}

public function stopPeriod()
{
    $authCheck = $this->checkAdminAuth();
    if ($authCheck) return $authCheck;
 
    // Check if there's an active period
    $activePeriod = SurveyPeriod::getActivePeriod();
    
    if (!$activePeriod) {
        return redirect()->route('admin.questions.index')
                        ->with('error', 'Tidak ada periode aktif yang dapat dihentikan.');
    }
 
    // Update active period to closed
    $activePeriod->update([
        'is_active' => false,
        'status' => 'closed',
        'end_date' => now()
    ]);
 
    return redirect()->route('admin.questions.index')
                    ->with('success', 'Periode "' . $activePeriod->period_name . '" berhasil dihentikan. Sistem sekarang terbuka dan pertanyaan dapat diubah kembali.');
}

}