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
            text-align: center;
        }

        .section-actions {
            justify-content: center;
            flex-wrap: wrap;
        }

        .questions-list {
            font-size: 14px;
        }

        .questions-list th,
        .questions-list td {
            padding: 10px 8px;
        }
    }
</style>
@endpush

@section('content')
<!-- Action Buttons -->
<div class="action-buttons">
    <a href="{{ route('admin.questions.create-section') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Tambahkan Bagian
    </a>
</div>

<!-- Sections -->
<div class="sections-container">
    @forelse($sections as $section)
    <div class="section-card {{ isset($section->is_permanent) && $section->is_permanent ? 'permanent' : '' }}">
        <div class="section-header {{ isset($section->is_permanent) && $section->is_permanent ? 'permanent' : '' }}">
            <div>
                <div class="section-title">
                    {{ $section->title }}
                </div>
                @if($section->description)
                    <div class="section-description">{{ $section->description }}</div>
                @endif
            </div>
            <div class="section-actions">
                <span class="status-badge {{ $section->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
                
                @if(isset($section->is_permanent) && $section->is_permanent)
                    {{-- Tombol disabled untuk section permanen --}}
                    <button class="btn btn-disabled btn-sm" disabled title="Section permanen tidak dapat diubah">
                        <i class="fas fa-plus"></i> Tambah Pertanyaan
                    </button>
                    <button class="btn btn-disabled btn-sm" disabled title="Section permanen tidak dapat diubah">
                        <i class="fas fa-lock"></i> Toggle Status
                    </button>
                    <button class="btn btn-disabled btn-sm" disabled title="Section permanen tidak dapat dihapus">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                @else
                    {{-- Tombol normal untuk section biasa --}}
                    <a href="{{ route('admin.questions.create-question', $section->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Tambah Pertanyaan
                    </a>
                    <a href="{{ route('admin.questions.edit-section', $section->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit Bagian
                    </a>
                    <a href="#" onclick="toggleSection({{ $section->id }})" class="btn btn-warning btn-sm">
                        <i class="fas {{ $section->is_active ? 'fa-lock' : 'fa-unlock' }}"></i> {{ $section->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </a>
                    <a href="#" onclick="deleteSection({{ $section->id }}, '{{ $section->title }}')" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                @endif
            </div>
        </div>

        <div class="section-body">
            @if($section->allQuestions->count() > 0)
                <table class="questions-list">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Pertanyaan</th>
                            <th style="width: 150px;">Jenis</th>
                            <th style="width: 100px;">Required</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($section->allQuestions as $question)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
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
                                <span class="status-badge {{ $question->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                @if(isset($question->is_permanent) && $question->is_permanent)
                                    {{-- Tombol disabled untuk pertanyaan permanen --}}
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <button class="btn btn-disabled btn-sm" disabled title="Pertanyaan permanen tidak dapat diedit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-disabled btn-sm" disabled title="Pertanyaan permanen tidak dapat diubah statusnya">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                        <button class="btn btn-disabled btn-sm" disabled title="Pertanyaan permanen tidak dapat dihapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @else
                                    {{-- Tombol normal untuk pertanyaan biasa --}}
                                    <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                        <a href="{{ route('admin.questions.edit-question', $question->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="toggleQuestion({{ $question->id }})" class="btn btn-warning btn-sm" title="{{ $question->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas {{ $question->is_active ? 'fa-lock' : 'fa-unlock' }}"></i>
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
@endsection

@push('scripts')
<script>
    function toggleSection(sectionId) {
        if (confirm('Yakin ingin mengubah status bagian ini?')) {
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
        if (confirm('Yakin ingin mengubah status pertanyaan ini?')) {
            const form = document.getElementById('toggleQuestionForm');
            form.action = `/admin/questions/question/${questionId}/toggle`;
            form.submit();
        }
    }

    function deleteQuestion(questionId, questionText) {
        const truncatedText = questionText.length > 50 ? questionText.substring(0, 50) + '...' : questionText;
        if (confirm(`Yakin ingin menghapus pertanyaan:\n"${truncatedText}"\n\nData ini tidak dapat dikembalikan.`)) {
            const form = document.getElementById('deleteQuestionForm');
            form.action = `/admin/questions/question/${questionId}`;
            form.submit();
        }
    }

    // Auto hide success message
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }
    });
</script>
@endpush