{{-- resources/views/admin/dashboard-sections.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard Bagian - Admin Survei')
@section('active-dashboard', 'active')
@section('page-title', 'Dashboard Survei')
@section('page-subtitle', 'Analisis jawaban per bagian dan pertanyaan')

@section('header-actions')
<div class="header-actions">
    <span class="admin-welcome">elamat datang, {{ session('admin_name') }}</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<style>
    /* Action Buttons */
    .action-buttons {
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
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
        font-size: 16px;
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
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    }

    /* Summary Stats */
    .summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
        text-align: center;
    }

    .summary-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #5a9b9e;
    }

    .summary-number {
        font-size: 36px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 8px;
    }

    .summary-label {
        font-size: 14px;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Tab Navigation */
    .tab-navigation {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .tab-nav {
        display: flex;
        background: #f8f9fa;
    }

    .tab-item {
        flex: 1;
        padding: 20px 25px;
        text-align: center;
        background: #f8f9fa;
        color: #6c757d;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border-right: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .tab-item:last-child {
        border-right: none;
    }

    .tab-item:hover {
        background: #e9ecef;
        color: #495057;
    }

    .tab-item.active {
        background: #5a9b9e;
        color: white;
    }

    .tab-icon {
        font-size: 18px;
    }

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .section-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    .section-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 30px 35px;
        position: relative;
    }

    .section-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transform: skewX(-15deg);
        transform-origin: top;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 10px;
        position: relative;
        z-index: 2;
    }

    .section-description {
        font-size: 16px;
        opacity: 0.9;
        line-height: 1.5;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }

    .section-meta {
        display: flex;
        gap: 25px;
        align-items: center;
        font-size: 14px;
        opacity: 0.9;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .section-stat {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .section-stat-number {
        font-weight: 700;
        font-size: 16px;
    }

    .section-body {
        padding: 0;
    }

    .section-order-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
        z-index: 3;
    }

    /* Question Cards inside Section */
    .question-card {
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.3s ease;
    }

    .question-card:last-child {
        border-bottom: none;
    }

    .question-card:hover {
        background: #f8f9fa;
    }

    .question-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 25px 30px;
    }

    .question-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .question-meta {
        display: flex;
        gap: 20px;
        align-items: center;
        font-size: 14px;
        opacity: 0.9;
        flex-wrap: wrap;
    }

    .question-type-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .question-body {
        padding: 30px;
    }

    .stats-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f8f9fa;
    }

    .total-responses {
        font-size: 24px;
        font-weight: 700;
        color: #5a9b9e;
    }

    .responses-label {
        color: #7f8c8d;
        font-size: 14px;
        margin-top: 5px;
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }

    /* Chart Controls */
    .chart-controls {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
        gap: 10px;
    }

    .chart-toggle-btn {
        padding: 8px 16px;
        border: 2px solid #5a9b9e;
        background: white;
        color: #5a9b9e;
        border-radius: 25px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .chart-toggle-btn:hover {
        background: #f0f8f8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(90, 155, 158, 0.2);
    }

    .chart-toggle-btn.active {
        background: #5a9b9e;
        color: white;
    }

    .chart-toggle-btn.active:hover {
        background: #4a8b8e;
    }

    /* Response Statistics Styles */
    .response-stats {
        display: grid;
        gap: 15px;
    }

    .response-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        border-left: 4px solid #5a9b9e;
        transition: all 0.3s ease;
    }

    .response-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .response-text {
        flex: 1;
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }

    .response-count {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 700;
        margin-left: 15px;
        min-width: 50px;
        text-align: center;
    }

    .response-percentage {
        font-size: 12px;
        color: #7f8c8d;
        margin-left: 10px;
    }

    /* Scale Statistics */
    .scale-stats {
        text-align: center;
        padding: 30px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        margin-bottom: 20px;
    }

    .scale-average {
        font-size: 48px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 10px;
    }

    .scale-label {
        color: #7f8c8d;
        font-size: 16px;
        margin-bottom: 25px;
    }

    .scale-distribution {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .scale-item {
        text-align: center;
        min-width: 50px;
    }

    .scale-number {
        display: block;
        width: 45px;
        height: 45px;
        line-height: 45px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .scale-item.active .scale-number {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        transform: scale(1.1);
    }

    .scale-count {
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 600;
    }

    /* Text Responses */
    .text-responses {
        display: grid;
        gap: 15px;
    }

    .text-response {
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        border-left: 4px solid #5a9b9e;
        font-style: italic;
        color: #495057;
        font-size: 15px;
        line-height: 1.6;
    }

    .text-response::before {
        content: '"';
        font-size: 24px;
        color: #5a9b9e;
        font-weight: bold;
    }

    .text-response::after {
        content: '"';
        font-size: 24px;
        color: #5a9b9e;
        font-weight: bold;
    }

    /* File Responses */
    .file-responses {
        display: grid;
        gap: 15px;
    }

    .file-response {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e8 100%);
        border-radius: 10px;
        border-left: 4px solid #689f38;
        transition: all 0.3s ease;
    }

    .file-response:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .file-icon {
        color: #689f38;
        font-size: 24px;
    }

    .file-info {
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
        margin-bottom: 5px;
    }

    .file-date {
        font-size: 14px;
        color: #7f8c8d;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 24px;
        margin-bottom: 15px;
        color: #2c3e50;
    }

    .empty-state p {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .no-questions-state {
        background: white;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 60px 40px;
        text-align: center;
        margin: 40px 0;
    }

    .show-more-info {
        background: #e8f4f8;
        border: 1px solid #bee5eb;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        text-align: center;
    }

    .show-more-info small {
        color: #5a9b9e;
        font-weight: 600;
    }

    .admin-welcome {
        font-size: 14px;
        color: #7f8c8d;
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }

        .summary-stats {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .tab-nav {
            flex-direction: column;
        }

        .tab-item {
            border-right: none;
            border-bottom: 1px solid #e9ecef;
        }

        .tab-item:last-child {
            border-bottom: none;
        }

        .section-header {
            padding: 25px 20px;
        }

        .section-meta {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .question-header {
            padding: 20px;
        }

        .question-body {
            padding: 20px;
        }

        .question-meta {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .scale-distribution {
            gap: 10px;
        }

        .scale-number {
            width: 40px;
            height: 40px;
            line-height: 40px;
            font-size: 14px;
        }

        .response-item {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .response-count {
            margin-left: 0;
        }

        .chart-container {
            height: 250px;
        }
    }
</style>
@endpush

@section('content')
<!-- Action Buttons -->
<div class="action-buttons">
    <a href="{{ route('admin.questions.index') }}" class="btn btn-primary">
        <i class="fas fa-cog"></i>
        Kelola Pertanyaan
    </a>
    <a href="{{ route('survey.index') }}" class="btn btn-success">
        <i class="fas fa-eye"></i>
        Preview Survei
    </a>
    <a href="{{ route('admin.export') }}" class="btn btn-warning">
        <i class="fas fa-download"></i>
        Export Data
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
        <div class="summary-number">{{ collect($sectionStats)->sum('total_responses') }}</div>
        <div class="summary-label">Total Jawaban</div>
    </div>
    <div class="summary-card">
        <div class="summary-number">{{ count($sectionStats) }}</div>
        <div class="summary-label">Total Bagian</div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="tab-navigation">
    <div class="tab-nav">
        <a href="{{ route('admin.dashboard', ['tab' => 'questions']) }}" class="tab-item active">
            <i class="fas fa-question-circle tab-icon"></i>
            <span>Pertanyaan</span>
        </a>
        <a href="{{ route('admin.dashboard', ['tab' => 'individual']) }}" class="tab-item">
            <i class="fas fa-users tab-icon"></i>
            <span>Individual</span>
        </a>
    </div>
</div>

<!-- Sections & Questions -->
@if($sectionStats && count($sectionStats) > 0)
    @foreach($sectionStats as $index => $sectionStat)
    <div class="section-card">
        <!-- Section Header -->
        <div class="section-header">
            <div class="section-order-badge">Bagian {{ $index + 1 }}</div>
            <div class="section-title">
                <i class="fas fa-layer-group"></i>
                {{ $sectionStat['section']->title }}
            </div>
            @if($sectionStat['section']->description)
                <div class="section-description">{{ $sectionStat['section']->description }}</div>
            @endif
            <div class="section-meta">
                <div class="section-stat">
                    <i class="fas fa-question-circle"></i>
                    <span class="section-stat-number">{{ $sectionStat['total_questions'] }}</span>
                    <span>Pertanyaan</span>
                </div>
                <div class="section-stat">
                    <i class="fas fa-reply-all"></i>
                    <span class="section-stat-number">{{ $sectionStat['total_responses'] }}</span>
                    <span>Total Jawaban</span>
                </div>
                <div class="section-stat">
                    <i class="fas fa-{{ $sectionStat['section']->is_active ? 'check-circle' : 'times-circle' }}"></i>
                    <span>{{ $sectionStat['section']->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
            </div>
        </div>

        <!-- Questions in this Section -->
        <div class="section-body">
            @foreach($sectionStat['questions_stats'] as $stat)
            <div class="question-card">
                <div class="question-header">
                    <div class="question-title">{{ $stat['question']->question_text }}</div>
                    <div class="question-meta">
                        <span class="question-type-badge">
                            <i class="fas fa-tag"></i> {{ $stat['question']->getQuestionTypeLabel() }}
                        </span>
                        <span><i class="fas fa-{{ $stat['question']->is_required ? 'star' : 'star-o' }}"></i> {{ $stat['question']->is_required ? 'Wajib' : 'Opsional' }}</span>
                        <span><i class="fas fa-sort-numeric-up"></i> Urutan: {{ $stat['question']->order_index }}</span>
                    </div>
                </div>

                <div class="question-body">
                    <div class="stats-header">
                        <div>
                            <div class="total-responses">{{ $stat['total_responses'] }}</div>
                            <div class="responses-label">Total Jawaban</div>
                        </div>
                        @if($stat['total_responses'] > 0)
                            <div style="color: #28a745;">
                                <i class="fas fa-check-circle"></i> Ada Jawaban
                            </div>
                        @else
                            <div style="color: #ffc107;">
                                <i class="fas fa-clock"></i> Belum Ada Jawaban
                            </div>
                        @endif
                    </div>

                    @if($stat['total_responses'] > 0)
                        @if(isset($stat['chart_enabled']) && $stat['chart_enabled'] && in_array($stat['question']->question_type, ['multiple_choice', 'dropdown', 'checkbox']))
                            <!-- Chart-enabled Question Types -->
                            <div class="chart-controls">
                                <button class="chart-toggle-btn active" onclick="toggleChart({{ $stat['question']->id }}, 'doughnut')">
                                    <i class="fas fa-chart-pie"></i> Donat
                                </button>
                                <button class="chart-toggle-btn" onclick="toggleChart({{ $stat['question']->id }}, 'bar')">
                                    <i class="fas fa-chart-bar"></i> Batang
                                </button>
                            </div>
                            
                            <div class="chart-container">
                                <canvas id="chart_{{ $stat['question']->id }}"></canvas>
                            </div>
                            
                            <div class="response-stats">
                                @foreach($stat['response_data'] as $response)
                                    <div class="response-item">
                                        <span class="response-text">{{ $response->answer }}</span>
                                        <div>
                                            <span class="response-count">{{ $response->count }}</span>
                                            @if($stat['question']->question_type === 'checkbox')
                                                <span class="response-percentage">({{ number_format(($response->count / $stat['response_data']->sum('count')) * 100, 1) }}%)</span>
                                            @else
                                                <span class="response-percentage">({{ number_format(($response->count / $stat['total_responses']) * 100, 1) }}%)</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @elseif(isset($stat['chart_enabled']) && $stat['chart_enabled'] && $stat['question']->question_type === 'linear_scale')
                            <!-- Linear Scale with Chart Toggle -->
                            <div class="chart-controls">
                                <button class="chart-toggle-btn active" onclick="toggleChart({{ $stat['question']->id }}, 'bar')">
                                    <i class="fas fa-chart-bar"></i> Batang
                                </button>
                                <button class="chart-toggle-btn" onclick="toggleChart({{ $stat['question']->id }}, 'doughnut')">
                                    <i class="fas fa-chart-pie"></i> Donat
                                </button>
                            </div>
                            
                            <div class="chart-container">
                                <canvas id="chart_{{ $stat['question']->id }}"></canvas>
                            </div>
                            
                            <div class="scale-stats">
                                <div class="scale-average">{{ $stat['response_data']['average'] ?? 0 }}</div>
                                <div class="scale-label">Rata-rata dari {{ $stat['response_data']['total_responses'] ?? 0 }} jawaban</div>
                                
                                @if(isset($stat['response_data']['distribution']))
                                <div class="scale-distribution">
                                    @for($i = ($stat['question']->settings['scale_min'] ?? 1); $i <= ($stat['question']->settings['scale_max'] ?? 5); $i++)
                                        <div class="scale-item {{ isset($stat['response_data']['distribution'][$i]) && $stat['response_data']['distribution'][$i] > 0 ? 'active' : '' }}">
                                            <span class="scale-number">{{ $i }}</span>
                                            <div class="scale-count">{{ $stat['response_data']['distribution'][$i] ?? 0 }} orang</div>
                                        </div>
                                    @endfor
                                </div>
                                @endif
                            </div>

                        @elseif(in_array($stat['question']->question_type, ['short_text', 'long_text']))
                            <!-- Text Responses -->
                            <div class="text-responses">
                                @if(isset($stat['sample_responses']))
                                    @foreach($stat['sample_responses'] as $response)
                                        <div class="text-response">
                                            <strong>{{ $response['created_at'] }}:</strong> {{ Str::limit($response['answer'], 200) }}
                                        </div>
                                    @endforeach
                                @endif
                                
                                @if($stat['total_responses'] > 5)
                                    <div class="show-more-info">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            Menampilkan 5 jawaban terbaru dari {{ $stat['total_responses'] }} total jawaban
                                        </small>
                                    </div>
                                @endif
                            </div>

                        @elseif($stat['question']->question_type === 'file_upload')
                            <!-- File Upload Responses -->
                            <div class="file-responses">
                                @if(isset($stat['response_data']))
                                    @foreach($stat['response_data'] as $file)
                                        <div class="file-response">
                                            <i class="fas fa-file file-icon"></i>
                                            <div class="file-info">
                                                <div class="file-name">{{ $file['filename'] }}</div>
                                                <div class="file-date">Diupload: {{ \Carbon\Carbon::parse($file['upload_date'])->format('d/m/Y H:i') }}</div>
                                                @if(isset($file['file_data']['size']))
                                                    <div style="font-size: 12px; color: #7f8c8d;">
                                                        {{ number_format($file['file_data']['size'] / 1024, 1) }} KB
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="file-actions">
                                                @if(isset($file['file_data']['mime_type']) && str_starts_with($file['file_data']['mime_type'], 'image/'))
                                                    <a href="{{ route('admin.viewFile', $file['response_id']) }}" target="_blank" 
                                                       style="background: #17a2b8; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; margin-right: 8px;">
                                                        <i class="fas fa-eye"></i> Lihat
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.downloadFile', $file['response_id']) }}" 
                                                   style="background: #28a745; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>Belum Ada Jawaban</h3>
                            <p>Pertanyaan ini belum dijawab oleh responden manapun.</p>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
@else
<div class="no-questions-state">
    <div class="empty-state">
        <i class="fas fa-question-circle"></i>
        <h3>Belum Ada Pertanyaan</h3>
        <p>Anda belum membuat pertanyaan survei. Mulai dengan membuat pertanyaan pertama untuk melihat jawaban responden di sini.</p>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Pertanyaan Pertama
        </a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Chart.js Configuration
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.color = '#2c3e50';

    // Color Palette
    const colors = ['#5a9b9e', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997'];

    // Store chart instances
    const chartInstances = {};

    // Chart data storage
    const chartData = {
        @if($sectionStats && count($sectionStats) > 0)
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
            chartConfig.options.cutout = '50%';
            chartConfig.data.datasets[0].borderWidth = 2;
            chartConfig.data.datasets[0].borderColor = '#ffffff';
        } else if (chartType === 'bar') {
            chartConfig.data.datasets[0].borderRadius = 8;
            chartConfig.data.datasets[0].borderSkipped = false;
            chartConfig.data.datasets[0].backgroundColor = data.type === 'linear_scale' ? '#5a9b9e' : colors.slice(0, data.labels.length);
            chartConfig.options.scales = {
                y: {
                    beginAtZero: true,
                    grid: { color: '#e9ecef' }
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
        // Update button states
        const buttons = document.querySelectorAll(`[onclick*="toggleChart(${questionId}"]`);
        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.onclick.toString().includes(`'${chartType}'`)) {
                btn.classList.add('active');
            }
        });

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

        // Section Card Animation
        const sectionCards = document.querySelectorAll('.section-card');
        
        // Animate section cards on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        sectionCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });

        // Auto hide success message
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
    });

    // Section interaction effects
    document.querySelectorAll('.section-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-3px)';
        });
    });

    // Question card interaction effects
    document.querySelectorAll('.question-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.background = '#f8f9fa';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.background = 'transparent';
        });
    });

    // Print functionality
    function printReport() {
        window.print();
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush