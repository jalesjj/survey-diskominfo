{{-- resources/views/admin/periods/responses-detail.blade.php

@extends('layouts.admin')

@section('active-periods', 'active')

@section('title', 'Detail Jawaban - Survey #' . $survey->id)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.periods.index') }}">
                    <i class="fas fa-calendar-alt"></i> Kelola Periode
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.periods.responses', $period->id) }}">
                    {{ $period->period_name }} - Jawaban
                </a>
            </li>
            <li class="breadcrumb-item active">Survey #{{ $survey->id }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-alt"></i> Detail Jawaban Survey #{{ $survey->id }}
            </h1>
            <div class="survey-info-bar">
                <span>
                    <i class="fas fa-calendar"></i>
                    Periode: <strong>{{ $period->period_name }}</strong>
                </span>
                <span>
                    <i class="fas fa-clock"></i>
                    Diisi: {{ $responses->first()->created_at->format('d M Y H:i') }}
                </span>
                <span>
                    <i class="fas fa-network-wired"></i>
                    IP: {{ $survey->ip_address ?? 'N/A' }}
                </span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.periods.responses', $period->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Responses by Section -->
    @php
        $responsesBySection = $responses->groupBy(function($response) {
            return $response->question->section_id ?? 'no_section';
        });
    @endphp

    @foreach($responsesBySection as $sectionId => $sectionResponses)
        @php
            $section = $sectionResponses->first()->question->section ?? null;
        @endphp

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group"></i>
                    {{ $section ? $section->title : 'Tanpa Bagian' }}
                </h5>
                @if($section && $section->description)
                    <small class="d-block mt-1 opacity-75">{{ $section->description }}</small>
                @endif
            </div>
            <div class="card-body">
                @foreach($sectionResponses as $index => $response)
                <div class="response-item {{ $index < $sectionResponses->count() - 1 ? 'border-bottom' : '' }} pb-3 mb-3">
                    <div class="question-header mb-2">
                        <span class="question-number">{{ $loop->iteration }}.</span>
                        <strong class="question-text">{{ $response->question->question_text }}</strong>
                        
                        @if($response->question->is_required)
                            <span class="badge bg-danger ms-2">Wajib</span>
                        @endif

                        @if($response->question->enable_saw)
                            <span class="badge bg-info ms-2">
                                SAW: {{ $response->question->criteria_name }}
                            </span>
                        @endif
                    </div>

                    @if($response->question->question_description)
                    <div class="question-description text-muted small mb-2">
                        {{ $response->question->question_description }}
                    </div>
                    @endif

                    <div class="answer-content">
                        @if($response->question->question_type == 'linear_scale')
                            <!-- Linear Scale -->
                            <div class="linear-scale-answer">
                                <div class="scale-value-display">
                                    <span class="scale-value">{{ $response->answer }}</span>
                                    @if($response->question->settings)
                                        <span class="scale-range">
                                            / {{ $response->question->settings['scale_max'] ?? 5 }}
                                        </span>
                                    @endif
                                </div>
                                @if(isset($response->question->settings['scale_min_label']) || isset($response->question->settings['scale_max_label']))
                                <div class="scale-labels mt-1">
                                    <small class="text-muted">
                                        {{ $response->question->settings['scale_min_label'] ?? '' }}
                                        <i class="fas fa-arrow-right mx-2"></i>
                                        {{ $response->question->settings['scale_max_label'] ?? '' }}
                                    </small>
                                </div>
                                @endif
                            </div>

                        @elseif($response->question->question_type == 'checkbox')
                            <!-- Checkbox (Multiple) -->
                            <div class="checkbox-answer">
                                @if($response->answer_data && is_array($response->answer_data))
                                    <ul class="list-unstyled mb-0">
                                        @foreach($response->answer_data as $item)
                                            <li><i class="fas fa-check-square text-success"></i> {{ $item }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $response->answer }}
                                @endif
                            </div>

                        @elseif($response->question->question_type == 'file_upload')
                            <!-- File Upload -->
                            <div class="file-answer">
                                @if($response->answer_data)
                                    <i class="fas fa-file"></i>
                                    <strong>{{ $response->answer_data['filename'] ?? $response->answer }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Ukuran: {{ isset($response->answer_data['size']) ? number_format($response->answer_data['size'] / 1024, 2) . ' KB' : 'N/A' }}
                                    </small>
                                @else
                                    {{ $response->answer }}
                                @endif
                            </div>

                        @else
                            <!-- Text / Radio / Dropdown -->
                            <div class="text-answer">
                                <p class="mb-0">{{ $response->answer }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if($responses->isEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Tidak ada jawaban untuk survey ini.
    </div>
    @endif
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.page-title {
    margin: 0 0 10px 0;
    font-size: 1.75rem;
}

.survey-info-bar {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    font-size: 0.9rem;
}

.survey-info-bar span {
    padding: 5px 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.response-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

.response-item:last-child {
    margin-bottom: 0;
}

.question-header {
    display: flex;
    align-items: center;
    gap: 5px;
}

.question-number {
    font-weight: bold;
    color: #007bff;
    min-width: 30px;
}

.question-text {
    flex: 1;
    color: #333;
}

.answer-content {
    margin-left: 30px;
    padding: 10px;
    background: white;
    border-radius: 4px;
    border-left: 3px solid #007bff;
}

.linear-scale-answer .scale-value-display {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.linear-scale-answer .scale-value {
    display: inline-block;
    min-width: 40px;
}

.linear-scale-answer .scale-range {
    color: #6c757d;
    font-size: 1.2rem;
}

.checkbox-answer ul li {
    padding: 5px 0;
}

.checkbox-answer i {
    margin-right: 8px;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 15px;
}
</style>
@endsection --}}