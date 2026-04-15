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

    /* Button Detail */
    .btn-detail {
        background: #2196F3;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-detail:hover {
        background: #1976D2;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
    }

    .btn-detail i {
        font-size: 12px;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        max-width: 600px;
        width: 100%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #f0f0f0;
        color: #666;
    }

    .modal-body {
        padding: 25px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
        font-size: 14px;
    }

    .detail-value {
        font-family: 'Courier New', monospace;
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }

    .detail-section {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .detail-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .detail-section-title {
        font-size: 12px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
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
        {{-- <div class="info-box">
            Tabel berikut menampilkan hasil perhitungan Simple Additive Weighting (SAW) untuk setiap kriteria. 
            Data diperoleh dari agregasi seluruh responden survey dan diolah menggunakan rumus SAW untuk 
            mendapatkan nilai preferensi akhir.
        </div> --}}

        <!-- Table -->
        <div class="table-container">
            <div class="table-title">
                <h2>Hasil Perhitungan SAW</h2>
            </div>

            <table class="simple-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Keterangan</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                        <tr>
                            <td class="criteria-name">{{ $result['criteria'] }}</td>
                            <td>
                                <span class="badge-status badge-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                                    {{ $result['interpretation'] }}
                                </span>
                            </td>
                            <td>
                                <button class="btn-detail" onclick="showDetail{{ $loop->index }}()">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </button>
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

        <!-- Modal untuk Detail -->
        @foreach($criteriaResults as $index => $result)
        <div class="modal-overlay" id="modal{{ $index }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Detail Perhitungan: {{ $result['criteria'] }}</h3>
                    <button class="modal-close" onclick="closeModal{{ $index }}()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="detail-section">
                        <div class="detail-section-title">Informasi Dasar</div>
                        <div class="detail-row">
                            <span class="detail-label">Kriteria</span>
                            <span class="detail-value">{{ $result['criteria'] }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Tipe Kriteria</span>
                            <span class="detail-value">
                                <span class="badge-status badge-{{ $result['criteria_type'] === 'benefit' ? 'baik' : 'cukup' }}">
                                    {{ ucfirst($result['criteria_type']) }}
                                </span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Jumlah Sub-Kriteria</span>
                            <span class="detail-value">{{ $result['questions_count'] }} pertanyaan</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total Respons</span>
                            <span class="detail-value">{{ $result['total_responses'] }} nilai</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <div class="detail-section-title">Nilai & Perhitungan</div>
                        <div class="detail-row">
                            <span class="detail-label">Skor Agregat (x)</span>
                            <span class="detail-value">{{ $result['score'] }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Bobot Asli</span>
                            <span class="detail-value">
                                @php
                                    $criteriaName = $result['criteria'];
                                    $firstQuestion = \App\Models\SurveyQuestion::where('enable_saw', true)
                                        ->where('criteria_name', $criteriaName)
                                        ->first();
                                    $originalWeight = $firstQuestion ? $firstQuestion->criteria_weight : 0;
                                @endphp
                                {{ number_format($originalWeight, 1) }}
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Bobot Ternormalisasi (w<sub>ᵢ</sub>)</span>
                            <span class="detail-value">{{ number_format($result['weight_normalized'], 3) }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Nilai Ternormalisasi (r<sub>ᵢ</sub>)</span>
                            <span class="detail-value">{{ number_format($result['normalized'], 3) }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Nilai Terbobot (w×r)</span>
                            <span class="detail-value">{{ number_format($result['weighted_score'], 4) }}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <div class="detail-section-title">Interpretasi</div>
                        <div class="detail-row">
                            <span class="detail-label">Keterangan</span>
                            <span class="detail-value">
                                <span class="badge-status badge-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                                    {{ $result['interpretation'] }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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

    // Generate functions for each modal
    @foreach($criteriaResults as $index => $result)
    function showDetail{{ $index }}() {
        document.getElementById('modal{{ $index }}').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal{{ $index }}() {
        document.getElementById('modal{{ $index }}').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.getElementById('modal{{ $index }}').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal{{ $index }}();
        }
    });
    @endforeach

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            @foreach($criteriaResults as $index => $result)
            if (document.getElementById('modal{{ $index }}').classList.contains('active')) {
                closeModal{{ $index }}();
            }
            @endforeach
        }
    });
</script>
@endpush