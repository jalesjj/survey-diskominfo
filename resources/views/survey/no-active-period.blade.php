{{-- resources/views/survey/no-active-period.blade.php --}}
@extends('layouts.app')

@section('title', 'Survey Tidak Aktif')

@section('content')
<style>
    .no-active-container {
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
    }

    .no-active-content {
        max-width: 500px;
        width: 100%;
        background: white;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .no-active-icon {
        margin-bottom: 20px;
    }

    .no-active-icon i {
        font-size: 50px;
        color: #6c757d;
    }

    .no-active-title {
        font-size: 24px;
        margin-bottom: 15px;
        color: #333;
    }

    .no-active-message {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .no-active-info {
        background: #f1f3f5;
        padding: 15px;
        border-radius: 5px;
        text-align: left;
        font-size: 14px;
        color: #555;
    }

    .no-active-info p {
        margin: 8px 0;
    }
</style>

<div class="no-active-container">
    <div class="no-active-content">
        
        <div class="no-active-icon">
            <i class="fas fa-lock"></i>
        </div>

        <h1 class="no-active-title">
            Survey Belum Dibuka
        </h1>

        <p class="no-active-message">
            Maaf, survey saat ini belum dapat diakses. Administrator belum membuka periode pengisian survey.
        </p>

        <div class="no-active-info">
            <p><strong>Informasi:</strong></p>
            <p>• Survey akan dibuka ketika administrator mengaktifkan periode pengisian</p>
            <p>• Coba akses kembali halaman ini nanti</p>
        </div>

    </div>
</div>
@endsection