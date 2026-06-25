{{-- resources/views/admin/dashboard/index.blade.php --}}
@extends('layouts.admin')
 
@section('title', 'Dashboard - Admin Survei')
@section('active-dashboard', 'active')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan Data Survey Periode Aktif')
 
@section('breadcrumb')
<div class="breadcrumb">
    <span>Dashboard</span>
</div>
@endsection
 
@section('headebrrtir-actions')
<div class="header-actions">
    {{-- DROPDOWN FILTER PERIODE --}}
    @if(isset($allPeriods) && $allPeriods->count() > 0)
    <div class="period-filter-container">
        <form action="{{ route('admin.dashboard.new') }}" method="GET" id="periodFilterForm">
            <select name="period_id" class="period-select" onchange="document.getElementById('periodFilterForm').submit()">
                <option value="">Semua Periode</option>
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
    
    <span class="admin-welcome">Selamat datang, {{ session('admin_name') }}</span>
</div>
@endsection
 
@push('styles')
<style>
    /* Header Actions */
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
        font-size: 14px;
        color: #64748b;
    }
 
    /* Dashboard Container */
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }
 
    /* Stats Cards Grid - 3 Kolom */
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
 
    /* Card Styling */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 30px 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid;
    }
 
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }
 
    .stat-card.card-nilai    { border-left-color: #5a9b9e; }
    .stat-card.card-kriteria { border-left-color: #3498db; }
    .stat-card.card-responden{ border-left-color: #2ecc71; }
 
    .stat-label {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 12px;
    }
 
    .stat-value {
        font-size: 42px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }
 
    .stat-description {
        font-size: 14px;
        color: #94a3b8;
        margin-top: 8px;
    }
 
    /* Badge Keterangan */
    .badge-keterangan {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        margin-top: 10px;
    }
 
    .badge-excellent        { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .badge-sangat-baik      { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
    .badge-baik             { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; }
    .badge-cukup            { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
    .badge-perlu-perbaikan  { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
    .badge-tidak-ada-data   { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); color: white; }
 
    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }
 
    .chart-header {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
 
    .chart-header-left h3 {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 6px;
    }
 
    .chart-header-left h3 i {
        color: #5a9b9e;
        margin-right: 8px;
    }
 
    .chart-header-left p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }
 
    .chart-body {
        position: relative;
        max-height: 420px;
        overflow-y: auto;
        padding: 10px 0;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }
 
    .chart-body::-webkit-scrollbar { width: 6px; height: 6px; }
    .chart-body::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
    .chart-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .chart-body::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
 
    .chart-inner {
        position: relative;
        min-height: 300px;
    }
 
    /* Perbandingan Antar Periode */
    .comparison-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
        overflow: hidden;
    }
 
    .comparison-header {
        padding: 20px 25px 15px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
 
    .comparison-header-left h3 {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
    }
 
    .comparison-header-left h3 i {
        color: #5a9b9e;
        margin-right: 8px;
    }

    .comparison-header-left p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }
 
    .view-toggle {
        display: flex;
        gap: 8px;
        align-items: center;
    }
 
    .toggle-btn {
        padding: 7px 16px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        background: white;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
 
    .toggle-btn:hover { border-color: #5a9b9e; color: #5a9b9e; }
    .toggle-btn.active { background: #5a9b9e; border-color: #5a9b9e; color: white; }
 
    .comparison-table-wrapper {
        max-height: 320px;
        overflow-y: auto;
        overflow-x: auto;
        padding: 0 25px 20px;
    }
 
    .comparison-table-wrapper::-webkit-scrollbar { width: 6px; height: 6px; }
    .comparison-table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .comparison-table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .comparison-table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
 
    .comparison-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-top: 15px;
    }
 
    .comparison-table thead th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
        z-index: 1;
    }
 
    .comparison-table tbody tr { transition: background 0.15s ease; }
    .comparison-table tbody tr:hover { background: #f8fafc; }
    .comparison-table tbody tr.active-period { background: #f0fdf4; }
 
    .comparison-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
        vertical-align: middle;
        white-space: nowrap;
    }
 
    .period-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #1e293b;
    }
 
    .active-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        flex-shrink: 0;
    }
 
    .predikat-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
 
    .predikat-excellent       { background: #d1fae5; color: #065f46; }
    .predikat-sangat-baik     { background: #dbeafe; color: #1e40af; }
    .predikat-baik            { background: #ede9fe; color: #5b21b6; }
    .predikat-cukup           { background: #fef3c7; color: #92400e; }
    .predikat-perlu-perbaikan { background: #fee2e2; color: #991b1b; }
    .predikat-belum-ada-data  { background: #f1f5f9; color: #64748b; }
 
    .comparison-chart-wrapper {
        padding: 15px 25px 20px;
        display: none;
    }
 
    .comparison-chart-body {
        position: relative;
        height: 300px;
    }
 
    /* Info Box */
    .info-box {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }
 
    .info-box h3 { font-size: 18px; font-weight: 600; color: #2c3e50; margin-bottom: 15px; }
    .info-box p  { font-size: 14px; color: #64748b; line-height: 1.6; margin: 0; }
 
    /* Periode Info */
    .periode-info {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
 
    .periode-info i { font-size: 24px; color: #0284c7; }
    .periode-info-text { flex: 1; }
    .periode-info-text strong { font-size: 16px; color: #0c4a6e; display: block; margin-bottom: 4px; }
    .periode-info-text span   { font-size: 13px; color: #0369a1; }

    /* Tombol unduh PNG */
    .btn-unduh-png {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #2563EB;
        color: white;
        border: none;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        white-space: nowrap;
    }
    .btn-unduh-png:hover { background: #1d4ed8; }

    /* ── Share Link Card ─────────────────────────────────────────── */
    .share-link-card {
        background: white;
        border-radius: 12px;
        padding: 18px 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        border-left: 4px solid #8b5cf6;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .share-link-label {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .share-link-label i { color: #8b5cf6; }

    .share-link-url-wrap {
        flex: 1;
        min-width: 180px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 14px;
        font-size: 13px;
        color: #475569;
        font-family: monospace;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .share-link-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-share-copy, .btn-share-native {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-share-copy {
        background: #f1f5f9;
        color: #475569;
        border: 1.5px solid #e2e8f0;
    }
    .btn-share-copy:hover  { background: #e2e8f0; }
    .btn-share-copy.copied { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }

    .btn-share-native {
        background: #8b5cf6;
        color: white;
    }
    .btn-share-native:hover { background: #7c3aed; }

    /* ── Modal Share ─────────────────────────────────────────────── */
    .share-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .share-modal-overlay.active { display: flex; }

    .share-modal {
        background: white;
        border-radius: 16px;
        padding: 28px;
        width: 90%;
        max-width: 380px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: modalPop 0.2s ease;
    }
    @keyframes modalPop {
        from { opacity:0; transform: scale(0.92) translateY(10px); }
        to   { opacity:1; transform: scale(1)    translateY(0);     }
    }

    .share-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .share-modal-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    .share-modal-close {
        background: none;
        border: none;
        font-size: 20px;
        color: #94a3b8;
        cursor: pointer;
        padding: 2px 6px;
        border-radius: 6px;
        line-height: 1;
        transition: all 0.2s;
    }
    .share-modal-close:hover { background: #f1f5f9; color: #475569; }

    .share-modal-url {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12px;
        font-family: monospace;
        color: #475569;
        word-break: break-all;
        margin-bottom: 20px;
        transition: all 0.2s;
    }

    .share-modal-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }

    .share-app-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 14px 8px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .share-app-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
    .share-app-btn i { font-size: 24px; }

    .share-app-wa       { background: #e7fbe6; color: #128C7E; }
    .share-app-tg       { background: #e3f2fd; color: #0088cc; }
    .share-app-email    { background: #fce4ec; color: #c62828; }
    .share-app-x        { background: #f1f5f9; color: #14171A; }
    .share-app-fb       { background: #e8f0fe; color: #1877F2; }
    .share-app-copy-mod { background: #f3e8ff; color: #7c3aed; }

    .share-modal-note {
        text-align: center;
        font-size: 12px;
        color: #94a3b8;
        margin: 4px 0 0;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .dashboard-stats { grid-template-columns: repeat(2, 1fr); }
        .chart-body { max-height: 380px; }
    }
 
    @media (max-width: 768px) {
        .header-actions { flex-direction: column; align-items: flex-start; gap: 10px; }
        .period-filter-container { width: 100%; margin-right: 0; }
        .period-select { width: 100%; min-width: auto; }
        .dashboard-stats { grid-template-columns: 1fr; }
        .stat-value { font-size: 36px; }
        .dashboard-container { padding: 15px; }
        .chart-body { max-height: 320px; }
        .chart-container { padding: 20px 15px; }
        .share-link-card { flex-direction: column; align-items: stretch; }
        .share-link-actions { justify-content: flex-end; }
    }
</style>
@endpush
 
@section('content')
<div class="dashboard-container">

    {{-- INFO PERIODE AKTIF --}}
    @if(isset($selectedPeriod) && $selectedPeriod)
    <div class="periode-info">
        <i class="fas fa-calendar-check"></i>
        <div class="periode-info-text">
            <strong>{{ $selectedPeriod->period_name }}</strong>
            <span>Tahun {{ $selectedPeriod->year }} 
                @if($selectedPeriod->is_active)
                    <span style="color: #059669; font-weight: 600;">● Aktif</span>
                @endif
            </span>
        </div>
    </div>
    @endif
 
    {{-- 3 CARD STATISTIK UTAMA --}}
    <div class="dashboard-stats">
        <div class="stat-card card-nilai">
            <div class="stat-label"><i class="fas fa-chart-line"></i> Total Nilai Semua Kriteria</div>
            <div class="stat-value">{{ number_format($totalNilaiPreferensi, 4) }}</div>
            <div class="badge-keterangan badge-{{ strtolower(str_replace(' ', '-', $keteranganNilai)) }}">
                {{ $keteranganNilai }}
            </div>
            <div class="stat-description">
                Dari periode {{ $selectedPeriod ? $selectedPeriod->period_name : 'Semua Periode' }}
            </div>
        </div>
 
        <div class="stat-card card-kriteria">
            <div class="stat-label"><i class="fas fa-list-check"></i> Kriteria Aktif</div>
            <div class="stat-value">{{ $jumlahKriteriaAktif }}</div>
            <div class="stat-description">Kriteria dengan SAW aktif</div>
        </div>
 
        <div class="stat-card card-responden">
            <div class="stat-label"><i class="fas fa-users"></i> Total Responden</div>
            <div class="stat-value">{{ number_format($totalResponden) }}</div>
            <div class="stat-description">Responden periode ini</div>
        </div>
    </div>

    {{-- SHARE LINK SURVEI --}}
    <div class="share-link-card">
        <div class="share-link-label">
            <i class="fas fa-share-alt"></i> Link Survei
        </div>
        <div class="share-link-url-wrap">{{ url('/') }}</div>
        <div class="share-link-actions">
            <button class="btn-share-copy" id="btnCopyLink" onclick="copyShareLink()">
                <i class="fas fa-copy"></i> Salin
            </button>
            <button class="btn-share-native" onclick="openShare()">
                <i class="fas fa-share-alt"></i> Bagikan
            </button>
        </div>
    </div>
 
    {{-- GRAFIK BAR CHART PER KRITERIA --}}
    @if($jumlahKriteriaAktif > 0)
    <div class="chart-container">
        <div class="chart-header">
            <div class="chart-header-left">
                <h3><i class="fas fa-chart-bar"></i> Grafik Nilai per Kriteria</h3>
                <p>Diurutkan dari nilai tertinggi (terbaik) hingga terendah (terburuk)</p>
            </div>
            <button class="btn-unduh-png" onclick="downloadCriteriaChart()">
                <i class="fas fa-download"></i> Unduh PNG
            </button>
        </div>
        <div class="chart-body">
            <div class="chart-inner" id="criteriaChartInner">
                <canvas id="criteriaChart"></canvas>
            </div>
        </div>
    </div>
    @endif
 
    {{-- BARIS KE-3: PERBANDINGAN ANTAR PERIODE --}}
    @if(isset($periodeComparison) && $periodeComparison->count() > 0)
    <div class="comparison-container">
        <div class="comparison-header">
            <div class="comparison-header-left">
                <h3><i class="fas fa-exchange-alt"></i> Perbandingan Antar Periode</h3>
                <p>Ringkasan nilai SAW seluruh periode survey · Jumlah kriteria bisa berbeda tiap periode</p>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <button class="btn-unduh-png" onclick="downloadSKMChart()">
                    <i class="fas fa-download"></i> Unduh PNG
                </button>
                <div class="view-toggle">
                    <button class="toggle-btn active" id="btnTable" onclick="showView('table')">
                        <i class="fas fa-table"></i> Tabel
                    </button>
                    <button class="toggle-btn" id="btnChart" onclick="showView('chart')">
                        <i class="fas fa-chart-line"></i> Grafik
                    </button>
                </div>
            </div>
        </div>
 
        {{-- TABEL --}}
        <div id="viewTable" class="comparison-table-wrapper">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Kriteria</th>
                        <th>Predikat</th>
                        <th>Total Nilai Semua Kriteria</th>
                        <th>Responden</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($periodeComparison as $p)
                    <tr class="{{ $p['is_active'] ? 'active-period' : '' }}">
                        <td>
                            <span class="period-badge">
                                @if($p['is_active'])
                                    <span class="active-dot" title="Periode Aktif"></span>
                                @endif
                                {{ $p['period_name'] }} ({{ $p['year'] }})
                            </span>
                        </td>
                        <td>
                            @if($p['jumlah_kriteria'] > 0)
                                {{ $p['jumlah_kriteria'] }} kriteria
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </td>
                        <td>
                            @php $slug = strtolower(str_replace(' ', '-', $p['predikat'])); @endphp
                            <span class="predikat-badge predikat-{{ $slug }}">{{ $p['predikat'] }}</span>
                        </td>
                        <td>
                            @if($p['jumlah_kriteria'] > 0)
                                <strong>{{ number_format($p['total_vi'], 4) }}</strong>
                            @else
                                <span style="color:#94a3b8;">tidak ada</span>
                            @endif
                        </td>
                        <td>{{ number_format($p['responden']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
 
        {{-- GRAFIK LINE CHART --}}
        <div id="viewChart" class="comparison-chart-wrapper">
            <div class="comparison-chart-body">
                <canvas id="periodeChart"></canvas>
            </div>
        </div>
    </div>
    @endif
 
    {{-- INFO BOX --}}
    <div class="info-box">
        <h3><i class="fas fa-info-circle"></i> Informasi Dashboard</h3>
        <p>
            Dashboard ini menampilkan ringkasan data survey dari periode yang dipilih. 
            <strong>Total Nilai Semua Kriteria</strong> dihitung menggunakan metode Simple Additive Weighting (SAW), 
            <strong>Kriteria Aktif</strong> menunjukkan jumlah kriteria yang digunakan dalam perhitungan SAW, 
            dan <strong>Total Responden</strong> adalah jumlah partisipan yang telah mengisi survey pada periode tersebut.
            Pada tabel perbandingan antar periode, jumlah kriteria dapat berbeda — sehingga nilai total semua kriteria antar periode
            sebaiknya dibaca bersama konteks jumlah kriterianya.
        </p>
    </div>
</div>

{{-- MODAL SHARE (fallback untuk desktop / browser tanpa Web Share API) --}}
<div class="share-modal-overlay" id="shareModalOverlay" onclick="closeShareModal(event)">
    <div class="share-modal">
        <div class="share-modal-header">
            <h3><i class="fas fa-share-alt" style="color:#8b5cf6; margin-right:8px;"></i> Bagikan Survei</h3>
            <button class="share-modal-close" onclick="closeShareModal()">&times;</button>
        </div>
        <div class="share-modal-url" id="shareModalUrl">{{ url('/') }}</div>
        <div class="share-modal-grid">
            <a class="share-app-btn share-app-wa" id="shareWa" href="#" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a class="share-app-btn share-app-tg" id="shareTg" href="#" target="_blank" rel="noopener">
                <i class="fab fa-telegram-plane"></i> Telegram
            </a>
            <a class="share-app-btn share-app-email" id="shareEmail" href="#" target="_blank" rel="noopener">
                <i class="fas fa-envelope"></i> Email
            </a>
            <a class="share-app-btn share-app-x" id="shareX" href="#" target="_blank" rel="noopener">
                <i class="fab fa-x-twitter"></i> X / Twitter
            </a>
            <a class="share-app-btn share-app-fb" id="shareFb" href="#" target="_blank" rel="noopener">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <button class="share-app-btn share-app-copy-mod" onclick="copyFromModal()">
                <i class="fas fa-copy"></i> Salin Link
            </button>
        </div>
        <div class="share-modal-note">Atau salin link di atas secara manual</div>
    </div>
</div>

@endsection
 
@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
 
<script>
    document.addEventListener('DOMContentLoaded', function() {
 
        // ============================================================
        // FUNGSI WARNA BERDASARKAN INTERPRETASI
        // ============================================================
        function getColorByInterpretation(interpretation) {
            const colorMap = {
                'Excellent':       '#10b981',
                'Sangat Baik':     '#3b82f6',
                'Baik':            '#8b5cf6',
                'Cukup':           '#f59e0b',
                'Kurang':          '#f97316',
                'Perlu Perbaikan': '#ef4444',
                'Tidak Ada Data':  '#94a3b8'
            };
            return colorMap[interpretation] || '#94a3b8';
        }
 
        // ============================================================
        // GRAFIK BAR CHART PER KRITERIA
        // ============================================================
        @if($jumlahKriteriaAktif > 0 && isset($criteriaChartData))
        const criteriaData = @json($criteriaChartData);
 
        const labels = criteriaData.map(item => item.criteria);
        const values = criteriaData.map(item => item.normalized_score);
        const colors = criteriaData.map(item => getColorByInterpretation(item.interpretation));
 
        const barHeight   = 52;
        const chartHeight = Math.max(300, criteriaData.length * barHeight + 60);
        const inner = document.getElementById('criteriaChartInner');
        if (inner) inner.style.height = chartHeight + 'px';
 
        const ctx = document.getElementById('criteriaChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Ternormalisasi (r)',
                    data: values,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 36,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const item = criteriaData[context.dataIndex];
                                return [
                                    'Nilai Ternormalisasi: ' + item.normalized_score.toFixed(3),
                                    'Nilai Terbobot: ' + item.weighted_score.toFixed(4),
                                    'Keterangan: ' + item.interpretation
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 1.0,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 11 }, callback: v => v.toFixed(2) }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 12, weight: '500' }, color: '#334155', padding: 8 }
                    }
                },
                layout: { padding: { right: 20, top: 10, bottom: 10 } }
            }
        });
        @endif
 
        // ============================================================
        // GRAFIK LINE CHART ANTAR PERIODE
        // ============================================================
        @if(isset($periodeChartData) && count($periodeChartData) > 0)
        const periodeData = @json($periodeChartData);
 
        function getPointColor(predikat) {
            const map = {
                'Excellent':       '#10b981',
                'Sangat Baik':     '#3b82f6',
                'Baik':            '#8b5cf6',
                'Cukup':           '#f59e0b',
                'Kurang':          '#f97316',
                'Perlu Perbaikan': '#ef4444',
            };
            return map[predikat] || '#94a3b8';
        }
 
        const periodeLabels = periodeData.map(p => p.period_name + '\n(' + p.year + ')');
        const periodeValues = periodeData.map(p => parseFloat(p.total_vi).toFixed(4));
        const pointColors   = periodeData.map(p => getPointColor(p.predikat));
        const pointSizes    = periodeData.map(p => p.is_active ? 10 : 6);
 
        const ctxPeriode = document.getElementById('periodeChart').getContext('2d');
        new Chart(ctxPeriode, {
            type: 'line',
            data: {
                labels: periodeLabels,
                datasets: [{
                    label: 'Total Nilai',
                    data: periodeValues,
                    borderColor: '#5a9b9e',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: { target: 'origin', above: 'rgba(90,155,158,0.08)' },
                    pointBackgroundColor: pointColors,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: pointSizes,
                    pointHoverRadius: 9,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.82)',
                        padding: 14,
                        callbacks: {
                            title: function(items) {
                                const p = periodeData[items[0].dataIndex];
                                return p.period_name + ' (' + p.year + ')';
                            },
                            label: function(context) {
                                const p = periodeData[context.dataIndex];
                                return [
                                    'Total Vi : ' + parseFloat(p.total_vi).toFixed(4),
                                    'Predikat : ' + p.predikat,
                                    'Kriteria : ' + p.jumlah_kriteria,
                                    'Responden: ' + p.responden,
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 11 }, color: '#334155' }
                    },
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 11 }, callback: v => parseFloat(v).toFixed(3) }
                    }
                }
            }
        });
        @endif
 
    }); // end DOMContentLoaded


    // ============================================================
    // DOWNLOAD GRAFIK KRITERIA SEBAGAI PNG
    // ============================================================
    function downloadCriteriaChart() {
        @if($jumlahKriteriaAktif > 0 && isset($criteriaChartData))
        const rawData = @json($criteriaChartData);

        const data = [...rawData].sort((a, b) => b.normalized_score - a.normalized_score);
        const values = data.map(d => d.normalized_score);

        const minVal = Math.min(...values);
        const maxVal = Math.max(...values);
        const axisMin = Math.floor(minVal * 10) / 10;
        const axisMax = Math.min(1.0, Math.ceil(maxVal * 10) / 10);

        const w = 720;
        const h = Math.max(260, data.length * 32 + 120);

        const offCanvas = document.createElement('canvas');
        offCanvas.width  = w;
        offCanvas.height = h;
        offCanvas.style.display = 'none';
        document.body.appendChild(offCanvas);

        const preCtx = offCanvas.getContext('2d');
        preCtx.fillStyle = '#ffffff';
        preCtx.fillRect(0, 0, w, h);

        const chart = new Chart(preCtx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.criteria),
                datasets: [{
                    data: values,
                    backgroundColor: '#2563EB',
                    borderColor: '#2563EB',
                    borderWidth: 0,
                    borderRadius: 2,
                    barThickness: 14,
                }]
            },
            options: {
                indexAxis: 'y',
                animation: false,
                responsive: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Grafik Nilai Per Kriteria',
                        font: { size: 14, weight: 'bold' },
                        color: '#1e293b',
                        padding: { top: 10, bottom: 14 }
                    }
                },
                scales: {
                    x: {
                        min: axisMin,
                        max: axisMax,
                        position: 'bottom',
                        grid: { color: 'rgba(0,0,0,0.07)' },
                        ticks: {
                            font: { size: 12 },
                            color: '#444',
                            padding: 6,
                            stepSize: 0.1,
                            callback: function(v) {
                                const rounded = Math.round(v * 10) / 10;
                                if (Math.abs(v - rounded) < 0.001) {
                                    return rounded.toFixed(1);
                                }
                                return null;
                            }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#334155', padding: 8 }
                    }
                },
                layout: { padding: { top: 0, right: 50, bottom: 20, left: 10 } }
            }
        });

        setTimeout(() => {
            const link = document.createElement('a');
            link.download = 'grafik_nilai_kriteria.png';
            link.href = offCanvas.toDataURL('image/png');
            link.click();

            chart.destroy();
            document.body.removeChild(offCanvas);
        }, 400);
        @else
        alert('Data kriteria belum tersedia.');
        @endif
    }


    // ============================================================
    // DOWNLOAD GRAFIK SKM HISTORIS SEBAGAI PNG
    // ============================================================
    function downloadSKMChart() {
        @if(isset($periodeChartData) && count($periodeChartData) > 0)
        let rawPeriode = @json($periodeChartData);

        if (rawPeriode.length > 8) rawPeriode = rawPeriode.slice(-8);

        const offCanvas = document.createElement('canvas');
        offCanvas.width  = 720;
        offCanvas.height = 400;
        offCanvas.style.display = 'none';
        document.body.appendChild(offCanvas);

        const preCtxS = offCanvas.getContext('2d');
        preCtxS.fillStyle = '#ffffff';
        preCtxS.fillRect(0, 0, 720, 400);

        const chart = new Chart(preCtxS, {
            type: 'line',
            data: {
                labels: rawPeriode.map(p => String(p.year)),
                datasets: [{
                    label: 'Total Nilai SKM',
                    data: rawPeriode.map(p => parseFloat(p.total_vi)),
                    borderColor: '#2563EB',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: { target: 'origin', above: 'rgba(37,99,235,0.08)' },
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                animation: false,
                responsive: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { font: { size: 12 }, color: '#334155' }
                    },
                    title: {
                        display: true,
                        text: 'Grafik Nilai SKM Historis',
                        font: { size: 14, weight: 'bold' },
                        color: '#1e293b',
                        padding: { top: 10, bottom: 8 }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 11 },
                            color: '#334155',
                            maxRotation: 30,
                            padding: 6,
                        }
                    },
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 11 },
                            color: '#555',
                            padding: 6,
                            callback: v => parseFloat(v).toFixed(3)
                        }
                    }
                },
                layout: { padding: { top: 0, right: 30, bottom: 20, left: 10 } }
            }
        });

        setTimeout(() => {
            const link = document.createElement('a');
            link.download = 'grafik_skm_historis.png';
            link.href = offCanvas.toDataURL('image/png');
            link.click();

            chart.destroy();
            document.body.removeChild(offCanvas);
        }, 400);
        @else
        alert('Data perbandingan periode belum tersedia.');
        @endif
    }


    // ============================================================
    // TOGGLE TABEL / GRAFIK
    // ============================================================
    function showView(view) {
        const tableEl  = document.getElementById('viewTable');
        const chartEl  = document.getElementById('viewChart');
        const btnTable = document.getElementById('btnTable');
        const btnChart = document.getElementById('btnChart');
 
        if (view === 'table') {
            tableEl.style.display = 'block';
            chartEl.style.display = 'none';
            btnTable.classList.add('active');
            btnChart.classList.remove('active');
        } else {
            tableEl.style.display = 'none';
            chartEl.style.display = 'block';
            btnTable.classList.remove('active');
            btnChart.classList.add('active');
        }
    }


    // ============================================================
    // SHARE LINK SURVEI
    // ============================================================
    (function () {
        const SURVEY_URL  = '{{ url('/') }}';
        const SHARE_TITLE = 'Survei Kepuasan Masyarakat — Diskominfo Lamongan';
        const SHARE_TEXT  = 'Bantu kami meningkatkan pelayanan! Isi survei kepuasan masyarakat Diskominfo Kabupaten Lamongan di link berikut:';

        /* Inject URL ke semua tombol share modal */
        document.addEventListener('DOMContentLoaded', function () {
            const enc = encodeURIComponent(SURVEY_URL);
            const msg = encodeURIComponent(SHARE_TEXT + ' ' + SURVEY_URL);

            const el = {
                wa    : document.getElementById('shareWa'),
                tg    : document.getElementById('shareTg'),
                email : document.getElementById('shareEmail'),
                x     : document.getElementById('shareX'),
                fb    : document.getElementById('shareFb'),
            };

            if (el.wa)    el.wa.href    = 'https://wa.me/?text=' + msg;
            if (el.tg)    el.tg.href    = 'https://t.me/share/url?url=' + enc + '&text=' + encodeURIComponent(SHARE_TEXT);
            if (el.email) el.email.href = 'mailto:?subject=' + encodeURIComponent(SHARE_TITLE) + '&body=' + msg;
            if (el.x)     el.x.href     = 'https://twitter.com/intent/tweet?text=' + msg;
            if (el.fb)    el.fb.href    = 'https://www.facebook.com/sharer/sharer.php?u=' + enc;
        });

        /* Tombol Bagikan — native dulu, fallback modal */
        window.openShare = function () {
            if (navigator.share) {
                navigator.share({ title: SHARE_TITLE, text: SHARE_TEXT, url: SURVEY_URL })
                    .catch(function () { /* user cancel */ });
            } else {
                document.getElementById('shareModalOverlay').classList.add('active');
            }
        };

        /* Tutup modal (klik overlay atau tombol × ) */
        window.closeShareModal = function (e) {
            if (!e || e.target === document.getElementById('shareModalOverlay')) {
                document.getElementById('shareModalOverlay').classList.remove('active');
            }
        };

        /* Salin dari card utama */
        window.copyShareLink = function () {
            navigator.clipboard.writeText(SURVEY_URL).then(function () {
                const btn = document.getElementById('btnCopyLink');
                btn.classList.add('copied');
                btn.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
                setTimeout(function () {
                    btn.classList.remove('copied');
                    btn.innerHTML = '<i class="fas fa-copy"></i> Salin';
                }, 2000);
            });
        };

        /* Salin dari dalam modal */
        window.copyFromModal = function () {
            navigator.clipboard.writeText(SURVEY_URL).then(function () {
                const box  = document.getElementById('shareModalUrl');
                const orig = box.textContent;
                box.textContent = '✅ Link berhasil disalin!';
                setTimeout(function () { box.textContent = orig; }, 2000);
            });
        };
    })();
</script>
@endpush