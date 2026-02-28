{{-- resources/views/admin/survey-results/show.blade.php - OUTPUT NILAI AKHIR KRITERIA SAW
@extends('layouts.admin')

@section('title', 'Hasil Survey SAW - Admin')
@section('active-results', 'active')
@section('page-title', 'Output Nilai Akhir Kriteria SAW')
@section('page-subtitle', 'Survey ID: #' . $survey->id . ' - Hasil Perhitungan Simple Additive Weighting')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-separator">></span>
    <a href="{{ route('admin.survey-results.index') }}">Hasil Survey</a>
    <span class="breadcrumb-separator">></span>
    <span>SAW #{{ $survey->id }}</span>
</div>
@endsection

@push('styles')
<style>
    .page-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .survey-header {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 25px 30px;
        margin-bottom: 25px;
        border-left: 5px solid #5a9b9e;
    }

    .survey-basic-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-icon {
        width: 35px;
        height: 35px;
        background: #5a9b9e;
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .info-content h4 {
        color: #2c3e50;
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 4px 0;
    }

    .info-content p {
        color: #6c757d;
        font-size: 13px;
        margin: 0;
    }

    /* TABEL UTAMA OUTPUT NILAI AKHIR KRITERIA */
    .saw-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .table-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 20px 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .table-header h3 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .table-description {
        background: #f8f9fa;
        padding: 20px 30px;
        border-bottom: 1px solid #e9ecef;
        font-size: 14px;
        color: #495057;
        line-height: 1.6;
    }

    .saw-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .saw-table thead th {
        background: #5a9b9e;
        color: white;
        font-weight: 700;
        padding: 18px 15px;
        text-align: center;
        font-size: 13px;
        line-height: 1.3;
        border: none;
    }

    .saw-table tbody td {
        padding: 16px 15px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
        text-align: center;
    }

    .saw-table tbody tr:nth-child(even) {
        background: #f8f9fa;
    }

    .saw-table tbody tr:hover {
        background: #e3f2fd;
        transition: background-color 0.2s ease;
    }

    /* Styling kolom sesuai output nilai akhir kriteria */
    .criteria-cell {
        font-weight: 700;
        color: #2c3e50;
        background: rgba(90, 155, 158, 0.1) !important;
        border-left: 4px solid #5a9b9e;
        font-size: 16px;
    }

    .aggregate-cell {
        font-weight: 700;
        color: #2c3e50;
        font-size: 15px;
        background: rgba(255, 193, 7, 0.1);
        font-family: 'Courier New', monospace;
    }

    .weight-cell {
        font-family: 'Courier New', monospace;
        color: #6c757d;
        font-weight: 600;
    }

    .normalized-cell {
        font-family: 'Courier New', monospace;
        color: #495057;
        font-weight: 600;
        background: rgba(40, 167, 69, 0.1);
    }

    .weighted-cell {
        font-family: 'Courier New', monospace;
        color: #2c3e50;
        font-weight: 700;
        background: rgba(23, 162, 184, 0.1);
    }

    .interpretation-cell {
        font-weight: 700;
        font-size: 13px;
        padding: 8px 15px;
    }

    /* Interpretations */
    .interpretation-sangat-baik {
        background: #d4edda;
        color: #155724;
        border-radius: 15px;
    }

    .interpretation-baik {
        background: #cce5ff;
        color: #004085;
        border-radius: 15px;
    }

    .interpretation-cukup {
        background: #fff3cd;
        color: #856404;
        border-radius: 15px;
    }

    .interpretation-kurang {
        background: #f8d7da;
        color: #721c24;
        border-radius: 15px;
    }

    .interpretation-sangat-kurang {
        background: #f5c6cb;
        color: #721c24;
        border-radius: 15px;
    }

    /* Total Section */
    .total-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 25px 30px;
        border-top: 3px solid #5a9b9e;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-left h4 {
        color: #2c3e50;
        font-size: 18px;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .total-left p {
        color: #6c757d;
        font-size: 13px;
        margin: 0;
    }

    .total-right {
        text-align: right;
    }

    .total-score {
        font-size: 36px;
        font-weight: 900;
        color: #5a9b9e;
        font-family: 'Courier New', monospace;
        margin-bottom: 8px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }

    .total-interpretation {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* No SAW Message */
    .no-saw-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 60px 40px;
        text-align: center;
        margin: 20px 0;
    }

    .no-saw-container i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .no-saw-container h4 {
        color: #495057;
        margin-bottom: 15px;
        font-size: 20px;
    }

    .no-saw-container p {
        color: #6c757d;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Formula Reference */
    .formula-reference {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 25px 30px;
        margin-top: 30px;
        border-left: 5px solid #17a2b8;
    }

    .formula-reference h4 {
        color: #17a2b8;
        margin-bottom: 15px;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .formula-item {
        margin-bottom: 15px;
        font-size: 14px;
        line-height: 1.6;
    }

    .formula-item strong {
        color: #2c3e50;
    }

    .formula-item code {
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        color: #e83e8c;
        border: 1px solid #e9ecef;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .survey-header {
            padding: 20px;
        }

        .survey-basic-info {
            grid-template-columns: 1fr;
        }

        .table-header {
            padding: 15px 20px;
        }

        .table-description {
            padding: 15px 20px;
        }

        .saw-table {
            font-size: 12px;
        }

        .saw-table th,
        .saw-table td {
            padding: 12px 8px;
        }

        .total-section {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .total-score {
            font-size: 30px;
        }
    }
</style>
@endpush

@section('content')
<div class="page-container">
    <!-- Survey Basic Info -->
    <div class="survey-header">
        <div class="survey-basic-info">
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div class="info-content">
                    <h4>Survey ID</h4>
                    <p>#{{ $survey->id }}</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="info-content">
                    <h4>Tanggal Pengisian</h4>
                    <p>{{ $survey->created_at->format('d F Y, H:i') }} WIB</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="info-content">
                    <h4>Alamat IP</h4>
                    <p><code>{{ $survey->ip_address ?? 'N/A' }}</code></p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas {{ $hasSAW ? 'fa-check' : 'fa-times' }}"></i>
                </div>
                <div class="info-content">
                    <h4>Status SAW</h4>
                    <p style="color: {{ $hasSAW ? '#28a745' : '#dc3545' }};">
                        {{ $hasSAW ? 'Aktif' : 'Nonaktif' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($hasSAW)
        <!-- TABEL UTAMA OUTPUT NILAI AKHIR KRITERIA SAW -->
        <div class="saw-table-container">
            <div class="table-header">
                <i class="fas fa-calculator"></i>
                <h3>Output Nilai Akhir Kriteria - Metode SAW</h3>
            </div>

            <div class="table-description">
                <strong>Perhitungan SAW:</strong> Tabel ini menampilkan output nilai akhir per kriteria (bukan sub-kriteria) 
                sesuai rumus Simple Additive Weighting. Setiap baris merepresentasikan satu kriteria utama yang telah 
                diagregasi dari sub-kriteria menggunakan rata-rata, dinormalisasi dengan pembagian nilai maksimum, 
                dan dikalikan dengan bobot ternormalisasi untuk menghasilkan nilai preferensi (Vi).
            </div>

            <table class="saw-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Nilai Agregat<br>(X<sub>ij</sub>)</th>
                        <th>Bobot Normalisasi<br>(w<sub>j</sub>)</th>
                        <th>Normalisasi SAW<br>(r<sub>ij</sub>)</th>
                        <th>Nilai Terbobot<br>(w<sub>j</sub> × r<sub>ij</sub>)</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                        <tr>
                            <td class="criteria-cell">{{ $result['criteria'] }}</td>
                            <td class="aggregate-cell">{{ $result['aggregate_value'] }}</td>
                            <td class="weight-cell">{{ number_format($result['weight_normalized'], 3) }}</td>
                            <td class="normalized-cell">{{ number_format($result['normalized'], 3) }}</td>
                            <td class="weighted-cell">{{ number_format($result['weighted_score'], 4) }}</td>
                            <td class="interpretation-cell interpretation-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                                {{ $result['interpretation'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total Score Section -->
            <div class="total-section">
                <div class="total-left">
                    <h4>Total Nilai Preferensi (V<sub>i</sub>)</h4>
                    <p>Hasil akhir SAW = Σ(w<sub>j</sub> × r<sub>ij</sub>) untuk semua kriteria</p>
                </div>
                <div class="total-right">
                    <div class="total-score">{{ number_format($totalVi, 4) }}</div>
                    @php
                        $totalInterpretation = $totalVi >= 0.9 ? 'Sangat Baik' : 
                                              ($totalVi >= 0.8 ? 'Baik' : 
                                              ($totalVi >= 0.6 ? 'Cukup' : 
                                              ($totalVi >= 0.4 ? 'Kurang' : 'Sangat Kurang')));
                        $interpretationClass = 'interpretation-' . strtolower(str_replace(' ', '-', $totalInterpretation));
                    @endphp
                    <div class="total-interpretation {{ $interpretationClass }}">
                        {{ $totalInterpretation }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Formula Reference -->
        <div class="formula-reference">
            <h4>
                <i class="fas fa-book"></i>
                Rumus SAW yang Digunakan
            </h4>
            <div class="formula-item">
                <strong>1. Agregasi Sub-Kriteria:</strong> 
                <code>X_ij = (Σ skor_sub-kriteria) / n_sub-kriteria</code>
            </div>
            <div class="formula-item">
                <strong>2. Normalisasi Bobot:</strong> 
                <code>w_j = W_j / Σ W_j</code>
            </div>
            <div class="formula-item">
                <strong>3. Normalisasi SAW:</strong> 
                <code>r_ij = X_ij / Max{X_ij}</code> (untuk kriteria benefit)
            </div>
            <div class="formula-item">
                <strong>4. Nilai Preferensi:</strong> 
                <code>V_i = Σ(w_j × r_ij)</code>
            </div>
            <div class="formula-item">
                <strong>Contoh:</strong> Kriteria Afektif dengan agregat 75.0, bobot 0.30, normalisasi 0.91, 
                menghasilkan nilai terbobot 0.273. Total Vi = penjumlahan semua nilai terbobot kriteria.
            </div>
        </div>
    @else
        <!-- No SAW Message -->
        <div class="no-saw-container">
            <i class="fas fa-info-circle"></i>
            <h4>Survey Ini Tidak Menggunakan Perhitungan SAW</h4>
            <p>
                {{ $message ?? 'Survey ini hanya berisi pertanyaan biasa tanpa perhitungan Sistem Pendukung Keputusan. 
                Untuk menggunakan fitur SAW, pertanyaan harus bertipe skala linier dengan pengaturan SAW yang diaktifkan.' }}
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎯 Output Nilai Akhir Kriteria SAW dimuat:', {
            surveyId: {{ $survey->id }},
            hasSAW: {{ $hasSAW ? 'true' : 'false' }},
            @if($hasSAW)
            totalVi: {{ $totalVi }},
            criteriaCount: {{ $criteriaResults->count() }},
            method: 'Simple Additive Weighting (SAW)'
            @endif
        });

        // Add print functionality
        function printResults() {
            window.print();
        }

        // Add export functionality if needed
        function exportResults() {
            // Implementation for export functionality
            console.log('Export SAW results functionality');
        }

        // Smooth scroll untuk responsif mobile
        if (window.innerWidth <= 768) {
            const tableContainer = document.querySelector('.saw-table-container');
            if (tableContainer) {
                tableContainer.style.overflowX = 'auto';
            }
        }
    });
</script>
@endpush --}}