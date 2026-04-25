{{-- resources/views/admin/periods/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Buat Periode Baru')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-plus-circle"></i> Buat Periode Baru
        </h1>
        <a href="{{ route('admin.periods.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle"></i> Terdapat Kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Informasi Periode</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.periods.store') }}" method="POST" id="createPeriodForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="period_name" class="form-label">
                                Nama Periode <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('period_name') is-invalid @enderror" 
                                   id="period_name" 
                                   name="period_name" 
                                   value="{{ old('period_name') }}"
                                   placeholder="Contoh: Tahun 2025, Semester 1 2025"
                                   required>
                            @error('period_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Nama yang akan ditampilkan untuk periode ini
                            </small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="year" class="form-label">
                                Tahun <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('year') is-invalid @enderror" 
                                   id="year" 
                                   name="year" 
                                   value="{{ old('year', date('Y')) }}"
                                   min="2020" 
                                   max="2100"
                                   required>
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Tahun periode survey
                            </small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date') }}"
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}"
                                   required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">
                        Deskripsi <span class="text-muted">(Opsional)</span>
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3"
                              placeholder="Deskripsi atau catatan tambahan untuk periode ini">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Catatan:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Periode akan dibuat dengan status <strong>Draft</strong></li>
                        <li>Anda perlu mengaktifkan periode setelah dibuat agar responden bisa mengisi</li>
                        <li>Hanya 1 periode yang bisa aktif dalam satu waktu</li>
                    </ul>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.periods.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Periode
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate period name
document.getElementById('year').addEventListener('change', function() {
    const year = this.value;
    const periodNameInput = document.getElementById('period_name');
    
    // Only auto-fill if field is empty
    if (!periodNameInput.value || periodNameInput.value.startsWith('Tahun ')) {
        periodNameInput.value = 'Tahun ' + year;
    }
});

// Validate date range
document.getElementById('createPeriodForm').addEventListener('submit', function(e) {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    
    if (endDate <= startDate) {
        e.preventDefault();
        alert('Tanggal selesai harus lebih besar dari tanggal mulai!');
        return false;
    }
});
</script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    border-bottom: none;
}

.form-label {
    font-weight: 600;
    color: #333;
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