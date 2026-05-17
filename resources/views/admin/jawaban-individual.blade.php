{{-- resources/views/admin/jawaban-individual.blade.php --}}
@extends('layouts.admin')

@section('title', 'Jawaban Individual - Survei Kepuasan')
@section('active-jawaban', 'active')
@section('page-title', 'Jawaban Individual')
@section('page-subtitle', 'Data responden individual')

@section('header-actions')
<div class="header-actions">
    <span class="admin-welcome">Selamat datang, {{ session('admin_name') }}</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* =========================================
       FLAT & SIMPLE DESIGN (NO SHADOWS)
       ========================================= */

    /* Action Buttons */
    .action-buttons {
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: background-color 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .btn-primary { background: #5a9b9e; color: white; }
    .btn-primary:hover { background: #4a8b8e; color: white; }

    .btn-success { background: #28a745; color: white; }
    .btn-success:hover { background: #218838; color: white; }

    .btn-info { background: #17a2b8; color: white; }
    .btn-info:hover { background: #138496; color: white; }

    .btn-warning { background: #ffc107; color: #212529; }
    .btn-warning:hover { background: #e0a800; color: #212529; }

    .btn-danger { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; color: white; }

    /* Summary Stats */
    .summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
        text-align: center;
    }

    .summary-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .summary-number {
        font-size: 28px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 5px;
    }

    .summary-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Tab Navigation */
    .tab-navigation {
        background: white;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .tab-nav {
        display: flex;
        background: #f8f9fa;
    }

    .tab-item {
        flex: 1;
        padding: 15px 20px;
        text-align: center;
        background: #f8f9fa;
        color: #6c757d;
        text-decoration: none;
        font-weight: 600;
        border-right: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background-color 0.2s;
    }

    .tab-item:last-child { border-right: none; }
    .tab-item:hover { background: #f1f5f9; color: #495057; }
    .tab-item.active { background: #5a9b9e; color: white; }

    /* Search and Filter Bar */
    .search-filter-bar {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.2s ease;
    }

    .search-input:focus { outline: none; border-color: #5a9b9e; }

    .filter-select {
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        color: #495057;
        background: white;
        cursor: pointer;
    }

    .filter-select:focus { outline: none; border-color: #5a9b9e; }

    .search-btn {
        background: #5a9b9e;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .search-btn:hover { background: #4a8b8e; }

    /* Response Cards */
    .response-card {
        background: white;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
        overflow: hidden;
    }

    .response-header {
        background: #2c3e50; /* Flat solid color */
        color: white;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #1a252f;
    }

    .respondent-details h4 {
        margin: 0 0 8px 0;
        font-size: 18px;
        font-weight: 600;
    }

    .respondent-meta {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #cbd5e1;
        flex-wrap: wrap;
    }

    .respondent-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .response-body { padding: 25px; }

    .response-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .summary-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 4px;
    }

    .summary-title {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
    }

    .response-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .action-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }

    .detail-btn { background: #17a2b8; color: white; }
    .detail-btn:hover { background: #138496; }
    
    .delete-btn { background: #dc3545; color: white; }
    .delete-btn:hover { background: #c82333; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
        margin: 30px 0;
    }

    .empty-state i { font-size: 48px; color: #cbd5e1; margin-bottom: 20px; }
    .empty-state h3 { font-size: 20px; color: #334155; margin-bottom: 10px; }
    .empty-state p { font-size: 15px; color: #64748b; margin-bottom: 25px; }

    /* Pagination */
    .pagination-wrapper { margin-top: 30px; display: flex; justify-content: center; }
    .pagination { display: flex; gap: 5px; list-style: none; margin: 0; padding: 0; }
    .page-item { border-radius: 4px; overflow: hidden; }
    .page-link {
        display: block;
        padding: 10px 15px;
        color: #5a9b9e;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        background: white;
    }
    .page-link:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .page-item.active .page-link { background: #5a9b9e; color: white; border-color: #5a9b9e; }
    .page-item.disabled .page-link { color: #94a3b8; background: #f8f9fa; cursor: not-allowed; }

    /* Detail Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(15, 23, 42, 0.6); /* Flat overlay */
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 0;
        border-radius: 8px;
        width: 90%;
        max-width: 700px;
        max-height: 80vh;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        position: relative;
    }

    .modal-header {
        background: #5a9b9e;
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title { font-size: 18px; font-weight: 600; margin: 0; flex: 1; }
    .close { color: white; font-size: 24px; cursor: pointer; opacity: 0.8; }
    .close:hover { opacity: 1; }
    .modal-body { padding: 25px; max-height: 60vh; overflow-y: auto; }

    .detail-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }

    .detail-info h4 { color: #1e293b; margin-bottom: 15px; font-size: 16px; font-weight: 600; }
    .detail-info p { color: #475569; margin: 0; line-height: 1.5; font-size: 14px;}

    /* Toast Notification */
    .toast {
        position: fixed;
        top: 20px; right: 20px;
        background: white;
        padding: 15px 20px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 10000;
        min-width: 300px;
        border-left: 4px solid #28a745;
    }

    .toast.error { border-left-color: #dc3545; }
    .toast-icon { font-size: 20px; }
    .toast.success .toast-icon { color: #28a745; }
    .toast.error .toast-icon { color: #dc3545; }
    .toast-content { flex: 1; }
    .toast-title { font-weight: 600; margin-bottom: 2px; color: #1e293b; font-size: 14px; }
    .toast-message { font-size: 13px; color: #64748b; }
    .toast-close { cursor: pointer; color: #94a3b8; font-size: 16px; }
    .toast-close:hover { color: #475569; }

    /* Responsive */
    @media (max-width: 768px) {
        .action-buttons { flex-direction: column; align-items: stretch; }
        .summary-stats { grid-template-columns: 1fr 1fr; }
        .search-filter-bar { flex-direction: column; align-items: stretch; }
        .search-input { min-width: auto; }
        .tab-nav { flex-direction: column; }
        .tab-item { border-right: none; border-bottom: 1px solid #e2e8f0; }
        .tab-item:last-child { border-bottom: none; }
        .response-header { flex-direction: column; text-align: center; gap: 10px; }
        .respondent-meta { justify-content: center; }
        .response-summary { grid-template-columns: 1fr; }
        .response-actions { flex-direction: column; }
    }
</style>
@endpush

@section('content')

<!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('admin.questions.index') }}" class="btn btn-primary">
            <i class="fas fa-cogs"></i> Kelola Pertanyaan
        </a>
        <a href="{{ route('admin.export') }}" class="btn btn-success">
            <i class="fas fa-download"></i> Export Data
        </a>
        <a href="{{ route('admin.hasil-survey.export-pdf') }}" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

<!-- Summary Statistics -->
<div class="summary-stats">
    <div class="summary-card">
        <div class="summary-number">{{ $questions->count() }}</div>
        <div class="summary-label">Total Pertanyaan</div>
    </div>
    <div class="summary-card">
        <div class="summary-number">{{ $totalSurveys }}</div>
        <div class="summary-label">Total Responden</div>
    </div>
    <div class="summary-card">
        <div class="summary-number">{{ $surveys->total() }}</div>
        <div class="summary-label">Total Survei</div>
    </div>
    <div class="summary-card">
        <div class="summary-number">{{ $surveys->sum(function($survey) { return $survey->responses->count(); }) }}</div>
        <div class="summary-label">Total Jawaban</div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="tab-navigation">
    <div class="tab-nav">
        <a href="{{ route('admin.jawaban', ['tab' => 'questions']) }}" class="tab-item">
            <i class="fas fa-question-circle tab-icon"></i>
            <span>Pertanyaan</span>
        </a>
        <a href="{{ route('admin.jawaban', ['tab' => 'individual']) }}" class="tab-item active">
            <i class="fas fa-users tab-icon"></i>
            <span>Individual</span>
        </a>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="search-filter-bar">
    <input type="text" class="search-input" placeholder="🔍 Cari berdasarkan jawaban responden..." id="searchInput">
    <select class="filter-select" id="dateFilter">
        <option value="">Semua Tanggal</option>
        <option value="today">Hari Ini</option>
        <option value="week">Minggu Ini</option>
        <option value="month">Bulan Ini</option>
    </select>
    <button class="search-btn" onclick="filterResponses()">
        <i class="fas fa-search"></i> Filter
    </button>
</div>

<!-- Individual Responses -->
@if($surveys->count() > 0)
    <div id="responsesContainer">
        @foreach($surveys as $survey)
        <div class="response-card" data-survey-id="{{ $survey->id }}">
            <div class="response-header">
                <div class="respondent-details">
                    <h4>Responden #{{ $survey->id }}</h4>
                    <div class="respondent-meta">
                        <span><i class="fas fa-calendar-alt"></i> {{ $survey->created_at->format('d/m/Y H:i') }}</span>
                        <span><i class="fas fa-globe"></i> {{ $survey->ip_address ?: 'Tidak diketahui' }}</span>
                        @if($survey->responses->count() > 0)
                            <span><i class="fas fa-check-circle"></i> {{ $survey->responses->count() }} Jawaban</span>
                        @else
                            <span><i class="fas fa-clock"></i> Belum ada jawaban</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="response-body">
                <div class="response-summary">
                    <div class="summary-item">
                        <div class="summary-value">{{ $survey->responses->count() }}</div>
                        <div class="summary-title">Total Jawaban</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">{{ $survey->responses->whereNotNull('answer')->where('answer', '!=', '')->count() }}</div>
                        <div class="summary-title">Jawaban Terisi</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">{{ number_format(($survey->responses->whereNotNull('answer')->where('answer', '!=', '')->count() / max($questions->count(), 1)) * 100, 1) }}%</div>
                        <div class="summary-title">Kelengkapan</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value">{{ $survey->responses->where('question.question_type', 'file_upload')->whereNotNull('answer_data')->count() }}</div>
                        <div class="summary-title">File Diupload</div>
                    </div>
                </div>

                <!-- Preview Jawaban (3 pertanyaan pertama) -->
                @if($survey->responses->count() > 0)
                <div style="margin-bottom: 20px;">
                    <h5 style="color: #1e293b; margin-bottom: 12px; font-size: 15px; font-weight: 600;">
                        <i class="fas fa-eye"></i> Preview Jawaban:
                    </h5>
                    @foreach($survey->responses->take(3) as $response)
                        @if($response->question && $response->answer)
                        <div style="background: #f8f9fa; padding: 12px 15px; margin-bottom: 8px; border-radius: 4px; border: 1px solid #e2e8f0;">
                            <strong style="color: #334155; font-size: 13px;">{{ $response->question->question_text }}</strong>
                            <div style="color: #475569; font-size: 13px; margin-top: 4px;">
                                {{ Str::limit($response->answer, 100) }}
                            </div>
                        </div>
                        @endif
                    @endforeach
                    @if($survey->responses->count() > 3)
                        <small style="color: #64748b; font-style: italic; display: block; margin-top: 8px;">
                            <i class="fas fa-plus-circle"></i> Dan {{ $survey->responses->count() - 3 }} jawaban lainnya...
                        </small>
                    @endif
                </div>
                @endif

                <div class="response-actions">
                    <button class="action-btn detail-btn" onclick="showDetailModal({{ $survey->id }})">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteSurvey({{ $survey->id }})">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $surveys->appends(request()->query())->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3>Belum Ada Responden</h3>
        <p>Belum ada responden yang mengisi survei Anda. Bagikan link survei untuk mulai mengumpulkan data responden.</p>
        <a href="{{ route('survey.index') }}" class="btn btn-primary" style="display: inline-flex;">
            <i class="fas fa-external-link-alt"></i> Buka Survei
        </a>
    </div>
@endif

<!-- Detail Modal -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Detail Responden</h2>
            <span class="close" onclick="closeDetailModal()">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterResponses() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const dateFilter = document.getElementById('dateFilter').value;
        const responseCards = document.querySelectorAll('.response-card');

        responseCards.forEach(card => {
            const surveyId = card.dataset.surveyId;
            const cardText = card.textContent.toLowerCase();
            const cardDate = card.querySelector('.respondent-meta span').textContent;
            
            let showCard = true;

            if (searchTerm && !cardText.includes(searchTerm)) {
                showCard = false;
            }

            if (dateFilter && dateFilter !== '') {
                // Logic Date Filtering
            }

            card.style.display = showCard ? 'block' : 'none';
        });
    }

    function showDetailModal(surveyId) {
        const modal = document.getElementById('detailModal');
        const modalBody = document.getElementById('modalBody');
        
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #5a9b9e; margin-bottom: 20px;"></i>
                <p style="color: #64748b;">Memuat detail responden...</p>
            </div>
        `;
        
        modal.style.display = 'block';
        
        fetch(`/admin/survey/${surveyId}/detail`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                let detailHTML = `
                    <div class="detail-info">
                        <h4><i class="fas fa-info-circle"></i> Informasi Responden</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-top: 15px; font-size: 14px;">
                            <div><strong>ID Survei:</strong> #${data.survey.id}</div>
                            <div><strong>Tanggal:</strong> ${data.survey.created_at}</div>
                            <div><strong>IP Address:</strong> ${data.survey.ip_address}</div>
                        </div>
                    </div>
                `;

                data.sections.forEach((section, sectionIndex) => {
                    detailHTML += `
                        <div class="detail-section" style="margin-bottom: 25px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                            <div style="background: #2c3e50; color: white; padding: 15px 20px;">
                                <h4 style="margin: 0; display: flex; align-items: center; gap: 10px; font-size: 15px;">
                                    <i class="fas fa-layer-group"></i> ${section.title}
                                </h4>
                                ${section.description ? `<p style="margin: 6px 0 0 0; color: #cbd5e1; font-size: 13px;">${section.description}</p>` : ''}
                            </div>
                            <div style="background: white;">
                    `;

                    section.responses.forEach((response, responseIndex) => {
                        const isLast = responseIndex === section.responses.length - 1;
                        const borderClass = isLast ? '' : 'border-bottom: 1px solid #e2e8f0;';
                        
                        detailHTML += `
                            <div style="padding: 20px; ${borderClass}">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                    <h5 style="margin: 0; color: #1e293b; flex: 1; font-size: 14px; font-weight: 600;">${response.question_text}</h5>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">${response.question_type_label}</span>
                                        ${response.is_required ? '<span style="color: #dc3545; font-size: 11px; font-weight: 600;"><i class="fas fa-asterisk"></i> Wajib</span>' : '<span style="color: #64748b; font-size: 11px;"><i class="far fa-circle"></i> Opsional</span>'}
                                    </div>
                                </div>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #e2e8f0;">
                        `;

                        if (response.answer) {
                            if (response.question_type === 'linear_scale' && response.scale_info) {
                                detailHTML += `
                                    <div style="text-align: center;">
                                        <div style="font-size: 20px; font-weight: 700; color: #5a9b9e; margin-bottom: 5px;">${response.answer}</div>
                                        <div style="font-size: 13px; color: #64748b;">
                                            Skala ${response.scale_info.min} - ${response.scale_info.max}
                                            ${response.scale_info.min_label || response.scale_info.max_label ? `<br><small>${response.scale_info.min_label} - ${response.scale_info.max_label}</small>` : ''}
                                        </div>
                                    </div>
                                `;
                            } else if (response.question_type === 'file_upload' && response.file_info) {
                                detailHTML += `
                                    <div style="display: flex; align-items: center; gap: 15px; justify-content: space-between; flex-wrap: wrap;">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <i class="fas fa-file" style="font-size: 20px; color: #28a745;"></i>
                                            <div>
                                                <div style="font-weight: 600; color: #1e293b; font-size: 13px;">${response.formatted_answer}</div>
                                                <div style="font-size: 12px; color: #64748b;">
                                                    ${response.file_info.size ? (response.file_info.size / 1024).toFixed(1) + ' KB' : 'Ukuran tidak diketahui'} 
                                                    ${response.file_info.mime_type ? '• ' + response.file_info.mime_type : ''}
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 8px;">
                                            ${isImage(response.file_info.mime_type) ? 
                                                `<a href="/admin/response/${response.response_id}/view" target="_blank" style="background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>` : ''
                                            }
                                            <a href="/admin/response/${response.response_id}/download" style="background: #28a745; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                `;
                            } else {
                                detailHTML += `<div style="color: #334155; line-height: 1.5; font-size: 13px;">${response.formatted_answer}</div>`;
                            }
                        } else {
                            detailHTML += `<div style="color: #dc3545; font-style: italic; font-size: 13px;"><i class="fas fa-minus-circle"></i> Tidak dijawab</div>`;
                        }

                        detailHTML += `
                                </div>
                            </div>
                        `;
                    });

                    detailHTML += `
                            </div>
                        </div>
                    `;
                });

                modalBody.innerHTML = detailHTML;
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = `
                    <div class="detail-info">
                        <h4><i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i> Error</h4>
                        <p style="color: #dc3545;">Terjadi kesalahan saat memuat detail responden.</p>
                    </div>
                `;
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('detailModal');
        if (event.target === modal) {
            closeDetailModal();
        }
    }

    function isImage(mimeType) {
        if (!mimeType) return false;
        return mimeType.startsWith('image/');
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const title = type === 'success' ? 'Berhasil!' : 'Error!';
        
        toast.innerHTML = `
            <i class="fas ${icon} toast-icon"></i>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <i class="fas fa-times toast-close" onclick="this.parentElement.remove()"></i>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function deleteSurvey(surveyId) {
        fetch(`/admin/survey/${surveyId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                const card = document.querySelector(`[data-survey-id="${surveyId}"]`);
                if (card) {
                    card.style.display = 'none';
                    const container = document.getElementById('responsesContainer');
                    if (container && container.querySelectorAll('.response-card[style!="display: none;"]').length === 0) {
                        location.reload();
                    }
                }
            } else {
                showToast(data.message || 'Gagal menghapus survey', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat menghapus survey', 'error');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') filterResponses();
        });
        document.getElementById('dateFilter').addEventListener('change', filterResponses);
    });
</script>
@endpush