{{-- resources/views/admin/hasil-survey/dashboard.blade.php --}}
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
        padding: 20px;
    }

    /* Stats Cards - Simple */
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }

    .stat-label {
        color: #666;
        font-size: 13px;
        margin-bottom: 8px;
        text-transform: uppercase;
        font-weight: 500;
    }

    .stat-value {
        color: #333;
        font-size: 28px;
        font-weight: 700;
    }

    /* Description Box - Simple */
    .info-box {
        background: #f5f5f5;
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 30px;
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }

    /* Table Container - Clean */
    .table-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e0e0e0;
        margin-bottom: 30px;
    }

    .table-title {
        padding: 20px;
        background: #f9f9f9;
        border-bottom: 1px solid #e0e0e0;
    }

    .table-title h2 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: #333;
    }

    /* Table - Minimalist */
    .simple-table {
        width: 100%;
        border-collapse: collapse;
    }

    .simple-table thead th {
        background: #fafafa;
        color: #555;
        font-weight: 600;
        padding: 15px;
        text-align: left;
        font-size: 13px;
        border-bottom: 2px solid #e0e0e0;
    }

    .simple-table thead th:not(:first-child) {
        text-align: center;
    }

    .simple-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        color: #444;
    }

    .simple-table tbody tr:hover {
        background: #fafafa;
    }

    .simple-table tbody td:not(:first-child) {
        text-align: center;
    }

    /* Cell Styles - Minimal */
    .criteria-name {
        font-weight: 600;
        color: #222;
    }

    .number-cell {
        font-family: 'Courier New', monospace;
        font-weight: 500;
    }

    .badge-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-sangat-baik {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .badge-baik {
        background: #e3f2fd;
        color: #1565c0;
    }

    .badge-cukup {
        background: #fff9e6;
        color: #f57c00;
    }

    .badge-kurang {
        background: #ffebee;
        color: #c62828;
    }

    .badge-sangat-kurang {
        background: #fce4ec;
        color: #ad1457;
    }

    /* Total Section - Clean */
    .total-box {
        background: #f9f9f9;
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 2px solid #e0e0e0;
    }

    .total-label {
        font-size: 16px;
        font-weight: 600;
        color: #555;
    }

    .total-value {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        font-family: 'Courier New', monospace;
    }

    /* No Data Message */
    .no-data {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 60px 40px;
        text-align: center;
    }

    .no-data i {
        font-size: 60px;
        color: #ccc;
        margin-bottom: 20px;
    }

    .no-data h3 {
        color: #666;
        margin-bottom: 10px;
        font-size: 20px;
        font-weight: 600;
    }

    .no-data p {
        color: #999;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-container {
            padding: 15px;
        }

        .dashboard-stats {
            grid-template-columns: 1fr;
        }

        .simple-table {
            font-size: 12px;
        }

        .simple-table th,
        .simple-table td {
            padding: 10px 8px;
        }

        .total-box {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
<div class="page-container">
    
    @if($hasSAW)
        <!-- Stats Cards -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-label">Total Nilai Preferensi</div>
                <div class="stat-value">{{ number_format($totalVi, 3) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Kriteria Aktif</div>
                <div class="stat-value">{{ $criteriaResults->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Responden</div>
                <div class="stat-value">{{ $totalResponses }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Metode Perhitungan</div>
                <div class="stat-value">SAW</div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            Tabel berikut menampilkan hasil perhitungan Simple Additive Weighting (SAW) untuk setiap kriteria. 
            Data diperoleh dari agregasi seluruh responden survey dan diolah menggunakan rumus SAW untuk 
            mendapatkan nilai preferensi akhir.
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-title">
                <h2>Hasil Perhitungan SAW</h2>
            </div>

            <table class="simple-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Skor (x)</th>
                        <th>Bobot (w<sub>ᵢ</sub>)</th>
                        <th>Normalisasi (r<sub>ᵢ</sub>)</th>
                        <th>Nilai Terbobot</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                        <tr>
                            <td class="criteria-name">{{ $result['criteria'] }}</td>
                            <td class="number-cell">{{ $result['score'] }}</td>
                            <td class="number-cell">{{ number_format($result['weight_normalized'], 3) }}</td>
                            <td class="number-cell">{{ number_format($result['normalized'], 3) }}</td>
                            <td class="number-cell">{{ number_format($result['weighted_score'], 4) }}</td>
                            <td>
                                <span class="badge-status badge-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                                    {{ $result['interpretation'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total -->
            <div class="total-box">
                <div class="total-label">Total Nilai Preferensi (V<sub>i</sub>)</div>
                <div>
                    <div class="total-value">{{ number_format($totalVi, 4) }}</div>
                    @php
                        $totalInterpretation = $totalVi >= 0.9 ? 'Excellent' : 
                                              ($totalVi >= 0.8 ? 'Sangat Baik' : 
                                              ($totalVi >= 0.6 ? 'Baik' : 
                                              ($totalVi >= 0.4 ? 'Cukup' : 'Perlu Perbaikan')));
                    @endphp
                    <div style="text-align: right; margin-top: 8px;">
                        <span class="badge-status badge-{{ strtolower(str_replace(' ', '-', $totalInterpretation)) }}">
                            {{ $totalInterpretation }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Data -->
        <div class="no-data">
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
        console.log('Dashboard SAW loaded');
    });
</script>
@endpush