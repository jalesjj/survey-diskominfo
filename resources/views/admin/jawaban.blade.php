{{-- resources/views/admin/jawaban.blade.php --}}
@extends('layouts.admin')

@section('title', 'Jawaban - Survei Kepuasan Diskominfo Lamongan')
@section('active-jawaban', 'active')
@section('page-title', 'Jawaban Survei')
@section('page-subtitle', 'Lihat dan analisis hasil jawaban survei kepuasan masyarakat')

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

    .btn-warning { background: #ffc107; color: #212529; }
    .btn-warning:hover { background: #e0a800; color: #212529; }

    .btn-info { background: #17a2b8; color: white; }
    .btn-info:hover { background: #138496; color: white; }

    .btn-danger { background: #e74c3c; color: white; }
    .btn-danger:hover { background: #c0392b; color: white; }

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
        flex-wrap: wrap;
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
        min-width: 50%;
        transition: background-color 0.2s;
    }

    .tab-item:last-child { border-right: none; }
    .tab-item:hover { background: #f1f5f9; color: #495057; }
    .tab-item.active { background: #5a9b9e; color: white; }

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

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 30px;
        overflow: hidden;
        counter-reset: question-counter;
    }

    .section-header {
        background: #2c3e50; /* Flat solid color */
        color: white;
        padding: 20px 25px;
        border-bottom: 1px solid #1a252f;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-description {
        font-size: 15px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }

    .section-meta {
        display: flex;
        gap: 15px;
        font-size: 14px;
        color: #94a3b8;
    }

    /* Question Cards */
    .question-card {
        padding: 25px;
        margin: 15px 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        position: relative;
    }

    /* Question Number Badge */
    .question-card::before {
        content: counter(question-counter);
        counter-increment: question-counter;
        position: absolute;
        top: 25px;
        left: 25px;
        background: #5a9b9e;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
    }

    .question-header {
        margin-bottom: 20px;
        padding-left: 45px;
    }

    .question-text {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .question-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .question-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #475569;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #e2e8f0;
    }

    /* Stats Grid */
    .response-stats {
        display: grid;
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        text-align: center;
        border: 1px solid #e2e8f0;
    }

    .stat-number {
        font-size: 18px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* Response List Items */
    .response-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        background: #ffffff;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    .response-text {
        flex: 1;
        font-weight: 500;
        color: #334155;
        font-size: 14px;
    }

    .response-count {
        background: #5a9b9e;
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }

    .response-percentage {
        font-size: 12px;
        color: #64748b;
        margin-left: 6px;
    }

    /* Chart Containers */
    .chart-container, .response-list {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-top: 15px;
    }

    .chart-container h4, .response-list h4, .sample-responses h4, .file-responses h4 {
        color: #1e293b;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 10px;
    }

    .chart-canvas-container {
        position: relative;
        width: 100%;
        height: 280px;
        margin-bottom: 15px;
    }

    /* Chart Toggle Controls */
    .chart-controls {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .chart-toggle-btn {
        padding: 6px 14px;
        border: 1px solid #cbd5e1;
        background: white;
        color: #64748b;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .chart-toggle-btn.active {
        background: #5a9b9e;
        color: white;
        border-color: #5a9b9e;
    }

    /* Simple Bars */
    .distribution-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #334155;
    }

    .distribution-item:last-child {
        border-bottom: none;
    }

    .distribution-bar {
        height: 12px;
        background: #5a9b9e;
        border-radius: 4px;
        margin-left: 10px;
        min-width: 20px;
    }

    /* Files */
    .file-responses {
        margin-top: 15px;
    }

    .file-response {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        margin-bottom: 10px;
    }

    .file-icon {
        color: #28a745;
        font-size: 20px;
    }

    .file-info { flex: 1; }
    .file-name { font-weight: 600; color: #1e293b; font-size: 14px; }
    .file-date { font-size: 12px; color: #64748b; margin-top: 4px; }
    
    .action-btn {
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    }

    .view-btn { background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; }
    .view-btn:hover { background: #e2e8f0; }
    .download-btn { background: #28a745; color: white; border: 1px solid #28a745; }
    .download-btn:hover { background: #218838; }

    /* Texts */
    .sample-responses {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
        border: 1px solid #e2e8f0;
    }

    .sample-responses h4 {
        margin: 0 0 12px 0;
    }

    .sample-responses-scroll {
        max-height: 280px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }

    .sample-responses-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .sample-responses-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .sample-responses-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .sample-response {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .sample-response:last-child { border-bottom: none; }
    .sample-response .response-text { font-style: italic; color: #334155; margin-bottom: 4px; }
    .sample-response .response-date { font-size: 12px; color: #94a3b8; }

    /* Layout Splits */
    .response-split {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 20px;
        align-items: start;
    }
    
    .response-list .response-stats {
        max-height: 350px;
        overflow-y: auto;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
        margin: 20px;
    }

    .empty-state i { font-size: 40px; margin-bottom: 15px; color: #cbd5e1; }
    .empty-state h3 { font-size: 18px; margin-bottom: 10px; color: #334155; }
    .empty-state p { font-size: 14px; margin-bottom: 20px; }

    .admin-welcome { font-size: 14px; color: #64748b; }

    /* Word breaks */
    .question-text, .section-title, .section-description {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* Scrollbars */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }

    /* Responsive */
    @media (max-width: 992px) {
        .response-split { grid-template-columns: 1fr; }
        .response-list .response-stats { max-height: none; }
    }

    @media (max-width: 768px) {
        .question-card { padding: 20px; }
        .question-header { padding-left: 35px; }
        .question-card::before { top: 20px; left: 15px; width: 24px; height: 24px; font-size: 12px; }
        .response-item { flex-direction: column; align-items: flex-start; gap: 8px; }
        .response-count { margin-left: 0; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    {{-- DROPDOWN FILTER PERIODE & INFO PERIODE - SATU BARIS --}}
    @if(isset($allPeriods) && $allPeriods->count() > 0)
    <div style="background: white; padding: 15px 20px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        {{-- Info Periode di Kiri --}}
        @if(isset($selectedPeriod) && $selectedPeriod)
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-calendar-check" style="color: #5a9b9e;"></i>
            <span style="font-weight: 600; color: #2c3e50; font-size: 14px;">
                Periode: {{ $selectedPeriod->period_name }} ({{ $selectedPeriod->year }})
                @if($selectedPeriod->is_active) <span style="color: #f39c12;">⭐</span> @endif
            </span>
        </div>
        @else
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-calendar" style="color: #95a5a6;"></i>
            <span style="font-weight: 600; color: #7f8c8d; font-size: 14px;">Periode: Semua</span>
        </div>
        @endif
        
        {{-- Dropdown di Kanan --}}
        <form action="{{ route('admin.jawaban') }}" method="GET" id="periodFilterForm" style="margin: 0;">
            <input type="hidden" name="tab" value="{{ request('tab', 'questions') }}">
            <select 
                name="period_id" 
                onchange="document.getElementById('periodFilterForm').submit()" 
                style="padding: 8px 35px 8px 15px; border: 2px solid #5a9b9e; border-radius: 8px; background: white url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2712%27 height=%2712%27 viewBox=%270 0 12 12%27%3E%3Cpath fill=%27%235a9b9e%27 d=%27M6 9L1 4h10z%27/%3E%3C/svg%3E') no-repeat right 12px center; color: #2c3e50; font-weight: 600; font-size: 14px; cursor: pointer; min-width: 250px; appearance: none; -webkit-appearance: none; -moz-appearance: none; transition: all 0.3s ease;">
                <option value="">Periode</option>
                @foreach($allPeriods as $period)
                    <option value="{{ $period->id }}" 
                        {{ (isset($selectedPeriod) && $selectedPeriod && $selectedPeriod->id == $period->id) ? 'selected' : '' }}>
                        {{ $period->year }}
                        @if($period->is_active) ⭐ @endif
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('admin.questions.index') }}" class="btn btn-primary">
            <i class="fas fa-cogs"></i> Kelola Pertanyaan
        </a>
        <a href="{{ route('admin.export', isset($selectedPeriod) && $selectedPeriod ? ['period_id' => $selectedPeriod->id] : []) }}" class="btn btn-success">
            <i class="fas fa-download"></i> Export Excel
        </a>
        {{-- <a href="{{ route('admin.hasil-survey.export-pdf', isset($selectedPeriod) && $selectedPeriod ? ['period_id' => $selectedPeriod->id] : []) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a> --}}
    </div>

    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <div class="tab-nav">
            <a href="{{ route('admin.jawaban', array_merge(['tab' => 'questions'], request('period_id') ? ['period_id' => request('period_id')] : [])) }}" 
                class="tab-item {{ request('tab', 'questions') === 'questions' ? 'active' : '' }}">
                <i class="tab-icon fas fa-question-circle"></i>
                Pertanyaan & Jawaban
            </a>
            <a href="{{ route('admin.jawaban', array_merge(['tab' => 'individual'], request('period_id') ? ['period_id' => request('period_id')] : [])) }}" 
                class="tab-item {{ request('tab') === 'individual' ? 'active' : '' }}">
                <i class="tab-icon fas fa-users"></i>
                Individual
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="summary-stats">
        <div class="summary-card">
            <div class="summary-number">{{ $totalSurveys }}</div>
            <div class="summary-label">Total Responden</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $questions->count() }}</div>
            <div class="summary-label">Total Pertanyaan</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $questions->where('is_required', true)->count() }}</div>
            <div class="summary-label">Wajib Diisi</div>
        </div>
        <div class="summary-card">
            <div class="summary-number">{{ $questions->sum(function($q) { return $q->responses->count(); }) }}</div>
            <div class="summary-label">Total Jawaban</div>
        </div>
    </div>

    <!-- Content Based on Available Data -->
    @if(isset($sectionStats) && count($sectionStats) > 0)
        <!-- Section-based View -->
        @foreach($sectionStats as $sectionStat)
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-layer-group"></i>
                        {{ $sectionStat['section']->title }}
                    </h2>
                    @if($sectionStat['section']->description)
                        <p class="section-description">{{ $sectionStat['section']->description }}</p>
                    @endif
                    <div class="section-meta">
                        <span><i class="fas fa-question-circle"></i> {{ $sectionStat['total_questions'] }} pertanyaan</span>
                        <span><i class="fas fa-comments"></i> {{ $sectionStat['total_responses'] }} jawaban</span>
                    </div>
                </div>

                @foreach($sectionStat['questions_stats'] as $stat)
                    <div class="question-card">
                        <div class="question-header">
                            <h3 class="question-text">{{ $stat['question']->question_text }}</h3>
                            <div class="question-meta">
                                <span><i class="fas fa-tag"></i> {{ $stat['question_type_label'] ?? 'Unknown' }}</span>
                                <span><i class="fas fa-users"></i> {{ $stat['total_responses'] }} responden</span>
                                <span><i class="fas fa-percentage"></i> {{ $stat['response_rate'] }}% tingkat respons</span>
                                @if($stat['question']->is_required)
                                    <span><i class="fas fa-asterisk"></i> Wajib diisi</span>
                                @endif
                            </div>
                        </div>

                        <div class="response-stats" style="grid-template-columns: repeat(2, 1fr);">
                            <div class="stat-item">
                                <div class="stat-number">{{ $stat['total_responses'] }}</div>
                                <div class="stat-label">Jawaban</div>
                            </div>
                            {{-- <div class="stat-item">
                                <div class="stat-number">{{ $stat['response_rate'] }}%</div>
                                <div class="stat-label">Tingkat Respons</div>
                            </div> --}}
                        </div>

                        <div class="response-data">
                            @if(isset($stat['chart_enabled']) && $stat['chart_enabled'] && isset($stat['response_data']))
                                @if($stat['question']->question_type === 'linear_scale')
                                    <!-- Linear Scale Display dengan Chart Toggle -->
                                    <div class="chart-container">
                                        <h4><i class="fas fa-chart-bar"></i> Statistik Skala</h4>
                                        
                                        <div class="chart-controls">
                                            <button class="chart-toggle-btn active" onclick="toggleChart('{{ $stat['question']->id }}', 'bar')">
                                                <i class="fas fa-chart-bar"></i> Batang
                                            </button>
                                            <button class="chart-toggle-btn" onclick="toggleChart('{{ $stat['question']->id }}', 'doughnut')">
                                                <i class="fas fa-chart-pie"></i> Donat
                                            </button>
                                        </div>

                                        <div class="chart-canvas-container">
                                            <canvas id="chart_{{ $stat['question']->id }}"></canvas>
                                        </div>

                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 15px; margin: 15px 0;">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $stat['data']['average'] ?? 0 }}</div>
                                                <div class="stat-label">Rata-rata</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $stat['data']['min'] ?? 0 }}</div>
                                                <div class="stat-label">Minimum</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $stat['data']['max'] ?? 0 }}</div>
                                                <div class="stat-label">Maximum</div>
                                            </div>
                                        </div>
                                    </div>
                                
                                @elseif(in_array($stat['question']->question_type, ['multiple_choice', 'dropdown', 'checkbox']))
                                    <!-- Chart-enabled Question Types dengan Toggle -->
                                    <div class="response-split">
                                        <!-- KIRI: CHART -->
                                        <div class="response-chart">
                                            <div class="chart-container">
                                                <h4><i class="fas fa-chart-pie"></i> Distribusi Jawaban</h4>

                                                <div class="chart-controls">
                                                    <button class="chart-toggle-btn active" onclick="toggleChart('{{ $stat['question']->id }}', 'doughnut')">
                                                        <i class="fas fa-chart-pie"></i> Donat
                                                    </button>
                                                    <button class="chart-toggle-btn" onclick="toggleChart('{{ $stat['question']->id }}', 'bar')">
                                                        <i class="fas fa-chart-bar"></i> Batang
                                                    </button>
                                                </div>

                                                <div class="chart-canvas-container">
                                                    <canvas id="chart_{{ $stat['question']->id }}"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- KANAN: LIST -->
                                        <div class="response-list">
                                            <div class="response-stats" style="grid-template-columns: 1fr; gap: 8px;">
                                                @foreach($stat['response_data'] as $response)
                                                    <div class="response-item">
                                                        <span class="response-text">{{ $response->answer }}</span>
                                                        <div>
                                                            <span class="response-count">{{ $response->count }}</span>
                                                            <span class="response-percentage">
                                                                ({{ number_format(($response->count / $stat['total_responses']) * 100, 1) }}%)
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            
                            @elseif(isset($stat['data']) && is_array($stat['data']) && count($stat['data']) > 0)
                                @if($stat['question']->question_type === 'linear_scale')
                                    <!-- Linear Scale Fallback Display (without chart) -->
                                    <div class="chart-container">
                                        <h4><i class="fas fa-chart-bar"></i> Statistik Skala</h4>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 15px; margin: 15px 0;">
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $stat['data']['average'] ?? 0 }}</div>
                                                <div class="stat-label">Rata-rata</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $stat['data']['min'] ?? 0 }}</div>
                                                <div class="stat-label">Minimum</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-number">{{ $stat['data']['max'] ?? 0 }}</div>
                                                <div class="stat-label">Maximum</div>
                                            </div>
                                        </div>
                                        @if(isset($stat['data']['distribution']))
                                            <h5 style="margin-top: 15px; color: #475569;">Distribusi Nilai:</h5>
                                            @foreach($stat['data']['distribution'] as $value => $count)
                                                <div class="distribution-item">
                                                    <span>Nilai {{ $value }}</span>
                                                    <div style="display: flex; align-items: center;">
                                                        <span style="margin-right: 10px; font-size: 13px;">{{ $count }} orang</span>
                                                        <div class="distribution-bar" style="width: {{ ($count / $stat['total_responses']) * 150 }}px;"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @else
                                    <!-- Multiple Choice / Checkbox Fallback Display -->
                                    <div class="chart-container">
                                        <h4><i class="fas fa-list"></i> Distribusi Jawaban</h4>
                                        @foreach($stat['data'] as $answer => $count)
                                            <div class="distribution-item">
                                                <span>{{ $answer }}</span>
                                                <div style="display: flex; align-items: center;">
                                                    <span style="margin-right: 10px; font-size: 13px;">{{ $count }} orang</span>
                                                    <div class="distribution-bar" style="width: {{ ($count / array_sum($stat['data'])) * 150 }}px;"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @elseif(isset($stat['response_data']) && is_array($stat['response_data']) && count($stat['response_data']) > 0)
                                <!-- File Upload Display -->
                                <div class="file-responses">
                                    <h4><i class="fas fa-file-upload"></i> File yang Diupload ({{ count($stat['response_data']) }})</h4>
                                    @foreach($stat['response_data'] as $file)
                                        <div class="file-response">
                                            <i class="fas fa-file file-icon"></i>
                                            <div class="file-info">
                                                <div class="file-name">{{ $file['filename'] ?? 'File tidak tersedia' }}</div>
                                                <div class="file-date">Upload: {{ isset($file['upload_date']) ? \Carbon\Carbon::parse($file['upload_date'])->format('d/m/Y H:i') : '-' }}</div>
                                            </div>
                                            @if(isset($file['response_id']))
                                            <div style="display: flex; gap: 8px;">
                                                @if(isset($file['file_data']['mime_type']) && str_starts_with($file['file_data']['mime_type'], 'image/'))
                                                    <a href="{{ route('admin.viewFile', $file['response_id']) }}" target="_blank" class="action-btn view-btn">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.downloadFile', $file['response_id']) }}" class="action-btn download-btn">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(isset($stat['sample_responses']) && is_array($stat['sample_responses']) && count($stat['sample_responses']) > 0)
                                <!-- Text Responses Display -->
                                <div class="sample-responses">
                                    <h4><i class="fas fa-comment-dots"></i> Semua Jawaban ({{ count($stat['sample_responses']) }})</h4>
                                    <div class="sample-responses-scroll">
                                    @foreach($stat['sample_responses'] as $response)
                                        <div class="sample-response">
                                            <div class="response-text">"{{ $response['answer'] }}"</div>
                                            <div class="response-date">{{ $response['created_at'] }}</div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada jawaban untuk pertanyaan ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @elseif($questions->count() > 0)
        <!-- Fallback: Simple Questions List -->
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>
                    Semua Pertanyaan
                </h2>
                <p class="section-description">Daftar pertanyaan survei dan statistik jawaban</p>
            </div>

            @foreach($questions as $question)
                <div class="question-card">
                    <div class="question-header">
                        <h3 class="question-text">{{ $question->question_text }}</h3>
                        <div class="question-meta">
                            <span><i class="fas fa-tag"></i> {{\App\Helpers\SurveyDefaults::getQuestionTypeLabel($question->question_type) }}</span>
                            <span><i class="fas fa-users"></i> {{ $question->responses->count() }} responden</span>
                            @if($question->is_required)
                                <span><i class="fas fa-asterisk"></i> Wajib diisi</span>
                            @endif
                            @if($question->section)
                                <span><i class="fas fa-folder"></i> {{ $question->section->title }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="response-stats" style="grid-template-columns: repeat(2, 1fr);">
                        <div class="stat-item">
                            <div class="stat-number">{{ $question->responses->count() }}</div>
                            <div class="stat-label">Total Jawaban</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $totalSurveys > 0 ? round(($question->responses->count() / $totalSurveys) * 100, 1) : 0 }}%</div>
                            <div class="stat-label">Tingkat Respons</div>
                        </div>
                    </div>

                    @if($question->responses->count() > 0)
                        <div class="response-data">
                            @if($question->question_type === 'multiple_choice')
                                <!-- Multiple Choice Simple View -->
                                @php
                                    $answers = $question->responses->pluck('answer')->countBy();
                                @endphp
                                <div class="chart-container">
                                    <h4><i class="fas fa-chart-pie"></i> Distribusi Jawaban</h4>
                                    @foreach($answers as $answer => $count)
                                        <div class="distribution-item">
                                            <span>{{ $answer }}</span>
                                            <div style="display: flex; align-items: center;">
                                                <span style="margin-right: 10px; font-size: 13px;">{{ $count }} orang</span>
                                                <div class="distribution-bar" style="width: {{ ($count / $question->responses->count()) * 150 }}px;"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->question_type === 'linear_scale')
                                <!-- Linear Scale Simple View -->
                                @php
                                    $responses = $question->responses->pluck('answer')->filter()->map(function($item) {
                                        return (int) $item;
                                    });
                                    $average = $responses->avg();
                                    $distribution = $responses->countBy();
                                @endphp
                                <div class="chart-container">
                                    <h4><i class="fas fa-chart-bar"></i> Statistik Skala</h4>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 15px; margin: 15px 0;">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ round($average, 2) }}</div>
                                            <div class="stat-label">Rata-rata</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $responses->min() }}</div>
                                            <div class="stat-label">Minimum</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $responses->max() }}</div>
                                            <div class="stat-label">Maximum</div>
                                        </div>
                                    </div>
                                    <h5 style="margin-top: 15px; color: #475569;">Distribusi Nilai:</h5>
                                    @foreach($distribution as $value => $count)
                                        <div class="distribution-item">
                                            <span>Nilai {{ $value }}</span>
                                            <div style="display: flex; align-items: center;">
                                                <span style="margin-right: 10px; font-size: 13px;">{{ $count }} orang</span>
                                                <div class="distribution-bar" style="width: {{ ($count / $question->responses->count()) * 150 }}px;"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->question_type === 'file_upload')
                                <!-- File Upload Simple View -->
                                @php
                                    $fileResponses = $question->responses->filter(function($response) {
                                        return $response->answer_data !== null;
                                    });
                                @endphp
                                <div class="file-responses">
                                    <h4><i class="fas fa-file-upload"></i> File yang Diupload ({{ $fileResponses->count() }})</h4>
                                    @foreach($fileResponses->take(5) as $response)
                                        <div class="file-response">
                                            <i class="fas fa-file file-icon"></i>
                                            <div class="file-info">
                                                <div class="file-name">{{ $response->answer }}</div>
                                                <div class="file-date">Upload: {{ $response->created_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                @if(isset($response->answer_data['mime_type']) && str_starts_with($response->answer_data['mime_type'], 'image/'))
                                                    <a href="{{ route('admin.viewFile', $response->id) }}" target="_blank" class="action-btn view-btn">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.downloadFile', $response->id) }}" class="action-btn download-btn">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($fileResponses->count() > 5)
                                        <div style="text-align: center; margin-top: 15px;">
                                            <a href="{{ route('admin.uploadedFiles') }}" class="btn btn-info">
                                                <i class="fas fa-eye"></i> Lihat Semua File ({{ $fileResponses->count() }})
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <!-- Text/Textarea Simple View -->
                                <div class="sample-responses">
                                    <h4><i class="fas fa-comment-dots"></i> Semua Jawaban ({{ $question->responses->count() }})</h4>
                                    <div class="sample-responses-scroll">
                                    @foreach($question->responses as $response)
                                        <div class="sample-response">
                                            <div class="response-text">"{{ Str::limit($response->answer, 150) }}"</div>
                                            <div class="response-date">{{ $response->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada jawaban untuk pertanyaan ini.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- No Questions State -->
        <div class="empty-state">
            <i class="fas fa-question-circle"></i>
            <h3>Belum Ada Pertanyaan</h3>
            <p>Anda belum membuat pertanyaan survei. Mulai dengan membuat pertanyaan pertama.</p>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-primary" style="margin-top: 10px;">
                <i class="fas fa-plus"></i> Buat Pertanyaan Pertama
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Chart.js Configuration
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.color = '#334155';

    // Color Palette
    const colors = ['#5a9b9e', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997'];

    // Store chart instances
    const chartInstances = {};

    // Chart data storage
    const chartData = {
        @if(isset($sectionStats) && count($sectionStats) > 0)
            @foreach($sectionStats as $sectionStat)
                @foreach($sectionStat['questions_stats'] as $stat)
                    @if($stat['total_responses'] > 0 && isset($stat['chart_enabled']) && $stat['chart_enabled'])
                        @if(in_array($stat['question']->question_type, ['multiple_choice', 'dropdown', 'checkbox']))
                            {{ $stat['question']->id }}: {
                                type: '{{ $stat['question']->question_type }}',
                                labels: @json($stat['response_data']->pluck('answer')),
                                data: @json($stat['response_data']->pluck('count')),
                                totalResponses: {{ $stat['total_responses'] }},
                                totalCount: {{ $stat['question']->question_type === 'checkbox' ? $stat['response_data']->sum('count') : $stat['total_responses'] }}
                            },
                        @elseif($stat['question']->question_type === 'linear_scale' && isset($stat['response_data']['distribution']))
                            {{ $stat['question']->id }}: {
                                type: 'linear_scale',
                                labels: [@for($i = ($stat['question']->settings['scale_min'] ?? 1); $i <= ($stat['question']->settings['scale_max'] ?? 5); $i++)'{{ $i }}',@endfor],
                                data: [@for($i = ($stat['question']->settings['scale_min'] ?? 1); $i <= ($stat['question']->settings['scale_max'] ?? 5); $i++){{ $stat['response_data']['distribution'][$i] ?? 0 }},@endfor],
                                totalResponses: {{ $stat['response_data']['total_responses'] ?? 0 }}
                            },
                        @endif
                    @endif
                @endforeach
            @endforeach
        @endif
    };

    // Function to create chart
    function createChart(questionId, chartType) {
        const ctx = document.getElementById('chart_' + questionId);
        if (!ctx) return;

        const data = chartData[questionId];
        if (!data) return;

        // Destroy existing chart if it exists
        if (chartInstances[questionId]) {
            chartInstances[questionId].destroy();
        }

        let chartConfig = {
            type: chartType,
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: colors.slice(0, data.labels.length),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: chartType === 'doughnut' ? 'bottom' : 'top',
                        display: chartType === 'doughnut',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    }
                }
            }
        };

        // Configure specific chart type options
        if (chartType === 'doughnut') {
            chartConfig.options.cutout = '55%';
            chartConfig.data.datasets[0].borderWidth = 2;
            chartConfig.data.datasets[0].borderColor = '#ffffff';
        } else if (chartType === 'bar') {
            chartConfig.data.datasets[0].borderRadius = 4; // Lebih kotak / flat
            chartConfig.data.datasets[0].borderSkipped = false;
            chartConfig.data.datasets[0].backgroundColor = data.type === 'linear_scale' ? '#5a9b9e' : colors.slice(0, data.labels.length);
            chartConfig.options.scales = {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            };
        }

        // For linear scale, always add label
        if (data.type === 'linear_scale') {
            chartConfig.data.datasets[0].label = 'Jumlah Responden';
        }

        // Create and store chart instance
        chartInstances[questionId] = new Chart(ctx.getContext('2d'), chartConfig);
    }

    // Function to toggle chart type
    function toggleChart(questionId, chartType) {
        console.log('Toggle chart called:', questionId, chartType);
        
        // Update button states - improved selector
        const questionCard = document.querySelector(`#chart_${questionId}`)?.closest('.question-card');
        if (questionCard) {
            const buttons = questionCard.querySelectorAll('.chart-toggle-btn');
            buttons.forEach(btn => {
                btn.classList.remove('active');
                const btnText = btn.textContent.toLowerCase();
                if ((chartType === 'doughnut' && btnText.includes('donat')) || 
                    (chartType === 'bar' && btnText.includes('batang'))) {
                    btn.classList.add('active');
                }
            });
        }

        // Recreate chart with new type
        createChart(questionId, chartType);
    }

    // Initialize all charts when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts with default types
        Object.keys(chartData).forEach(questionId => {
            const data = chartData[questionId];
            let defaultType = 'doughnut';
            
            // Set default chart type based on question type
            if (data.type === 'checkbox' || data.type === 'linear_scale') {
                defaultType = 'bar';
            }
            
            createChart(questionId, defaultType);
        });
    });
</script>
@endpush