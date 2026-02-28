{{-- resources/views/admin/survey-results/index.blade.php - DAFTAR SURVEY
@extends('layouts.admin')

@section('title', 'Hasil Survey - Admin')
@section('active-results', 'active')
@section('page-title', 'Hasil Survey')
@section('page-subtitle', 'Daftar Survey dan Akses ke Halaman Output Nilai Akhir Kriteria SAW')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-separator">></span>
    <span>Hasil Survey</span>
</div>
@endsection

@push('styles')
<style>
    .surveys-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .surveys-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 25px 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .surveys-title {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    .surveys-body {
        padding: 30px;
    }

    .surveys-info {
        background: #f8f9fa;
        border-left: 4px solid #5a9b9e;
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 0 8px 8px 0;
    }

    .surveys-info h4 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .surveys-info p {
        color: #495057;
        margin: 0;
        line-height: 1.6;
        font-size: 14px;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #5a9b9e;
        text-align: center;
    }

    .stats-number {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .stats-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 600;
    }

    .surveys-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .surveys-table thead th {
        background: #2c3e50;
        color: white;
        padding: 15px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
    }

    .surveys-table thead th:first-child {
        border-radius: 8px 0 0 0;
    }

    .surveys-table thead th:last-child {
        border-radius: 0 8px 0 0;
        text-align: center;
    }

    .surveys-table tbody td {
        padding: 15px 12px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .surveys-table tbody tr:hover {
        background: #f8f9fa;
    }

    .survey-id {
        font-weight: 700;
        color: #2c3e50;
        font-size: 16px;
    }

    .survey-date {
        color: #495057;
        font-size: 14px;
    }

    .survey-ip {
        color: #6c757d;
        font-size: 13px;
        font-family: monospace;
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .survey-responses {
        text-align: center;
        font-weight: 600;
        color: #5a9b9e;
    }

    .saw-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .saw-status.active {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .saw-status.inactive {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .survey-actions {
        text-align: center;
    }

    .btn-view-result {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-view-result:hover {
        background: linear-gradient(135deg, #4a8b8e 0%, #3a7b7e 100%);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(90, 155, 158, 0.3);
    }

    .no-surveys {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .no-surveys i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .no-surveys h4 {
        color: #495057;
        margin-bottom: 10px;
    }

    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .surveys-body {
            padding: 20px;
        }

        .surveys-table {
            font-size: 12px;
        }

        .surveys-table th,
        .surveys-table td {
            padding: 10px 8px;
        }

        .stats-cards {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="surveys-container">
    <div class="surveys-header">
        <i class="fas fa-chart-bar"></i>
        <h1 class="surveys-title">Hasil Survey SAW</h1>
    </div>

    <div class="surveys-body">
        <div class="surveys-info">
            <h4><i class="fas fa-info-circle"></i> Tentang Output Nilai Akhir Kriteria SAW</h4>
            <p>
                Halaman ini menampilkan daftar semua survey yang telah diisi. Klik tombol "Lihat Hasil SAW" 
                untuk mengakses halaman output nilai akhir kriteria menggunakan metode Simple Additive Weighting (SAW). 
                <strong>Output berupa tabel per kriteria (bukan sub-kriteria)</strong> dengan perhitungan agregasi, 
                normalisasi, dan nilai preferensi sesuai rumus SAW.
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stats-number">{{ $surveys->total() }}</div>
                <div class="stats-label">Total Survey</div>
            </div>
            <div class="stat-card">
                <div class="stats-number">{{ $surveys->where('has_saw', true)->count() }}</div>
                <div class="stats-label">Survey dengan SAW</div>
            </div>
            <div class="stat-card">
                <div class="stats-number">{{ $surveys->sum('response_count') }}</div>
                <div class="stats-label">Total Responses</div>
            </div>
            <div class="stat-card">
                <div class="stats-number">{{ $surveys->where('response_count', '>', 0)->count() }}</div>
                <div class="stats-label">Survey Selesai</div>
            </div>
        </div>

        @if($surveys->count() > 0)
            <table class="surveys-table">
                <thead>
                    <tr>
                        <th>Survey</th>
                        <th>Tanggal</th>
                        <th>Responses</th>
                        <th>Status SAW</th>
                        <th>Alamat IP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveys as $survey)
                        <tr>
                            <td>
                                <div class="survey-id">Survey #{{ $survey->id }}</div>
                                <div class="survey-date">{{ $survey->created_at->format('d F Y, H:i') }} WIB</div>
                            </td>
                            <td>{{ $survey->created_at->format('d/m/Y') }}</td>
                            <td class="survey-responses">{{ $survey->response_count }}</td>
                            <td>
                                <span class="saw-status {{ $survey->has_saw ? 'active' : 'inactive' }}">
                                    {{ $survey->has_saw ? 'SAW Aktif' : 'SAW Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <span class="survey-ip">{{ $survey->ip_address ?? 'N/A' }}</span>
                            </td>
                            <td class="survey-actions">
                                <a href="{{ route('admin.survey-results.show', $survey->id) }}" 
                                   class="btn-view-result"
                                   title="Lihat Output Nilai Akhir Kriteria SAW #{{ $survey->id }}">
                                    <i class="fas fa-calculator"></i>
                                    Lihat Hasil SAW
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $surveys->links() }}
            </div>
        @else
            <div class="no-surveys">
                <i class="fas fa-inbox"></i>
                <h4>Belum Ada Survey</h4>
                <p>Belum ada survey yang tersedia untuk dilihat hasil SAW-nya. Survey akan muncul di sini setelah ada responden yang mengisi.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📋 Halaman Index Hasil Survey SAW dimuat:', {
            totalSurveys: {{ $surveys->total() }},
            surveysWithSAW: {{ $surveys->where('has_saw', true)->count() }},
            currentPage: {{ $surveys->currentPage() }}
        });

        // Add confirmation for viewing SAW results
        const viewButtons = document.querySelectorAll('.btn-view-result');
        viewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const surveyId = this.href.split('/').pop();
                console.log('🎯 Mengakses output nilai akhir kriteria SAW:', surveyId);
            });
        });
    });
</script>
@endpush --}}