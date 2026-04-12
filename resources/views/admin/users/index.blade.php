{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Manajemen User - Admin Survei')
@section('active-users', 'active')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola admin dan super admin sistem')

@push('styles')
<style>
    /* Header Actions di Content */
    .content-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-left {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    /* Statistics Cards */
    .users-stats {
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
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card.total {
        border-left-color: #5a9b9e;
    }

    .stat-card.super-admin {
        border-left-color: #e74c3c;
    }

    .stat-card.admin {
        border-left-color: #3498db;
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 14px;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 20px 25px;
    }

    .table-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .table-subtitle {
        font-size: 14px;
        opacity: 0.8;
    }

    .table-content {
        padding: 0;
    }

    .user-item {
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.3s ease;
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-item:hover {
        background-color: #f8f9fa;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .user-meta {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .user-username {
        font-size: 14px;
        color: #7f8c8d;
    }

    .user-role {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .role-super-admin {
        background: #e74c3c;
        color: white;
    }

    .role-admin {
        background: #3498db;
        color: white;
    }

    .user-last-login {
        font-size: 12px;
        color: #95a5a6;
    }

    .user-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .btn-warning {
        background: #f39c12;
        color: white;
    }

    .btn-warning:hover {
        background: #e67e22;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    .btn-primary {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        font-size: 15px;
        padding: 12px 24px;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4a8b8e 0%, #3a7b7e 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(90, 155, 158, 0.4);
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #7f8c8d;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .current-user {
        background: #f0f8f8;
        border-left: 4px solid #5a9b9e;
    }

    .current-user-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        background: #5a9b9e;
        color: white;
        margin-left: 8px;
    }

    .admin-info {
        font-size: 14px;
        color: #7f8c8d;
    }

    @media (max-width: 768px) {
        .user-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .user-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .user-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .content-header-custom {
            flex-direction: column;
            align-items: stretch;
        }

        .header-left {
            width: 100%;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<!-- Header dengan Tombol Tambah User -->
<div class="content-header-custom">
    <div class="header-left">
        <!-- TOMBOL INI PASTI MUNCUL UNTUK SUPER ADMIN -->
        @php
            $isSuperAdmin = false;
            
            // Cek dari $currentAdmin variable
            if(isset($currentAdmin) && $currentAdmin->role === 'super_admin') {
                $isSuperAdmin = true;
            }
            
            // Cek dari session sebagai backup
            if(session('admin_role') === 'super_admin') {
                $isSuperAdmin = true;
            }
        @endphp

        @if($isSuperAdmin)
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                <span>Tambah User Baru</span>
            </a>
        @else
            <div class="btn btn-secondary" style="cursor: not-allowed; opacity: 0.6;" title="Hanya Super Admin yang dapat menambah user">
                <i class="fas fa-lock"></i>
                <span>Tambah User (Super Admin Only)</span>
            </div>
        @endif
    </div>
    
    <div class="admin-info">
        Login sebagai: <strong>{{ $currentAdmin->name ?? session('admin_name') ?? 'Unknown' }}</strong>
        ({{ ucfirst(str_replace('_', ' ', $currentAdmin->role ?? session('admin_role') ?? 'unknown')) }})
    </div>
</div>

<!-- Statistics Cards -->
<div class="users-stats">
    <div class="stat-card total">
        <div class="stat-number">{{ $users->count() }}</div>
        <div class="stat-label">Total User</div>
    </div>
    <div class="stat-card super-admin">
        <div class="stat-number">{{ $users->where('role', 'super_admin')->count() }}</div>
        <div class="stat-label">Super Admin</div>
    </div>
    <div class="stat-card admin">
        <div class="stat-number">{{ $users->where('role', 'admin')->count() }}</div>
        <div class="stat-label">Admin</div>
    </div>
</div>

<!-- User Table -->
<div class="user-table">
    <div class="table-header">
        <div class="table-title">Daftar User Admin</div>
        <div class="table-subtitle">Kelola akses administrator sistem</div>
    </div>

    <div class="table-content">
        @if($users->count() > 0)
            @foreach($users as $user)
                <div class="user-item {{ $user->id === ($currentAdmin->id ?? session('admin_id')) ? 'current-user' : '' }}">
                    <div class="user-info">
                        <div class="user-name">
                            {{ $user->name }}
                            @if($user->id === ($currentAdmin->id ?? session('admin_id')))
                                <span class="current-user-badge">Anda</span>
                            @endif
                        </div>
                        <div class="user-meta">
                            <span class="user-username"><i class="fas fa-user"></i> {{ $user->username }}</span>
                            <span class="user-role role-{{ str_replace('_', '-', $user->role) }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                            @if($user->last_login_at)
                                <span class="user-last-login">
                                    <i class="fas fa-clock"></i> Terakhir login: {{ $user->last_login_at->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span class="user-last-login">
                                    <i class="fas fa-clock"></i> Belum pernah login
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="user-actions">
                        <!-- Edit Password: Super admin bisa edit semua, admin hanya diri sendiri -->
                        @php
                            $canEditPassword = false;
                            if(isset($currentAdmin)) {
                                $canEditPassword = ($currentAdmin->role === 'super_admin') || ($user->id === $currentAdmin->id);
                            } else {
                                $canEditPassword = (session('admin_role') === 'super_admin') || ($user->id === session('admin_id'));
                            }
                        @endphp

                        @if($canEditPassword)
                            <a href="{{ route('admin.users.edit-password', $user->id) }}" class="btn btn-warning">
                                <i class="fas fa-key"></i> Ubah Password
                            </a>
                        @endif

                        <!-- Delete: Hanya super admin dan tidak bisa hapus diri sendiri -->
                        @php
                            $canDelete = false;
                            $currentUserId = $currentAdmin->id ?? session('admin_id');
                            
                            if(isset($currentAdmin)) {
                                $canDelete = ($currentAdmin->role === 'super_admin') && ($user->id !== $currentUserId);
                            } else {
                                $canDelete = (session('admin_role') === 'super_admin') && ($user->id !== $currentUserId);
                            }
                        @endphp

                        @if($canDelete)
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" 
                                  style="display: inline;" 
                                  onsubmit="return confirm('⚠️ Yakin ingin menghapus user {{ $user->name }}?\n\nTindakan ini tidak dapat dibatalkan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-users"></i></div>
                <h3>Belum ada user</h3>
                <p>Silakan tambah user admin pertama</p>
                @if($isSuperAdmin ?? false)
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                        <i class="fas fa-user-plus"></i> Tambah User Pertama
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection