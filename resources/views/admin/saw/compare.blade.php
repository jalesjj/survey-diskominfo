{{-- resources/views/admin/saw/compare.blade.php --}}

@extends('layouts.admin')

@section('active-periods', 'active')

@section('title', 'Perbandingan Antar Periode')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i> Perbandingan Hasil SAW Antar Periode
        </h1>
        <a href="{{ route('admin.periods.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        <i class="fas fa-info-circle"></i> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        @foreach($comparisonData as $data)
        <div class="col-md-3">
            <div class="card period-summary-card">
                <div class="card-body">
                    <h6 class="text-muted mb-1">{{ $data['period']->period_name }}</h6>
                    <h3 class="mb-0">{{ number_format($data['total_vi'], 4) }}</h3>
                    <small class="text-muted">Total Vi</small>
                    <div class="mt-2">
                        <span class="badge badge-{{ $data['period']->status }}">
                            {{ ucfirst($data['period']->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Chart -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-chart-area"></i> Tren Total Nilai Preferensi (Vi)</h5>
        </div>
        <div class="card-body">
            <canvas id="trendChart" height="80"></canvas>
        </div>
    </div>

    <!-- Comparison Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-table"></i> Perbandingan Detail per Kriteria</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle">Kriteria</th>
                            @foreach($periods as $period)
                                <th colspan="2" class="text-center">{{ $period->period_name }}</th>
                            @endforeach
                            <th rowspan="2" class="text-center align-middle">Trend</th>
                        </tr>
                        <tr>
                            @foreach($periods as $period)
                                <th class="text-center">Vi</th>
                                <th>Keterangan</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allCriteria as $criteriaName)
                        <tr>
                            <td><strong>{{ $criteriaName }}</strong></td>
                            
                            @php $scores = []; @endphp
                            
                            @foreach($periods as $period)
                                @php
                                    $result = $comparisonData[$loop->index]['results']
                                                ->where('criteria_name', $criteriaName)
                                                ->first();
                                    $score = $result ? $result->weighted_score : 0;
                                    $normalized = $result ? $result->normalized_score : 0;
                                    $scores[] = $score;
                                @endphp
                                <td class="text-center">
                                    {{ $result ? number_format($score, 4) : '-' }}
                                </td>
                                <td>
                                    @if($result)
                                        <span class="badge bg-{{ getInterpretationColor($normalized) }}">
                                            {{ getInterpretation($normalized) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endforeach
                            
                            <td class="text-center">
                                @php
                                    $firstScore = reset($scores);
                                    $lastScore = end($scores);
                                    $diff = $lastScore - $firstScore;
                                @endphp
                                
                                @if($diff > 0)
                                    <i class="fas fa-arrow-up text-success"></i>
                                    <span class="text-success">+{{ number_format($diff, 4) }}</span>
                                @elseif($diff < 0)
                                    <i class="fas fa-arrow-down text-danger"></i>
                                    <span class="text-danger">{{ number_format($diff, 4) }}</span>
                                @else
                                    <i class="fas fa-minus text-muted"></i>
                                    <span class="text-muted">Stabil</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        
                        <!-- Total Row -->
                        <tr class="table-secondary fw-bold">
                            <td>TOTAL Vi</td>
                            @foreach($comparisonData as $data)
                                <td colspan="2" class="text-center">
                                    {{ number_format($data['total_vi'], 4) }}
                                </td>
                            @endforeach
                            <td class="text-center">
                                @php
                                    $firstTotal = $comparisonData[0]['total_vi'];
                                    $lastTotal = end($comparisonData)['total_vi'];
                                    $totalDiff = $lastTotal - $firstTotal;
                                @endphp
                                
                                @if($totalDiff > 0)
                                    <i class="fas fa-arrow-up text-success"></i>
                                    <span class="text-success">+{{ number_format($totalDiff, 4) }}</span>
                                @elseif($totalDiff < 0)
                                    <i class="fas fa-arrow-down text-danger"></i>
                                    <span class="text-danger">{{ number_format($totalDiff, 4) }}</span>
                                @else
                                    <span class="text-muted">Stabil</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@php
function getInterpretation($normalized) {
    if ($normalized >= 0.9) return 'Sangat Baik';
    if ($normalized >= 0.8) return 'Baik';
    if ($normalized >= 0.6) return 'Cukup';
    if ($normalized >= 0.4) return 'Kurang';
    return 'Sangat Kurang';
}

function getInterpretationColor($normalized) {
    if ($normalized >= 0.9) return 'success';
    if ($normalized >= 0.8) return 'primary';
    if ($normalized >= 0.6) return 'info';
    if ($normalized >= 0.4) return 'warning';
    return 'danger';
}
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: [@foreach($periods as $period) '{{ $period->period_name }}', @endforeach],
        datasets: [{
            label: 'Total Nilai Preferensi (Vi)',
            data: [@foreach($comparisonData as $data) {{ $data['total_vi'] }}, @endforeach],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Total Vi: ' + context.parsed.y.toFixed(4);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 1,
                ticks: {
                    callback: function(value) {
                        return value.toFixed(2);
                    }
                }
            }
        }
    }
});
</script>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-title {
    margin: 0;
    font-size: 1.75rem;
}

.period-summary-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.period-summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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
</style>
@endsection