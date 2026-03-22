{{-- resources/views/admin/hasil-survey/export-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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

    {{-- TABEL KRITERIA SAW --}}
    <div class="sub-header">A. Tabel Kriteria SAW</div>

    <table>
        <thead>
            <tr>
                <th>Kriteria</th>
                <th class="text-center">Skor (x)</th>
                <th class="text-center">Bobot (w<sub>ᵢ</sub>)</th>
                <th class="text-center">Normalisasi (r<sub>ᵢ</sub>)</th>
                <th class="text-center">Nilai Terbobot</th>
                <th class="text-center">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($criteriaResults as $result)
            <tr>
                <td style="font-weight: bold;">{{ $result['criteria'] }}</td>
                <td class="text-center">{{ $result['score'] }}</td>
                <td class="text-center">{{ number_format($result['weight_normalized'], 3) }}</td>
                <td class="text-center">{{ number_format($result['normalized'], 3) }}</td>
                <td class="text-center" style="font-weight: bold;">{{ number_format($result['weighted_score'], 4) }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ strtolower(str_replace(' ', '-', $result['interpretation'])) }}">
                        {{ $result['interpretation'] }}
                    </span>
                </td>
            </tr>
            @endforeach
            <tr style="background: #34495e; color: white; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL NILAI PREFERENSI (V<sub>i</sub>)</td>
                <td class="text-center">{{ number_format($totalVi, 4) }}</td>
                <td class="text-center">
                    @php
                        $totalInt = $totalVi >= 0.9 ? 'Excellent' : 
                                   ($totalVi >= 0.8 ? 'Sangat Baik' : 
                                   ($totalVi >= 0.6 ? 'Baik' : 
                                   ($totalVi >= 0.4 ? 'Cukup' : 'Perlu Perbaikan')));
                    @endphp
                    {{ $totalInt }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- DATA MENTAH RESPONDEN --}}
    <div class="section-header">2. DATA MENTAH RESPONDEN</div>

    <div class="info-box">
        Berikut adalah detail jawaban lengkap dari setiap responden, termasuk nilai SAW untuk pertanyaan yang memiliki bobot penilaian.
    </div>

    {{-- A. Detail Jawaban per Responden --}}
    <div class="sub-header">A. Detail Jawaban per Responden</div>

    @foreach($surveysWithSAW->take(20) as $index => $survey)
        <div class="responden-box">
            <div class="responden-header">
                RESPONDEN #{{ $index + 1 }}: {{ $survey->nama }}
            </div>
            <div class="responden-meta">
                Email: {{ $survey->responses->firstWhere('question.question_text', 'like', '%email%')?->answer ?? '-' }} | 
                Tanggal Submit: {{ $survey->created_at->format('d F Y H:i:s') }} | 
                Skor SAW: {{ number_format($survey->saw_score, 4) }} (Rank: {{ $index + 1 }})
            </div>

            <div class="section-divider"></div>

            @foreach($sections as $section)
                @php
                    $sectionQuestions = $section->questions;
                    $hasAnswers = false;
                @endphp

                @foreach($sectionQuestions as $question)
                    @php
                        $response = $survey->responses->firstWhere('question_id', $question->id);
                        if ($response) $hasAnswers = true;
                    @endphp
                @endforeach

                @if($hasAnswers)
                    <div style="margin-top: 12px; margin-bottom: 8px;">
                        <strong style="color: #2c3e50; font-size: 10pt;">{{ strtoupper($section->title) }}</strong>
                    </div>

                    @foreach($sectionQuestions as $question)
                        @php
                            $response = $survey->responses->firstWhere('question_id', $question->id);
                        @endphp

                        @if($response)
                            <div class="question-item">
                                <div class="question-label">
                                    Q{{ $question->id }}. {{ $question->question_text }}
                                </div>
                                <div class="answer-text">
                                    Jawaban: {{ Str::limit($response->answer, 100) }}
                                    
                                    @if($question->enable_saw && $question->question_type === 'linear_scale')
                                        @php
                                            $totalWeight = \App\Models\SurveyQuestion::where('enable_saw', true)->sum('criteria_weight');
                                            $weightNormalized = $totalWeight > 0 ? ($question->criteria_weight / $totalWeight) : 0;
                                            
                                            // Simplified normalization for display
                                            $normalized = ((float)$response->answer) / 10; // Assuming scale 1-10
                                        @endphp
                                        <div class="saw-detail">
                                            ({{ ucfirst($question->criteria_type) }}) | 
                                            Skor: {{ $response->answer }} | 
                                            Bobot: {{ number_format($weightNormalized, 3) }} | 
                                            Nilai Ternormalisasi: {{ number_format($normalized, 3) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if(($index + 1) % 3 == 0 && $index + 1 < $surveysWithSAW->count())
            <div class="page-break"></div>
        @endif
    @endforeach

    @if($surveysWithSAW->count() > 20)
        <div class="info-box" style="margin-top: 20px;">
            <strong>Catatan:</strong> PDF ini menampilkan detail 20 responden pertama. 
            Total responden: {{ $surveysWithSAW->count() }} orang.
        </div>
    @endif

    <div class="page-break"></div>

    {{-- RINGKASAN STATISTIK PER PERTANYAAN --}}
    <div class="section-header">3. RINGKASAN STATISTIK PER PERTANYAAN</div>

    @foreach($questionStats->groupBy('section_name') as $sectionName => $stats)
        <div class="sub-header">{{ $sectionName }}</div>

        @foreach($stats as $stat)
            <div style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-left: 3px solid #3498db;">
                <div style="font-weight: bold; margin-bottom: 5px;">
                    Q{{ $stat['question_id'] }}: {{ $stat['question_text'] }}
                </div>
                
                <div style="font-size: 8pt; color: #666; margin-bottom: 8px;">
                    Tipe: {{ ucfirst(str_replace('_', ' ', $stat['question_type'])) }} | 
                    Total Jawaban: {{ $stat['total_responses'] }} responden
                </div>

                @if(!empty($stat['distribution']))
                    <table style="margin-top: 8px;">
                        <thead>
                            <tr>
                                <th>Pilihan Jawaban</th>
                                <th class="text-center" width="20%">Jumlah</th>
                                <th class="text-center" width="20%">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stat['distribution'] as $answer => $data)
                            <tr>
                                <td>{{ $answer }}</td>
                                <td class="text-center">{{ $data['count'] }}</td>
                                <td class="text-center">{{ $data['percentage'] }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="chart-placeholder" style="padding: 20px; margin-top: 10px;">
                        <strong>Grafik Bar/Pie Chart</strong><br>
                        Visualisasi distribusi jawaban
                    </div>
                @endif

                @if(isset($stat['average']))
                    <div style="margin-top: 8px; font-size: 9pt;">
                        <strong>Statistik:</strong><br>
                        Rata-rata: {{ $stat['average'] }} | 
                        Min: {{ $stat['min'] }} | 
                        Max: {{ $stat['max'] }}
                    </div>
                @endif
            </div>
        @endforeach
    @endforeach

    <div class="page-break"></div>

    {{-- LAMPIRAN --}}
    <div class="section-header">4. LAMPIRAN</div>

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

    {{-- B. Konfigurasi Bobot --}}
    <div class="sub-header">B. Konfigurasi Bobot Kriteria</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="10%">Kode</th>
                <th width="50%">Pertanyaan</th>
                <th class="text-center" width="20%">Tipe Kriteria</th>
                <th class="text-center" width="20%">Bobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sawConfig as $config)
            <tr>
                <td class="text-center">{{ $config['code'] }}</td>
                <td>{{ $config['question'] }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $config['type'] === 'benefit' ? 'baik' : 'cukup' }}">
                        {{ ucfirst($config['type']) }}
                    </span>
                </td>
                <td class="text-center" style="font-weight: bold;">{{ $config['weight'] }}</td>
            </tr>
            @endforeach
            <tr style="background: #ecf0f1; font-weight: bold;">
                <td colspan="3" class="text-right">TOTAL BOBOT</td>
                <td class="text-center">{{ $sawConfig->sum('weight') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Footer --}}
    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #ecf0f1; text-align: center; font-size: 8pt; color: #999;">
        Dokumen ini dibuat secara otomatis oleh sistem pada {{ $generated_at }}<br>
        Laporan ini bersifat rahasia dan hanya untuk keperluan internal
    </div>

</body>
</html>