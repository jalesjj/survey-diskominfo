{{-- resources/views/admin/questions/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Pertanyaan - Admin Survei')
@section('active-questions', 'active')
@section('page-title', 'Manajemen Pertanyaan')
@section('page-subtitle', 'Kelola bagian dan pertanyaan survei kepuasan')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Action Buttons */
    .action-buttons {
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
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

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    /* Disabled button for permanent sections */
    .btn-disabled {
        background: #6c757d;
        color: #fff;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-disabled:hover {
        background: #6c757d;
        transform: none;
        box-shadow: none;
    }

    /* Sections Container */
    .sections-container {
        display: grid;
        gap: 30px;
    }

    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* Permanent section styling */
    .section-card.permanent {
        border: 2px solid #6c757d;
    }

    .section-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Permanent section header */
    .section-header.permanent {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-description {
        font-size: 14px;
        opacity: 0.9;
    }

    .section-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .section-body {
        padding: 0;
    }

    .questions-list {
        border-collapse: collapse;
        width: 100%;
    }

    .questions-list th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 1px solid #dee2e6;
    }

    .questions-list td {
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
    }

    .questions-list tr:hover {
        background: #f8f9fa;
    }

    .question-type-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-short-text { background: #e3f2fd; color: #1976d2; }
    .badge-long-text { background: #f3e5f5; color: #7b1fa2; }
    .badge-multiple-choice { background: #e8f5e8; color: #388e3c; }
    .badge-checkbox { background: #fff3e0; color: #f57c00; }
    .badge-dropdown { background: #fce4ec; color: #c2185b; }
    .badge-file-upload { background: #f1f8e9; color: #689f38; }
    .badge-linear-scale { background: #e0f2f1; color: #00796b; }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    /* Badge permanen */
    .badge-permanent {
        background: #6c757d;
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Custom Checkbox Styling */
    .checkbox-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .custom-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #5a9b9e;
        transform: scale(1.3);
    }

    .custom-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }

    .custom-checkbox:hover:not(:disabled) {
        transform: scale(1.4);
        transition: transform 0.2s ease;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h4 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .empty-state p {
        margin-bottom: 20px;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }

        .section-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .section-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .questions-list {
            font-size: 12px;
        }

        .questions-list th, 
        .questions-list td {
            padding: 10px;
        }
    }

    /* Success/Error Messages */
    .success-message, .error-message, .warning-message {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .warning-message {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
</style>
@endpush

@section('content')
<!-- Messages -->
@if(session('success'))
    <div class="success-message">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="error-message">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Action Buttons -->
<div class="action-buttons">
    @if(!$isLocked)
        <a href="{{ route('admin.questions.create-section') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Bagian Baru
        </a>
        <a href="{{ route('admin.questions.lock-form') }}" class="btn btn-success">
            <i class="fas fa-lock"></i> Lock Sistem & Mulai Periode
        </a>
    @else
        <button class="btn btn-disabled" disabled>
            <i class="fas fa-lock"></i> Sistem Terkunci
        </button>
        <button onclick="confirmStopPeriod()" class="btn btn-danger">
            <i class="fas fa-stop-circle"></i> Stop Periode & Unlock
        </button>
    @endif
</div>

<!-- Period Lock Info -->
@if($isLocked && $activePeriod)
    <div class="warning-message">
        <div>
            <i class="fas fa-lock"></i>
            <strong>SISTEM TERKUNCI</strong>
            <p style="margin: 5px 0 0 0;">
                Pertanyaan dikunci untuk periode: <strong>{{ $activePeriod->period_name }}</strong> ({{ $activePeriod->year }})
                <br>
                <small>Dimulai: {{ $activePeriod->start_date->format('d F Y') }}</small>
            </p>
            @if($activePeriod->description)
                <p style="margin-top: 10px; opacity: 0.9;">
                    <i class="fas fa-info-circle"></i> {{ $activePeriod->description }}
                </p>
            @endif
        </div>
    </div>
@endif

<!-- Sections -->
<div class="sections-container">
    @forelse($sections as $section)
    <div class="section-card {{ isset($section->is_permanent) && $section->is_permanent ? 'permanent' : '' }}">
        <div class="section-header {{ isset($section->is_permanent) && $section->is_permanent ? 'permanent' : '' }}">
    <div>
        <div class="section-title">
            @if(isset($section->is_permanent) && $section->is_permanent)
                <span class="badge-permanent">
                    <i class="fas fa-lock"></i> PERMANEN
                </span>
            @elseif($isLocked)
                <i class="fas fa-lock" style="opacity: 0.7;"></i>
            @endif
            {{ $section->title }}
            
            {{-- Status Badge untuk Section --}}
            @if(!isset($section->is_permanent) || !$section->is_permanent)
                <span class="status-badge {{ $section->is_active ? 'status-active' : 'status-inactive' }}" style="margin-left: 10px;">
                    {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            @endif
        </div>
        @if($section->description)
            <div class="section-description">{{ $section->description }}</div>
        @endif
    </div>
    <div class="section-actions">
        @if(isset($section->is_permanent) && $section->is_permanent)
            {{-- Tombol disabled untuk section permanen --}}
            <button class="btn btn-disabled btn-sm" disabled title="Bagian permanen tidak dapat diedit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-disabled btn-sm" disabled title="Bagian permanen tidak dapat diubah statusnya">
                <i class="fas fa-{{ $section->is_active ? 'eye-slash' : 'eye' }}"></i>
            </button>
            <button class="btn btn-disabled btn-sm" disabled title="Bagian permanen tidak dapat dihapus">
                <i class="fas fa-trash"></i>
            </button>
        @elseif($isLocked)
            {{-- Tombol disabled saat sistem locked --}}
            <button class="btn btn-disabled btn-sm" disabled title="Sistem terkunci">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-disabled btn-sm" disabled title="Sistem terkunci">
                <i class="fas fa-{{ $section->is_active ? 'eye-slash' : 'eye' }}"></i>
            </button>
            <button class="btn btn-disabled btn-sm" disabled title="Sistem terkunci">
                <i class="fas fa-trash"></i>
            </button>
        @else
            {{-- Tombol normal untuk section biasa --}}
            <a href="{{ route('admin.questions.edit-section', $section->id) }}" class="btn btn-primary btn-sm" title="Edit Bagian">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" onclick="toggleSection({{ $section->id }})" class="btn btn-warning btn-sm" title="{{ $section->is_active ? 'Nonaktifkan Bagian' : 'Aktifkan Bagian' }}">
                <i class="fas fa-{{ $section->is_active ? 'eye-slash' : 'eye' }}"></i>
            </a>
            <a href="#" onclick="deleteSection({{ $section->id }}, '{{ addslashes($section->title) }}')" class="btn btn-danger btn-sm" title="Hapus Bagian">
                <i class="fas fa-trash"></i>
            </a>
        @endif
        
        {{-- Tombol Tambah Pertanyaan - selalu tampil kecuali locked --}}
        @if(!$isLocked)
            <a href="{{ route('admin.questions.create-question', $section->id) }}" class="btn btn-success btn-sm" title="Tambah Pertanyaan">
                <i class="fas fa-plus"></i> Pertanyaan
            </a>
        @endif
    </div>
</div>

        <div class="section-body">
            @if($section->allQuestions->count() > 0)
                <table class="questions-list">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">Aktif</th>
                            <th>Pertanyaan</th>
                            <th style="width: 150px;">Jenis</th>
                            <th style="width: 100px;">Required</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($section->allQuestions as $question)
                        <tr>
                            <td>
                                <div class="checkbox-container">
                                    @if(isset($question->is_permanent) && $question->is_permanent)
                                        {{-- Checkbox disabled untuk pertanyaan permanen --}}
                                        <input 
                                            type="checkbox" 
                                            class="custom-checkbox" 
                                            checked 
                                            disabled 
                                            title="Pertanyaan permanen selalu aktif"
                                        >
                                    @elseif($isLocked)
                                        {{-- Checkbox disabled saat sistem locked --}}
                                        <input 
                                            type="checkbox" 
                                            class="custom-checkbox" 
                                            {{ $question->is_active ? 'checked' : '' }} 
                                            disabled 
                                            title="Sistem terkunci"
                                        >
                                    @else
                                        {{-- Checkbox normal yang bisa diklik --}}
                                        <input 
                                            type="checkbox" 
                                            class="custom-checkbox" 
                                            {{ $question->is_active ? 'checked' : '' }}
                                            onclick="toggleQuestion({{ $question->id }})"
                                            title="Klik untuk {{ $question->is_active ? 'menonaktifkan' : 'mengaktifkan' }}"
                                        >
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">
                                    {{ $question->question_text }}
                                </div>
                                @if($question->options && count($question->options) > 0)
                                    <small style="color: #7f8c8d;">
                                        Opsi: {{ implode(', ', array_slice($question->options, 0, 3)) }}
                                        @if(count($question->options) > 3)
                                            ... (+{{ count($question->options) - 3 }} lainnya)
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="question-type-badge badge-{{ str_replace('_', '-', $question->question_type) }}">
                                    {{ \App\Helpers\SurveyDefaults::getQuestionTypeLabel($question->question_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $question->is_required ? 'status-active' : 'status-inactive' }}">
                                    {{ $question->is_required ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td>
                                @if(isset($question->is_permanent) && $question->is_permanent)
    {{-- Tombol disabled untuk pertanyaan permanen --}}
    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
        <button class="btn btn-disabled btn-sm" disabled title="Pertanyaan permanen tidak dapat diedit">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-disabled btn-sm" disabled title="Pertanyaan permanen tidak dapat dihapus">
            <i class="fas fa-trash"></i>
        </button>
    </div>
@elseif($isLocked)
    {{-- Tombol disabled saat sistem locked --}}
    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
        <button class="btn btn-disabled btn-sm" disabled title="Sistem terkunci">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-disabled btn-sm" disabled title="Sistem terkunci">
            <i class="fas fa-trash"></i>
        </button>
    </div>
@else
    {{-- Tombol normal untuk pertanyaan biasa (Edit dan Hapus saja) --}}
    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
        <a href="{{ route('admin.questions.edit-question', $question->id) }}" class="btn btn-primary btn-sm" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <a href="#" onclick="deleteQuestion({{ $question->id }}, '{{ addslashes($question->question_text) }}')" class="btn btn-danger btn-sm" title="Hapus">
            <i class="fas fa-trash"></i>
        </a>
    </div>
@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-question-circle"></i></div>
                    <h4>Belum Ada Pertanyaan</h4>
                    <p>Bagian ini belum memiliki pertanyaan. Klik tombol "Tambah Pertanyaan" untuk menambahkan pertanyaan pertama.</p>
                    <br>
                    @if(!isset($section->is_permanent) || !$section->is_permanent)
                        <a href="{{ route('admin.questions.create-question', $section->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Pertanyaan Pertama
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    @empty
    <div class="section-card">
        <div class="empty-state">
            <div class="empty-icon"><i class="fas fa-file-alt"></i></div>
            <h3>Belum Ada Bagian Survei</h3>
            <p>Mulai membuat survei dengan menambahkan bagian pertama. Setiap bagian dapat berisi beberapa pertanyaan yang akan ditampilkan pada halaman yang sama.</p>
            <br>
            <a href="{{ route('admin.questions.create-section') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambahkan Bagian Pertama
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Hidden Forms for POST requests -->
<form id="toggleSectionForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

<form id="deleteSectionForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="toggleQuestionForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

<form id="deleteQuestionForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="stopPeriodForm" method="POST" action="{{ route('admin.questions.stop-period') }}" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<script>
    function toggleSection(sectionId) {
        if (confirm('Yakin ingin mengubah status bagian ini?\n\nJika dinonaktifkan, bagian ini tidak akan muncul di halaman responden.')) {
            const form = document.getElementById('toggleSectionForm');
            form.action = `/admin/questions/section/${sectionId}/toggle`;
            form.submit();
        }
    }

    function deleteSection(sectionId, sectionTitle) {
        if (confirm(`Yakin ingin menghapus bagian "${sectionTitle}"?\n\nSemua pertanyaan di bagian ini juga akan terhapus dan tidak dapat dikembalikan.`)) {
            const form = document.getElementById('deleteSectionForm');
            form.action = `/admin/questions/section/${sectionId}`;
            form.submit();
        }
    }

    function toggleQuestion(questionId) {
        // Simpan posisi scroll sebelum submit
        sessionStorage.setItem('scrollPosition', window.scrollY);
        
        const form = document.getElementById('toggleQuestionForm');
        form.action = `/admin/questions/question/${questionId}/toggle`;
        form.submit();
    }

    function deleteQuestion(questionId, questionText) {
        const truncatedText = questionText.length > 50 ? questionText.substring(0, 50) + '...' : questionText;
        if (confirm(`Yakin ingin menghapus pertanyaan:\n"${truncatedText}"\n\nData ini tidak dapat dikembalikan.`)) {
            const form = document.getElementById('deleteQuestionForm');
            form.action = `/admin/questions/question/${questionId}`;
            form.submit();
        }
    }

    function confirmStopPeriod() {
        const periodName = '{{ $activePeriod ? $activePeriod->period_name : "" }}';
        const periodYear = '{{ $activePeriod ? $activePeriod->year : "" }}';
        
        if (confirm(`KONFIRMASI STOP PERIODE\n\nAnda akan menghentikan periode:\n"${periodName}" (${periodYear})\n\nSetelah periode dihentikan:\n✓ Pertanyaan dapat ditambah/edit/hapus kembali\n✓ Data responden periode ini tersimpan permanen\n✓ Sistem kembali ke mode terbuka (unlocked)\n\nLanjutkan?`)) {
            document.getElementById('stopPeriodForm').submit();
        }
    }

    // Auto hide success message & Restore scroll position
    document.addEventListener('DOMContentLoaded', function() {
        // Hide success message
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }

        // Restore scroll position setelah toggle
        const savedScrollPosition = sessionStorage.getItem('scrollPosition');
        if (savedScrollPosition) {
            window.scrollTo(0, parseInt(savedScrollPosition));
            // Hapus setelah restore agar tidak mengganggu navigasi normal
            sessionStorage.removeItem('scrollPosition');
        }
    });
</script>
@endpush