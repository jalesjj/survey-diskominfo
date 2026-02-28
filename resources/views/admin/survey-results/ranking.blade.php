{{-- resources/views/admin/survey-results/ranking.blade.php
@extends('layouts.admin')

@section('title', 'Ranking Responden - Admin')
@section('active-results', 'active')
@section('page-title', 'Ranking Responden')
@section('page-subtitle', 'Peringkat responden berdasarkan perhitungan SAW')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.survey-results.index') }}">Hasil Survey</a>
    <span class="breadcrumb-separator">></span>
    <span>Ranking</span>
</div>
@endsection

@push('styles')
<style>
    .ranking-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .ranking-header {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
        padding: 25px 30px;
    }

    .ranking-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .ranking-subtitle {
        font-size: 14px;
        opacity: 0.9;
    }

    .ranking-body {
        padding: 30px;
    }

    .ranking-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        border-color: #f39c12;
        transform: translateY(-2px);
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

    .ranking-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .ranking-table th {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        font-weight: 600;
        padding: 15px 12px;
        text-align: center;
        font-size: 14px;
    }

    .ranking-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #e9ecef;
        text-align: center;
        font-size: 14px;
    }

    .ranking-table tr:nth-child(even) {
        background: #f8f9fa;
    }

    .ranking-table tr:hover {
        background: #e3f2fd;
    }

    .rank-cell {
        font-weight: 700;
        font-size: 18px;
        position: relative;
    }

    .rank-1 {
        color: #f39c12;
        background: linear-gradient(135deg, #fff3cd 0%, #fef9e7 100%) !important;
    }

    .rank-2 {
        color: #6c757d;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    }

    .rank-3 {
        color: #e67e22;
        background: linear-gradient(135deg, #fdf2e9 0%, #fcf4ed 100%) !important;
    }

    .rank-cell i {
        margin-right: 5px;
    }

    .survey-id-cell {
        font-weight: 600;
        color: #5a9b9e;
    }

    .date-cell {
        color: #6c757d;
        font-size: 13px;
    }

    .score-cell {
        font-weight: 700;
        font-family: monospace;
        font-size: 16px;
    }

    .score-excellent {
        color: #28a745;
    }

    .score-good {
        color: #17a2b8;
    }

    .score-fair {
        color: #ffc107;
    }

    .score-poor {
        color: #fd7e14;
    }

    .score-very-poor {
        color: #dc3545;
    }

    .action-cell {
        padding: 10px;
    }

    .btn-detail {
        background: #5a9b9e;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-detail:hover {
        background: #4a8b8e;
        transform: translateY(-1px);
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
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

    .export-section {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .btn-export {
        background: #28a745;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-export:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    .trophy-icon {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
</style>
@endpush

@section('content')
<div class="ranking-container">
    <div class="ranking-header">
        <div class="ranking-title"><i class="fas fa-trophy trophy-icon"></i> Ranking Responden SAW</div>
        <div class="ranking-subtitle">Peringkat responden berdasarkan perhitungan Simple Additive Weighting</div>
    </div>

    <div class="ranking-body">
        @if($rankings->count() > 0)
            <!-- Statistics -->
            <div class="ranking-stats">
                <div class="stat-card">
                    <div class="stat-value">{{ $rankings->count() }}</div>
                    <div class="stat-label">Total Responden</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($rankings->avg('total_score'), 4) }}</div>
                    <div class="stat-label">Rata-rata Skor</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($rankings->max('total_score'), 4) }}</div>
                    <div class="stat-label">Skor Tertinggi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($rankings->min('total_score'), 4) }}</div>
                    <div class="stat-label">Skor Terendah</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <div class="filter-group">
                    <label class="filter-label">Filter Peringkat</label>
                    <select class="filter-input" id="rankFilter">
                        <option value="">Semua Peringkat</option>
                        <option value="1-10">Top 10</option>
                        <option value="1-5">Top 5</option>
                        <option value="1-3">Top 3</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Filter Tanggal</label>
                    <input type="date" class="filter-input" id="dateFilter">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Minimum Skor</label>
                    <input type="number" class="filter-input" step="0.001" placeholder="0.000" id="scoreFilter">
                </div>
            </div>

            <!-- Export Options -->
            <div class="export-section">
                <button class="btn-export" onclick="exportRanking('excel')">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button class="btn-export" onclick="exportRanking('pdf')">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn-export" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>

            <!-- Ranking Table -->
            <table class="ranking-table" id="rankingTable">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Survey ID</th>
                        <th>Tanggal Submit</th>
                        <th>Total Skor SAW</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankings as $ranking)
                        @php
                            $scoreClass = $ranking['total_score'] >= 0.8 ? 'excellent' : 
                                        ($ranking['total_score'] >= 0.6 ? 'good' : 
                                        ($ranking['total_score'] >= 0.4 ? 'fair' : 
                                        ($ranking['total_score'] >= 0.2 ? 'poor' : 'very-poor')));
                            
                            $scoreLabel = $ranking['total_score'] >= 0.8 ? 'Sangat Baik' : 
                                        ($ranking['total_score'] >= 0.6 ? 'Baik' : 
                                        ($ranking['total_score'] >= 0.4 ? 'Cukup' : 
                                        ($ranking['total_score'] >= 0.2 ? 'Kurang' : 'Sangat Kurang')));
                            
                            $rankClass = $ranking['rank'] <= 3 ? 'rank-' . $ranking['rank'] : '';
                        @endphp
                        <tr data-rank="{{ $ranking['rank'] }}" data-score="{{ $ranking['total_score'] }}" data-date="{{ $ranking['survey_date']->format('Y-m-d') }}">
                            <td class="rank-cell {{ $rankClass }}">
                                @if($ranking['rank'] == 1)
                                    <i class="fas fa-crown"></i>
                                @elseif($ranking['rank'] == 2)
                                    <i class="fas fa-medal"></i>
                                @elseif($ranking['rank'] == 3)
                                    <i class="fas fa-award"></i>
                                @endif
                                {{ $ranking['rank'] }}
                            </td>
                            <td class="survey-id-cell"># {{ $ranking['survey_id'] }}</td>
                            <td class="date-cell">{{ $ranking['survey_date']->format('d/m/Y H:i') }}</td>
                            <td class="score-cell score-{{ $scoreClass }}">{{ number_format($ranking['total_score'], 4) }}</td>
                            <td class="score-cell score-{{ $scoreClass }}">{{ $scoreLabel }}</td>
                            <td class="action-cell">
                                <a href="{{ route('admin.survey-results.show', $ranking['survey_id']) }}" class="btn-detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <i class="fas fa-trophy"></i>
                <h4>Belum Ada Data Ranking</h4>
                <p>Ranking akan muncul setelah ada responden yang mengisi survey dengan perhitungan SAW.</p>
                <p style="margin-top: 15px;">
                    <a href="{{ route('admin.survey-results.index') }}" class="btn-detail">
                        <i class="fas fa-arrow-left"></i> Kembali ke Hasil Survey
                    </a>
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportRanking(format) {
        // TODO: Implement export functionality
        alert(`Export ${format.toUpperCase()} akan segera tersedia`);
    }

    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const rankFilter = document.getElementById('rankFilter');
        const dateFilter = document.getElementById('dateFilter');
        const scoreFilter = document.getElementById('scoreFilter');
        const table = document.getElementById('rankingTable');

        function applyFilters() {
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                let showRow = true;
                
                // Rank filter
                if (rankFilter.value) {
                    const rankRange = rankFilter.value.split('-');
                    const rowRank = parseInt(row.dataset.rank);
                    if (rankRange.length === 2) {
                        const minRank = parseInt(rankRange[0]);
                        const maxRank = parseInt(rankRange[1]);
                        if (rowRank < minRank || rowRank > maxRank) {
                            showRow = false;
                        }
                    }
                }
                
                // Date filter
                if (dateFilter.value) {
                    if (row.dataset.date !== dateFilter.value) {
                        showRow = false;
                    }
                }
                
                // Score filter
                if (scoreFilter.value) {
                    const minScore = parseFloat(scoreFilter.value);
                    const rowScore = parseFloat(row.dataset.score);
                    if (rowScore < minScore) {
                        showRow = false;
                    }
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }

        if (rankFilter) rankFilter.addEventListener('change', applyFilters);
        if (dateFilter) dateFilter.addEventListener('change', applyFilters);
        if (scoreFilter) scoreFilter.addEventListener('input', applyFilters);
        
        // Print styles
        const printStyles = `
            @media print {
                .filters-section, .export-section, .breadcrumb { display: none !important; }
                .ranking-container { box-shadow: none; }
                .ranking-table { font-size: 12px; }
            }
        `;
        
        const styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = printStyles;
        document.head.appendChild(styleSheet);
    });
</script>
@endpush --}}
