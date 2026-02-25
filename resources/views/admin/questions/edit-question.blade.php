{{-- resources/views/admin/questions/edit-question.blade.php - FIXED DISABLED FIELD SUBMISSION --}}
@extends('layouts.admin')

@section('title', 'Edit Pertanyaan - Admin Survei')
@section('active-questions', 'active')
@section('page-title', 'Edit Pertanyaan')
@section('page-subtitle', 'Perbarui pertanyaan: {{ $question->section->title }}')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.questions.index') }}">Pertanyaan</a>
    <span class="breadcrumb-separator">></span>
    <span>{{ $question->section->title }}</span>
    <span class="breadcrumb-separator">></span>
    <span>Edit Pertanyaan</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Form Styles */
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 900px;
        margin: 0 auto;
    }

    .form-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 12px 12px 0 0;
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-subtitle {
        font-size: 14px;
        opacity: 0.9;
    }

    .form-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }

    .form-input {
        width: 100%;
        padding: 15px 18px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fff;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #5a9b9e;
        box-shadow: 0 0 0 3px rgba(90, 155, 158, 0.1);
    }

    .form-input:disabled {
        background-color: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }

    .form-input[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-help {
        font-size: 14px;
        color: #7f8c8d;
        margin-top: 5px;
    }

    /* Question Type Cards */
    .question-type-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .type-card {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .type-card:hover {
        border-color: #5a9b9e;
        background: rgba(90, 155, 158, 0.05);
    }

    .type-card.selected {
        border-color: #5a9b9e;
        background: rgba(90, 155, 158, 0.1);
    }

    .type-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .type-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 16px;
    }

    .type-description {
        color: #7f8c8d;
        font-size: 14px;
        line-height: 1.4;
    }

    /* Options Container */
    .options-container, .scale-container {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
        display: none;
    }

    .options-container.show, .scale-container.show {
        display: block;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .option-input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .remove-option {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    .remove-option:hover {
        background: #c82333;
    }

    .add-option {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        margin-top: 10px;
    }

    .add-option:hover {
        background: #218838;
    }

    /* Scale Settings */
    .scale-settings {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }

    .scale-labels {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    /* SAW Toggle */
    .saw-toggle-container {
        background: #e8f4f8;
        border: 2px solid #bee5eb;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .toggle-switch {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        font-size: 14px;
        user-select: none;
        margin-bottom: 10px;
    }

    .toggle-switch input[type="checkbox"] {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: relative;
        width: 50px;
        height: 25px;
        margin-right: 12px;
        background-color: #ccc;
        border-radius: 25px;
        transition: 0.4s;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 19px;
        width: 19px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: 0.4s;
    }

    input:checked + .toggle-slider {
        background-color: #5a9b9e;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(25px);
    }

    .toggle-text {
        font-weight: 600;
        color: #2c3e50;
    }

    .toggle-description {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
        line-height: 1.4;
    }

    /* SAW Fields */
    .saw-fields {
        background: #f1f8ff;
        border: 2px solid #d4edda;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
        display: none;
    }

    .saw-fields.show {
        display: block;
    }

    .criteria-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 15px;
    }

    .new-criteria-fields {
        background: #fff3cd;
        border: 2px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }

    .new-criteria-fields.show {
        display: block;
    }

    /* Buttons */
    .form-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #e9ecef;
    }

    .btn {
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .btn-primary {
        background: #5a9b9e;
        color: white;
    }

    .btn-primary:hover {
        background: #4a8b8e;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(90, 155, 158, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    /* Checkbox */
    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }

    .checkbox-input {
        width: 18px;
        height: 18px;
        accent-color: #5a9b9e;
    }

    .checkbox-label {
        font-size: 16px;
        font-weight: 500;
        color: #2c3e50;
        cursor: pointer;
    }

    /* Error Messages */
    .error-message {
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-container {
            margin: 20px;
        }

        .form-body {
            padding: 20px;
        }

        .question-type-cards {
            grid-template-columns: 1fr;
        }

        .scale-settings,
        .scale-labels,
        .criteria-row {
            grid-template-columns: 1fr;
        }

        .form-buttons {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="admin-container">
    <div class="form-container">
        <div class="form-header">
            <h1 class="form-title">Edit Pertanyaan</h1>
            <p class="form-subtitle">Memperbarui pertanyaan dalam bagian: {{ $question->section->title }}</p>
        </div>

        <div class="form-body">
            <form id="questionForm" action="{{ route('admin.questions.update-question', $question->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Section Info -->
                <div class="form-group">
                    <label class="form-label">Bagian Survei</label>
                    <div style="padding: 12px 15px; background: #f8f9fa; border-radius: 6px; color: #2c3e50; font-weight: 500;">
                        {{ $question->section->title }}
                    </div>
                    @if($question->section->description)
                        <p class="form-help">{{ $question->section->description }}</p>
                    @endif
                </div>

                <!-- Question Text -->
                <div class="form-group">
                    <label class="form-label" for="question_text">Teks Pertanyaan *</label>
                    <textarea id="question_text" name="question_text" class="form-input form-textarea" placeholder="Masukkan pertanyaan yang akan ditanyakan kepada responden..." required>{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Question Description -->
                <div class="form-group">
                    <label class="form-label" for="question_description">Deskripsi/Bantuan (Opsional)</label>
                    <textarea id="question_description" name="question_description" class="form-input" rows="3" placeholder="Tambahkan deskripsi atau petunjuk untuk membantu responden memahami pertanyaan...">{{ old('question_description', $question->question_description) }}</textarea>
                    @error('question_description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Question Type -->
                <div class="form-group">
                    <label class="form-label">Jenis Pertanyaan *</label>
                    <div class="question-type-cards">
                        <label class="type-card" for="type_short_text">
                            <input type="radio" id="type_short_text" name="question_type" value="short_text" {{ old('question_type', $question->question_type) == 'short_text' ? 'checked' : '' }} onchange="toggleQuestionOptions()">
                            <div class="type-title">📝 Jawaban Singkat</div>
                            <div class="type-description">Responden dapat memberikan jawaban singkat dalam satu baris</div>
                        </label>

                        <label class="type-card" for="type_long_text">
                            <input type="radio" id="type_long_text" name="question_type" value="long_text" {{ old('question_type', $question->question_type) == 'long_text' ? 'checked' : '' }} onchange="toggleQuestionOptions()">
                            <div class="type-title">📄 Paragraf</div>
                            <div class="type-description">Responden dapat memberikan jawaban panjang dalam beberapa baris</div>
                        </label>

                        <label class="type-card" for="type_multiple_choice">
                            <input type="radio" id="type_multiple_choice" name="question_type" value="multiple_choice" {{ old('question_type', $question->question_type) == 'multiple_choice' ? 'checked' : '' }} onchange="toggleQuestionOptions()">
                            <div class="type-title">🔘 Pilihan Ganda</div>
                            <div class="type-description">Responden memilih satu jawaban dari beberapa pilihan</div>
                        </label>

                        <label class="type-card" for="type_checkbox">
                            <input type="radio" id="type_checkbox" name="question_type" value="checkbox" {{ old('question_type', $question->question_type) == 'checkbox' ? 'checked' : '' }} onchange="toggleQuestionOptions()">
                            <div class="type-title">☑️ Kotak Centang</div>
                            <div class="type-description">Responden dapat memilih beberapa jawaban sekaligus</div>
                        </label>

                        <label class="type-card" for="type_dropdown">
                            <input type="radio" id="type_dropdown" name="question_type" value="dropdown" {{ old('question_type', $question->question_type) == 'dropdown' ? 'checked' : '' }} onchange="toggleQuestionOptions()">
                            <div class="type-title">🔽 Drop-down</div>
                            <div class="type-description">Responden memilih satu jawaban dari daftar dropdown</div>
                        </label>

                        <label class="type-card" for="type_file_upload">
                            <input type="radio" id="type_file_upload" name="question_type" value="file_upload" {{ old('question_type', $question->question_type) == 'file_upload' ? 'checked' : '' }} onchange="toggleQuestionOptions()">
                            <div class="type-title">📎 Upload File</div>
                            <div class="type-description">Responden dapat mengunggah dokumen atau gambar</div>
                        </label>

                        <label class="type-card" for="type_linear_scale">
                            <input type="radio" id="type_linear_scale" name="question_type" value="linear_scale" {{ old('question_type', $question->question_type) == 'linear_scale' ? 'checked' : '' }} onchange="toggleQuestionOptions()">
                            <div class="type-title">📊 Skala Linier</div>
                            <div class="type-description">Responden memberikan penilaian dengan skala angka</div>
                        </label>
                    </div>
                    @error('question_type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Options for Multiple Choice, Checkbox, Dropdown -->
                <div id="optionsContainer" class="options-container">
                    <label class="form-label">Pilihan Jawaban *</label>
                    <div id="optionsList">
                        @php
                            $existingOptions = old('options', $question->options ?? []);
                        @endphp
                        @if(!empty($existingOptions))
                            @foreach($existingOptions as $index => $option)
                                <div class="option-item">
                                    <input type="text" name="options[]" class="option-input" value="{{ $option }}" placeholder="Opsi {{ $index + 1 }}">
                                    <button type="button" class="remove-option" onclick="removeOption(this)">Hapus</button>
                                </div>
                            @endforeach
                        @else
                            <div class="option-item">
                                <input type="text" name="options[]" class="option-input" placeholder="Opsi 1">
                                <button type="button" class="remove-option" onclick="removeOption(this)">Hapus</button>
                            </div>
                            <div class="option-item">
                                <input type="text" name="options[]" class="option-input" placeholder="Opsi 2">
                                <button type="button" class="remove-option" onclick="removeOption(this)">Hapus</button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="add-option" onclick="addOption()">+ Tambah Opsi</button>
                </div>

                <!-- Scale Settings for Linear Scale -->
                <div id="scaleContainer" class="scale-container">
                    <label class="form-label">Pengaturan Skala</label>
                    
                    <div class="scale-settings">
                        <div>
                            <label class="form-label" for="scale_min">Nilai Minimum</label>
                            <select id="scale_min" name="scale_min" class="form-input">
                                @for($i = 0; $i <= 1; $i++)
                                    <option value="{{ $i }}" {{ old('scale_min', $question->settings['scale_min'] ?? 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="scale_max">Nilai Maksimum</label>
                            <select id="scale_max" name="scale_max" class="form-input">
                                @for($i = 2; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('scale_max', $question->settings['scale_max'] ?? 5) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="scale-labels">
                        <div>
                            <label class="form-label" for="scale_min_label">Label Minimum (Opsional)</label>
                            <input type="text" id="scale_min_label" name="scale_min_label" class="form-input" placeholder="contoh: Sangat Tidak Setuju" value="{{ old('scale_min_label', $question->settings['scale_min_label'] ?? '') }}">
                        </div>
                        <div>
                            <label class="form-label" for="scale_max_label">Label Maksimum (Opsional)</label>
                            <input type="text" id="scale_max_label" name="scale_max_label" class="form-input" placeholder="contoh: Sangat Setuju" value="{{ old('scale_max_label', $question->settings['scale_max_label'] ?? '') }}">
                        </div>
                    </div>

                    <!-- SAW Toggle -->
                    <div class="saw-toggle-container">
                        <label class="toggle-switch" for="enable_saw">
                            <input type="checkbox" id="enable_saw" name="enable_saw" value="1" {{ old('enable_saw', $question->enable_saw) ? 'checked' : '' }} onchange="toggleSAW()">
                            <div class="toggle-slider"></div>
                            <span class="toggle-text">Aktifkan Perhitungan SAW (Simple Additive Weighting)</span>
                        </label>
                        <p class="toggle-description">Jika diaktifkan, pertanyaan ini akan digunakan dalam perhitungan Sistem Pendukung Keputusan menggunakan metode Simple Additive Weighting untuk ranking hasil survei.</p>
                    </div>

                    <!-- SAW Fields -->
                    <div id="sawFields" class="saw-fields">
                        <div class="form-group">
                            <label class="form-label" for="criteria_selection">Kriteria *</label>
                            <select id="criteria_selection" name="criteria_selection" class="form-input" onchange="handleCriteriaChange()">
                                <option value="">-- Pilih Kriteria --</option>
                                <option value="new" {{ old('criteria_selection', !$question->criteria_name ? 'new' : '') == 'new' ? 'selected' : '' }}>+ Buat Kriteria Baru</option>
                                @php
                                    $existingCriteria = \App\Models\SurveyQuestion::where('question_type', 'linear_scale')
                                        ->whereNotNull('criteria_name')
                                        ->select('criteria_name', 'criteria_weight', 'criteria_type')
                                        ->distinct()
                                        ->get();
                                @endphp
                                @foreach($existingCriteria as $criteria)
                                    <option value="{{ $criteria->criteria_name }}" 
                                            data-weight="{{ $criteria->criteria_weight }}" 
                                            data-type="{{ $criteria->criteria_type }}"
                                            {{ old('criteria_selection', $question->criteria_name) == $criteria->criteria_name ? 'selected' : '' }}>
                                        {{ $criteria->criteria_name }} (Bobot: {{ $criteria->criteria_weight }}, {{ ucfirst($criteria->criteria_type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('criteria_selection')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Criteria Fields -->
                        <div id="newCriteriaFields" class="new-criteria-fields">
                            <div class="form-group">
                                <label class="form-label" for="criteria_name">Nama Kriteria Baru *</label>
                                <input type="text" id="criteria_name" name="criteria_name" class="form-input" placeholder="Contoh: Afektif, Kognitif, Psikomotorik" value="{{ old('criteria_name', $question->criteria_name) }}">
                                @error('criteria_name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Criteria Weight & Type -->
                        <div class="criteria-row">
                            <div>
                                <label class="form-label" for="criteria_weight">Bobot Kriteria *</label>
                                <input type="number" id="criteria_weight" name="criteria_weight" class="form-input" 
                                       min="0.1" max="10" step="0.1" placeholder="Contoh: 0.3 atau 3" value="{{ old('criteria_weight', $question->criteria_weight) }}">
                                <p class="form-help">Bobot akan dinormalisasi otomatis oleh sistem</p>
                                @error('criteria_weight')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label" for="criteria_type">Tipe Kriteria *</label>
                                <select id="criteria_type" name="criteria_type" class="form-input">
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="benefit" {{ old('criteria_type', $question->criteria_type) == 'benefit' ? 'selected' : '' }}>Benefit (Semakin tinggi semakin baik)</option>
                                    <option value="cost" {{ old('criteria_type', $question->criteria_type) == 'cost' ? 'selected' : '' }}>Cost (Semakin rendah semakin baik)</option>
                                </select>
                                @error('criteria_type')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                                
                                <!-- Hidden field untuk criteria_type yang tidak disabled - SOLUSI UNTUK MASALAH DISABLED FIELD -->
                                <input type="hidden" id="criteria_type_hidden" name="criteria_type_backup" value="{{ old('criteria_type', $question->criteria_type) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Required Checkbox -->
                <div class="checkbox-container">
                    <input type="checkbox" id="is_required" name="is_required" class="checkbox-input" value="1" {{ old('is_required', $question->is_required) ? 'checked' : '' }}>
                    <label for="is_required" class="checkbox-label">Wajib diisi</label>
                </div>

                <!-- Form Buttons -->
                <div class="form-buttons">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Pertanyaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let optionCounter = {{ $question->options ? count($question->options) : 2 }};

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');
        
        // Initialize form state
        toggleQuestionOptions();
        updateTypeCardSelection();
        
        // Initialize SAW state on page load
        initializeSAWState();
        
        // Then handle criteria change
        handleCriteriaChange();
        
        console.log('Initialization complete');
    });

    // Initialize SAW state based on existing data
    function initializeSAWState() {
        const enableSaw = document.getElementById('enable_saw');
        const sawFields = document.getElementById('sawFields');
        
        console.log('Initializing SAW state...');
        console.log('Enable SAW checked:', enableSaw ? enableSaw.checked : 'not found');
        
        if (enableSaw && sawFields) {
            if (enableSaw.checked) {
                console.log('SAW is enabled, showing fields');
                sawFields.classList.add('show');
            } else {
                console.log('SAW is disabled, hiding fields');
                sawFields.classList.remove('show');
            }
        }
    }

    // Toggle question options based on type
    function toggleQuestionOptions() {
        const selectedType = document.querySelector('input[name="question_type"]:checked');
        const optionsContainer = document.getElementById('optionsContainer');
        const scaleContainer = document.getElementById('scaleContainer');
        
        console.log('Toggle question options for type:', selectedType ? selectedType.value : 'none');
        
        // Hide all containers
        if (optionsContainer) optionsContainer.classList.remove('show');
        if (scaleContainer) scaleContainer.classList.remove('show');
        
        if (selectedType) {
            const type = selectedType.value;
            
            if (['multiple_choice', 'checkbox', 'dropdown'].includes(type)) {
                if (optionsContainer) optionsContainer.classList.add('show');
            } else if (type === 'linear_scale') {
                if (scaleContainer) scaleContainer.classList.add('show');
                // Re-initialize SAW state when linear scale is shown
                initializeSAWState();
            }
        }
        
        updateTypeCardSelection();
    }

    // Update visual selection for type cards
    function updateTypeCardSelection() {
        const cards = document.querySelectorAll('.type-card');
        cards.forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    }

    // Add option
    function addOption() {
        optionCounter++;
        const optionsList = document.getElementById('optionsList');
        const newOption = document.createElement('div');
        newOption.className = 'option-item';
        newOption.innerHTML = `
            <input type="text" name="options[]" class="option-input" placeholder="Opsi ${optionCounter}">
            <button type="button" class="remove-option" onclick="removeOption(this)">Hapus</button>
        `;
        optionsList.appendChild(newOption);
    }

    // Remove option
    function removeOption(button) {
        const optionsList = document.getElementById('optionsList');
        if (optionsList.children.length > 2) {
            button.parentElement.remove();
        } else {
            alert('Minimal harus ada 2 opsi jawaban.');
        }
    }

    // Toggle SAW
    function toggleSAW() {
        const enableSaw = document.getElementById('enable_saw');
        const sawFields = document.getElementById('sawFields');
        
        console.log('Toggle SAW called, checkbox checked:', enableSaw ? enableSaw.checked : 'not found');
        
        if (enableSaw && sawFields) {
            if (enableSaw.checked) {
                console.log('Showing SAW fields');
                sawFields.classList.add('show');
            } else {
                console.log('Hiding SAW fields and clearing values');
                sawFields.classList.remove('show');
                
                // Clear SAW form values when disabled
                const criteriaSelection = document.getElementById('criteria_selection');
                const criteriaWeight = document.getElementById('criteria_weight');
                const criteriaType = document.getElementById('criteria_type');
                
                if (criteriaSelection) criteriaSelection.value = '';
                if (criteriaWeight) criteriaWeight.value = '';
                if (criteriaType) criteriaType.value = '';
                
                handleCriteriaChange();
            }
        }
    }

    // Handle criteria selection change - FIXED VERSION
    function handleCriteriaChange() {
        const criteriaSelection = document.getElementById('criteria_selection');
        const newCriteriaFields = document.getElementById('newCriteriaFields');
        const criteriaWeightInput = document.getElementById('criteria_weight');
        const criteriaTypeInput = document.getElementById('criteria_type');
        const criteriaTypeHidden = document.getElementById('criteria_type_hidden');
        
        console.log('Handle criteria change called');
        console.log('Current selection:', criteriaSelection ? criteriaSelection.value : 'not found');
        
        if (criteriaSelection && newCriteriaFields && criteriaWeightInput && criteriaTypeInput) {
            if (criteriaSelection.value === 'new') {
                // Show new criteria fields
                console.log('Showing new criteria fields');
                newCriteriaFields.classList.add('show');
                const criteriaName = document.getElementById('criteria_name');
                if (criteriaName) criteriaName.setAttribute('required', 'required');
                
                // Enable weight and type inputs
                criteriaWeightInput.removeAttribute('readonly');
                criteriaTypeInput.removeAttribute('disabled');
                
                // Don't clear values if this is from existing data on page load
                const enableSaw = document.getElementById('enable_saw');
                if (!enableSaw || !enableSaw.checked) {
                    criteriaWeightInput.value = '';
                    criteriaTypeInput.value = '';
                    if (criteriaTypeHidden) criteriaTypeHidden.value = '';
                }
            } else if (criteriaSelection.value && criteriaSelection.value !== '') {
                // Hide new criteria fields and auto-fill from existing
                console.log('Using existing criteria:', criteriaSelection.value);
                newCriteriaFields.classList.remove('show');
                const criteriaName = document.getElementById('criteria_name');
                if (criteriaName) criteriaName.removeAttribute('required');
                
                // Auto-fill from selected option
                const selectedOption = criteriaSelection.options[criteriaSelection.selectedIndex];
                const weight = selectedOption.getAttribute('data-weight');
                const type = selectedOption.getAttribute('data-type');
                
                if (weight && type) {
                    criteriaWeightInput.value = weight;
                    criteriaTypeInput.value = type;
                    
                    // SOLUSI: Update hidden field dengan nilai yang sama
                    if (criteriaTypeHidden) criteriaTypeHidden.value = type;
                    
                    // Make them readonly/disabled to prevent changes
                    criteriaWeightInput.setAttribute('readonly', 'readonly');
                    criteriaTypeInput.setAttribute('disabled', 'disabled');
                }
            } else {
                // No selection - hide new criteria fields
                console.log('No criteria selected');
                newCriteriaFields.classList.remove('show');
                const criteriaName = document.getElementById('criteria_name');
                if (criteriaName) criteriaName.removeAttribute('required');
                
                criteriaWeightInput.removeAttribute('readonly');
                criteriaTypeInput.removeAttribute('disabled');
                criteriaWeightInput.value = '';
                criteriaTypeInput.value = '';
                if (criteriaTypeHidden) criteriaTypeHidden.value = '';
            }
        }
    }

    // SOLUSI: Before form submit, ensure hidden field is synced with visible field
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        const criteriaType = document.getElementById('criteria_type');
        const criteriaTypeHidden = document.getElementById('criteria_type_hidden');
        
        // Sync hidden field with visible field before submit
        if (criteriaType && criteriaTypeHidden) {
            criteriaTypeHidden.value = criteriaType.value;
        }
        
        // Continue with existing validation...
        const selectedType = document.querySelector('input[name="question_type"]:checked');
        
        if (!selectedType) {
            e.preventDefault();
            alert('Silakan pilih jenis pertanyaan.');
            return;
        }

        // Validate options for multiple choice, checkbox, dropdown
        if (['multiple_choice', 'checkbox', 'dropdown'].includes(selectedType.value)) {
            const options = document.querySelectorAll('.option-input');
            const filledOptions = Array.from(options).filter(input => input.value.trim());
            
            if (filledOptions.length < 2) {
                e.preventDefault();
                alert('Pertanyaan pilihan harus memiliki minimal 2 opsi jawaban.');
                return;
            }
        }

        // Validate scale for linear_scale
        if (selectedType.value === 'linear_scale') {
            const scaleMin = parseInt(document.getElementById('scale_min').value);
            const scaleMax = parseInt(document.getElementById('scale_max').value);
            
            if (scaleMin >= scaleMax) {
                e.preventDefault();
                alert('Nilai maksimum harus lebih besar dari nilai minimum.');
                return;
            }

            // Validate SAW fields if enabled
            const enableSaw = document.getElementById('enable_saw');
            if (enableSaw && enableSaw.checked) {
                const criteriaSelection = document.getElementById('criteria_selection').value;
                const criteriaWeight = document.getElementById('criteria_weight').value;
                const criteriaTypeValue = criteriaType ? criteriaType.value : '';

                if (!criteriaSelection) {
                    e.preventDefault();
                    alert('Silakan pilih kriteria atau buat kriteria baru.');
                    return;
                }

                if (criteriaSelection === 'new') {
                    const criteriaName = document.getElementById('criteria_name').value;
                    if (!criteriaName.trim()) {
                        e.preventDefault();
                        alert('Nama kriteria baru wajib diisi.');
                        return;
                    }
                }

                if (!criteriaWeight || parseFloat(criteriaWeight) <= 0) {
                    e.preventDefault();
                    alert('Bobot kriteria harus diisi dengan nilai positif.');
                    return;
                }

                if (!criteriaTypeValue) {
                    e.preventDefault();
                    alert('Tipe kriteria wajib dipilih.');
                    return;
                }
            }
        }
    });
</script>
@endpush