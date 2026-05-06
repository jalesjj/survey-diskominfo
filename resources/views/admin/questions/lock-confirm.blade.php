{{-- resources/views/admin/questions/lock-confirm.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kunci Pertanyaan - Admin Survei')
@section('active-questions', 'active')
@section('page-title', 'Kunci Sistem Pertanyaan')
@section('page-subtitle', 'Lock pertanyaan dan mulai periode pengumpulan data')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.questions.index') }}">Pertanyaan</a>
    <span class="breadcrumb-separator">></span>
    <span>Kunci Sistem</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .lock-container {
        max-width: 700px;
        margin: 0 auto;
    }

    .warning-card {
        background: #fff3cd;
        border-left: 5px solid #ffc107;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .warning-card h3 {
        color: #856404;
        margin: 0 0 15px 0;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .warning-card ul {
        color: #856404;
        margin: 10px 0 0 20px;
        line-height: 1.8;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 25px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .info-item {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .info-number {
        font-size: 36px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 5px;
    }

    .info-label {
        font-size: 14px;
        color: #6c757d;
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

    .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 15px;
    }

    .form-help {
        font-size: 13px;
        color: #7f8c8d;
        margin-top: 6px;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
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

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #f5c6cb;
    }

    .lock-icon {
        font-size: 80px;
        color: #ffc107;
        text-align: center;
        margin-bottom: 20px;
    }

    .char-counter {
        text-align: right;
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 5px;
    }
</style>
@endpush

@section('content')
<div class="lock-container">
    @if($errors->any())
        <div class="error-message">
            <strong><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="lock-icon">
        <i class="fas fa-lock"></i>
    </div>

    <div class="warning-card">
        <h3>
            <i class="fas fa-exclamation-triangle"></i>
            PERHATIAN: Sistem Akan Dikunci!
        </h3>
        <p style="margin: 0 0 10px 0;">Setelah sistem dikunci, Anda TIDAK DAPAT:</p>
        <ul>
            <li>Menambah bagian (section) baru</li>
            <li>Mengedit atau menghapus bagian yang ada</li>
            <li>Menambah pertanyaan baru</li>
            <li>Mengedit atau menghapus pertanyaan yang ada</li>
            <li>Mengubah kriteria SAW</li>
        </ul>
        <p style="margin: 15px 0 0 0; font-weight: 600;">
            Semua jawaban responden akan masuk ke periode yang Anda buat di bawah ini.
        </p>
    </div>

    <div class="info-card">
        <h4 style="margin: 0 0 20px 0; color: #2c3e50;">Ringkasan Pertanyaan Saat Ini:</h4>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-number">{{ $totalSections }}</div>
                <div class="info-label">Bagian Custom</div>
            </div>
            <div class="info-item">
                <div class="info-number">{{ $totalDefaultQuestions }}</div>
                <div class="info-label">Pertanyaan Data Diri</div>
            </div>
            <div class="info-item">
                <div class="info-number">{{ $totalQuestions }}</div>
                <div class="info-label">Pertanyaan Custom</div>
            </div>
            <div class="info-item">
                <div class="info-number">{{ $totalAllQuestions }}</div>
                <div class="info-label">Total Semua Pertanyaan</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.questions.lock-submit') }}" id="lockForm">
            @csrf

            <h4 style="margin: 25px 0 20px 0; color: #2c3e50; border-top: 1px solid #dee2e6; padding-top: 25px;">
                Informasi Periode:
            </h4>

            <div class="form-row">
                <div class="form-group">
                    <label for="period_name" class="form-label">
                        Nama Periode<span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="period_name" 
                        name="period_name" 
                        class="form-input" 
                        value="{{ old('period_name') }}" 
                        required
                        maxlength="255"
                        placeholder="Contoh: Semester 1 Tahun 2025"
                    >
                    <div class="form-help">
                        Nama untuk mengidentifikasi periode pengumpulan data
                    </div>
                </div>

                <div class="form-group">
                    <label for="year" class="form-label">
                        Tahun<span class="required">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="year" 
                        name="year" 
                        class="form-input" 
                        value="{{ old('year', date('Y')) }}" 
                        required
                        min="2020"
                        max="2100"
                        placeholder="{{ date('Y') }}"
                    >
                    <div class="form-help">
                        Tahun periode
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">
                    Deskripsi/Catatan
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-input form-textarea"
                    placeholder="Opsional: Catatan atau keterangan tambahan untuk periode ini..."
                    maxlength="1000"
                >{{ old('description') }}</textarea>
                <div class="form-help">
                    Catatan tambahan tentang periode ini (opsional)
                </div>
                <div class="char-counter">
                    <span id="descCount">{{ strlen(old('description', '')) }}</span>/1000 karakter
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-danger" id="submitBtn">
                    <i class="fas fa-lock"></i> Ya, Kunci Sistem
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
    // Character counter untuk description
    const descInput = document.getElementById('description');
    const descCount = document.getElementById('descCount');
    
    descInput.addEventListener('input', function() {
        descCount.textContent = this.value.length;
    });

    // Konfirmasi sebelum submit
    document.getElementById('lockForm').addEventListener('submit', function(e) {
        const periodName = document.getElementById('period_name').value;
        const year = document.getElementById('year').value;
        
        if (!confirm(`KONFIRMASI KUNCI SISTEM\n\nAnda akan mengunci sistem untuk periode:\n"${periodName}" - Tahun ${year}\n\nSetelah dikunci, pertanyaan tidak dapat diubah.\nLanjutkan?`)) {
            e.preventDefault();
        }
    });
</script>
@endpush