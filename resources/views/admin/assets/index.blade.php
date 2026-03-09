{{-- resources/views/admin/assets/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen Assets - Admin Survei')
@section('active-assets', 'active')
@section('page-title', 'Manajemen Assets')
@section('page-subtitle', 'Kelola logo dan asset gambar sistem')

@section('header-actions')
<div class="header-actions">
    <a href="{{ route('admin.assets.create') }}" class="btn btn-primary">
        <span class="btn-icon">📤</span>
        Upload Asset
    </a>
    <span class="admin-welcome">{{ $currentAdmin->name }} ({{ ucfirst(str_replace('_', ' ', $currentAdmin->role)) }})</span>
</div>
@endsection

@push('styles')
<style>
    .page-container {
        padding: 0;
    }

    /* Success/Error Messages */
    .success-message, .error-message {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideDown 0.3s ease;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f1aeb5;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Statistics Cards */
    .assets-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        border-left: 4px solid;
        position: relative;
        overflow: hidden;
    }

    .stat-card.total {
        border-left-color: #5a9b9e;
    }

    .stat-card.active {
        border-left-color: #28a745;
    }

    .stat-card.inactive {
        border-left-color: #6c757d;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 100%);
        border-radius: 0 0 0 60px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        line-height: 1;
    }

    .stat-label {
        color: #7f8c8d;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .stat-icon {
        font-size: 12px;
        opacity: 0.7;
    }

    /* Upload Section */
    .upload-section {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(90, 155, 158, 0.3);
    }

    .upload-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(180deg); }
    }

    .upload-content {
        position: relative;
        z-index: 2;
    }

    .upload-icon {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
    }

    .upload-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .upload-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .upload-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-upload {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 12px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        backdrop-filter: blur(10px);
    }

    .btn-upload:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }

    .btn-upload.primary {
        background: rgba(255, 255, 255, 0.95);
        color: #5a9b9e;
        border-color: transparent;
    }

    .btn-upload.primary:hover {
        background: white;
        color: #4a8b8e;
    }

    /* Assets Grid */
    .assets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .asset-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .asset-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .asset-card.active {
        border: 2px solid #5a9b9e;
        box-shadow: 0 4px 20px rgba(90, 155, 158, 0.3);
    }

    .asset-preview {
        height: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .asset-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .asset-status {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background: #28a745;
        color: white;
    }

    .status-inactive {
        background: #6c757d;
        color: white;
    }

    .asset-info {
        padding: 20px;
    }

    .asset-type {
        background: #e8f6f3;
        color: #5a9b9e;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 10px;
    }

    .asset-name {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        word-wrap: break-word;
    }

    .asset-meta {
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 15px;
    }

    .asset-actions {
        display: flex;
        gap: 8px;
    }

    .asset-actions form {
        flex: 1;
    }

    .btn {
        width: 100%;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-1px);
    }

    /* Empty State */
    .empty-state {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 60px 40px;
        text-align: center;
        margin: 40px 0;
        grid-column: 1 / -1;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
        border-radius: 50%;
        margin: 0 auto 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #adb5bd;
    }

    .empty-state h3 {
        color: #495057;
        margin-bottom: 15px;
        font-size: 22px;
        font-weight: 600;
    }

    .empty-state p {
        color: #6c757d;
        line-height: 1.6;
        max-width: 400px;
        margin: 0 auto 25px;
    }

    .empty-state .btn {
        display: inline-flex;
        width: auto;
        padding: 12px 25px;
        background: #5a9b9e;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        text-transform: none;
    }

    .empty-state .btn:hover {
        background: #4a8b8e;
        text-decoration: none;
        transform: translateY(-2px);
    }

    /* Info Banner */
    .info-banner {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-top: 25px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(90, 155, 158, 0.3);
    }

    .info-banner h4 {
        margin-bottom: 10px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .info-banner p {
        margin: 0;
        line-height: 1.6;
        opacity: 0.95;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .assets-grid {
            grid-template-columns: 1fr;
        }
        
        .asset-actions {
            flex-direction: column;
            gap: 8px;
        }
        
        .btn {
            flex: none;
        }

        .upload-actions {
            flex-direction: column;
        }

        .assets-stats {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }
</style>
@endpush

@section('content')
<div class="page-container">
    @if(session('success'))
    <div class="success-message">
        <span>✅</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="error-message">
        <span>❌</span>
        {{ session('error') }}
    </div>
    @endif

    <!-- Upload Section - Always Visible -->
    <div class="upload-section">
        <div class="upload-content">
            <span class="upload-icon">📤</span>
            <div class="upload-title">Upload Logo Baru</div>
            <div class="upload-subtitle">
                Tambahkan lebih banyak logo untuk sistem survei. 
                @if($assets->count() > 0)
                    Saat ini Anda memiliki {{ $assets->count() }} asset ({{ $assets->where('is_active', true)->count() }} aktif).
                @else
                    Upload asset pertama untuk memulai.
                @endif
            </div>
            <div class="upload-actions">
                <a href="{{ route('admin.assets.create') }}" class="btn-upload primary">
                    📷 Upload Logo
                </a>
                {{-- <a href="{{ route('admin.assets.create') }}" class="btn-upload">
                    🎨 Upload Banner
                </a>
                <a href="{{ route('admin.assets.create') }}" class="btn-upload">
                    🔰 Upload Icon
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="assets-stats">
        <div class="stat-card total">
            <div class="stat-number">{{ $assets->count() }}</div>
            <div class="stat-label">Total Assets</div>
            <div class="stat-icon">📊</div>
        </div>
        <div class="stat-card active">
            <div class="stat-number">{{ $assets->where('is_active', true)->count() }}</div>
            <div class="stat-label">Assets Aktif</div>
            <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card inactive">
            <div class="stat-number">{{ $assets->where('is_active', false)->count() }}</div>
            <div class="stat-label">Assets Non-aktif</div>
            <div class="stat-icon">⏸️</div>
        </div>
    </div>

    <!-- Assets Grid -->
    <div class="assets-grid">
        @forelse($assets as $asset)
        <div class="asset-card {{ $asset->is_active ? 'active' : '' }}">
            <div class="asset-preview">
                <img src="{{ $asset->file_url }}" alt="{{ $asset->original_name }}" loading="lazy">
                <div class="asset-status {{ $asset->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $asset->is_active ? 'AKTIF' : 'NONAKTIF' }}
                </div>
            </div>
            
            <div class="asset-info">
                <div class="asset-type">{{ $availableTypes[$asset->type] ?? $asset->type }}</div>
                <div class="asset-name">{{ $asset->original_name }}</div>
                
                <div class="asset-meta">
                    📅 {{ $asset->created_at->format('d/m/Y H:i') }}
                </div>
                
                <div class="asset-actions">
                    @if(!$asset->is_active)
                    <form method="POST" action="{{ route('admin.assets.toggle', $asset->id) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">
                            Aktifkan
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.assets.toggle', $asset->id) }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-warning">
                            Nonaktifkan
                        </button>
                    </form>
                    @endif
                    
                    <form method="POST" action="{{ route('admin.assets.destroy', $asset->id) }}" 
                          onsubmit="return confirm('❗ Yakin ingin menghapus asset {{ $asset->original_name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">📁</div>
            <h3>Belum ada asset yang diupload</h3>
            <p>Upload logo atau asset gambar pertama untuk sistem survei. Logo yang diupload akan otomatis tampil di header halaman survei.</p>
            <a href="{{ route('admin.assets.create') }}" class="btn">
                📤 Upload Asset Pertama
            </a>
        </div>
        @endforelse
    </div>

    @if($assets->count() > 0)
    <div class="info-banner">
        <h4>ℹ️ Informasi Sistem Logo</h4>
        <p>Logo yang aktif akan tampil di header halaman survei dengan ukuran yang disesuaikan otomatis. Anda dapat mengelola multiple logo sekaligus dan mengatur status aktif/non-aktif untuk setiap logo.</p>
    </div>
    @endif
</div>

<script>
// Auto hide messages
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.success-message, .error-message');
    
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                message.style.display = 'none';
            }, 300);
        }, 5000);
    });
});

// Console info
console.log('📊 Asset Management System v2.0');
console.log('📈 Total assets: {{ $assets->count() }}');
console.log('✅ Active assets: {{ $assets->where("is_active", true)->count() }}');
console.log('🚀 Multiple upload ready');
</script>
@endsection