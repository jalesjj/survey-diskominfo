{{-- resources/views/admin/hasil-survey/dashboard.blade.php - LANGSUNG TABEL SAW --}}
@extends('layouts.admin')

@section('title', 'Hasil Survey SAW - Admin')
@section('active-hasil-survey', 'active')
@section('page-title', 'Hasil Survey SAW')
@section('page-subtitle', 'Dashboard Nilai Akhir Kriteria - Simple Additive Weighting')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-separator">></span>
    <span>Hasil Survey</span>
</div>
@endsection

@push('styles')
<style>
    .page-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-header {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 25px 30px;
        margin-bottom: 25px;
        border-left: 5px solid #5a9b9e;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: #5a9b9e;
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-content h3 {
        color: #2c3e50;
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 4px 0;
    }

    .stat-content p {
        color: #6c757d;
        font-size: 14px;
        margin: 0;
        font-weight: 500;
    }

    .dashboard-description {
        background: #f8f9fa;
        border-left: 4px solid #5a9b9e;
        padding: 20px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 25px;
    }

    .dashboard-description h4 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .dashboard-description p {
        color: #495057;
        margin: 0;
        line-height: 1.6;
        font-size: 14px;
    }

    /* TABEL UTAMA SAW - LANGSUNG MUNCUL */
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
        padding: 25px 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .table-header h2 {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
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
        padding: 20px 15px;
        text-align: center;
        font-size: 13px;
        line-height: 1.3;
        border: none;
        position: relative;
    }

    .saw-table tbody td {
        padding: 18px 15px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
        text-align: center;
        font-size: 14px;
    }

    .saw-table tbody tr:nth-child(even) {
        background: #f8f9fa;
    }

    .saw-table tbody tr:hover {
        background: #e3f2fd;
        transition: background-color 0.2s ease;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Styling kolom sesuai struktur yang diminta */
    .criteria-cell {
        font-weight: 700;
        color: #2c3e50;
        background: rgba(90, 155, 158, 0.15) !important;
        border-left: 4px solid #5a9b9e;
        font-size: 16px;
        text-align: left !important;
        padding-left: 20px !important;
    }

    .score-cell {
        font-weight: 700;
        color: #2c3e50;
        font-size: 16px;
        background: rgba(255, 193, 7, 0.1);
        font-family: 'Courier New', monospace;
    }

    .weight-cell {
        font-family: 'Courier New', monospace;
        color: #6c757d;
        font-weight: 600;
        font-size: 13px;
    }

    .normalized-cell {
        font-family: 'Courier New', monospace;
        color: #495057;
        font-weight: 600;
        background: rgba(40, 167, 69, 0.1);
        font-size: 13px;
    }

    .weighted-cell {
        font-family: 'Courier New', monospace;
        color: #2c3e50;
        font-weight: 700;
        background: rgba(23, 162, 184, 0.1);
        font-size: 14px;
    }

    .interpretation-cell {
        font-weight: 700;
        font-size: 13px;
        padding: 10px 15px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Interpretations dengan warna yang lebih jelas */
    .interpretation-sangat-baik {
        background: #d4edda !important;
        color: #155724 !important;
        border-radius: 15px;
        border: 2px solid #c3e6cb;
    }

    .interpretation-baik {
        background: #d1ecf1 !important;
        color: #0c5460 !important;
        border-radius: 15px;
        border: 2px solid #bee5eb;
    }

    .interpretation-cukup {
        background: #fff3cd !important;
        color: #856404 !important;
        border-radius: 15px;
        border: 2px solid #ffeaa7;
    }

    .interpretation-kurang {
        background: #f8d7da !important;
        color: #721c24 !important;
        border-radius: 15px;
        border: 2px solid #f5c6cb;
    }

    .interpretation-sangat-kurang {
        background: #f5c6cb !important;
        color: #721c24 !important;
        border-radius: 15px;
        border: 2px solid #e3a9ae;
    }

    /* Total Section */
    .total-section {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-left h3 {
        font-size: 20px;
        margin-bottom: 8px;
        font-weight: 700;
    }

    .total-left p {
        font-size: 14px;
        opacity: 0.9;
        margin: 0;
    }

    .total-right {
        text-align: right;
    }

    .total-score {
        font-size: 42px;
        font-weight: 900;
        font-family: 'Courier New', monospace;
        margin-bottom: 8px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .total-interpretation {
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    /* No SAW Message */
    .no-saw-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 80px 40px;
        text-align: center;
        margin: 20px 0;
    }

    .no-saw-container i {
        font-size: 80px;
        color: #dee2e6;
        margin-bottom: 25px;
    }

    .no-saw-container h3 {
        color: #495057;
        margin-bottom: 15px;
        font-size: 24px;
    }

    .no-saw-container p {
        color: #6c757d;
        line-height: 1.6;
        max-width: 700px;
        margin: 0 auto;
        font-size: 16px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 20px;
        }

        .dashboard-stats {
            grid-template-columns: 1fr;
        }

        .table-header {
            padding: 20px;
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
            gap: 20px;
            text-align: center;
        }

        .total-score {
            font-size: 36px;
        }

        .criteria-cell {
            padding-left: 12px !important;
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="page-container">
    <!-- Dashboard Header dengan Stats -->
    <div class="dashboard-header">
        <div class="dashboard-stats">
            @if($hasSAW)
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($totalVi, 3) }}</h3>
                    <p>Total Nilai Preferensi (Vi)</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $criteriaResults->count() }}</h3>
                    <p>Kriteria Aktif</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalResponses }}</h3>
                    <p>Total Responden</p>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-content">
                    <h3>SAW</h3>
                    <p>Metode Perhitungan</p>
                </div>
            </div>
            @endif
        </div>

        @if($hasSAW)
            <div class="dashboard-description">
                <h4><i class="fas fa-info-circle"></i> Tentang Hasil Survey SAW</h4>
                <p>
                    Tabel di bawah menampilkan hasil perhitungan Simple Additive Weighting (SAW) untuk setiap kriteria. 
                    Data diperoleh dari agregasi seluruh responden survey dan diolah menggunakan rumus SAW untuk 
                    mendapatkan nilai preferensi akhir. Semakin tinggi nilai, semakin baik kondisi kriteria tersebut.
                </p>
            </div>
        @endif
    </div>

    @if($hasSAW)
        <!-- TABEL UTAMA SAW - LANGSUNG MUNCUL -->
        <div class="saw-table-container">
            <div class="table-header">
                <i class="fas fa-table"></i>
                <h2>Hasil Perhitungan SAW - Nilai Akhir Kriteria</h2>
            </div>

            <table class="saw-table">
                <thead>
                    <tr>
                        <th style="text-align: left; padding-left: 20px;">Kriteria</th>
                        <th>Skor (x)</th>
                        <th>Bobot Normalisasi<br>(w<sub>ᵢ</sub>)</th>
                        <th>Normalisasi<br>(r<sub>ᵢ</sub>)</th>
                        <th>Nilai Terbobot<br>(w<sub>ᵢ</sub>×r<sub>ᵢ</sub>)</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                        <tr>
                            <td class="criteria-cell">{{ $result['criteria'] }}</td>
                            <td class="score-cell">{{ $result['score'] }}</td>
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
                    <h3>Total Nilai Preferensi (V<sub>i</sub>)</h3>
                    <p>Hasil akhir perhitungan SAW dari seluruh kriteria</p>
                </div>
                <div class="total-right">
                    <div class="total-score">{{ number_format($totalVi, 4) }}</div>
                    @php
                        $totalInterpretation = $totalVi >= 0.9 ? 'Excellent' : 
                                              ($totalVi >= 0.8 ? 'Sangat Baik' : 
                                              ($totalVi >= 0.6 ? 'Baik' : 
                                              ($totalVi >= 0.4 ? 'Cukup' : 'Perlu Perbaikan')));
                    @endphp
                    <div class="total-interpretation">
                        {{ $totalInterpretation }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No SAW Message -->
        <div class="no-saw-container">
            <i class="fas fa-chart-bar"></i>
            <h3>Belum Ada Data SAW</h3>
            <p>
                {{ $message ?? 'Belum ada pertanyaan dengan pengaturan SAW yang aktif. 
                Silakan buat pertanyaan dengan tipe skala linier dan aktifkan fitur SAW 
                pada halaman manajemen pertanyaan untuk melihat hasil perhitungan di sini.' }}
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎯 Dashboard SAW langsung tabel dimuat:', {
            hasSAW: {{ $hasSAW ? 'true' : 'false' }},
            @if($hasSAW)
            totalVi: {{ $totalVi }},
            criteriaCount: {{ $criteriaResults->count() }},
            totalResponses: {{ $totalResponses }},
            method: 'Direct SAW Table Dashboard'
            @endif
        });

        // Add auto-refresh functionality if needed
        function refreshData() {
            window.location.reload();
        }

        // Add print functionality
        function printTable() {
            window.print();
        }

        // Enhanced table interactivity
        const tableRows = document.querySelectorAll('.saw-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('click', function() {
                // Optional: Add click functionality if needed
                console.log('Kriteria clicked:', this.querySelector('.criteria-cell').textContent);
            });
        });

        // Smooth animations for mobile
        if (window.innerWidth <= 768) {
            const tableContainer = document.querySelector('.saw-table-container');
            if (tableContainer) {
                tableContainer.style.overflowX = 'auto';
                tableContainer.style.scrollBehavior = 'smooth';
            }
        }
    });
</script>
@endpush