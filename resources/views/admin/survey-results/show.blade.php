{{-- resources/views/admin/survey-results/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Hasil Survey - Admin')
@section('active-results', 'active')
@section('page-title', 'Detail Hasil Survey')
@section('page-subtitle', 'Survey ID: #' . $survey->id . ' - ' . $survey->created_at->format('d/m/Y H:i'))

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.survey-results.index') }}">Hasil Survey</a>
    <span class="breadcrumb-separator">></span>
    <span>Detail #{{ $survey->id }}</span>
</div>
@endsection

@push('styles')
<style>
    .detail-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .detail-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 25px 30px;
    }

    .detail-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .detail-subtitle {
        font-size: 14px;
        opacity: 0.9;
    }

    .detail-body {
        padding: 30px;
    }

    .survey-info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        border-left: 4px solid #5a9b9e;
    }

    .info-card h4 {
        color: #2c3e50;
        font-size: 14px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .info-value {
        font-size: 16px;
        color: #495057;
        margin-bottom: 5px;
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
    }

    .saw-results-section {
        margin-top: 30px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .saw-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .saw-table th {
        background: #2c3e50;
        color: white;
        font-weight: 600;
        padding: 15px 12px;
        text-align: center;
        font-size: 13px;
    }

    .saw-table td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        text-align: center;
        font-size: 14px;
    }

    .saw-table tr:nth-child(even) {
        background: #f8f9fa;
    }

    .saw-table tr:hover {
        background: #e3f2fd;
    }

    .criteria-cell {
        font-weight: 600;
        color: #5a9b9e;
        text-align: left;
        background: #f1f8ff !important;
    }

    .sub-criteria-cell {
        text-align: left;
        color: #2c3e50;
        padding-left: 20px;
    }

    .score-cell {
        font-weight: 600;
        color: #2c3e50;
    }

    .weight-cell {
        color: #6c757d;
        font-family: monospace;
    }

    .normalized-cell {
        color: #495057;
        font-family: monospace;
    }

    .weighted-cell {
        font-weight: 600;
        color: #5a9b9e;
        font-family: monospace;
    }

    .interpretation-cell {
        font-weight: 500;
    }

    .interpretation-excellent {
        color: #28a745;
    }

    .interpretation-good {
        color: #17a2b8;
    }

    .interpretation-fair {
        color: #ffc107;
    }

    .interpretation-poor {
        color: #fd7e14;
    }

    .interpretation-very-poor {
        color: #dc3545;
    }

    .total-score-section {
        margin-top: 30px;
        padding: 20px;
        background: linear-gradient(135deg, #e8f5e8 0%, #f0f9ff 100%);
        border-radius: 8px;
        border: 2px solid #5a9b9e;
    }

    .total-score {
        text-align: center;
    }

    .total-score-value {
        font-size: 32px;
        font-weight: 700;
        color: #5a9b9e;
        margin-bottom: 5px;
    }

    .total-score-label {
        font-size: 16px;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .total-score-interpretation {
        font-size: 18px;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 20px;
        display: inline-block;
    }

    .no-saw-message {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
    }

    .no-saw-message i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        color: white;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary {
        background: #6c757d;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-primary {
        background: #5a9b9e;
    }

    .btn-primary:hover {
        background: #4a8b8e;
    }

    .btn-success {
        background: #28a745;
    }

    .btn-success:hover {
        background: #218838;
    }
</style>
@endpush

@section('content')
<div class="detail-container">
    <div class="detail-header">
        <div class="detail-title"><i class="fas fa-chart-line"></i> Analisis Hasil Survey SAW</div>
        <div class="detail-subtitle">Transparansi perhitungan metode Simple Additive Weighting</div>
    </div>

    <div class="detail-body">
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.survey-results.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            @if($hasSAW)
                <button class="btn btn-primary" onclick="printResults()">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <button class="btn btn-success" onclick="exportResults()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            @endif
        </div>

        <!-- Survey Information Cards -->
        <div class="survey-info-cards">
            <div class="info-card">
                <h4><i class="fas fa-calendar"></i> Informasi Survey</h4>
                <div class="info-value">Survey #{{ $survey->id }}</div>
                <div class="info-label">{{ $survey->created_at->format('d F Y, H:i') }} WIB</div>
            </div>
            <div class="info-card">
                <h4><i class="fas fa-network-wired"></i> Informasi Teknis</h4>
                <div class="info-value">{{ $survey->ip_address ?: 'IP tidak diketahui' }}</div>
                <div class="info-label">Alamat IP responden</div>
            </div>
            <div class="info-card">
                <h4><i class="fas fa-chart-bar"></i> Status Perhitungan</h4>
                @if($hasSAW)
                    <div class="info-value" style="color: #28a745;">
                        <i class="fas fa-check-circle"></i> SAW Aktif
                    </div>
                    <div class="info-label">Menggunakan perhitungan SAW</div>
                @else
                    <div class="info-value" style="color: #dc3545;">
                        <i class="fas fa-times-circle"></i> SAW Nonaktif
                    </div>
                    <div class="info-label">Tidak menggunakan perhitungan SAW</div>
                @endif
            </div>
            @if($hasSAW)
                <div class="info-card">
                    <h4><i class="fas fa-calculator"></i> Total Jawaban SAW</h4>
                    <div class="info-value">{{ $sawResults->count() }} pertanyaan</div>
                    <div class="info-label">Pertanyaan dengan perhitungan SAW</div>
                </div>
            @endif
        </div>

        @if($hasSAW)
            <!-- SAW Results Section -->
            <div class="saw-results-section">
                <h3 class="section-title">
                    <i class="fas fa-table"></i>
                    Tabel Hasil Perhitungan SAW
                </h3>

                <table class="saw-table">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Sub-Kriteria</th>
                            <th>Skor (x)</th>
                            <th>Bobot Normalisasi<br>(w<sub>i</sub>)</th>
                            <th>Normalisasi<br>(r<sub>i</sub>)</th>
                            <th>Nilai Terbobot<br>(w<sub>i</sub> × r<sub>i</sub>)</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentCriteria = null;
                            $totalWeightedScore = $sawResults->sum('weighted_score');
                        @endphp
                        
                        @foreach($sawResults as $result)
                            <tr>
                                @if($currentCriteria !== $result['criteria'])
                                    <td class="criteria-cell">{{ $result['criteria'] }}</td>
                                    @php $currentCriteria = $result['criteria']; @endphp
                                @else
                                    <td class="criteria-cell" style="background: transparent; font-weight: normal; color: #6c757d;">
                                        ↳ {{ $result['criteria'] }}
                                    </td>
                                @endif
                                
                                <td class="sub-criteria-cell">{{ $result['sub_criteria'] }}</td>
                                <td class="score-cell">{{ $result['score'] }}</td>
                                <td class="weight-cell">{{ number_format($result['weight_normalized'], 3) }}</td>
                                <td class="normalized-cell">{{ number_format($result['normalized'], 3) }}</td>
                                <td class="weighted-cell">{{ number_format($result['weighted_score'], 3) }}</td>
                                <td class="interpretation-cell interpretation-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                                    {{ $result['interpretation'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Total Score Section -->
                <div class="total-score-section">
                    <div class="total-score">
                        <div class="total-score-value">{{ number_format($totalWeightedScore, 4) }}</div>
                        <div class="total-score-label">Total Nilai Preferensi (V<sub>i</sub>)</div>
                        @php
                            $totalInterpretation = $totalWeightedScore >= 0.8 ? 'Sangat Baik' : 
                                                  ($totalWeightedScore >= 0.6 ? 'Baik' : 
                                                  ($totalWeightedScore >= 0.4 ? 'Cukup' : 
                                                  ($totalWeightedScore >= 0.2 ? 'Kurang' : 'Sangat Kurang')));
                            $interpretationClass = strtolower(str_replace(' ', '-', $totalInterpretation));
                        @endphp
                        <div class="total-score-interpretation interpretation-{{ $interpretationClass }}">
                            {{ $totalInterpretation }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="no-saw-message">
                <i class="fas fa-info-circle"></i>
                <h4>Survey Ini Tidak Menggunakan Perhitungan SAW</h4>
                <p>{{ $message ?? 'Survey ini hanya berisi pertanyaan biasa tanpa perhitungan Sistem Pendukung Keputusan.' }}</p>
                <p style="margin-top: 15px;">
                    <a href="{{ route('admin.individual', $survey->id) }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i> Lihat Jawaban Survey
                    </a>
                </p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printResults() {
        window.print();
    }

    function exportResults() {
        // TODO: Implement Excel export
        alert('Fitur export Excel akan segera tersedia');
    }

    // Add interpretation class helper for JavaScript
    function getInterpretationClass(interpretation) {
        const classes = {
            'Sangat Baik': 'excellent',
            'Baik': 'good', 
            'Cukup': 'fair',
            'Kurang': 'poor',
            'Sangat Kurang': 'very-poor'
        };
        return classes[interpretation] || 'fair';
    }

    function getTotalInterpretation(score) {
        if (score >= 0.8) return 'Sangat Baik';
        if (score >= 0.6) return 'Baik';
        if (score >= 0.4) return 'Cukup';
        if (score >= 0.2) return 'Kurang';
        return 'Sangat Kurang';
    }

    // Print styles
    const printStyles = `
        @media print {
            .action-buttons, .breadcrumb { display: none !important; }
            .detail-container { box-shadow: none; }
            .saw-table { font-size: 12px; }
        }
    `;
    
    const styleSheet = document.createElement("style");
    styleSheet.type = "text/css";
    styleSheet.innerText = printStyles;
    document.head.appendChild(styleSheet);
</script>
@endpush
