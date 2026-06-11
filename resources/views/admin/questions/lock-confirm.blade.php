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
        margin: auto;
        padding: 20px;
    }

    .lock-icon {
        text-align: center;
        font-size: 50px;
        color: #666;
        margin-bottom: 20px;
    }

    .warning-card,
    .info-card,
    .error-message {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .warning-card {
        background: #f8f9fa;
    }

    .warning-card h3 {
        margin-bottom: 10px;
        font-size: 18px;
        color: #333;
    }

    .warning-card ul {
        margin-left: 20px;
        line-height: 1.8;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2,1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .info-item {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        text-align: center;
    }

    .info-number {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    .info-label {
        font-size: 14px;
        color: #666;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .required {
        color: red;
    }

    .form-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: #777;
    }

    .form-help {
        margin-top: 5px;
        font-size: 12px;
        color: #777;
    }

    .form-textarea {
        min-height: 100px;
    }

    .char-counter {
        text-align: right;
        margin-top: 5px;
        font-size: 12px;
        color: #777;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        padding: 10px 18px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .error-message ul {
        margin-top: 10px;
        margin-left: 20px;
    }

    @media(max-width:768px){
        .info-grid{
            grid-template-columns:1fr;
        }

        .form-actions{
            flex-direction:column;
        }
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

            .<div class="form-group">
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
                        Tahun periode survei
                    </div>
                .</div>

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
        const year = document.getElementById('year').value;
        
        if (!confirm(`KONFIRMASI KUNCI SISTEM\n\nAnda akan mengunci sistem untuk:\nPeriode Tahun ${year}\n\nSetelah dikunci, pertanyaan tidak dapat diubah.\nLanjutkan?`)) {
            e.preventDefault();
        }
    });
</script>
@endpush