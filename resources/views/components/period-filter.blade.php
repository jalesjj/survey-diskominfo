{{-- Component Dropdown Filter Periode
@if($allPeriods && $allPeriods->count() > 0)
<div class="period-filter-component mb-3">
    <div class="card">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label mb-0 me-2">
                        <i class="fas fa-calendar-alt"></i> Filter Periode:
                    </label>
                    <select class="form-select form-select-sm d-inline-block w-auto" 
                            id="periodFilter" 
                            onchange="window.location.href = updatePeriodParam(this.value)">
                        <option value="">Semua Periode</option>
                        @foreach($allPeriods as $period)
                            <option value="{{ $period->id }}" 
                                    {{ $selectedPeriod && $selectedPeriod->id == $period->id ? 'selected' : '' }}>
                                {{ $period->period_name }}
                                @if($period->status == 'active') (Aktif) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-end">
                    @if($selectedPeriod)
                        <span class="badge bg-primary">
                            <i class="fas fa-filter"></i> Menampilkan: {{ $selectedPeriod->period_name }}
                        </span>
                        <a href="?" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i> Reset Filter
                        </a>
                    @else
                        <span class="text-muted">Menampilkan semua periode</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePeriodParam(periodId) {
    const url = new URL(window.location.href);
    
    if (periodId) {
        url.searchParams.set('period_id', periodId);
    } else {
        url.searchParams.delete('period_id');
    }
    
    return url.toString();
}
</script>

<style>
.period-filter-component .card {
    border: none;
    background: #f8f9fa;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.period-filter-component .form-select {
    min-width: 200px;
}
</style>
@endif --}}