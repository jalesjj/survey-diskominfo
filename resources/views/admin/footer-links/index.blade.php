{{-- resources/views/admin/footer-links/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Footer Links - Admin')
@section('active-footer-links', 'active')
@section('page-title', 'Manajemen Footer Links')
@section('page-subtitle', 'Kelola link yang ditampilkan di footer website')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-separator">></span>
    <span>Footer Links</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .page-actions .btn {
        padding: 12px 20px;
        background: #5a9b9e;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .page-actions .btn:hover {
        background: #4a8b8e;
        transform: translateY(-2px);
    }

    .links-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .links-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .section-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .section-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 14px;
    }

    .links-list {
        padding: 0;
    }

    .link-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 25px;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.3s ease;
    }

    .link-item:last-child {
        border-bottom: none;
    }

    .link-item:hover {
        background-color: #f8f9fa;
    }

    .link-info {
        flex: 1;
    }

    .link-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
        font-size: 16px;
    }

    .link-url {
        color: #7f8c8d;
        font-size: 14px;
        word-break: break-all;
    }

    .link-url a {
        color: #5a9b9e;
        text-decoration: none;
    }

    .link-url a:hover {
        text-decoration: underline;
    }

    .link-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .action-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-toggle {
        background: #ffc107;
        color: #212529;
    }

    .btn-toggle:hover {
        background: #e0a800;
        transform: translateY(-1px);
    }

    .btn-edit {
        background: #17a2b8;
        color: white;
    }

    .btn-edit:hover {
        background: #138496;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background: #c82333;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #5a6c7d;
    }

    .empty-state p {
        font-size: 16px;
        margin-bottom: 25px;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }

    @media (max-width: 768px) {
        .links-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .page-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .link-item {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .link-actions {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2 style="margin: 0; color: #2c3e50;">Manajemen Footer Links</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d;">Kelola link Layanan dan Informasi di footer website</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.footer-links.create') }}" class="btn">
            <i class="fas fa-plus"></i> Tambah Link
        </a>
    </div>
</div>

<div class="links-container">
    <!-- Section Layanan -->
    <div class="links-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-cogs"></i> Layanan
            </div>
            <div class="section-count">{{ $layananLinks->count() }} link</div>
        </div>
        
        <div class="links-list">
            @forelse($layananLinks as $link)
            <div class="link-item">
                <div class="link-info">
                    <div class="link-title">{{ $link->title }}</div>
                    <div class="link-url">
                        <a href="{{ $link->url }}" target="_blank">{{ $link->url }}</a>
                    </div>
                </div>
                <div class="link-actions">
                    <span class="status-badge {{ $link->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $link->is_active ? 'Aktif' : 'Non-aktif' }}
                    </span>
                    
                    <form method="POST" action="{{ route('admin.footer-links.toggle', $link->id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="action-btn btn-toggle" title="{{ $link->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fas fa-{{ $link->is_active ? 'eye-slash' : 'eye' }}"></i>
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.footer-links.edit', $link->id) }}" class="action-btn btn-edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form method="POST" action="{{ route('admin.footer-links.destroy', $link->id) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus link ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn btn-delete" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-link"></i>
                <h3>Belum Ada Link Layanan</h3>
                <p>Mulai tambahkan link layanan untuk ditampilkan di footer</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Section Informasi -->
    <div class="links-section">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-info-circle"></i> Informasi
            </div>
            <div class="section-count">{{ $informasiLinks->count() }} link</div>
        </div>
        
        <div class="links-list">
            @forelse($informasiLinks as $link)
            <div class="link-item">
                <div class="link-info">
                    <div class="link-title">{{ $link->title }}</div>
                    <div class="link-url">
                        <a href="{{ $link->url }}" target="_blank">{{ $link->url }}</a>
                    </div>
                </div>
                <div class="link-actions">
                    <span class="status-badge {{ $link->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $link->is_active ? 'Aktif' : 'Non-aktif' }}
                    </span>
                    
                    <form method="POST" action="{{ route('admin.footer-links.toggle', $link->id) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="action-btn btn-toggle" title="{{ $link->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fas fa-{{ $link->is_active ? 'eye-slash' : 'eye' }}"></i>
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.footer-links.edit', $link->id) }}" class="action-btn btn-edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <form method="POST" action="{{ route('admin.footer-links.destroy', $link->id) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus link ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn btn-delete" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-link"></i>
                <h3>Belum Ada Link Informasi</h3>
                <p>Mulai tambahkan link informasi untuk ditampilkan di footer</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto hide success message
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }
    });
</script>
@endpush