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

    /* Filter Styles */
    .filter-container {
        background: white;
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .filter-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }

    .filter-form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 200px;
    }

    .filter-label {
        color: #6c757d;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        padding: 10px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        color: #495057;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .filter-select:hover {
        border-color: #5a9b9e;
    }

    .filter-select:focus {
        outline: none;
        border-color: #5a9b9e;
        box-shadow: 0 0 0 3px rgba(90, 155, 158, 0.1);
    }

    .filter-btn-apply {
        padding: 10px 20px;
        background: #5a9b9e;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        align-self: flex-end;
    }

    .filter-btn-apply:hover {
        background: #4a8b8e;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(90, 155, 158, 0.2);
    }

    .filter-btn-reset {
        padding: 10px 20px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        align-self: flex-end;
    }

    .filter-btn-reset:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .filter-form {
            flex-direction: column;
        }

        .filter-group {
            width: 100%;
        }

        .filter-btn-apply,
        .filter-btn-reset {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<!-- Inline script untuk instant scroll restore SEBELUM page render -->
<script>
    // Restore scroll position ASAP untuk mencegah jump
    (function() {
        const savedScrollPosition = sessionStorage.getItem('scrollPosition');
        if (savedScrollPosition) {
            const scrollPos = parseInt(savedScrollPosition);
            window.scrollTo(0, scrollPos);
        }
    })();
</script>

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

<!-- Filter Pertanyaan -->
<div class="filter-container">
    <div class="filter-header">
        <i class="fas fa-filter"></i>
        Filter Pertanyaan
    </div>
    
    <form action="{{ route('admin.questions.index') }}" method="GET" class="filter-form" id="filterForm">
        <!-- Filter Status -->
        <div class="filter-group">
            <label class="filter-label" for="filter-status">
                <i class="fas fa-toggle-on"></i> Status
            </label>
            <select name="status" id="filter-status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="active" {{ $filterStatus === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $filterStatus === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        
        <!-- Filter Criteria (SAW) -->
        <div class="filter-group">
            <label class="filter-label" for="filter-criteria">
                <i class="fas fa-chart-line"></i> Kriteria SAW
            </label>
            <select name="criteria" id="filter-criteria" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="all" {{ $filterCriteria === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="benefit" {{ $filterCriteria === 'benefit' ? 'selected' : '' }}>Benefit</option>
                <option value="cost" {{ $filterCriteria === 'cost' ? 'selected' : '' }}>Cost</option>
            </select>
        </div>
        
        <!-- Filter Tipe Pertanyaan (Dinamis) -->
        @if($availableTypes->count() > 0)
        <div class="filter-group">
            <label class="filter-label" for="filter-type">
                <i class="fas fa-list"></i> Tipe Pertanyaan
            </label>
            <select name="type" id="filter-type" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="all" {{ $filterType === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                @foreach($availableTypes as $type)
                    <option value="{{ $type }}" {{ $filterType === $type ? 'selected' : '' }}>
                        {{ $typeLabels[$type] ?? ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        
        <!-- Tombol Reset (hanya muncul jika ada filter aktif) -->
        @if($filterStatus !== 'all' || $filterCriteria !== 'all' || $filterType !== 'all')
        <a href="{{ route('admin.questions.index') }}" class="filter-btn-reset">
            <i class="fas fa-redo"></i>
            Reset Filter
        </a>
        @endif
    </form>
</div>

<!-- Sections -->
<div class="sections-container">
    @forelse($sections as $section)
    <div class="section-card {{ isset($section->is_permanent) && $section->is_permanent ? 'permanent' : '' }}">
        <div class="section-header {{ isset($section->is_permanent) && $section->is_permanent ? 'permanent' : '' }}">
    <div>
        <div class="section-title">
            @if(isset($section->is_permanent) && $section->is_permanent)
                {{-- Section Data Diri: Tidak ada badge PERMANEN --}}
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
            {{-- Section Data Diri: Tidak ada tombol sama sekali --}}
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
            <a href="{{ route('admin.questions.edit-section', $section->id) }}" class="btn btn-primary btn-sm" title="Edit Bagian" onclick="saveScrollPosition()">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" onclick="toggleSection({{ $section->id }}); return false;" class="btn btn-warning btn-sm" title="{{ $section->is_active ? 'Nonaktifkan Bagian' : 'Aktifkan Bagian' }}">
                <i class="fas fa-{{ $section->is_active ? 'eye-slash' : 'eye' }}"></i>
            </a>
            <a href="#" onclick="deleteSection({{ $section->id }}, '{{ addslashes($section->title) }}'); return false;" class="btn btn-danger btn-sm" title="Hapus Bagian">
                <i class="fas fa-trash"></i>
            </a>
        @endif
        
        {{-- Tombol Tambah Pertanyaan - tidak tampil untuk Data Diri --}}
        @if(!$isLocked && (!isset($section->is_permanent) || !$section->is_permanent))
            <a href="{{ route('admin.questions.create-question', $section->id) }}" class="btn btn-success btn-sm" title="Tambah Pertanyaan">
                <i class="fas fa-plus"></i> Pertanyaan
            </a>
        @endif
    </div>
</div>

        <div class="section-body">
            @if((is_array($section->allQuestions) ? count($section->allQuestions) : $section->allQuestions->count()) > 0)
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
                                        {{-- Pertanyaan Data Diri: Tampilkan teks "Aktif" tanpa checkbox --}}
                                        <span style="color: #27ae60; font-weight: 600; font-size: 12px;">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>
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
    {{-- Pertanyaan Data Diri: Tidak ada tombol sama sekali --}}
    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
        {{-- Kosong - tidak ada tombol --}}
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
        <a href="{{ route('admin.questions.edit-question', $question->id) }}" class="btn btn-primary btn-sm" title="Edit" onclick="saveScrollPosition()">
            <i class="fas fa-edit"></i>
        </a>
        <a href="#" onclick="deleteQuestion({{ $question->id }}, '{{ addslashes($question->question_text) }}'); return false;" class="btn btn-danger btn-sm" title="Hapus">
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
        // Simpan posisi scroll
        sessionStorage.setItem('scrollPosition', window.scrollY);
        
        const form = document.getElementById('toggleSectionForm');
        // Ambil parameter filter dari URL saat ini
        const urlParams = new URLSearchParams(window.location.search);
        const filterParams = urlParams.toString();
        form.action = `/admin/questions/section/${sectionId}/toggle${filterParams ? '?' + filterParams : ''}`;
        form.submit();
    }

    function deleteSection(sectionId, sectionTitle) {
        sessionStorage.setItem('scrollPosition', window.scrollY);
        const form = document.getElementById('deleteSectionForm');
        const urlParams = new URLSearchParams(window.location.search);
        const filterParams = urlParams.toString();
        form.action = `/admin/questions/section/${sectionId}${filterParams ? '?' + filterParams : ''}`;
        form.submit();
    }

    function toggleQuestion(questionId) {
        sessionStorage.setItem('scrollPosition', window.scrollY);
        const form = document.getElementById('toggleQuestionForm');
        const urlParams = new URLSearchParams(window.location.search);
        const filterParams = urlParams.toString();
        form.action = `/admin/questions/question/${questionId}/toggle${filterParams ? '?' + filterParams : ''}`;
        form.submit();
    }

    function deleteQuestion(questionId, questionText) {
        sessionStorage.setItem('scrollPosition', window.scrollY);
        const form = document.getElementById('deleteQuestionForm');
        const urlParams = new URLSearchParams(window.location.search);
        const filterParams = urlParams.toString();
        form.action = `/admin/questions/question/${questionId}${filterParams ? '?' + filterParams : ''}`;
        form.submit();
    }

    function saveScrollPosition() {
        sessionStorage.setItem('scrollPosition', window.scrollY);
    }

    function confirmStopPeriod() {
        const periodName = '{{ $activePeriod ? $activePeriod->period_name : "" }}';
        const periodYear = '{{ $activePeriod ? $activePeriod->year : "" }}';
        
        const userInput = prompt(`Apakah Anda yakin untuk mengakhiri periode ini?\n\nPeriode: ${periodName} (${periodYear})\n\nKetik "saya yakin" untuk mengakhiri periode ini:`);
        
        if (userInput !== null && userInput.toLowerCase().trim() === 'saya yakin') {
            document.getElementById('stopPeriodForm').submit();
        } else if (userInput !== null) {
            alert('Konfirmasi gagal. Anda harus mengetik "saya yakin" dengan benar.');
        }
    }

    // Disable browser default scroll restoration
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    // Auto hide success message & Restore scroll position
    document.addEventListener('DOMContentLoaded', function() {
        // Unlock scroll (jika terkunci dari action sebelumnya)
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
        
        // Hide success message
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }

        // Restore scroll position IMMEDIATELY
        const savedScrollPosition = sessionStorage.getItem('scrollPosition');
        if (savedScrollPosition) {
            // Set scroll ASAP tanpa animation
            const scrollPos = parseInt(savedScrollPosition);
            document.documentElement.scrollTop = scrollPos;
            document.body.scrollTop = scrollPos;
            window.scrollTo({
                top: scrollPos,
                behavior: 'instant' // Instant, no smooth scrolling
            });
            // Hapus setelah restore agar tidak mengganggu navigasi normal
            sessionStorage.removeItem('scrollPosition');
        }
    });
</script>
@endpush