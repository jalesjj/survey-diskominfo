{{-- resources/views/admin/hasil-survey/export-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }

        /* Cover Page */
        .cover-page {
            text-align: center;
            padding: 100px 40px;
        }

        .cover-title {
            font-size: 28pt;
            font-weight: bold;
            margin-bottom: 40px;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cover-info {
            font-size: 12pt;
            margin: 15px 0;
            color: #555;
        }

        .cover-info strong {
            color: #000;
        }

        .cover-date {
            margin-top: 60px;
            font-size: 11pt;
            color: #666;
        }

        /* Section Headers */
        .section-header {
            background: #2c3e50;
            color: white;
            padding: 12px 15px;
            margin: 30px 0 15px 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .sub-header {
            background: #34495e;
            color: white;
            padding: 8px 15px;
            margin: 20px 0 10px 0;
            font-size: 11pt;
            font-weight: bold;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }

        table th {
            background: #ecf0f1;
            padding: 8px 6px;
            border: 1px solid #bdc3c7;
            font-weight: bold;
            text-align: left;
            font-size: 9pt;
        }

        table td {
            padding: 7px 6px;
            border: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-sangat-baik {
            background: #27ae60;
            color: white;
        }

        .badge-baik {
            background: #3498db;
            color: white;
        }

        .badge-cukup {
            background: #f39c12;
            color: white;
        }

        .badge-kurang {
            background: #e74c3c;
            color: white;
        }

        .badge-sangat-kurang {
            background: #c0392b;
            color: white;
        }

        /* Rank Badge */
        .rank-badge {
            background: #f39c12;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8pt;
        }

        /* Stats Box */
        .stats-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }

        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            background: #ecf0f1;
            border: 1px solid #bdc3c7;
            text-align: center;
        }

        .stat-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Info Box */
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin: 15px 0;
            font-size: 9pt;
            line-height: 1.5;
        }

        /* Responden Detail */
        .responden-box {
            background: #f8f9fa;
            padding: 12px;
            margin: 15px 0;
            border-left: 4px solid #3498db;
        }

        .responden-header {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .responden-meta {
            font-size: 8pt;
            color: #666;
            margin-bottom: 10px;
        }

        .question-item {
            margin: 10px 0;
            padding-left: 10px;
        }

        .question-label {
            font-weight: bold;
            color: #34495e;
            margin-bottom: 3px;
            font-size: 9pt;
        }

        .answer-text {
            color: #555;
            padding-left: 15px;
            font-size: 9pt;
        }

        .saw-detail {
            font-size: 8pt;
            color: #7f8c8d;
            font-style: italic;
            padding-left: 15px;
            margin-top: 2px;
        }

        /* Section Divider */
        .section-divider {
            border-bottom: 2px solid #ecf0f1;
            margin: 20px 0;
        }

        /* Chart placeholders */
        .chart-placeholder {
            background: #f8f9fa;
            border: 2px dashed #bdc3c7;
            padding: 40px;
            text-align: center;
            margin: 15px 0;
            color: #7f8c8d;
            font-style: italic;
        }

        /* Footer */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #999;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }

        /* Formulas */
        .formula-box {
            background: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
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
            <strong>Periode:</strong> {{ $period_start }} - {{ $period_end }}
        </div>
        
        <div class="cover-info">
            <strong>Total Responden:</strong> {{ $total_responses }} orang
        </div>
        
        <div class="cover-date">
            <strong>Tanggal Generate:</strong><br>
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

    {{-- A. Tabel Rangking Hasil SAW --}}
    <div class="sub-header">A. Tabel Rangking Hasil SAW</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="8%">Rank</th>
                <th width="30%">Nama Responden</th>
                <th width="30%">Email</th>
                <th class="text-center" width="15%">Skor SAW</th>
                <th class="text-center" width="17%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($surveysWithSAW as $index => $survey)
            <tr>
                <td class="text-center">
                    <span class="rank-badge">{{ $index + 1 }}</span>
                </td>
                <td>{{ $survey->nama }}</td>
                <td style="font-size: 8pt;">{{ Str::limit($survey->responses->firstWhere('question.question_text', 'like', '%email%')?->answer ?? '-', 35) }}</td>
                <td class="text-center" style="font-weight: bold;">{{ number_format($survey->saw_score, 4) }}</td>
                <td class="text-center">
                    @php
                        $interpretation = $survey->saw_score >= 0.9 ? 'Sangat Baik' : 
                                        ($survey->saw_score >= 0.8 ? 'Baik' : 
                                        ($survey->saw_score >= 0.6 ? 'Cukup' : 
                                        ($survey->saw_score >= 0.4 ? 'Kurang' : 'Sangat Kurang')));
                    @endphp
                    <span class="badge badge-{{ strtolower(str_replace(' ', '-', $interpretation)) }}">
                        {{ $interpretation }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- B. Grafik Distribusi Skor SAW --}}
    <div class="sub-header">B. Grafik Distribusi Skor SAW</div>
    
    <div class="chart-placeholder">
        <strong>Bar Chart: Distribusi Skor SAW</strong><br>
        [Grafik menampilkan skor SAW untuk setiap responden]
    </div>

    <div class="chart-placeholder">
        <strong>Pie Chart: Kategori Hasil SAW</strong><br>
        @php
            $categories = [
                'Sangat Baik' => $surveysWithSAW->filter(fn($s) => $s->saw_score >= 0.9)->count(),
                'Baik' => $surveysWithSAW->filter(fn($s) => $s->saw_score >= 0.8 && $s->saw_score < 0.9)->count(),
                'Cukup' => $surveysWithSAW->filter(fn($s) => $s->saw_score >= 0.6 && $s->saw_score < 0.8)->count(),
                'Kurang' => $surveysWithSAW->filter(fn($s) => $s->saw_score < 0.6)->count(),
            ];
        @endphp
        @foreach($categories as $cat => $count)
            {{ $cat }}: {{ $count }} ({{ $total_responses > 0 ? round(($count/$total_responses)*100, 1) : 0 }}%) |
        @endforeach
    </div>

    {{-- C. Statistik SAW --}}
    <div class="sub-header">C. Statistik SAW</div>
    
    <table>
        <tr>
            <td width="30%" style="font-weight: bold;">Skor Tertinggi</td>
            <td>{{ number_format($surveysWithSAW->max('saw_score'), 4) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Skor Terendah</td>
            <td>{{ number_format($surveysWithSAW->min('saw_score'), 4) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Rata-rata</td>
            <td>{{ number_format($surveysWithSAW->avg('saw_score'), 4) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Median</td>
            <td>{{ number_format($surveysWithSAW->median('saw_score'), 4) }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- TABEL KRITERIA SAW --}}
    <div class="sub-header">D. Tabel Kriteria SAW</div>

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

    {{-- A. Informasi Responden --}}
    <div class="sub-header">A. Informasi Responden</div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="25%">Nama</th>
                <th width="30%">Email</th>
                <th class="text-center" width="20%">Tanggal Submit</th>
                <th class="text-center" width="10%">Skor SAW</th>
                <th class="text-center" width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($surveysWithSAW as $index => $survey)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $survey->nama }}</td>
                <td style="font-size: 8pt;">{{ Str::limit($survey->responses->firstWhere('question.question_text', 'like', '%email%')?->answer ?? '-', 30) }}</td>
                <td class="text-center" style="font-size: 8pt;">{{ $survey->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-center" style="font-weight: bold;">{{ number_format($survey->saw_score, 4) }}</td>
                <td class="text-center">
                    <span class="badge badge-baik">Complete</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- B. Detail Jawaban per Responden --}}
    <div class="sub-header">B. Detail Jawaban per Responden</div>

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