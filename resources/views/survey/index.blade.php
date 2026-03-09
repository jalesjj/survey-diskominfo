{{-- resources/views/survey/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Survei Kepuasan Layanan Diskominfo Lamongan')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .progress-container {
        background: white;
        border-bottom: 1px solid #e9ecef;
        position: relative;
        padding: 20px 0;
    }

    .progress-bar {
        height: 4px;
        background: #e9ecef;
        position: relative;
        overflow: visible;
        margin: 0 60px;
    }

    .progress-fill {
        height: 100%;
        background: #5a9b9e;
        width: 0%;
        transition: width 0.5s ease;
    }

    .progress-logo-container {
        position: absolute;
        top: -25px;
        left: 0%;
        transform: translateX(-50%);
        transition: left 0.5s ease;
        z-index: 10;
    }

    .progress-speech-bubble {
        position: absolute;
        bottom: 45px;
        left: 50%;
        transform: translateX(-50%);
        background: #2c3e50;
        color: white;
        padding: 8px 12px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        animation: bounceIn 0.6s ease forwards;
    }

    .progress-speech-bubble::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid #2c3e50;
    }

    .progress-logo {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .logo-placeholder {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #5a9b9e;
        font-weight: 600;
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: translateX(-50%) scale(0.3);
        }
        50% {
            opacity: 1;
            transform: translateX(-50%) scale(1.1);
        }
        100% {
            opacity: 1;
            transform: translateX(-50%) scale(1);
        }
    }

    .section-container {
        display: none;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.4s ease;
        max-width: 800px;
        margin: 0 auto 30px auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .section-container.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .section-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 30px 40px;
        text-align: center;
    }

    .section-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .section-description {
        font-size: 16px;
        opacity: 0.9;
        line-height: 1.5;
    }

    .section-body {
        padding: 40px;
    }

    .question-group {
        margin-bottom: 35px;
    }

    .question-label {
        display: block;
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .question-description {
    font-size: 14px;
    color: #6c757d;
    margin-top: 5px;
    margin-bottom: 15px;
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 4px;
    line-height: 1.5;
}

.question-description:empty {
    display: none;
}

    .required-mark {
        color: #e74c3c;
        margin-left: 4px;
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

    .form-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-select {
        appearance: none;
        background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="%23666" d="m0,1l2,2l2,-2z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 12px;
        padding-right: 40px;
    }

    /* Multiple Choice & Dropdown */
    .radio-group, .checkbox-group {
        display: grid;
        gap: 12px;
        margin-top: 10px;
    }

    .radio-option, .checkbox-option {
        position: relative;
    }

    .radio-option input, .checkbox-option input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .radio-option label, .checkbox-option label {
        display: block;
        padding: 15px 20px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        padding-left: 50px;
    }

    .radio-option label::before, .checkbox-option label::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        border: 2px solid #dee2e6;
        background: white;
    }

    .radio-option label::before {
        border-radius: 50%;
    }

    .checkbox-option label::before {
        border-radius: 4px;
    }

    .radio-option input:checked + label,
    .checkbox-option input:checked + label {
        background: #e8f4f8;
        border-color: #5a9b9e;
        color: #2c3e50;
    }

    .radio-option input:checked + label::before {
        border-color: #5a9b9e;
        background: #5a9b9e;
        box-shadow: inset 0 0 0 4px white;
    }

    .checkbox-option input:checked + label::before {
        border-color: #5a9b9e;
        background: #5a9b9e;
    }

    .checkbox-option input:checked + label::after {
        content: '✓';
        position: absolute;
        left: 19px;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 14px;
        font-weight: bold;
    }

    /* Linear Scale */
    .linear-scale {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin: 20px 0;
        flex-wrap: wrap;
    }

    .scale-label {
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 500;
        white-space: nowrap;
    }

    .scale-options {
        display: flex;
        gap: 8px;
    }

    .scale-option {
        position: relative;
    }

    .scale-option input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .scale-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .scale-option input:checked + label {
        background: #5a9b9e;
        border-color: #5a9b9e;
        color: white;
        transform: scale(1.1);
    }

    /* File Upload */
    .file-upload {
        position: relative;
        display: inline-block;
        cursor: pointer;
        width: 100%;
    }

    .file-upload input[type="file"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .file-upload-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload:hover .file-upload-label,
    .file-upload.has-file .file-upload-label {
        border-color: #5a9b9e;
        background: #e8f4f8;
        color: #5a9b9e;
    }

    .file-upload-icon {
        font-size: 24px;
        margin-right: 10px;
    }

    /* Navigation */
    .navigation-buttons {
        display: flex;
        gap: 15px;
        justify-content: space-between;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid #e9ecef;
    }

    .btn {
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #5a9b9e;
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background: #4a8b8e;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(90, 155, 158, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .success-message {
        display: none;
        text-align: center;
        padding: 60px 40px;
        color: #28a745;
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .success-icon {
        font-size: 80px;
        margin-bottom: 30px;
        color: #28a745;
    }

    .success-message h3 {
        font-size: 28px;
        margin-bottom: 15px;
        color: #2c3e50;
    }

    .success-message p {
        font-size: 16px;
        line-height: 1.6;
        color: #6c757d;
        margin-bottom: 30px;
    }

    .error-message {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .section-body {
            .question-description {
        font-size: 13px;
        padding: 6px 10px;
        margin-bottom: 12px;
    }
        }

        .navigation-buttons {
            flex-direction: column-reverse;
        }

        .linear-scale {
            flex-direction: column;
            gap: 20px;
        }

        .scale-options {
            justify-content: center;
            flex-wrap: wrap;
        }

        .radio-group, .checkbox-group {
            gap: 8px;
        }

        .progress-bar {
            margin: 0 30px;
        }

        .progress-logo {
            width: 40px;
            height: 40px;
        }

        .logo-placeholder {
            width: 40px;
            height: 40px;
            font-size: 10px;
        }

        .progress-speech-bubble {
            font-size: 12px;
            padding: 6px 10px;
            bottom: 35px;
        }
    }
</style>
@endpush

@section('content')
<div class="progress-container">
    <div class="progress-bar">
        <div class="progress-fill" id="progressBar"></div>
        <div class="progress-logo-container" id="progressLogoContainer">
            <div class="progress-speech-bubble" id="progressBubble">0%</div>
            <div class="progress-logo">
                <div class="logo-placeholder" id="logoPlaceholder">
                    <img src="{{ asset('images/logos/logo-diskominfo.png') }}" alt="Logo Diskominfo" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span style="display: none;">%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="error-message" id="errorMessage"></div>

<form id="surveyForm" enctype="multipart/form-data">
    @csrf
    
    @foreach($sections as $sectionIndex => $section)
    <div class="section-container {{ $sectionIndex === 0 ? 'active' : '' }}" data-section="{{ $sectionIndex }}">
        <div class="section-header">
            <h2 class="section-title">{{ $section->title }}</h2>
            @if($section->description)
                <p class="section-description">{{ $section->description }}</p>
            @endif
        </div>

        <div class="section-body">
            @foreach($section->questions as $question)
            <div class="question-group">
                <label class="question-label">
                    {{ $question->question_text }}
                    @if($question->is_required)
                        <span class="required-mark">*</span>
                    @endif
                </label>

                {{-- Tambahkan bagian untuk menampilkan deskripsi pertanyaan --}}
                @if($question->question_description)
                    <div class="question-description">
                        {{ $question->question_description }}
                    </div>
                @endif

                @if($question->question_type === 'short_text')
                    <input 
                        type="text" 
                        name="question_{{ $question->id }}" 
                        class="form-input"
                        {{ $question->is_required ? 'required' : '' }}
                        placeholder="Masukkan jawaban Anda">

                @elseif($question->question_type === 'long_text')
                    <textarea 
                        name="question_{{ $question->id }}" 
                        class="form-input form-textarea"
                        {{ $question->is_required ? 'required' : '' }}
                        placeholder="Masukkan jawaban Anda dengan detail"></textarea>

                @elseif($question->question_type === 'multiple_choice')
                    <div class="radio-group">
                        @foreach($question->options as $optionIndex => $option)
                        <div class="radio-option">
                            <input 
                                type="radio" 
                                name="question_{{ $question->id }}" 
                                id="q{{ $question->id }}_{{ $optionIndex }}"
                                value="{{ $option }}"
                                {{ $question->is_required ? 'required' : '' }}>
                            <label for="q{{ $question->id }}_{{ $optionIndex }}">{{ $option }}</label>
                        </div>
                        @endforeach
                    </div>

                @elseif($question->question_type === 'checkbox')
                    <div class="checkbox-group">
                        @foreach($question->options as $optionIndex => $option)
                        <div class="checkbox-option">
                            <input 
                                type="checkbox" 
                                name="question_{{ $question->id }}[]" 
                                id="q{{ $question->id }}_{{ $optionIndex }}"
                                value="{{ $option }}">
                            <label for="q{{ $question->id }}_{{ $optionIndex }}">{{ $option }}</label>
                        </div>
                        @endforeach
                    </div>

                @elseif($question->question_type === 'dropdown')
                    <select 
                        name="question_{{ $question->id }}" 
                        class="form-input form-select"
                        {{ $question->is_required ? 'required' : '' }}>
                        <option value="">-- Pilih jawaban --</option>
                        @foreach($question->options as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>

                @elseif($question->question_type === 'file_upload')
                    <div class="file-upload">
                        <input 
                            type="file" 
                            name="question_{{ $question->id }}" 
                            id="file_{{ $question->id }}"
                            {{ $question->is_required ? 'required' : '' }}
                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                            onchange="updateFileLabel(this)">
                        <div class="file-upload-label">
                            <span class="file-upload-icon"></span>
                            <span class="file-upload-text">Klik untuk memilih file</span>
                        </div>
                    </div>

                @elseif($question->question_type === 'linear_scale')
                    <div class="linear-scale">
                        @if(isset($question->settings['scale_min_label']))
                            <div class="scale-label">{{ $question->settings['scale_min_label'] }}</div>
                        @endif
                        
                        <div class="scale-options">
                            @for($i = $question->settings['scale_min'] ?? 1; $i <= ($question->settings['scale_max'] ?? 5); $i++)
                            <div class="scale-option">
                                <input 
                                    type="radio" 
                                    name="question_{{ $question->id }}" 
                                    id="scale_{{ $question->id }}_{{ $i }}"
                                    value="{{ $i }}"
                                    {{ $question->is_required ? 'required' : '' }}>
                                <label for="scale_{{ $question->id }}_{{ $i }}">{{ $i }}</label>
                            </div>
                            @endfor
                        </div>

                        @if(isset($question->settings['scale_max_label']))
                            <div class="scale-label">{{ $question->settings['scale_max_label'] }}</div>
                        @endif
                    </div>
                @endif
            </div>
            @endforeach

            <div class="navigation-buttons">
                <div>
                    @if($sectionIndex > 0)
                        <button type="button" class="btn btn-secondary" onclick="previousSection()">
                            ← Sebelumnya
                        </button>
                    @endif
                </div>
                <div>
                    @if($sectionIndex < $sections->count() - 1)
                        <button type="button" class="btn btn-primary" onclick="nextSection()">
                            Selanjutnya →
                        </button>
                    @else
                        <button type="button" class="btn btn-primary" onclick="submitSurvey()">
                            Kirim Survei
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Success Message -->
    <div class="success-message" id="successMessage">
    <div class="success-icon"><i class="fas fa-check-circle"></i></div>
    <h3>Survei Berhasil Dikirim!</h3>
    <p>Terima kasih atas partisipasi Anda dalam survei kepuasan masyarakat. Feedback Anda sangat berharga untuk meningkatkan kualitas layanan Dinas Komunikasi dan Informatika Kabupaten Lamongan.</p>
    <a href="{{ route('survey.index') }}" class="btn btn-primary">
        <i class="fas fa-home"></i> Kembali ke Awal
    </a>
</div>
</form>
@endsection

@push('scripts')
<script>
    let currentSection = 0;
    const totalSections = {{ $sections->count() }};
    let isSubmitting = false;

    function updateProgress() {
        const progress = ((currentSection + 1) / totalSections) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
        document.getElementById('progressLogoContainer').style.left = progress + '%';
        document.getElementById('progressBubble').textContent = Math.round(progress) + '%';
        
        // Trigger animasi bubble
        const bubble = document.getElementById('progressBubble');
        bubble.style.animation = 'none';
        bubble.offsetHeight; // Trigger reflow
        bubble.style.animation = 'bounceIn 0.6s ease forwards';
    }

    function showError(message) {
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
    }

    function validateCurrentSection() {
        const currentSectionEl = document.querySelector(`[data-section="${currentSection}"]`);
        const requiredInputs = currentSectionEl.querySelectorAll('[required]');
        
        for (let input of requiredInputs) {
            if (input.type === 'checkbox') {
                const checkboxGroup = currentSectionEl.querySelectorAll(`[name="${input.name}"]:checked`);
                if (checkboxGroup.length === 0) {
                    showError('Mohon lengkapi semua pertanyaan yang wajib diisi');
                    input.focus();
                    return false;
                }
            } else if (input.type === 'radio') {
                const radioGroup = currentSectionEl.querySelectorAll(`[name="${input.name}"]:checked`);
                if (radioGroup.length === 0) {
                    showError('Mohon lengkapi semua pertanyaan yang wajib diisi');
                    input.focus();
                    return false;
                }
            } else if (!input.value.trim()) {
                showError('Mohon lengkapi semua pertanyaan yang wajib diisi');
                input.focus();
                return false;
            }
        }
        
        return true;
    }

    function nextSection() {
        if (!validateCurrentSection()) {
            return;
        }

        if (currentSection < totalSections - 1) {
            document.querySelector(`[data-section="${currentSection}"]`).classList.remove('active');
            currentSection++;
            document.querySelector(`[data-section="${currentSection}"]`).classList.add('active');
            updateProgress();
            
            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }

    function previousSection() {
        if (currentSection > 0) {
            document.querySelector(`[data-section="${currentSection}"]`).classList.remove('active');
            currentSection--;
            document.querySelector(`[data-section="${currentSection}"]`).classList.add('active');
            updateProgress();
            
            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }

    // Updated JavaScript section for survey/index.blade.php
function submitSurvey() {
    if (isSubmitting) return;
    
    if (!validateCurrentSection()) {
        return;
    }

    isSubmitting = true;
    const submitBtn = event.target;
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ Mengirim...';

    const formData = new FormData(document.getElementById('surveyForm'));
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    fetch('{{ route("survey.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Handle both success and error responses
        return response.json().then(data => ({
            status: response.status,
            ok: response.ok,
            data: data
        }));
    })
    .then(({status, ok, data}) => {
        console.log('Response data:', data);
        
        if (ok && data.success) {
            // Hide current section
            document.querySelector(`[data-section="${currentSection}"]`).classList.remove('active');
            
            // Show success message
            document.getElementById('successMessage').style.display = 'block';
            
            // Update progress to 100%
            document.getElementById('progressBar').style.width = '100%';
            document.getElementById('progressLogoContainer').style.left = '100%';
            document.getElementById('progressBubble').textContent = '100%';
            
            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            // Clear any saved draft data
            try {
                sessionStorage.removeItem('surveyDraft');
            } catch (e) {
                console.warn('Could not clear session storage');
            }
            
            console.log('Survey submitted successfully!', data);
        } else {
            // Handle validation errors
            if (status === 422 && data.errors) {
                let errorMessages = [];
                Object.values(data.errors).forEach(errorArray => {
                    if (Array.isArray(errorArray)) {
                        errorMessages = errorMessages.concat(errorArray);
                    }
                });
                throw new Error(errorMessages.join('\n') || 'Validation failed');
            } else {
                throw new Error(data.message || 'Server error occurred');
            }
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        
        let errorMessage = 'Terjadi kesalahan saat mengirim survei.';
        
        if (error.message && error.message !== 'Failed to fetch') {
            errorMessage = error.message;
        } else if (error.message === 'Failed to fetch') {
            errorMessage = 'Koneksi terputus. Periksa koneksi internet Anda.';
        }
        
        showError(errorMessage + ' Silakan coba lagi.');
        
        isSubmitting = false;
        submitBtn.disabled = false;
        submitBtn.textContent = '🚀 Kirim Survei';
    });
}

    function updateFileLabel(input) {
        const fileUpload = input.closest('.file-upload');
        const label = fileUpload.querySelector('.file-upload-text');
        
        if (input.files && input.files[0]) {
            label.textContent = input.files[0].name;
            fileUpload.classList.add('has-file');
        } else {
            label.textContent = 'Klik untuk memilih file';
            fileUpload.classList.remove('has-file');
        }
    }

    // Auto-save functionality
    let autoSaveTimeout;
    function autoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            const formData = new FormData(document.getElementById('surveyForm'));
            const savedData = {};
            
            for (let [key, value] of formData.entries()) {
                if (savedData[key]) {
                    if (!Array.isArray(savedData[key])) {
                        savedData[key] = [savedData[key]];
                    }
                    savedData[key].push(value);
                } else {
                    savedData[key] = value;
                }
            }
            
            // Save to sessionStorage (dalam environment yang mendukung)
            try {
                sessionStorage.setItem('surveyDraft', JSON.stringify(savedData));
            } catch (e) {
                // SessionStorage not available
            }
        }, 1000);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateProgress();
        
        // Add auto-save listeners
        document.addEventListener('input', autoSave);
        document.addEventListener('change', autoSave);

        // Load saved data on page load
        try {
            const savedData = sessionStorage.getItem('surveyDraft');
            if (savedData) {
                const data = JSON.parse(savedData);
                
                for (let [name, value] of Object.entries(data)) {
                    const inputs = document.querySelectorAll(`[name="${name}"]`);
                    
                    inputs.forEach(input => {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            if (Array.isArray(value)) {
                                input.checked = value.includes(input.value);
                            } else {
                                input.checked = input.value === value;
                            }
                        } else if (input.type !== 'file') {
                            input.value = value;
                        }
                    });
                }
            }
        } catch (e) {
            // Error loading saved data
        }
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                if (currentSection < totalSections - 1) {
                    nextSection();
                } else {
                    submitSurvey();
                }
            }
        });
    });
</script>
@endpush