{{-- resources/views/admin/questions/edit-section.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Bagian - Admin Survei')
@section('active-questions', 'active')
@section('page-title', 'Edit Bagian Survei')
@section('page-subtitle', 'Perbarui informasi bagian survei')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.questions.index') }}">Pertanyaan</a>
    <span class="breadcrumb-separator">></span>
    <span>Edit Bagian</span>
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
        max-width: 800px;
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

    .required {
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

    .form-help {
        font-size: 13px;
        color: #7f8c8d;
        margin-top: 6px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        padding-top: 10px;
    }

    .btn {
        padding: 14px 28px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
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

    /* Error Messages */
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #f5c6cb;
    }

    .error-message ul {
        margin: 0;
        padding-left: 20px;
    }

    .error-message li {
        margin: 5px 0;
    }

    /* Character Counter */
    .char-counter {
        text-align: right;
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 5px;
    }
</style>
@endpush

@section('content')
@if($errors->any())
    <div class="error-message">
        <strong><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="form-container">
    <div class="form-header">
        <div class="form-title">
            <i class="fas fa-edit"></i> Edit Bagian Survei
        </div>
        <div class="form-subtitle">
            Perbarui judul dan deskripsi bagian survei
        </div>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.questions.update-section', $section->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title" class="form-label">
                    Judul Bagian<span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input" 
                    value="{{ old('title', $section->title) }}" 
                    required
                    maxlength="255"
                    placeholder="Contoh: Kualitas Layanan, Fasilitas, dsb."
                >
                <div class="form-help">
                    Berikan judul yang jelas dan deskriptif untuk bagian ini
                </div>
                <div class="char-counter">
                    <span id="titleCount">{{ strlen(old('title', $section->title)) }}</span>/255 karakter
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">
                    Deskripsi Bagian
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-input form-textarea"
                    placeholder="Opsional: Jelaskan secara singkat tentang bagian ini..."
                    maxlength="1000"
                >{{ old('description', $section->description) }}</textarea>
                <div class="form-help">
                    Deskripsi akan ditampilkan di bawah judul bagian pada halaman survei (opsional)
                </div>
                <div class="char-counter">
                    <span id="descCount">{{ strlen(old('description', $section->description ?? '')) }}</span>/1000 karakter
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Character counter untuk title
    const titleInput = document.getElementById('title');
    const titleCount = document.getElementById('titleCount');
    
    titleInput.addEventListener('input', function() {
        titleCount.textContent = this.value.length;
    });

    // Character counter untuk description
    const descInput = document.getElementById('description');
    const descCount = document.getElementById('descCount');
    
    descInput.addEventListener('input', function() {
        descCount.textContent = this.value.length;
    });
</script>
@endpush