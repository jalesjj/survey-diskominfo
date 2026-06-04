{{-- resources/views/admin/jawaban-individual.blade.php --}}
@extends('layouts.admin')

@section('title', 'Jawaban Individual - Survei Kepuasan')
@section('active-jawaban', 'active')
@section('page-title', 'Jawaban Individual')
@section('page-subtitle', 'Data responden individual')

@section('header-actions')
<div class="header-actions">
    {{-- DROPDOWN FILTER PERIODE - POSISI KANAN ATAS --}}
    @if(isset($allPeriods) && $allPeriods->count() > 0)
    <div class="period-filter-container">
        <form action="{{ route('admin.jawaban') }}" method="GET" id="periodFilterForm">
            <input type="hidden" name="tab" value="individual">
            <select name="period_id" class="period-select" onchange="document.getElementById('periodFilterForm').submit()">
                <option value="">📅 Semua Periode</option>
                @foreach($allPeriods as $period)
                    <option value="{{ $period->id }}" 
                        {{ (isset($selectedPeriod) && $selectedPeriod && $selectedPeriod->id == $period->id) ? 'selected' : '' }}>
                        {{ $period->period_name }} ({{ $period->year }})
                        @if($period->is_active) ⭐ @endif
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif
    
    <span class="admin-welcome">Selamat datang, {{ session('admin_name') }}</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* =========================================
       DROPDOWN PERIODE - STYLING
       ========================================= */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .period-filter-container {
        margin-right: auto;
    }

    .period-select {
        padding: 8px 35px 8px 15px;
        border: 2px solid #5a9b9e;
        border-radius: 8px;
        background: white;
        color: #2c3e50;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%235a9b9e' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        min-width: 250px;
    }

    .period-select:hover {
        border-color: #4a8b8e;
        box-shadow: 0 2px 8px rgba(90, 155, 158, 0.2);
    }

    .period-select:focus {
        outline: none;
        border-color: #5a9b9e;
        box-shadow: 0 0 0 3px rgba(90, 155, 158, 0.1);
    }

    .admin-welcome {
        white-space: nowrap;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .period-filter-container {
            width: 100%;
            margin-right: 0;
        }

        .period-select {
            width: 100%;
            min-width: auto;
        }
    }

    /* =========================================
       INFO BOX PERIODE - SEPERTI DI HALAMAN QUESTIONS
       ========================================= */
    .period-info-box {
        background: white;
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .period-info-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .period-info-left i {
        color: #5a9b9e;
    }

    .period-info-text {
        font-weight: 600;
        color: #2c3e50;
        font-size: 14px;
    }

    .period-info-all {
        font-weight: 600;
        color: #7f8c8d;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .period-info-box {
            flex-direction: column;
            align-items: flex-start;
        }
    }

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
        transition: all 0.2s ease;
    }

    .tab-item:last-child {
        border-right: none;
    }

    .tab-item:hover {
        background: #e9ecef;
        color: #495057;
    }

    .tab-item.active {
        background: white;
        color: #5a9b9e;
        border-bottom: 2px solid #5a9b9e;
    }

    .tab-icon {
        font-size: 16px;
    }

    /* Search and Filter Bar */
    .search-filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
    }

    .filter-select {
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        background: white;
        cursor: pointer;
    }

    .search-btn {
        padding: 10px 20px;
        background: #5a9b9e;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s ease;
    }

    .search-btn:hover {
        background: #4a8b8e;
    }

    /* Response Cards */
    .response-card {
        background: white;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        overflow: hidden;
        transition: border-color 0.2s ease;
    }

    .response-card:hover {
        border-color: #cbd5e0;
    }

    .response-header {
        background: #f8f9fa;
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .respondent-details h4 {
        margin: 0 0 10px 0;
        color: #2c3e50;
        font-size: 18px;
    }

    .respondent-meta {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        font-size: 13px;
        color: #64748b;
    }

    .respondent-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .response-body {
        padding: 20px;
    }

    .response-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
        text-align: center;
    }

    .summary-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 5px;
    }

    .summary-title {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }

    .action-row {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    .action-row .btn {
        font-size: 13px;
        padding: 8px 16px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #64748b;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #94a3b8;
    }

    /* Pagination */
    .pagination-container {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pagination li {
        display: inline-block;
    }

    .pagination a,
    .pagination span {
        display: block;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        text-decoration: none;
        color: #64748b;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .pagination a:hover {
        background: #f8f9fa;
        border-color: #cbd5e0;
    }

    .pagination .active span {
        background: #5a9b9e;
        color: white;
        border-color: #5a9b9e;
    }

    .pagination .disabled span {
        color: #cbd5e0;
        cursor: not-allowed;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        overflow: auto;
    }

    .modal-content {
        background-color: white;
        margin: 50px auto;
        padding: 0;
        border-radius: 8px;
        width: 90%;
        max-width: 800px;
        max-height: 85vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        background: #5a9b9e;
        color: white;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 20px;
    }

    .close {
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
        transition: color 0.2s ease;
    }

    .close:hover {
        color: #e2e8f0;
    }

    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }

    .detail-section {
        margin-bottom: 30px;
    }

    .detail-section h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 16px;
        font-weight: 700;
        border-bottom: 2px solid #5a9b9e;
        padding-bottom: 10px;
    }

    .detail-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .detail-question {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .detail-answer {
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        margin-left: 8px;
    }

    .badge-required {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-optional {
        background: #e0f2fe;
        color: #075985;
    }

    /* Loading State */
    .loading {
        text-align: center;
        padding: 40px;
        color: #64748b;
    }

    .spinner {
        border: 3px solid #f3f4f6;
        border-top: 3px solid #5a9b9e;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .summary-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .response-summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .search-filter-bar {
            flex-direction: column;
        }

        .search-input {
            width: 100%;
        }

        .modal-content {
            width: 95%;
            margin: 20px auto;
        }
    }

    /*=========================================
    SAW BADGE — paste di dalam blok <style>
    ========================================= */
    .saw-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
        margin-left: 10px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .saw-badge.sangat-baik  { background: #d1fae5; color: #065f46; }
    .saw-badge.baik         { background: #dbeafe; color: #1e40af; }
    .saw-badge.cukup        { background: #fef9c3; color: #854d0e; }
    .saw-badge.kurang       { background: #ffedd5; color: #9a3412; }
    .saw-badge.sangat-kurang{ background: #fee2e2; color: #991b1b; }
    .saw-badge.no-saw       { background: #f1f5f9; color: #94a3b8; }
    
    /* Tambahkan di dalam blok <style> yang sudah ada */
.respondent-table-scroll {
    max-height: 305px;  /* tinggi sekitar 5 baris (1 baris ~56px + header ~49px) */
    overflow-y: auto;
}

/* Supaya header tetap terlihat saat scroll */
.respondent-saw-table thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #f8f9fa;
}
</style>
@endpush

@section('content')
<div class="page-container">

    {{-- INFO BOX PERIODE - SAMA SEPERTI DI HALAMAN QUESTIONS --}}
    @if(isset($allPeriods) && $allPeriods->count() > 0)
    <div class="period-info-box">
        {{-- Info Periode di Kiri --}}
        @if(isset($selectedPeriod) && $selectedPeriod)
        <div class="period-info-left">
            <i class="fas fa-calendar-check"></i>
            <span class="period-info-text">
                Periode: {{ $selectedPeriod->period_name }} ({{ $selectedPeriod->year }})
                @if($selectedPeriod->is_active) <span style="color: #f39c12;">⭐</span> @endif
            </span>
        </div>
        @else
        <div class="period-info-left">
            <i class="fas fa-calendar" style="color: #95a5a6;"></i>
            <span class="period-info-all">Periode: Semua</span>
        </div>
        @endif
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('admin.questions.index') }}" class="btn btn-primary">
            <i class="fas fa-cogs"></i> Kelola Pertanyaan
        </a>
        <a href="{{ route('admin.export') }}" class="btn btn-success">
            <i class="fas fa-download"></i> Export Excel
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
        <a href="{{ route('admin.jawaban', array_merge(['tab' => 'questions'], request('period_id') ? ['period_id' => request('period_id')] : [])) }}" class="tab-item">
            <i class="fas fa-question-circle tab-icon"></i>
            <span>Pertanyaan</span>
        </a>
        <a href="{{ route('admin.jawaban', array_merge(['tab' => 'individual'], request('period_id') ? ['period_id' => request('period_id')] : [])) }}" class="tab-item active">
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
                    @php
                        // ✅ Ambil nama dari responses dengan question_id = 'nama'
                        $namaResponse = $survey->responses->where('question_id', 'nama')->first();
                        $nama = $namaResponse ? $namaResponse->answer : 'Responden #' . $survey->id;
                    @endphp
                    @php
                        $sawData  = $sawScores[$survey->id] ?? ['score' => 0, 'has_saw' => false, 'interpretation' => '-'];
                        $sawClass = match(true) {
                            !$sawData['has_saw']                                 => 'no-saw',
                            $sawData['interpretation'] === 'Sangat Baik'         => 'sangat-baik',
                            $sawData['interpretation'] === 'Baik'                => 'baik',
                            $sawData['interpretation'] === 'Cukup'               => 'cukup',
                            $sawData['interpretation'] === 'Kurang'              => 'kurang',
                            default                                              => 'sangat-kurang',
                        };
                    @endphp
                    
                    <h4>
                        {{ $nama }}
                        @if($sawData['has_saw'])
                            <span class="saw-badge {{ $sawClass }}">
                                <i class=""></i>
                                {{ number_format($sawData['score'], 3) }} &mdash; {{ $sawData['interpretation'] }}
                            </span>
                        @else
                            <span class="saw-badge no-saw">
                                <i class="fas fa-minus"></i> Tidak ada data SAW
                            </span>
                        @endif
                    </h4>
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
                        @php
                            // ✅ FIX: Hitung kelengkapan berdasarkan total jawaban survey ini
                            $totalJawaban = $survey->responses->count();
                            $jawabanTerisi = $survey->responses->whereNotNull('answer')->where('answer', '!=', '')->count();
                            $kelengkapan = $totalJawaban > 0 ? ($jawabanTerisi / $totalJawaban) * 100 : 0;
                        @endphp
                        <div class="summary-value">{{ number_format($kelengkapan, 1) }}%</div>
                        <div class="summary-title">Kelengkapan</div>
                    </div>
                </div>

                <div class="action-row">
                    <button class="btn btn-info" onclick="viewDetail({{ $survey->id }})">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </button>
                    <button class="btn btn-danger" onclick="confirmDelete({{ $survey->id }})">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $surveys->appends(request()->query())->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>Belum Ada Data Responden</h3>
        <p>Belum ada survey yang diisi untuk periode ini. Silakan tunggu responden mengisi survey.</p>
    </div>
@endif

</div>

<!-- Modal untuk Detail Jawaban -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Detail Jawaban Responden</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body" id="modalBody">
            <div class="loading">
                <div class="spinner"></div>
                <p>Memuat data...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewDetail(surveyId) {
    const modal = document.getElementById('detailModal');
    const modalBody = document.getElementById('modalBody');
    
    modal.style.display = 'block';
    modalBody.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Memuat data...</p>
        </div>
    `;

    // ✅ FIX: Ambil period_id dari URL dan kirim ke backend
    const urlParams = new URLSearchParams(window.location.search);
    const periodId = urlParams.get('period_id');
    // ✅ FIX: URL yang benar sesuai route Laravel
    const url = `/admin/survey/${surveyId}/detail${periodId ? '?period_id=' + periodId : ''}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalBody.innerHTML = `<p style="color: #dc3545;">Error: ${data.error}</p>`;
                return;
            }

            let html = `
                <div class="detail-section">
                    <div class="detail-item">
                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                            <strong>Tanggal:</strong><span>${data.survey.created_at}</span>
                        </div>
                    </div>
                </div>
            `;

            data.sections.forEach(section => {
                html += `
                    <div class="detail-section">
                        <h3><i class="fas fa-folder"></i> ${section.title}</h3>
                        ${section.description ? `<p style="color: #64748b; margin-bottom: 15px;">${section.description}</p>` : ''}
                `;

                section.responses.forEach(response => {
                    const requiredBadge = response.is_required 
                        ? '<span class="badge badge-required">Wajib</span>' 
                        : '<span class="badge badge-optional">Opsional</span>';
                    
                    html += `
                        <div class="detail-item">
                            <div class="detail-question">
                                ${response.question_text} ${requiredBadge}
                            </div>
                            <div class="detail-answer">
                                <strong>Jawaban:</strong> ${response.formatted_answer || '-'}
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
            });

            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = `<p style="color: #dc3545;">Terjadi kesalahan saat memuat data.</p>`;
            console.error('Error:', error);
        });
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}

function confirmDelete(surveyId) {
    if (confirm('Apakah Anda yakin ingin menghapus survey ini? Tindakan ini tidak dapat dibatalkan.')) {
        fetch(`/admin/survey/${surveyId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat menghapus survey');
            console.error('Error:', error);
        });
    }
}

function filterResponses() {
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const dateFilter = document.getElementById('dateFilter').value;
    const cards = document.querySelectorAll('.response-card');

    cards.forEach(card => {
        let showCard = true;

        // Filter by search text (you can customize this to search in survey data)
        if (searchText && !card.textContent.toLowerCase().includes(searchText)) {
            showCard = false;
        }

        // Filter by date (you can implement date filtering logic here)
        if (dateFilter && dateFilter !== '') {
            // Implement date filtering logic based on your requirements
        }

        card.style.display = showCard ? 'block' : 'none';
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
@endpush
@endsection