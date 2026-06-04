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

@section('header-actions')
<div class="header-actions">
    {{-- DROPDOWN FILTER PERIODE - POSISI KANAN ATAS --}}
    @if(isset($allPeriods) && $allPeriods->count() > 0)
    <div class="period-filter-container">
        <form action="{{ route('admin.hasil-survey') }}" method="GET" id="periodFilterForm">
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
<style>
    /* =========================================
       DROPDOWN PERIODE - STYLING
       ========================================= */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
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

    /* =========================================
       TABEL SAW PER RESPONDEN
       ========================================= */
    .respondent-table-container {
        background: white;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-top: 30px;
        overflow: hidden;
    }
 
    .respondent-table-container .table-title {
        padding: 20px 25px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
 
    .respondent-table-container .table-title h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
    }
 
    .respondent-table-container .table-title p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }
 
    .respondent-saw-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
 
    .respondent-saw-table thead tr {
        background: #f8f9fa;
        border-bottom: 2px solid #e2e8f0;
    }
 
    .respondent-saw-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
 
    .respondent-saw-table th.text-center,
    .respondent-saw-table td.text-center {
        text-align: center;
    }
 
    .respondent-saw-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }
 
    .respondent-saw-table tbody tr:last-child {
        border-bottom: none;
    }
 
    .respondent-saw-table tbody tr:hover {
        background: #f8fafc;
    }
 
    .respondent-saw-table td {
        padding: 12px 16px;
        color: #374151;
        vertical-align: middle;
    }
 
    .respondent-saw-table td.nama-col {
        font-weight: 600;
        color: #2c3e50;
    }
 
    .respondent-saw-table td.skor-col {
        font-weight: 700;
        color: #5a9b9e;
        font-size: 15px;
    }
 
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 13px;
    }
 
    .rank-1 { background: #fef9c3; color: #854d0e; }
    .rank-2 { background: #f1f5f9; color: #475569; }
    .rank-3 { background: #fef3c7; color: #92400e; }
    .rank-other { background: #f8f9fa; color: #94a3b8; }
 
    .interp-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
 
    .interp-badge.sangat-baik   { background: #d1fae5; color: #065f46; }
    .interp-badge.baik          { background: #dbeafe; color: #1e40af; }
    .interp-badge.cukup         { background: #fef9c3; color: #854d0e; }
    .interp-badge.kurang        { background: #ffedd5; color: #9a3412; }
    .interp-badge.sangat-kurang { background: #fee2e2; color: #991b1b; }
    .interp-badge.no-data       { background: #f1f5f9; color: #94a3b8; }
 
    .respondent-table-container .table-footer {
        padding: 12px 20px;
        background: #f8f9fa;
        border-top: 1px solid #e2e8f0;
        font-size: 13px;
        color: #64748b;
        text-align: right;
    }
</style>
@endpush

@section('content')
<div class="page-container">
    {{-- INFO BAR PERIODE - SATU BARIS (info di kiri, dropdown di kanan) --}}
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
            @if($selectedPeriod->description)
                <span style="color: #95a5a6; font-size: 13px;">— {{ $selectedPeriod->description }}</span>
            @endif
        </div>
        @else
        <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-calendar" style="color: #95a5a6;"></i>
            <span style="font-weight: 600; color: #7f8c8d; font-size: 14px;">Periode: Semua</span>
        </div>
        @endif

        {{-- Dropdown History di Kanan --}}
        <form action="{{ route('admin.hasil-survey') }}" method="GET" id="periodFilterFormContent" style="margin: 0;">
            <select
                name="period_id"
                onchange="document.getElementById('periodFilterFormContent').submit()"
                style="padding: 8px 35px 8px 15px; border: 2px solid #5a9b9e; border-radius: 8px; background: white url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2712%27 height=%2712%27 viewBox=%270 0 12 12%27%3E%3Cpath fill=%27%235a9b9e%27 d=%27M6 9L1 4h10z%27/%3E%3C/svg%3E') no-repeat right 12px center; color: #2c3e50; font-weight: 600; font-size: 14px; cursor: pointer; min-width: 250px; appearance: none; -webkit-appearance: none; -moz-appearance: none; transition: all 0.3s ease;">
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

        {{-- ============================================================
             CARD: SAW PER RESPONDEN
             ============================================================ --}}
        @if(isset($surveys) && $surveys->count() > 0 && isset($respondentSawScores))
        @php
            // Buat daftar responden dengan skor SAW, filter yang punya data SAW
            $respondentList = $surveys->map(function($survey) use ($respondentSawScores) {
                $namaResp     = $survey->responses->where('question_id', 'nama')->first();
                $emailResp    = $survey->responses->where('question_id', 'email')->first();
                $genderResp   = $survey->responses->where('question_id', 'jenis_kelamin')->first();
                $umurResp     = $survey->responses->where('question_id', 'umur')->first();
                $pendidResp   = $survey->responses->where('question_id', 'jenis_pendidikan')->first();
                $pekerjaanResp= $survey->responses->where('question_id', 'pekerjaan')->first();
 
                $saw = $respondentSawScores[$survey->id] ?? ['score' => 0, 'has_saw' => false, 'interpretation' => '-'];
 
                return [
                    'survey_id'       => $survey->id,
                    'nama'            => $namaResp?->answer ?? 'Responden #' . $survey->id,
                    'email'           => $emailResp?->answer ?? '-',
                    'jenis_kelamin'   => $genderResp?->answer ?? '-',
                    'umur'            => $umurResp?->answer ?? '-',
                    'jenis_pendidikan'=> $pendidResp?->answer ?? '-',
                    'pekerjaan'       => $pekerjaanResp?->answer ?? '-',
                    'saw_score'       => $saw['score'],
                    'has_saw'         => $saw['has_saw'],
                    'interpretation'  => $saw['interpretation'],
                ];
            })
            ->filter(fn($r) => $r['has_saw'])        // hanya yang punya data SAW
            ->sortByDesc('saw_score')                 // urutkan dari skor tertinggi
            ->values();
        @endphp
 
        @if($respondentList->count() > 0)
        <div class="respondent-table-container">
            <div class="table-title">
                <div>
                    <h2><i class="fas fa-users" style="color:#5a9b9e; margin-right:8px;"></i>Hasil SAW Per Responden</h2>
                    <p>Diurutkan dari nilai preferensi tertinggi · {{ $respondentList->count() }} responden</p>
                </div>
            </div>
 
            <div class="respondent-table-scroll">
                <table class="respondent-saw-table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th class="text-center">Jenis Kelamin</th>
                        <th class="text-center">Umur</th>
                        <th class="text-center">Pendidikan</th>
                        <th class="text-center">Pekerjaan</th>
                        <th class="text-center">Skor V<sub>i</sub></th>
                        <th class="text-center">Interpretasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($respondentList as $i => $resp)
                    @php
                        $rank = $i + 1;
                        $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                        $interpClass = match($resp['interpretation']) {
                            'Sangat Baik'   => 'sangat-baik',
                            'Baik'          => 'baik',
                            'Cukup'         => 'cukup',
                            'Kurang'        => 'kurang',
                            'Sangat Kurang' => 'sangat-kurang',
                            default         => 'no-data',
                        };
                    @endphp
                    <tr>
                        <td class="text-center">
                            <span class="rank-badge {{ $rankClass }}">{{ $rank }}</span>
                        </td>
                        <td class="nama-col">{{ $resp['nama'] }}</td>
                        <td>{{ $resp['email'] }}</td>
                        <td class="text-center">{{ $resp['jenis_kelamin'] }}</td>
                        <td class="text-center">{{ $resp['umur'] }}</td>
                        <td class="text-center">{{ $resp['jenis_pendidikan'] }}</td>
                        <td class="text-center">{{ $resp['pekerjaan'] }}</td>
                        <td class="text-center skor-col">{{ number_format($resp['saw_score'], 3) }}</td>
                        <td class="text-center">
                            <span class="interp-badge {{ $interpClass }}">{{ $resp['interpretation'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
 
            <div class="table-footer">
                Total {{ $respondentList->count() }} responden dengan data SAW
            </div>
        </div>
        @endif
        @endif

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
                @if(isset($selectedPeriod) && $selectedPeriod)
                    Belum ada data SAW untuk periode {{ $selectedPeriod->period_name }}.
                @else
                    {{ $message ?? 'Belum ada pertanyaan dengan pengaturan SAW yang aktif.' }}
                @endif
                Silakan buat pertanyaan dengan tipe skala linier dan aktifkan fitur SAW 
                pada halaman manajemen pertanyaan untuk melihat hasil perhitungan di sini.
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