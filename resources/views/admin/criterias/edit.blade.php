{{-- resources/views/admin/criterias/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Kriteria SAW - Admin')
@section('active-criterias', 'active')
@section('page-title', 'Edit Kriteria')
@section('page-subtitle', 'Perbarui data kriteria SAW')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.criterias.index') }}">Kriteria SAW</a>
    <span class="breadcrumb-separator">></span>
    <span>Edit</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        max-width: 600px;
        margin: 0 auto;
        overflow: hidden;
    }
    .form-header {
        background: linear-gradient(135deg, #f0a500, #d9940a);
        color: white;
        padding: 25px 30px;
    }
    .form-header h2 { font-size: 18px; font-weight: 600; }
    .form-header p  { font-size: 13px; opacity: 0.9; margin-top: 5px; }
    .form-body { padding: 30px; }
    .form-group { margin-bottom: 22px; }
    .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
    .form-input, .form-select {
        width: 100%; padding: 12px 15px;
        border: 2px solid #e9ecef; border-radius: 8px;
        font-size: 15px; transition: border-color 0.2s;
    }
    .form-input:focus, .form-select:focus { outline: none; border-color: #5a9b9e; }
    .form-help { font-size: 13px; color: #888; margin-top: 5px; }
    .error-message { color: #dc3545; font-size: 13px; margin-top: 4px; }
    .info-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 13px;
        color: #856404;
        margin-bottom: 22px;
    }
    .info-box i { margin-right: 6px; }
    .form-buttons { display: flex; gap: 12px; justify-content: flex-end; margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee; }
    .btn { padding: 10px 22px; border-radius: 8px; font-weight: 500; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; transition: all 0.2s; }
    .btn-primary { background: #5a9b9e; color: white; }
    .btn-primary:hover { background: #4a8b8e; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn-secondary:hover { background: #5a6268; }

    /* Locked state */
    .locked-banner {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        background: #fff8e1;
        border: 1px solid #f0a500;
        border-left: 4px solid #f0a500;
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 22px;
        font-size: 14px;
        color: #7a5c00;
    }
    .locked-banner i {
        font-size: 22px;
        color: #f0a500;
        margin-top: 1px;
        flex-shrink: 0;
    }
    .locked-banner strong { display: block; font-size: 15px; margin-bottom: 4px; color: #5a4000; }

    .field-readonly {
        background: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
    }
    .field-group { margin-bottom: 20px; }
    .field-value {
        padding: 12px 15px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 15px;
        color: #495057;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .badge-benefit { background: #d4edda; color: #155724; }
    .badge-cost    { background: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <h2><i class="fas fa-{{ $activePeriod ? 'lock' : 'edit' }}"></i> Edit Kriteria</h2>
        <p>Digunakan oleh {{ $criteria->questions_count }} pertanyaan</p>
    </div>
    <div class="form-body">

        @if($activePeriod)
            {{-- MODE TERKUNCI: periode aktif --}}
            <div class="locked-banner">
                <i class="fas fa-lock"></i>
                <div>
                    <strong>Kriteria terkunci selama periode aktif</strong>
                    Periode <strong>{{ $activePeriod->period_name }}</strong> sedang berjalan.
                    Kriteria tidak dapat diubah untuk menjaga konsistensi normalisasi bobot SAW di tengah periode.
                    Tutup periode di <a href="{{ route('admin.periods.index') }}" style="color:#5a4000;font-weight:600;">Kelola Periode</a> untuk membuka kunci.
                </div>
            </div>

            {{-- Tampilkan data read-only saja --}}
            <div class="field-group">
                <label class="form-label">Nama Kriteria</label>
                <div class="field-value">{{ $criteria->criteria_name }}</div>
            </div>

            <div class="field-group">
                <label class="form-label">Bobot</label>
                <div class="field-value">{{ $criteria->criteria_weight }}</div>
            </div>

            <div class="field-group">
                <label class="form-label">Tipe Kriteria</label>
                <div class="field-value">
                    <span class="badge badge-{{ $criteria->criteria_type }}">
                        {{ $criteria->criteria_type === 'benefit' ? 'Benefit — Semakin tinggi semakin baik' : 'Cost — Semakin rendah semakin baik' }}
                    </span>
                </div>
            </div>

            <div class="form-buttons" style="border-top:1px solid #eee; margin-top:25px; padding-top:20px;">
                <a href="{{ route('admin.criterias.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

        @else
            {{-- MODE NORMAL: tidak ada periode aktif --}}
            @if($criteria->questions_count > 0)
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                Kriteria ini digunakan oleh <strong>{{ $criteria->questions_count }} pertanyaan</strong>.
                Perubahan bobot dan tipe akan otomatis berlaku untuk semua pertanyaan tersebut.
            </div>
            @endif

            <form action="{{ route('admin.criterias.update', $criteria->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="criteria_name">Nama Kriteria *</label>
                    <input type="text" id="criteria_name" name="criteria_name" class="form-input"
                           placeholder="Contoh: Afektif, Kognitif, Psikomotorik"
                           value="{{ old('criteria_name', $criteria->criteria_name) }}" required>
                    @error('criteria_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="criteria_weight">Bobot *</label>
                    <input type="number" id="criteria_weight" name="criteria_weight" class="form-input"
                           min="0.1" max="10" step="0.1" placeholder="Contoh: 0.3"
                           value="{{ old('criteria_weight', $criteria->criteria_weight) }}" required>
                    <p class="form-help">Bobot akan dinormalisasi otomatis oleh sistem saat perhitungan SAW.</p>
                    @error('criteria_weight')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="criteria_type">Tipe Kriteria *</label>
                    <select id="criteria_type" name="criteria_type" class="form-select" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="benefit" {{ old('criteria_type', $criteria->criteria_type) == 'benefit' ? 'selected' : '' }}>
                            Benefit — Semakin tinggi semakin baik
                        </option>
                        <option value="cost" {{ old('criteria_type', $criteria->criteria_type) == 'cost' ? 'selected' : '' }}>
                            Cost — Semakin rendah semakin baik
                        </option>
                    </select>
                    @error('criteria_type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-buttons">
                    <a href="{{ route('admin.criterias.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Kriteria
                    </button>
                </div>
            </form>
        @endif

    </div>
</div>
@endsection