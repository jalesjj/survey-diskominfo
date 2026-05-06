{{-- resources/views/admin/periods/responses.blade.php
@extends('layouts.admin')

@section('active-periods', 'active')

@section('title', 'Jawaban Responden - ' . $period->period_name)

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
            <li class="breadcrumb-item active">{{ $period->period_name }} - Jawaban</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-list"></i> Jawaban Responden
            </h1>
            <div class="period-info-bar">
                <span class="badge badge-{{ $period->status }}">
                    {{ ucfirst($period->status) }}
                </span>
                <span>
                    <i class="fas fa-calendar"></i>
                    {{ $period->start_date->format('d M Y') }} - 
                    {{ $period->end_date->format('d M Y') }}
                </span>
                <span>
                    <i class="fas fa-users"></i>
                    {{ $totalResponses }} responden
                </span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.periods.saw', $period->id) }}" class="btn btn-info">
                <i class="fas fa-calculator"></i> Hasil SAW
            </a>
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

    <!-- Filter & Search -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="searchInput" 
                               placeholder="Cari berdasarkan ID survey...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">
                        Total: <strong>{{ $totalResponses }}</strong> responden di periode ini
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Responses -->
    @if($responses->count() > 0)
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-table"></i> Daftar Responden - {{ $period->period_name }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="responsesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="80">No</th>
                            <th>Survey ID</th>
                            <th>IP Address</th>
                            <th>Tanggal Isi</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $index => $response)
                        <tr>
                            <td>{{ $responses->firstItem() + $index }}</td>
                            <td>
                                <strong>#{{ $response->survey_id }}</strong>
                            </td>
                            <td>
                                @if($response->survey)
                                    <small class="text-muted">
                                        {{ $response->survey->ip_address ?? 'N/A' }}
                                    </small>
                                @else
                                    <small class="text-muted">N/A</small>
                                @endif
                            </td>
                            <td>
                                {{ $response->created_at->format('d M Y H:i') }}
                                <br>
                                <small class="text-muted">
                                    {{ $response->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.periods.response-detail', [$period->id, $response->survey_id]) }}" 
                                   class="btn btn-sm btn-primary"
                                   title="Lihat Detail Jawaban">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $responses->links() }}
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Belum Ada Responden</strong>
        <p class="mb-0 mt-2">
            Belum ada responden yang mengisi survey pada periode {{ $period->period_name }}.
        </p>
    </div>
    @endif
</div>

<script>
// Search functionality
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#responsesTable tbody tr');
    
    tableRows.forEach(row => {
        const surveyId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const ipAddress = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        
        if (surveyId.includes(searchValue) || ipAddress.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

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
@endsection --}}