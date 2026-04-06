{{-- resources/views/admin/hasil-survey/export-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 1in 1in 1in 1in; /* Top Right Bottom Left - semua 1 inch */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }

        /* Cover Page - Simple */
        .cover-page {
            text-align: center;
            padding-top: 150px;
        }

        .cover-title {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 60px;
            color: #000;
        }

        .cover-info {
            font-size: 11pt;
            margin: 12px 0;
            color: #333;
        }

        .cover-date {
            margin-top: 80px;
            font-size: 10pt;
            color: #666;
        }

        /* Section Headers - Simple */
        .section-header {
            font-size: 12pt;
            font-weight: bold;
            margin: 25px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
            color: #000;
        }

        .sub-header {
            font-size: 11pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #333;
        }

        /* Tables - Simple */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
            font-size: 9pt;
        }

        table th {
            background: #f5f5f5;
            padding: 8px;
            border: 1px solid #ccc;
            font-weight: bold;
            text-align: left;
        }

        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Badges - Simple */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 8pt;
            font-weight: normal;
            border: 1px solid #999;
        }

        .badge-sangat-baik {
            background: #e8f5e9;
            border-color: #4caf50;
            color: #2e7d32;
        }

        .badge-baik {
            background: #e3f2fd;
            border-color: #2196f3;
            color: #1565c0;
        }

        .badge-cukup {
            background: #fff9e6;
            border-color: #ffc107;
            color: #f57c00;
        }

        .badge-kurang {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
        }

        .badge-sangat-kurang {
            background: #fce4ec;
            border-color: #e91e63;
            color: #ad1457;
        }

        .badge-excellent {
            background: #e8f5e9;
            border-color: #4caf50;
            color: #2e7d32;
        }

        .badge-perlu-perbaikan {
            background: #ffebee;
            border-color: #f44336;
            color: #c62828;
        }

        /* Stats Box - Simple */
        .stats-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }

        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 12px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            text-align: center;
        }

        .stat-label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
        }

        /* Info Box - Simple */
        .info-box {
            background: #f5f5f5;
            padding: 10px 12px;
            margin: 12px 0;
            font-size: 9pt;
            line-height: 1.5;
            border: 1px solid #ddd;
        }

        /* Responden Detail - Simple */
        .responden-box {
            margin: 12px 0;
            padding: 10px;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        .responden-header {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 6px;
            color: #000;
        }

        .responden-meta {
            font-size: 8pt;
            color: #666;
            margin-bottom: 8px;
        }

        .question-item {
            margin: 8px 0;
        }

        .question-label {
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
            font-size: 9pt;
        }

        .answer-text {
            color: #333;
            margin-left: 10px;
            font-size: 9pt;
        }

        .saw-detail {
            font-size: 8pt;
            color: #666;
            font-style: italic;
            margin-left: 10px;
            margin-top: 2px;
        }

        /* Section Divider - Simple */
        .section-divider {
            border-bottom: 1px solid #ddd;
            margin: 12px 0;
        }

        /* Chart placeholders - Simple */
        .chart-placeholder {
            background: #f9f9f9;
            border: 1px dashed #ccc;
            padding: 30px;
            text-align: center;
            margin: 12px 0;
            color: #666;
            font-size: 9pt;
        }

        /* Formulas - Simple */
        .formula-box {
            background: #fafafa;
            border: 1px solid #ddd;
            padding: 8px 10px;
            margin: 8px 0;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
        }

        /* Calculation Step Box */
        .calc-step {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 12px;
            margin: 15px 0;
        }

        .calc-step-title {
            font-weight: bold;
            font-size: 10pt;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .calc-step-content {
            font-size: 9pt;
            line-height: 1.6;
        }

        .calc-step-code {
            background: #ecf0f1;
            padding: 8px;
            margin: 8px 0;
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            border-radius: 3px;
        }

        .calc-result {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            padding: 8px;
            margin: 8px 0;
            font-weight: bold;
        }

        .highlight-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
            font-size: 9pt;
        }
    </style>
</head>
<body>

    {{-- COVER PAGE --}}
    <div class="cover-page">
        <div class="cover-title">{{ $title }}</div>
        
        <div class="cover-info">
            Periode: {{ $period_start }} - {{ $period_end }}
        </div>
        
        <div class="cover-info">
            Total Responden: {{ $total_responses }} orang
        </div>
        
        <div class="cover-date">
            Tanggal Generate:<br>
            {{ $generated_at }}
        </div>
    </div>

    <div class="page-break"></div>

    {{-- HASIL PERHITUNGAN SAW --}}
    <div class="section-header">1. HASIL PERHITUNGAN SAW (Simple Additive Weighting)</div>

    {{-- Statistics --}}
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-label">Total Nilai</div>
            <div class="stat-value">{{ number_format($totalVi, 3) }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Kriteria Aktif</div>
            <div class="stat-value">{{ $criteriaResults->count() }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Responden</div>
            <div class="stat-value">{{ $total_responses }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Metode</div>
            <div class="stat-value" style="font-size: 14pt;">SAW</div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- TABEL LAPORAN HASIL KUESIONER --}}
    <div class="sub-header">Laporan Hasil Kuesioner</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 30%;">Pertanyaan</th>
                <th style="width: 12%;" class="text-center">Jenis</th>
                <th style="width: 28%;">Jawaban Responden</th>
                <th style="width: 12%;" class="text-center">Frekuensi</th>
                <th style="width: 13%;" class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php
                $questionNumber = 1;
            @endphp
            
            @foreach($sections as $sectionIndex => $section)
                @if($section->questions && $section->questions->count() > 0)
                    @foreach($section->questions as $questionIndex => $question)
                        @php
                            $responses = $question->responses;
                            $totalResponses = $responses->count();
                            $questionType = $question->question_type;
                            
                            // Tentukan label jenis pertanyaan
                            $typeLabels = [
                                'short_text' => 'Jawaban Singkat',
                                'text' => 'Jawaban Singkat',
                                'long_text' => 'Paragraf',
                                'textarea' => 'Paragraf',
                                'multiple_choice' => 'Pilihan Ganda',
                                'radio' => 'Pilihan Ganda',
                                'checkbox' => 'Kotak Centang',
                                'dropdown' => 'Drop-down',
                                'select' => 'Drop-down',
                                'file_upload' => 'Upload File',
                                'linear_scale' => 'Skala Linier'
                            ];
                            
                            $typeLabel = $typeLabels[$questionType] ?? ucfirst(str_replace('_', ' ', $questionType));
                            
                            // Tambahkan info skala untuk linear_scale
                            if ($questionType === 'linear_scale') {
                                $scaleMin = $question->settings['scale_min'] ?? 1;
                                $scaleMax = $question->settings['scale_max'] ?? 5;
                                $typeLabel .= ' (' . $scaleMin . '-' . $scaleMax . ')';
                            }
                            
                            // Hitung distribusi jawaban
                            $distribution = [];
                            
                            // Untuk Multiple Choice, Radio, Dropdown, Select
                            if (in_array($questionType, ['multiple_choice', 'radio', 'dropdown', 'select'])) {
                                if ($totalResponses > 0) {
                                    $grouped = $responses->groupBy('answer');
                                    foreach ($grouped as $answer => $group) {
                                        if (!empty($answer)) {
                                            $count = $group->count();
                                            $distribution[] = [
                                                'answer' => $answer,
                                                'count' => $count,
                                                'percentage' => round(($count / $totalResponses) * 100) . '%'
                                            ];
                                        }
                                    }
                                }
                                
                            } elseif ($questionType === 'checkbox') {
                                $allOptions = [];
                                foreach ($responses as $response) {
                                    if ($response->answer_data && is_array($response->answer_data)) {
                                        foreach ($response->answer_data as $option) {
                                            if (!isset($allOptions[$option])) {
                                                $allOptions[$option] = 0;
                                            }
                                            $allOptions[$option]++;
                                        }
                                    } elseif ($response->answer) {
                                        $answerArray = json_decode($response->answer, true);
                                        if (is_array($answerArray)) {
                                            foreach ($answerArray as $option) {
                                                if (!isset($allOptions[$option])) {
                                                    $allOptions[$option] = 0;
                                                }
                                                $allOptions[$option]++;
                                            }
                                        }
                                    }
                                }
                                
                                foreach ($allOptions as $option => $count) {
                                    $distribution[] = [
                                        'answer' => $option,
                                        'count' => $count,
                                        'percentage' => $totalResponses > 0 ? round(($count / $totalResponses) * 100) . '%' : '0%'
                                    ];
                                }
                                
                            } elseif ($questionType === 'linear_scale') {
                                $scaleMin = $question->settings['scale_min'] ?? 1;
                                $scaleMax = $question->settings['scale_max'] ?? 5;
                                
                                for ($i = $scaleMin; $i <= $scaleMax; $i++) {
                                    $count = $responses->where('answer', (string)$i)->count();
                                    $distribution[] = [
                                        'answer' => (string)$i,
                                        'count' => $count,
                                        'percentage' => $totalResponses > 0 ? round(($count / $totalResponses) * 100) . '%' : '0%'
                                    ];
                                }
                                
                            } elseif ($questionType === 'file_upload') {
                                $uploadedCount = $responses->filter(function($r) {
                                    return !empty($r->answer) || !empty($r->answer_data);
                                })->count();
                                $notUploadedCount = $totalResponses - $uploadedCount;
                                
                                if ($uploadedCount > 0 || $notUploadedCount > 0) {
                                    $distribution = [
                                        [
                                            'answer' => 'File diterima',
                                            'count' => $uploadedCount,
                                            'percentage' => $totalResponses > 0 ? round(($uploadedCount / $totalResponses) * 100) . '%' : '0%'
                                        ],
                                        [
                                            'answer' => 'Tidak upload',
                                            'count' => $notUploadedCount,
                                            'percentage' => $totalResponses > 0 ? round(($notUploadedCount / $totalResponses) * 100) . '%' : '0%'
                                        ]
                                    ];
                                }
                            }
                            
                            $isTextType = in_array($questionType, ['text', 'textarea', 'short_text', 'long_text']);
                            $hasDistribution = !empty($distribution);
                        @endphp
                        
                        <tr>
                            <td class="text-center">{{ $questionNumber }}</td>
                            <td>{{ $question->question_text }}</td>
                            <td class="text-center">{{ $typeLabel }}</td>
                            @if($hasDistribution)
                                <td>
                                    @foreach($distribution as $dist)
                                        {{ $dist['answer'] }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @foreach($distribution as $dist)
                                        {{ $dist['count'] }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @foreach($distribution as $dist)
                                        {{ $dist['percentage'] }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </td>
                            @else
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                            @endif
                        </tr>
                        
                        @php
                            $questionNumber++;
                        @endphp
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- TABEL HASIL INTERPRETASI PER KRITERIA --}}
    <div class="sub-header">Hasil Interpretasi per Kriteria</div>

    <table>
        <thead>
            <tr>
                <th>Kriteria</th>
                <th class="text-center">Jumlah Pertanyaan</th>
                <th class="text-center">Nilai Ternormalisasi (r)</th>
                <th class="text-center">Bobot Asli</th>
                <th class="text-center">Tipe Kriteria</th>
                <th class="text-center">Interpretasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($criteriaResults as $result)
            @php
                // Ambil bobot asli dari database berdasarkan criteria_name
                $criteriaName = $result['criteria'];
                $firstQuestion = \App\Models\SurveyQuestion::where('enable_saw', true)
                    ->where('criteria_name', $criteriaName)
                    ->first();
                $originalWeight = $firstQuestion ? $firstQuestion->criteria_weight : 0;
            @endphp
            <tr>
                <td style="font-weight: bold;">{{ $result['criteria'] }}</td>
                <td class="text-center">{{ $result['questions_count'] }}</td>
                <td class="text-center">{{ number_format($result['normalized'], 3) }}</td>
                <td class="text-center"><strong>{{ number_format($originalWeight, 1) }}</strong></td>
                <td class="text-center">
                    <span class="badge badge-{{ $result['criteria_type'] === 'benefit' ? 'baik' : 'cukup' }}">
                        {{ ucfirst($result['criteria_type']) }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge badge-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                        {{ $result['interpretation'] }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- LAMPIRAN - DETAIL PERHITUNGAN SAW --}}
    <div class="section-header">2. LAMPIRAN - DETAIL PERHITUNGAN SAW</div>

    {{-- A. Penjelasan Metode SAW --}}
    <div class="sub-header">A. Penjelasan Metode SAW (Simple Additive Weighting)</div>

    <div style="margin: 15px 0; line-height: 1.6; font-size: 9pt;">
        <p style="margin-bottom: 10px;">
            Simple Additive Weighting (SAW) adalah metode penjumlahan terbobot yang mencari 
            penjumlahan terbobot dari rating kinerja pada setiap alternatif pada semua kriteria.
        </p>

        <strong>Langkah Perhitungan:</strong>
        <ol style="margin-left: 20px; margin-top: 8px;">
            <li style="margin: 5px 0;">Menentukan kriteria dan bobot masing-masing kriteria</li>
            <li style="margin: 5px 0;">Melakukan normalisasi matriks keputusan</li>
            <li style="margin: 5px 0;">Menghitung nilai preferensi untuk setiap alternatif</li>
        </ol>

        <div class="formula-box" style="margin-top: 15px;">
            <strong>Rumus Normalisasi (Benefit):</strong><br>
            r<sub>ij</sub> = x<sub>ij</sub> / Max{x<sub>ij</sub>}
        </div>

        <div class="formula-box">
            <strong>Rumus Normalisasi (Cost):</strong><br>
            r<sub>ij</sub> = Min{x<sub>ij</sub>} / x<sub>ij</sub>
        </div>

        <div class="formula-box">
            <strong>Rumus Nilai Preferensi:</strong><br>
            V<sub>i</sub> = Σ w<sub>j</sub> × r<sub>ij</sub>
        </div>

        <div style="margin-top: 15px; font-size: 8pt; color: #666;">
            <strong>Keterangan:</strong><br>
            r<sub>ij</sub> = nilai rating kinerja ternormalisasi<br>
            x<sub>ij</sub> = nilai atribut yang dimiliki dari setiap kriteria<br>
            w<sub>j</sub> = nilai bobot dari setiap kriteria<br>
            V<sub>i</sub> = nilai preferensi untuk setiap alternatif
        </div>
    </div>

    <div class="page-break"></div>

    {{-- B. DETAIL PERHITUNGAN STEP BY STEP (Line 82-164) --}}
    <div class="sub-header">B. Detail Perhitungan SAW (Sesuai Implementasi Kode)</div>

    <div class="info-box">
        Berikut adalah detail perhitungan SAW yang dilakukan oleh sistem sesuai dengan kode di <strong>SurveyResultController.php (Line 82-164)</strong>
    </div>

    {{-- STEP 1: Grouping dan Agregasi --}}
    <div class="calc-step">
        <div class="calc-step-title">STEP 1: Grouping dan Agregasi per Kriteria</div>
        <div class="calc-step-content">
            <p>Kode yang dijalankan (Line 82-111):</p>
            <div class="calc-step-code">
// Group questions by criteria<br>
$questionsByCriteria = $sawQuestions->groupBy('criteria_name');<br>
<br>
foreach ($questionsByCriteria as $criteriaName => $questions) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;$allScores = collect();<br>
&nbsp;&nbsp;&nbsp;&nbsp;foreach ($questions as $question) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;foreach ($question->responses as $response) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$allScores->push((float) $response->answer);<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
&nbsp;&nbsp;&nbsp;&nbsp;}<br>
&nbsp;&nbsp;&nbsp;&nbsp;$criteriaAverage = $allScores->avg();<br>
}
            </div>

            <p style="margin-top: 10px;"><strong>Hasil Agregasi:</strong></p>
            <table style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th class="text-center">Jumlah Sub-Kriteria</th>
                        <th class="text-center">Total Data</th>
                        <th class="text-center">Skor Agregat (x)</th>
                        <th class="text-center">Bobot Asli</th>
                        <th class="text-center">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                    <tr>
                        <td><strong>{{ $result['criteria'] }}</strong></td>
                        <td class="text-center">{{ $result['questions_count'] }} pertanyaan</td>
                        <td class="text-center">{{ $result['total_responses'] }} nilai</td>
                        <td class="text-center"><strong>{{ $result['score'] }}</strong></td>
                        <td class="text-center">
                            @php
                                // Ambil bobot asli dari database berdasarkan criteria_name
                                $criteriaName = $result['criteria'];
                                $firstQuestion = \App\Models\SurveyQuestion::where('enable_saw', true)
                                    ->where('criteria_name', $criteriaName)
                                    ->first();
                                $originalWeight = $firstQuestion ? $firstQuestion->criteria_weight : 0;
                            @endphp
                            <strong>{{ number_format($originalWeight, 1) }}</strong>
                        </td>
                        <td class="text-center">{{ ucfirst($result['criteria_type']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="highlight-box">
                <strong>Catatan:</strong> Sistem mengumpulkan SEMUA jawaban dari SEMUA pertanyaan (sub-kriteria) yang memiliki nama kriteria yang sama, kemudian menghitung rata-ratanya sebagai nilai agregat kriteria.
            </div>
        </div>
    </div>

    {{-- STEP 2: Normalisasi Bobot --}}
    <div class="calc-step">
        <div class="calc-step-title">STEP 2: Normalisasi Bobot Kriteria (w)</div>
        <div class="calc-step-content">
            <p>Kode yang dijalankan (Line 115-119):</p>
            <div class="calc-step-code">
$totalWeight = $criteriaAggregates->sum('criteria_weight');<br>
$weightNormalized = $criteria['criteria_weight'] / $totalWeight;
            </div>

            <p style="margin-top: 10px;"><strong>Perhitungan:</strong></p>
            
            @php
                // Hitung total bobot asli dari semua kriteria
                $criteriaWeights = [];
                foreach($criteriaResults as $result) {
                    $criteriaName = $result['criteria'];
                    // Cari bobot asli dari sawConfig untuk kriteria ini
                    $configItem = $sawConfig->first(function($item) use ($criteriaName) {
                        // Ambil criteria_name dari pertanyaan pertama yang match
                        return true; // Akan diambil dari grouping
                    });
                    
                    // Ambil dari pertanyaan pertama dengan criteria_name yang sama
                    $firstQuestion = \App\Models\SurveyQuestion::where('enable_saw', true)
                        ->where('criteria_name', $criteriaName)
                        ->first();
                    
                    if ($firstQuestion) {
                        $criteriaWeights[$criteriaName] = $firstQuestion->criteria_weight;
                    }
                }
                $totalOriginalWeight = array_sum($criteriaWeights);
            @endphp

            <div style="margin: 10px 0; font-size: 9pt;">
                Total Bobot = 
                @foreach($criteriaWeights as $index => $weight)
                    @if($loop->index > 0) + @endif
                    {{ number_format($weight, 1) }}
                @endforeach
                = <strong>{{ number_format($totalOriginalWeight, 1) }}</strong>
            </div>

            <table style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th class="text-center">Bobot Asli</th>
                        <th class="text-center">Rumus</th>
                        <th class="text-center">Bobot Ternormalisasi (w)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                    @php
                        $criteriaName = $result['criteria'];
                        $originalWeight = $criteriaWeights[$criteriaName] ?? 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $result['criteria'] }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($originalWeight, 1) }}</strong></td>
                        <td class="text-center">
                            {{ number_format($originalWeight, 1) }} / {{ number_format($totalOriginalWeight, 1) }}
                        </td>
                        <td class="text-center"><strong>{{ number_format($result['weight_normalized'], 3) }}</strong></td>
                    </tr>
                    @endforeach
                    <tr style="background: #e8f5e9; font-weight: bold;">
                        <td>TOTAL</td>
                        <td class="text-center">{{ number_format($totalOriginalWeight, 1) }}</td>
                        <td class="text-center">-</td>
                        <td class="text-center">{{ number_format($criteriaResults->sum('weight_normalized'), 3) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="calc-result">
                ✓ Verifikasi: Total bobot ternormalisasi = {{ number_format($criteriaResults->sum('weight_normalized'), 3) }} (Harus = 1.000)
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- STEP 3: Normalisasi Nilai --}}
    <div class="calc-step">
        <div class="calc-step-title">STEP 3: Normalisasi Nilai (r) - Benefit dan Cost</div>
        <div class="calc-step-content">
            <p>Kode yang dijalankan (Line 126-138):</p>
            <div class="calc-step-code">
if ($criteria['criteria_type'] === 'benefit') {<br>
&nbsp;&nbsp;&nbsp;&nbsp;$maxScore = $criteriaAggregates->max('average_score');<br>
&nbsp;&nbsp;&nbsp;&nbsp;$normalized = $maxScore > 0 ? <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;($criteria['average_score'] / $maxScore) : 0;<br>
} else { // cost<br>
&nbsp;&nbsp;&nbsp;&nbsp;$minScore = $criteriaAggregates->min('average_score');<br>
&nbsp;&nbsp;&nbsp;&nbsp;$normalized = $criteria['average_score'] > 0 ? <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;($minScore / $criteria['average_score']) : 0;<br>
}<br>
$normalized = max(0, min(1, $normalized));
            </div>

            @php
                $maxScore = $criteriaResults->max('score');
                $minScore = $criteriaResults->min('score');
            @endphp

            <div class="highlight-box">
                <strong>Data yang digunakan:</strong><br>
                Max Score (untuk Benefit) = {{ number_format($maxScore, 2) }}<br>
                Min Score (untuk Cost) = {{ number_format($minScore, 2) }}
            </div>

            <p style="margin-top: 10px;"><strong>Perhitungan per Kriteria:</strong></p>

            <table style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Skor (x)</th>
                        <th class="text-center">Rumus</th>
                        <th class="text-center">Normalisasi (r)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                    <tr>
                        <td><strong>{{ $result['criteria'] }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-{{ $result['criteria_type'] === 'benefit' ? 'baik' : 'cukup' }}">
                                {{ ucfirst($result['criteria_type']) }}
                            </span>
                        </td>
                        <td class="text-center">{{ $result['score'] }}</td>
                        <td class="text-center" style="font-size: 8pt;">
                            @if($result['criteria_type'] === 'benefit')
                                {{ $result['score'] }} / {{ number_format($maxScore, 2) }}
                            @else
                                {{ number_format($minScore, 2) }} / {{ $result['score'] }}
                            @endif
                        </td>
                        <td class="text-center"><strong>{{ number_format($result['normalized'], 3) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="calc-result">
                ✓ Semua nilai ternormalisasi berada dalam range 0-1 (sudah di-validasi dengan max(0, min(1, value)))
            </div>
        </div>
    </div>

    {{-- STEP 4: Nilai Terbobot --}}
    <div class="calc-step">
        <div class="calc-step-title">STEP 4: Perhitungan Nilai Terbobot (Vi)</div>
        <div class="calc-step-content">
            <p>Kode yang dijalankan (Line 141):</p>
            <div class="calc-step-code">
$weightedScore = $weightNormalized * $normalized;
            </div>

            <p style="margin-top: 10px;"><strong>Perhitungan:</strong></p>

            <table style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th class="text-center">Bobot (w)</th>
                        <th class="text-center">Normalisasi (r)</th>
                        <th class="text-center">Rumus</th>
                        <th class="text-center">Nilai Terbobot (V<sub>i</sub>)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                    <tr>
                        <td><strong>{{ $result['criteria'] }}</strong></td>
                        <td class="text-center">{{ number_format($result['weight_normalized'], 3) }}</td>
                        <td class="text-center">{{ number_format($result['normalized'], 3) }}</td>
                        <td class="text-center" style="font-size: 8pt;">
                            {{ number_format($result['weight_normalized'], 3) }} × {{ number_format($result['normalized'], 3) }}
                        </td>
                        <td class="text-center"><strong>{{ number_format($result['weighted_score'], 4) }}</strong></td>
                    </tr>
                    @endforeach
                    <tr style="background: #34495e; color: white; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL NILAI PREFERENSI (ΣV<sub>i</sub>)</td>
                        <td class="text-center">{{ number_format($totalVi, 4) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="calc-result">
                ✓ Total Vi = {{ number_format($totalVi, 4) }}
            </div>
        </div>
    </div>

    {{-- STEP 5: Interpretasi --}}
    <div class="calc-step">
        <div class="calc-step-title">STEP 5: Interpretasi Hasil</div>
        <div class="calc-step-content">
            <p>Kode yang dijalankan (Line 157-164):</p>
            <div class="calc-step-code">
private function getSAWInterpretation($normalizedScore)<br>
{<br>
&nbsp;&nbsp;&nbsp;&nbsp;if ($normalizedScore >= 0.9) return 'Sangat Baik';<br>
&nbsp;&nbsp;&nbsp;&nbsp;if ($normalizedScore >= 0.8) return 'Baik';<br>
&nbsp;&nbsp;&nbsp;&nbsp;if ($normalizedScore >= 0.6) return 'Cukup';<br>
&nbsp;&nbsp;&nbsp;&nbsp;if ($normalizedScore >= 0.4) return 'Kurang';<br>
&nbsp;&nbsp;&nbsp;&nbsp;return 'Sangat Kurang';<br>
}
            </div>

            <p style="margin-top: 10px;"><strong>Kategori Interpretasi:</strong></p>

            <table style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th class="text-center">Range Nilai</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Badge</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">≥ 0.900</td>
                        <td class="text-center">Sangat Baik</td>
                        <td class="text-center"><span class="badge badge-sangat-baik">Sangat Baik</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">0.800 - 0.899</td>
                        <td class="text-center">Baik</td>
                        <td class="text-center"><span class="badge badge-baik">Baik</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">0.600 - 0.799</td>
                        <td class="text-center">Cukup</td>
                        <td class="text-center"><span class="badge badge-cukup">Cukup</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">0.400 - 0.599</td>
                        <td class="text-center">Kurang</td>
                        <td class="text-center"><span class="badge badge-kurang">Kurang</span></td>
                    </tr>
                    <tr>
                        <td class="text-center">< 0.400</td>
                        <td class="text-center">Sangat Kurang</td>
                        <td class="text-center"><span class="badge badge-sangat-kurang">Sangat Kurang</span></td>
                    </tr>
                </tbody>
            </table>

            <p style="margin-top: 15px;"><strong>Hasil Interpretasi per Kriteria:</strong></p>

            <table style="margin-top: 8px;">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th class="text-center">Nilai Ternormalisasi</th>
                        <th class="text-center">Interpretasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaResults as $result)
                    <tr>
                        <td><strong>{{ $result['criteria'] }}</strong></td>
                        <td class="text-center">{{ number_format($result['normalized'], 3) }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                                {{ $result['interpretation'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="calc-result" style="margin-top: 15px;">
                <strong>KESIMPULAN AKHIR:</strong><br>
                Total Nilai Preferensi = {{ number_format($totalVi, 4) }}<br>
                @php
                    $totalInt = $totalVi >= 0.9 ? 'Excellent' : 
                               ($totalVi >= 0.8 ? 'Sangat Baik' : 
                               ($totalVi >= 0.6 ? 'Baik' : 
                               ($totalVi >= 0.4 ? 'Cukup' : 'Perlu Perbaikan')));
                @endphp
                Kategori: <strong>{{ $totalInt }}</strong><br>
                <br>
                Sistem penilaian menunjukkan hasil yang 
                @if($totalVi >= 0.9)
                    <strong>sangat memuaskan</strong> dengan semua kriteria berkontribusi positif.
                @elseif($totalVi >= 0.8)
                    <strong>baik</strong> dengan mayoritas kriteria berkinerja optimal.
                @elseif($totalVi >= 0.6)
                    <strong>cukup</strong> namun masih ada ruang untuk perbaikan.
                @else
                    <strong>memerlukan perbaikan</strong> pada beberapa kriteria.
                @endif
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- C. Konfigurasi Bobot Lengkap --}}
    <div class="sub-header">C. Konfigurasi Bobot Kriteria dan Sub-Kriteria</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="10%">Kode</th>
                <th width="40%">Kriteria / Sub-Kriteria (Pertanyaan)</th>
                <th class="text-center" width="15%">Tipe Kriteria</th>
                <th class="text-center" width="15%">Bobot Kriteria</th>
                <th class="text-center" width="20%">Jumlah Sub-Kriteria</th>
            </tr>
        </thead>
        <tbody>
            @php
                $criteriaGrouped = collect($sawConfig)->groupBy('criteria_name');
            @endphp
            
            @foreach($criteriaGrouped as $criteriaName => $questions)
                @php
                    $firstQuestion = $questions->first();
                @endphp
                <tr style="background: #e8f5e9; font-weight: bold;">
                    <td class="text-center">{{ $firstQuestion['code'] }}</td>
                    <td colspan="2"><strong>KRITERIA: {{ $criteriaName }}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-{{ $firstQuestion['type'] === 'benefit' ? 'baik' : 'cukup' }}">
                            {{ ucfirst($firstQuestion['type']) }}
                        </span>
                    </td>
                    <td class="text-center"><strong>{{ $firstQuestion['weight'] }}</strong></td>
                    <td class="text-center">{{ $questions->count() }} pertanyaan</td>
                </tr>
                
                @foreach($questions as $index => $config)
                <tr>
                    <td class="text-center" style="font-size: 8pt;">{{ $config['code'] }}.{{ $index + 1 }}</td>
                    <td style="padding-left: 20px; font-size: 8pt;">↳ {{ $config['question'] }}</td>
                    <td class="text-center" style="font-size: 8pt;">Sub-Kriteria</td>
                    <td class="text-center" style="font-size: 8pt;">-</td>
                    <td class="text-center" style="font-size: 8pt;">-</td>
                </tr>
                @endforeach
            @endforeach
            
            <tr style="background: #ecf0f1; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL BOBOT</td>
                <td class="text-center">{{ $criteriaGrouped->map(function($questions) { return $questions->first()['weight']; })->sum() }}</td>
                <td class="text-center">{{ $sawConfig->count() }} pertanyaan</td>
            </tr>
        </tbody>
    </table>

    {{-- Footer --}}
    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #ecf0f1; text-align: center; font-size: 8pt; color: #999;">
        Dokumen ini dibuat secara otomatis oleh sistem pada {{ $generated_at }}<br>
        Laporan ini bersifat rahasia dan hanya untuk keperluan internal<br>
        <strong>Perhitungan SAW mengikuti implementasi di SurveyResultController.php (Line 82-164)</strong>
    </div>

</body>
</html>