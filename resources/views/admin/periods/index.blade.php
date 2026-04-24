{{-- resources/views/admin/periods/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kelola Periode Survey')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt"></i> Kelola Periode Survey
        </h1>
        <a href="{{ route('admin.periods.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Buat Periode Baru
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @php
        $activePeriod = $periods->where('status', 'active')->first();
    @endphp

    @if($activePeriod)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Periode Aktif:</strong> {{ $activePeriod->period_name }}
        ({{ $activePeriod->start_date->format('d M Y') }} s/d {{ $activePeriod->end_date->format('d M Y') }})
        <br>
        Responden yang mengisi sekarang akan masuk ke periode ini.
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Tidak ada periode aktif!</strong> 
        Silakan aktifkan periode atau buat periode baru.
    </div>
    @endif

    <div class="periods-grid">
        @forelse($periods as $period)
        <div class="period-card status-{{ $period->status }}">
            <div class="period-header">
                <h3>{{ $period->period_name }}</h3>
                <span class="badge badge-{{ $period->status }}">
                    @if($period->status == 'active')
                        <i class="fas fa-play-circle"></i> Aktif
                    @elseif($period->status == 'closed')
                        <i class="fas fa-lock"></i> Ditutup
                    @else
                        <i class="fas fa-clock"></i> Draft
                    @endif
                </span>
            </div>
            
            <div class="period-stats">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <div class="stat-content">
                        <span class="stat-value">{{ $period->total_respondents }}</span>
                        <span class="stat-label">Responden</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <i class="fas fa-calendar"></i>
                    <div class="stat-content">
                        <span class="stat-value">{{ $period->year }}</span>
                        <span class="stat-label">Tahun</span>
                    </div>
                </div>
                
                <div class="stat-item">
                    <i class="fas fa-chart-line"></i>
                    <div class="stat-content">
                        @if($period->sawResults->isNotEmpty())
                            <span class="stat-value">{{ number_format($period->sawResults->sum('weighted_score'), 4) }}</span>
                            <span class="stat-label">Total Vi</span>
                        @else
                            <span class="stat-value">-</span>
                            <span class="stat-label">Belum dihitung</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="period-dates">
                <i class="fas fa-calendar-check"></i>
                {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}
            </div>
            
            <div class="period-actions">
                <a href="{{ route('admin.periods.responses', $period->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-list"></i> Jawaban
                </a>
                
                <a href="{{ route('admin.periods.saw', $period->id) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-calculator"></i> Hasil SAW
                </a>
                
                @if($period->status == 'draft')
                    <form action="{{ route('admin.periods.activate', $period->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" 
                                onclick="return confirm('Aktifkan periode {{ $period->period_name }}?')">
                            <i class="fas fa-play"></i> Aktifkan
                        </button>
                    </form>
                @elseif($period->status == 'active')
                    <form action="{{ route('admin.periods.close', $period->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning" 
                                onclick="return confirm('Tutup periode {{ $period->period_name }}? Hasil SAW akan disimpan.')">
                            <i class="fas fa-lock"></i> Tutup
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>Belum Ada Periode</h3>
            <p>Silakan buat periode survey pertama Anda.</p>
            <a href="{{ route('admin.periods.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Periode Baru
            </a>
        </div>
        @endforelse
    </div>

    @if($periods->count() > 1)
    <div class="mt-4 text-center">
        <a href="{{ route('admin.saw.compare') }}" class="btn btn-lg btn-outline-primary">
            <i class="fas fa-chart-line"></i> Bandingkan Antar Periode
        </a>
    </div>
    @endif
</div>

<style>
.periods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.period-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 4px solid #ddd;
    transition: transform 0.2s, box-shadow 0.2s;
}

.period-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.period-card.status-active {
    border-left-color: #28a745;
}

.period-card.status-closed {
    border-left-color: #6c757d;
}

.period-card.status-draft {
    border-left-color: #ffc107;
}

.period-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.period-header h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #333;
}

.badge-active {
    background: #28a745;
    color: white;
}

.badge-closed {
    background: #6c757d;
    color: white;
}

.badge-draft {
    background: #ffc107;
    color: #000;
}

.period-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stat-item i {
    font-size: 1.5rem;
    color: #007bff;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
}

.stat-label {
    font-size: 0.75rem;
    color: #666;
}

.period-dates {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #666;
}

.period-dates i {
    margin-right: 5px;
    color: #007bff;
}

.period-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.period-actions .btn {
    flex: 1;
    min-width: 100px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h3 {
    margin-bottom: 10px;
}

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
</style>
@endsection