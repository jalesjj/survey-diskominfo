{{-- resources/views/admin/survey-results/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hasil Survey - Admin')
@section('active-results', 'active')
@section('page-title', 'Hasil Survey')
@section('page-subtitle', 'Daftar hasil survey dan perhitungan SAW')

@section('breadcrumb')
<div class="breadcrumb">
    <span>Hasil Survey</span>
</div>
@endsection

@push('styles')
<style>
    .results-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .results-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 25px 30px;
    }

    .results-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .results-subtitle {
        font-size: 14px;
        opacity: 0.9;
    }

    .results-body {
        padding: 30px;
    }

    .filters-section {
        display: flex;
        gap: 15px;
        align-items: center;
        margin-bottom: 25px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
    }

    .filter-input {
        padding: 8px 12px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        font-size: 14px;
    }

    .results-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .results-table th {
        background: #f8f9fa;
        color: #2c3e50;
        font-weight: 600;
        padding: 15px 12px;
        border-bottom: 2px solid #e9ecef;
        text-align: left;
    }

    .results-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }

    .results-table tr:hover {
        background: #f8f9fa;
    }

    .survey-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .survey-id {
        font-weight: 600;
        color: #2c3e50;
    }

    .survey-date {
        font-size: 13px;
        color: #6c757d;
    }

    .survey-ip {
        font-size: 12px;
        color: #9ca3af;
    }

    .responses-count {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .responses-count i {
        color: #5a9b9e;
    }

    .saw-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .saw-enabled {
        background: #d4edda;
        color: #155724;
    }

    .saw-disabled {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-view {
        background: #5a9b9e;
    }

    .btn-view:hover {
        background: #4a8b8e;
        transform: translateY(-1px);
    }

    .btn-export {
        background: #28a745;
    }

    .btn-export:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    .pagination-wrapper {
        margin-top: 25px;
        display: flex;
        justify-content: center;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="results-container">
    <div class="results-header">
        <div class="results-title"><i class="fas fa-chart-bar"></i> Hasil Survey & Analisis SAW</div>
        <div class="results-subtitle">Kelola dan analisis hasil survey dengan perhitungan Simple Additive Weighting</div>
    </div>

    <div class="results-body">
        <!-- Statistics Cards -->
        @php
            $totalSurveys = $surveys->total();
            $sawEnabledSurveys = $surveys->filter(function($survey) {
                return $survey->responses->some(function($response) {
                    return $response->question->enable_saw ?? false;
                });
            })->count();
            $completedSurveys = $surveys->filter(function($survey) {
                return $survey->responses->count() > 0;
            })->count();
        @endphp

        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-value">{{ $totalSurveys }}</div>
                <div class="stat-label">Total Survey</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $completedSurveys }}</div>
                <div class="stat-label">Survey Selesai</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $sawEnabledSurveys }}</div>
                <div class="stat-label">Menggunakan SAW</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    <a href="{{ route('admin.survey-results.ranking') }}" class="btn btn-view">
                        <i class="fas fa-trophy"></i> Lihat Ranking
                    </a>
                </div>
                <div class="stat-label">Peringkat Responden</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <div class="filter-group">
                <label class="filter-label">Cari Survey ID</label>
                <input type="text" class="filter-input" placeholder="Masukkan ID survey...">
            </div>
            <div class="filter-group">
                <label class="filter-label">Filter Tanggal</label>
                <input type="date" class="filter-input">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status SAW</label>
                <select class="filter-input">
                    <option value="">Semua</option>
                    <option value="enabled">Menggunakan SAW</option>
                    <option value="disabled">Tanpa SAW</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">&nbsp;</label>
                <button class="btn-action btn-view">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>

        @if($surveys->count() > 0)
            <!-- Results Table -->
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Survey</th>
                        <th>Jumlah Jawaban</th>
                        <th>Status SAW</th>
                        <th>Total Skor</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveys as $survey)
                        @php
                            $hasSAW = $survey->responses->some(function($response) {
                                return $response->question->enable_saw ?? false;
                            });
                            $responsesCount = $survey->responses->count();
                        @endphp
                        <tr>
                            <td>
                                <div class="survey-info">
                                    <div class="survey-id"># {{ $survey->id }}</div>
                                    <div class="survey-date">{{ $survey->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="survey-ip">IP: {{ $survey->ip_address ?: 'Unknown' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="responses-count">
                                    <i class="fas fa-comment"></i>
                                    <span>{{ $responsesCount }} jawaban</span>
                                </div>
                            </td>
                            <td>
                                @if($hasSAW)
                                    <span class="saw-indicator saw-enabled">
                                        <i class="fas fa-check-circle"></i>
                                        SAW Aktif
                                    </span>
                                @else
                                    <span class="saw-indicator saw-disabled">
                                        <i class="fas fa-times-circle"></i>
                                        SAW Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($hasSAW)
                                    <span style="font-weight: 600; color: #5a9b9e;">Lihat Detail</span>
                                @else
                                    <span style="color: #6c757d;">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.survey-results.show', $survey->id) }}" class="btn-action btn-view">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                    @if($hasSAW)
                                        <button class="btn-action btn-export" onclick="exportSurvey({{ $survey->id }})">
                                            <i class="fas fa-download"></i> Export
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $surveys->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h4>Belum Ada Data Survey</h4>
                <p>Survey yang sudah diisi akan muncul di halaman ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportSurvey(surveyId) {
        // TODO: Implement export functionality
        alert('Fitur export akan segera tersedia');
    }

    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        // TODO: Implement real-time filtering
        console.log('Survey results page loaded');
    });
</script>
@endpush
