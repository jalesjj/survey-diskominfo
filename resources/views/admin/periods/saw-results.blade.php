{{-- resources/views/admin/periods/saw-result.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hasil SAW - ' . $selectedPeriod->period_name)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.periods.index') }}">
                    <i class="fas fa-calendar-alt"></i> Kelola Periode
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $selectedPeriod->period_name }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-calculator"></i> Hasil SAW
            </h1>
            <div class="period-info-bar">
                <span class="badge badge-{{ $selectedPeriod->status }}">
                    {{ ucfirst($selectedPeriod->status) }}
                </span>
                <span>
                    <i class="fas fa-calendar"></i>
                    {{ $selectedPeriod->start_date->format('d M Y') }} - 
                    {{ $selectedPeriod->end_date->format('d M Y') }}
                </span>
                <span>
                    <i class="fas fa-users"></i>
                    {{ $totalResponses }} responden
                </span>
                <span>
                    <i class="fas fa-chart-line"></i>
                    Total Vi: {{ number_format($totalVi, 4) }}
                </span>
            </div>
        </div>
        <div class="header-actions">
            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar-alt"></i> {{ $selectedPeriod->period_name }}
                </button>
                <ul class="dropdown-menu">
                    @foreach($allPeriods as $period)
                        <li>
                            <a class="dropdown-item {{ $period->id == $selectedPeriod->id ? 'active' : '' }}" 
                               href="{{ route('admin.periods.saw', $period->id) }}">
                                {{ $period->period_name }}
                                @if($period->status == 'active')
                                    <span class="badge bg-success ms-2">Aktif</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a href="{{ route('admin.periods.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex gap-2 justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Navigasi Cepat</h6>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.periods.responses', $selectedPeriod->id) }}" 
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list"></i> Lihat Jawaban
                    </a>
                    <form action="{{ route('admin.periods.saw.recalculate', $selectedPeriod->id) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Hitung ulang hasil SAW untuk periode ini?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-sync"></i> Hitung Ulang
                        </button>
                    </form>
                    @if($allPeriods->count() > 1)
                        <a href="{{ route('admin.saw.compare') }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-chart-line"></i> Bandingkan Periode
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- SAW Results Table -->
    @if($hasSAW)
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-table"></i> Hasil Perhitungan SAW - {{ $selectedPeriod->period_name }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kriteria</th>
                            <th>Tipe</th>
                            <th class="text-center">Skor (x)</th>
                            <th class="text-center">Bobot (w)</th>
                            <th class="text-center">Normalisasi (r)</th>
                            <th class="text-center">Nilai Terbobot (V)</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criteriaResults as $result)
                        <tr>
                            <td>
                                <strong>{{ $result['criteria'] }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $result['questions_count'] }} pertanyaan, 
                                    {{ $result['total_responses'] }} jawaban
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-{{ $result['criteria_type'] == 'benefit' ? 'success' : 'info' }}">
                                    {{ ucfirst($result['criteria_type']) }}
                                </span>
                            </td>
                            <td class="text-center">{{ number_format($result['score'], 2) }}</td>
                            <td class="text-center">{{ number_format($result['weight_normalized'], 3) }}</td>
                            <td class="text-center">{{ number_format($result['normalized'], 3) }}</td>
                            <td class="text-center">
                                <strong>{{ number_format($result['weighted_score'], 4) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ getInterpretationBadgeColor($result['normalized']) }}">
                                    {{ $result['interpretation'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="5" class="text-end"><strong>TOTAL NILAI PREFERENSI (Vi)</strong></td>
                            <td class="text-center">
                                <strong class="text-primary" style="font-size: 1.2em;">
                                    {{ number_format($totalVi, 4) }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ getOverallBadgeColor($totalVi) }}">
                                    {{ getOverallInterpretation($totalVi) }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if($selectedPeriod->status == 'closed')
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i>
        <strong>Periode Ditutup:</strong> 
        Ini adalah data historis. Hasil perhitungan terakhir kali disimpan pada 
        {{ now()->format('d M Y H:i') }}.
    </div>
    @endif

    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Belum Ada Data SAW</strong>
        <p class="mb-0 mt-2">
            Periode {{ $selectedPeriod->period_name }} belum memiliki data hasil SAW.
            Pastikan sudah ada responden yang mengisi survey di periode ini.
        </p>
    </div>
    @endif
</div>

@php
function getInterpretationBadgeColor($normalized) {
    if ($normalized >= 0.9) return 'success';
    if ($normalized >= 0.8) return 'primary';
    if ($normalized >= 0.6) return 'info';
    if ($normalized >= 0.4) return 'warning';
    return 'danger';
}

function getOverallBadgeColor($totalVi) {
    if ($totalVi >= 0.9) return 'success';
    if ($totalVi >= 0.7) return 'primary';
    if ($totalVi >= 0.5) return 'warning';
    return 'danger';
}

function getOverallInterpretation($totalVi) {
    if ($totalVi >= 0.9) return 'Sangat Baik';
    if ($totalVi >= 0.7) return 'Baik';
    if ($totalVi >= 0.5) return 'Cukup';
    return 'Perlu Perbaikan';
}
@endphp

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.page-title {
    margin: 0 0 10px 0;
    font-size: 1.75rem;
}

.period-info-bar {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    font-size: 0.9rem;
}

.period-info-bar span {
    padding: 5px 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.period-info-bar .badge {
    padding: 5px 10px;
}

.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.badge-active {
    background: #28a745;
}

.badge-closed {
    background: #6c757d;
}

.badge-draft {
    background: #ffc107;
    color: #000;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 15px;
}
</style>
@endsection