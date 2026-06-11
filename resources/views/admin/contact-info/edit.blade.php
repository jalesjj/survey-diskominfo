{{-- resources/views/admin/contact-info/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Informasi Kontak - Admin')
@section('active-contact-info', 'active')
@section('page-title', 'Informasi Kontak')
@section('page-subtitle', 'Kelola informasi kontak yang ditampilkan di footer website')

@section('breadcrumb')
<div class="breadcrumb">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span class="breadcrumb-separator">></span>
    <span>Informasi Kontak</span>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-header {
        background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 12px 12px 0 0;
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-subtitle {
        font-size: 14px;
        opacity: 0.9;
    }

    .form-body {
        padding: 30px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }

    .form-input {
        width: 100%;
        padding: 15px 18px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #fff;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #5a9b9e;
        box-shadow: 0 0 0 3px rgba(90, 155, 158, 0.1);
    }

    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    .form-help {
        font-size: 14px;
        color: #7f8c8d;
        margin-top: 5px;
    }

    .info-preview {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
    }

    .info-preview h4 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .preview-content {
        color: #5a6c7d;
        line-height: 1.6;
    }

    .preview-content p {
        margin-bottom: 5px;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 16px;
    }

    .btn-primary {
        background: #5a9b9e;
        color: white;
    }

    .btn-primary:hover {
        background: #4a8b8e;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(90, 155, 158, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }

    .error-message {
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .form-container {
            margin: 0;
            border-radius: 8px;
        }

        .form-body {
            padding: 20px;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="form-header">
        <div class="form-title"><i class="fas fa-address-book"></i> Informasi Kontak</div>
        <div class="form-subtitle">Kelola informasi kontak yang ditampilkan di footer website</div>
    </div>

    <div class="form-body">
        <!-- Preview Current Info -->
        <div class="info-preview">
            <h4><i class="fas fa-eye"></i> Preview Footer</h4>
            <div class="preview-content">
                <p><strong>{{ $contact->department_name }}</strong></p>
                <p>{{ $contact->regency_name }}</p>
                <p>{{ $contact->address }}</p>
                <p>{{ $contact->province }}</p>
                <p>WhatsApp : {{ $contact->whatsapp }}</p>
                <p>Email: {{ $contact->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.contact-info.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="department_name">Nama Dinas *</label>
                    <input 
                        type="text" 
                        id="department_name" 
                        name="department_name" 
                        class="form-input" 
                        placeholder="Contoh: Dinas Komunikasi dan Informatika" 
                        value="{{ old('department_name', $contact->department_name) }}"
                        required
                    >
                    @error('department_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Nama lengkap dinas/instansi</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="regency_name">Nama Kabupaten/Kota *</label>
                    <input 
                        type="text" 
                        id="regency_name" 
                        name="regency_name" 
                        class="form-input" 
                        placeholder="Contoh: Kabupaten Lamongan" 
                        value="{{ old('regency_name', $contact->regency_name) }}"
                        required
                    >
                    @error('regency_name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Nama kabupaten atau kota</div>
                </div>
            </div>

            <div class="form-group full-width">
                <label class="form-label" for="address">Alamat Lengkap *</label>
                <textarea 
                    id="address" 
                    name="address" 
                    class="form-input form-textarea" 
                    placeholder="Contoh: Jl. Basuki Rahmat No. 1, Lamongan"
                    required
                >{{ old('address', $contact->address) }}</textarea>
                @error('address')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <div class="form-help">Alamat lengkap kantor/instansi</div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="province">Provinsi & Kode Pos *</label>
                    <input 
                        type="text" 
                        id="province" 
                        name="province" 
                        class="form-input" 
                        placeholder="Contoh: Jawa Timur 62211" 
                        value="{{ old('province', $contact->province) }}"
                        required
                    >
                    @error('province')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Provinsi dan kode pos</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="whatsapp">Nomor WhatsApp *</label>
                    <input 
                        type="text" 
                        id="whatsapp" 
                        name="whatsapp" 
                        class="form-input" 
                        placeholder="Contoh: +628113021708" 
                        value="{{ old('whatsapp', $contact->whatsapp) }}"
                        required
                    >
                    @error('whatsapp')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="form-help">Nomor WhatsApp dengan kode negara</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email *</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="Contoh: diskominfo@lamongankab.go.id" 
                    value="{{ old('email', $contact->email) }}"
                    required
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <div class="form-help">Alamat email resmi instansi</div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto focus pada input pertama
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('department_name').focus();
    });

    // Live preview update
    function updatePreview() {
        const departmentName = document.getElementById('department_name').value || 'Dinas Komunikasi dan Informatika';
        const regencyName = document.getElementById('regency_name').value || 'Kabupaten Lamongan';
        const address = document.getElementById('address').value || 'Jl. Basuki Rahmat No. 1, Lamongan';
        const province = document.getElementById('province').value || 'Jawa Timur 62211';
        const whatsapp = document.getElementById('whatsapp').value || '+628113021708';
        const email = document.getElementById('email').value || 'diskominfo@lamongankab.go.id';

        const previewContent = document.querySelector('.preview-content');
        previewContent.innerHTML = `
            <p><strong>${departmentName}</strong></p>
            <p>${regencyName}</p>
            <p>${address}</p>
            <p>${province}</p>
            <p>WhatsApp : ${whatsapp}</p>
            <p>Email: ${email}</p>
        `;
    }

    // Add event listeners for live preview
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    // Auto hide success message
    const successMessage = document.querySelector('.success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    }
</script>
@endpush