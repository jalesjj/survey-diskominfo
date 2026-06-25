{{-- resources/views/admin/criterias/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Kriteria SAW - Admin')
@section('active-criterias', 'active')
@section('page-title', 'Manajemen Kriteria SAW')
@section('page-subtitle', 'Kelola kriteria untuk perhitungan Simple Additive Weighting')

@section('breadcrumb')
<div class="breadcrumb">
    <span>Kriteria SAW</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .page-actions { margin-bottom: 25px; }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .btn-primary { background: #5a9b9e; color: white; }
    .btn-primary:hover { background: #4a8b8e; color: white; }
    .btn-warning { background: #f0a500; color: white; }
    .btn-warning:hover { background: #d9940a; color: white; }
    .btn-danger { background: #e74c3c; color: white; }
    .btn-danger:hover { background: #c0392b; color: white; }
    .btn-sm { padding: 6px 14px; font-size: 13px; }

    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    table { width: 100%; border-collapse: collapse; }
    thead { background: linear-gradient(135deg, #5a9b9e, #4a8b8e); color: white; }
    th { padding: 14px 18px; text-align: left; font-weight: 600; font-size: 14px; }
    td { padding: 14px 18px; border-bottom: 1px solid #f0f0f0; font-size: 14px; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: #f8fffe; }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-benefit { background: #d4edda; color: #155724; }
    .badge-cost    { background: #f8d7da; color: #721c24; }

    .used-count {
        font-weight: 600;
        color: #5a9b9e;
    }
    .used-count.zero { color: #aaa; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #aaa;
    }
    .empty-state i { font-size: 48px; margin-bottom: 15px; display: block; }
</style>
@endpush

@section('content')
<div class="page-actions">
    <a href="{{ route('admin.criterias.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Kriteria
    </a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Kriteria</th>
                <th>Bobot</th>
                <th>Tipe</th>
                <th>Dipakai Pertanyaan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($criterias as $criteria)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $criteria->criteria_name }}</strong></td>
                <td>
                    @php
                        $weightLabels = [10 => 'Sangat Prioritas', 8 => 'Prioritas', 6 => 'Cukup Prioritas', 4 => 'Tidak Prioritas', 2 => 'Sangat Tidak Prioritas'];
                    @endphp
                    {{ $weightLabels[(int) $criteria->criteria_weight] ?? $criteria->criteria_weight }}
                </td>
                <td>
                    <span class="badge badge-{{ $criteria->criteria_type }}">
                        {{ $criteria->criteria_type === 'benefit' ? 'Benefit' : 'Cost' }}
                    </span>
                </td>
                <td>
                    <span class="used-count {{ $criteria->questions_count == 0 ? 'zero' : '' }}">
                        {{ $criteria->questions_count }} pertanyaan
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.criterias.edit', $criteria->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.criterias.destroy', $criteria->id) }}" method="POST" style="display:inline"
                          onsubmit="return confirmDelete('{{ $criteria->criteria_name }}', {{ $criteria->questions_count }})">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-layer-group"></i>
                        Belum ada kriteria. <a href="{{ route('admin.criterias.create') }}">Tambah kriteria pertama</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(name, usedCount) {
    if (usedCount > 0) {
        alert(`Kriteria "${name}" tidak bisa dihapus karena masih digunakan oleh ${usedCount} pertanyaan.\n\nPindahkan pertanyaan tersebut ke kriteria lain terlebih dahulu.`);
        return false;
    }
    return confirm(`Yakin ingin menghapus kriteria "${name}"?`);
}
</script>
@endpush