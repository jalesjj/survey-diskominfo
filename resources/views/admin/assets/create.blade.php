{{-- resources/views/admin/assets/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Upload Asset - Admin Survei')
@section('active-assets', 'active')
@section('page-title', 'Upload Asset Baru')
@section('page-subtitle', 'Upload logo atau asset gambar untuk sistem')

@section('header-actions')
<div class="header-actions">
    <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
        <span class="btn-icon">←</span>
        Kembali
    </a>
</div>
@endsection

@push('styles')
<style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 25px 30px;
        text-align: center;
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 5px;
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
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #5a9b9e;
        box-shadow: 0 0 0 3px rgba(90, 155, 158, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .file-upload-area {
        border: 2px dashed #bdc3c7;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        min-height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .file-upload-area:hover {
        border-color: #5a9b9e;
        background: rgba(90, 155, 158, 0.05);
        transform: translateY(-2px);
    }

    .file-upload-area.dragover {
        border-color: #5a9b9e;
        background: rgba(90, 155, 158, 0.1);
        transform: scale(1.02);
    }

    .file-upload-area.has-file {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.05);
    }

    .file-upload-icon {
        width: 48px;
        height: 48px;
        background: #ecf0f1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 20px;
        color: #7f8c8d;
        transition: all 0.3s ease;
    }

    .file-upload-area:hover .file-upload-icon {
        background: #5a9b9e;
        color: white;
        transform: scale(1.1);
    }

    .file-upload-icon::before {
        content: "📁";
    }

    .file-upload-area.has-file .file-upload-icon::before {
        content: "✅";
    }

    .file-upload-text {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .file-upload-subtext {
        font-size: 13px;
        color: #7f8c8d;
        margin-bottom: 0;
    }

    .file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        top: 0;
        left: 0;
    }

    .file-preview {
        display: none;
        margin-top: 20px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 8px;
        border: 2px solid #e9ecef;
        animation: fadeIn 0.3s ease;
        text-align: center;
    }

    .file-preview.show {
        display: block;
    }

    .file-preview-img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        margin-bottom: 15px;
        object-fit: contain;
        display: block;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .file-preview-info {
        text-align: center;
        background: white;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .file-preview-info > div {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .file-preview-info > div:last-child {
        margin-bottom: 0;
    }

    .file-change-btn {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        margin-top: 10px;
        transition: all 0.3s ease;
    }

    .file-change-btn:hover {
        background: #138496;
        transform: translateY(-1px);
    }

    .form-error {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 5px;
        background: #f8d7da;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid #f1aeb5;
    }

    .type-info {
        background: #e8f6f3;
        border: 1px solid #5a9b9e;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            padding-top: 0;
            padding-bottom: 0;
        }
        to {
            opacity: 1;
            max-height: 200px;
            padding-top: 15px;
            padding-bottom: 15px;
        }
    }

    .type-info h5 {
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .type-info p {
        color: #5a9b9e;
        font-size: 12px;
        margin: 0;
        line-height: 1.4;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        min-width: 120px;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transition: all 0.3s ease;
        transform: translate(-50%, -50%);
    }

    .btn:hover:not(:disabled)::before {
        width: 300px;
        height: 300px;
    }

    .btn-primary {
        background: #5a9b9e;
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background: #4a8b8e;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(90, 155, 158, 0.3);
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
    }

    .success-message, .error-message {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f1aeb5;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .upload-hint {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
        padding: 12px 15px;
        border-radius: 6px;
        font-size: 13px;
        margin-top: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        line-height: 1.4;
    }

    .upload-hint::before {
        content: "💡";
        font-size: 16px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <div class="form-title">Upload Asset Baru</div>
        <div class="form-subtitle">Upload logo atau asset gambar untuk sistem survei</div>
    </div>

    <div class="form-body">
        @if(session('success'))
        <div class="success-message">
            <span>✅</span>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="error-message">
            <span>❌</span>
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.assets.store') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="type">Tipe Asset</label>
                <select id="type" name="type" class="form-select" required onchange="showTypeInfo()">
                    <option value="">Pilih Tipe Asset</option>
                    @foreach($availableTypes as $key => $label)
                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')
                    <div class="form-error">{{ $message }}</div>
                @enderror

                <div id="type-logo-info" class="type-info" style="display: none;">
                    <h5>📷 Logo</h5>
                    <p>Logo yang akan ditampilkan di header halaman survei. Anda bisa menambahkan beberapa logo sekaligus dan mengatur statusnya.</p>
                </div>

                <div id="type-banner-info" class="type-info" style="display: none;">
                    <h5>🎨 Banner</h5>
                    <p>Banner atau spanduk untuk keperluan tampilan khusus di sistem.</p>
                </div>

                <div id="type-icon-info" class="type-info" style="display: none;">
                    <h5>🔰 Icon</h5>
                    <p>Icon kecil untuk keperluan navigasi atau penanda di sistem.</p>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="file">File Gambar</label>
                <div class="file-upload-area" id="fileUploadArea">
                    <div class="file-upload-icon"></div>
                    <div class="file-upload-text" id="uploadText">Klik atau seret file gambar ke sini</div>
                    <div class="file-upload-subtext" id="uploadSubtext">Format: JPEG, PNG, JPG, GIF, SVG, WebP (Max: 2MB)</div>
                    <input type="file" id="file" name="file" class="file-input" accept="image/*" required>
                </div>
                
                <div class="file-preview" id="filePreview">
                    <img id="previewImg" class="file-preview-img" src="" alt="Preview">
                    <div class="file-preview-info">
                        <div id="previewName"></div>
                        <div id="previewSize"></div>
                        <div id="previewType"></div>
                    </div>
                    <button type="button" class="file-change-btn" onclick="changeFile()">
                        🔄 Ganti File
                    </button>
                </div>
                
                @error('file')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                
                <div class="upload-hint">
                    Logo yang diupload akan otomatis aktif dan langsung tampil di halaman survei. Anda bisa mengatur status aktif/non-aktif setelah upload.
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
                    ← Batal
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <span id="submitIcon">📁</span>
                    <span id="submitText">Pilih File Dulu</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let selectedFile = null;

function showTypeInfo() {
    // Hide all info boxes
    const infoBoxes = document.querySelectorAll('.type-info');
    infoBoxes.forEach(box => box.style.display = 'none');
    
    // Show selected info box
    const selectedType = document.getElementById('type').value;
    if (selectedType) {
        const infoBox = document.getElementById('type-' + selectedType + '-info');
        if (infoBox) {
            infoBox.style.display = 'block';
        }
    }
    
    updateSubmitButton();
}

function previewFile(file) {
    if (!file) return;
    
    // Validate file size (2MB = 2048KB)
    if (file.size > 2048 * 1024) {
        alert('❌ Ukuran file terlalu besar! Maksimal 2MB.');
        clearFileSelection();
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('❌ Format file tidak didukung! Gunakan JPEG, PNG, JPG, GIF, SVG, atau WebP.');
        clearFileSelection();
        return;
    }
    
    selectedFile = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        // Update upload area
        const uploadArea = document.getElementById('fileUploadArea');
        const uploadText = document.getElementById('uploadText');
        const uploadSubtext = document.getElementById('uploadSubtext');
        
        uploadArea.classList.add('has-file');
        uploadText.textContent = '✅ File dipilih: ' + file.name;
        uploadSubtext.textContent = 'Ukuran: ' + formatFileSize(file.size);
        
        // Show preview
        const filePreview = document.getElementById('filePreview');
        const previewImg = document.getElementById('previewImg');
        const previewName = document.getElementById('previewName');
        const previewSize = document.getElementById('previewSize');
        const previewType = document.getElementById('previewType');
        
        previewImg.src = e.target.result;
        previewName.innerHTML = '<strong>📄 ' + file.name + '</strong>';
        previewSize.innerHTML = '📏 ' + formatFileSize(file.size);
        previewType.innerHTML = '🏷️ ' + file.type.split('/')[1].toUpperCase();
        
        filePreview.classList.add('show');
        
        // Smooth scroll to preview
        filePreview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        updateSubmitButton();
    };
    
    reader.readAsDataURL(file);
}

function clearFileSelection() {
    selectedFile = null;
    
    // Reset file input
    const fileInput = document.getElementById('file');
    fileInput.value = '';
    
    // Reset upload area
    const uploadArea = document.getElementById('fileUploadArea');
    const uploadText = document.getElementById('uploadText');
    const uploadSubtext = document.getElementById('uploadSubtext');
    
    uploadArea.classList.remove('has-file');
    uploadText.textContent = 'Klik atau seret file gambar ke sini';
    uploadSubtext.textContent = 'Format: JPEG, PNG, JPG, GIF, SVG, WebP (Max: 2MB)';
    
    // Hide preview
    const filePreview = document.getElementById('filePreview');
    filePreview.classList.remove('show');
    
    updateSubmitButton();
}

function changeFile() {
    clearFileSelection();
    document.getElementById('file').click();
}

function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');
    const typeSelect = document.getElementById('type');
    
    const hasFile = selectedFile !== null;
    const hasType = typeSelect.value !== '';
    
    if (hasFile && hasType) {
        submitBtn.disabled = false;
        submitIcon.textContent = '📤';
        submitText.textContent = 'Upload ' + (typeSelect.options[typeSelect.selectedIndex].text || 'Asset');
    } else if (hasFile && !hasType) {
        submitBtn.disabled = true;
        submitIcon.textContent = '⚠️';
        submitText.textContent = 'Pilih Tipe Dulu';
    } else if (!hasFile && hasType) {
        submitBtn.disabled = true;
        submitIcon.textContent = '📁';
        submitText.textContent = 'Pilih File Dulu';
    } else {
        submitBtn.disabled = true;
        submitIcon.textContent = '📁';
        submitText.textContent = 'Pilih File & Tipe';
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// File input change event - SINGLE EVENT HANDLER
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        previewFile(file);
    } else {
        clearFileSelection();
    }
});

// Upload area click event - DIRECT FILE INPUT TRIGGER
document.getElementById('fileUploadArea').addEventListener('click', function(e) {
    // Prevent event bubbling
    e.stopPropagation();
    
    // Only trigger if not clicking on file input directly
    if (e.target !== document.getElementById('file')) {
        document.getElementById('file').click();
    }
});

// Enhanced drag and drop functionality
const fileUploadArea = document.getElementById('fileUploadArea');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    fileUploadArea.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

['dragenter', 'dragover'].forEach(eventName => {
    fileUploadArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    fileUploadArea.addEventListener(eventName, unhighlight, false);
});

fileUploadArea.addEventListener('drop', handleDrop, false);

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    fileUploadArea.classList.add('dragover');
}

function unhighlight(e) {
    fileUploadArea.classList.remove('dragover');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        const fileInput = document.getElementById('file');
        fileInput.files = files;
        
        // Trigger preview
        previewFile(files[0]);
    }
}

// Form submission handling
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');
    
    // Final validation before submit
    const fileInput = document.getElementById('file');
    const typeSelect = document.getElementById('type');
    
    if (!fileInput.files || fileInput.files.length === 0 || !selectedFile) {
        e.preventDefault();
        alert('❌ Silakan pilih file gambar terlebih dahulu!');
        return false;
    }
    
    if (!typeSelect.value) {
        e.preventDefault();
        alert('❌ Silakan pilih tipe asset terlebih dahulu!');
        return false;
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitIcon.textContent = '⏳';
    submitText.textContent = 'Mengupload...';
    
    // Form akan submit normal
    return true;
});

// Auto hide messages
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.success-message, .error-message');
    
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                message.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Initialize submit button state
    updateSubmitButton();
});

// Console info
console.log('📤 Asset Upload System - Single Click Fixed');
console.log('🎯 One click file selection');
console.log('🚀 Enhanced user experience');
</script>
@endsection