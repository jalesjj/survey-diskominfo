{{-- resources/views/admin/hasil-survey/laporan-kominfo.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="html; charset=utf-8"/>
    <title>Laporan SKM - {{ $period_label }}</title>
    <style>
        @page {
            margin: 2cm 2cm 2cm 2cm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5pt;
            line-height: 1.6;
            color: #222;
        }
        .page-break { page-break-after: always; }

        /* COVER */
        .cover { text-align: center; padding-top: 100px; }
        .cover-title { font-size: 18pt; font-weight: bold; color: #000; margin-bottom: 8px; line-height: 1.4; }
        .cover-subtitle { font-size: 13pt; color: #333; margin-bottom: 20px; }
        .cover-divider { border: none; border-top: 2px solid #222; width: 55%; margin: 18px auto; }
        .cover-info { font-size: 11pt; color: #333; margin: 7px 0; }
        .cover-instansi { font-size: 12pt; font-weight: bold; color: #000; margin-top: 50px; }
        .cover-tahun { font-size: 11pt; color: #555; margin-top: 5px; }
        .cover-generated { margin-top: 50px; font-size: 8.5pt; color: #888; }

        /* SECTION HEADER */
        .section-header {
            font-size: 11pt; font-weight: bold; color: #000;
            margin: 22px 0 10px 0; padding: 6px 10px;
            border-left: 4px solid #333; background: #f4f4f4;
        }

        /* TABEL */
        table { width: 100%; border-collapse: collapse; margin: 6px 0 16px 0; font-size: 9pt; }
        table th { background: #ebebeb; padding: 7px 8px; border: 1px solid #aaa; font-weight: bold; text-align: left; color: #000; }
        table td { padding: 6px 8px; border: 1px solid #ccc; color: #222; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .font-bold   { font-weight: bold; }
        .row-total td { background: #e8e8e8; font-weight: bold; }
        .row-section-header td { background: #e4e4e4; font-weight: bold; }

        /* INFO BOX */
        .info-box {
            background: #f9f9f9; border: 1px solid #ccc;
            padding: 9px 12px; margin: 8px 0 14px 0; font-size: 9pt;
        }

        /* CHART BAR CSS - gaya referensi */
        .chart-wrap { margin: 8px 0 4px 0; }
        .chart-table { width: 100%; border-collapse: collapse; margin: 0; }
        .chart-table td { border: none; padding: 2px 0; vertical-align: middle; }
        .chart-label { width: 34%; font-size: 8pt; color: #333; text-align: right; padding-right: 8px; white-space: nowrap; }
        .chart-bar-cell { width: 58%; padding: 0; }
        .chart-bar-outer { width: 100%; background: transparent; height: 14px; position: relative; }
        .chart-bar-inner { background: #8B1A1A; height: 14px; display: block; }
        .chart-value { width: 8%; font-size: 7.5pt; color: #555; padding-left: 4px; white-space: nowrap; }
        /* sumbu X bawah */
        .chart-axis-row td { border-top: 1px solid #888; padding: 2px 0 0 0; font-size: 7pt; color: #777; text-align: center; }
        .chart-axis-label-cell { width: 34%; }
        .chart-axis-ticks { width: 58%; }

        /* CHART SKM CSS - kolom */
        .skm-wrap { width: 100%; margin: 8px 0 18px 0; }
        .skm-cols { width: 100%; border-collapse: collapse; }
        .skm-cols td { border: none; padding: 0 4px; vertical-align: bottom; text-align: center; }
        .skm-bar-col { width: 100%; display: block; background: #2563EB; margin: 0 auto; }
        .skm-val { font-size: 7.5pt; color: #333; display: block; text-align: center; padding-bottom: 2px; }
        .skm-label { font-size: 7pt; color: #555; display: block; text-align: center; padding-top: 3px; border-top: 1px solid #ccc; }

        /* FOOTER */
        .footer-note { margin-top: 28px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 8pt; color: #888; text-align: center; }
    </style>
</head>
<body>

{{-- ═══════ COVER ═══════ --}}
<div class="cover">
    <div class="cover-title">LAPORAN HASIL<br>SURVEI KEPUASAN MASYARAKAT</div>
    <div class="cover-subtitle">(SKM)</div>
    <hr class="cover-divider">
    <div class="cover-info">Metode Pengolahan : <strong>Simple Additive Weighting (SAW)</strong></div>
    <div class="cover-info">Periode : <strong>{{ $period_label }}</strong></div>
    <div class="cover-info">Total Responden : <strong>{{ $total_responses }} orang</strong></div>
    <div class="cover-instansi">Dinas Komunikasi dan Informatika</div>
    <div class="cover-tahun">Kabupaten Lamongan &mdash; {{ $tahun }}</div>
    <div class="cover-generated">Digenerate pada: {{ $generated_at }}</div>
</div>

<div class="page-break"></div>


{{-- ═══════ BAB 1 — GRAFIK NILAI PER KRITERIA ═══════ --}}
<div class="section-header">1. Grafik Nilai Per Kriteria</div>

@php
    // urut dari terbesar (atas) ke terkecil (bawah) — sesuai gambar referensi
    $sortedCriteria = collect($criteriaResults)->sortByDesc('normalized')->values();
    $maxNorm = $sortedCriteria->max('normalized') ?: 1;
    $minNorm = $sortedCriteria->min('normalized') ?: 0;

    // Sumbu X: mulai dari sedikit di bawah nilai minimum, akhir di atas maksimum
    // Mirip Excel — tidak mulai dari 0
    $axisMin  = floor($minNorm * 10) / 10;          // e.g. 0.7
    $axisMax  = ceil($maxNorm * 10) / 10;            // e.g. 1.0
    $axisRange = $axisMax - $axisMin ?: 0.1;

    // Tick marks sumbu X
    $step = 0.1;
    $ticks = [];
    for ($t = $axisMin; $t <= $axisMax + 0.001; $t += $step) {
        $ticks[] = round($t, 2);
    }
    $nTicks = count($ticks);
@endphp

<div class="chart-wrap">
<table class="chart-table">
    <tbody>
        @foreach($sortedCriteria as $r)
            @php
                $norm = (float)($r['normalized'] ?? 0);
                // lebar bar relatif terhadap rentang sumbu
                $pct  = $axisRange > 0
                    ? round((($norm - $axisMin) / $axisRange) * 100)
                    : 0;
                $pct  = max(1, min(100, $pct));
            @endphp
            <tr>
                <td class="chart-label">{{ $r['criteria'] }}</td>
                <td class="chart-bar-cell">
                    <div class="chart-bar-outer">
                        <div class="chart-bar-inner" style="width:{{ $pct }}%;"></div>
                    </div>
                </td>
                <td class="chart-value">{{ number_format($norm, 3) }}</td>
            </tr>
        @endforeach

        {{-- Sumbu X dengan tick labels --}}
        <tr class="chart-axis-row">
            <td class="chart-axis-label-cell"></td>
            <td class="chart-axis-ticks">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        @foreach($ticks as $tick)
                            <td style="width:{{ round(100/($nTicks),1) }}%; text-align:center; font-size:7pt; color:#666; border:none; padding:2px 0;">
                                {{ $tick }}
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
</div>


{{-- ═══════ BAB 2 — TABEL KARAKTERISTIK RESPONDEN ═══════ --}}
<div class="section-header">2. Karakteristik Responden</div>

@php
    use Illuminate\Support\Facades\DB;

    $safeTotal = $total_responses > 0 ? $total_responses : 1;

    $getDist = function(string $qid) use ($periodId, $safeTotal) {
        $q = DB::table('survey_responses')
            ->where('question_id', $qid)
            ->whereNotNull('answer')
            ->where('answer', '!=', '');
        if ($periodId) $q->where('period_id', $periodId);
        return $q->select('answer', DB::raw('count(*) as cnt'))
                 ->groupBy('answer')
                 ->orderByDesc('cnt')
                 ->get()
                 ->map(fn($row) => [
                     'label' => $row->answer,
                     'count' => (int)$row->cnt,
                     'pct'   => round($row->cnt / $safeTotal * 100, 1),
                 ])->values()->toArray();
    };

    $layananQ = \App\Models\SurveyQuestion::whereIn('question_type', ['multiple_choice','dropdown','radio'])
        ->where(function($q){
            $q->where('question_text','LIKE','%layanan%')
              ->orWhere('question_text','LIKE','%Layanan%');
        })->first();

    $layananDist = [];
    if ($layananQ) {
        $lq = DB::table('survey_responses')
            ->where('question_id', $layananQ->id)
            ->whereNotNull('answer')->where('answer','!=','');
        if ($periodId) $lq->where('period_id', $periodId);
        $layananDist = $lq->select('answer', DB::raw('count(*) as cnt'))
            ->groupBy('answer')->orderByDesc('cnt')->get()
            ->map(fn($row) => [
                'label' => $row->answer,
                'count' => (int)$row->cnt,
                'pct'   => round($row->cnt / $safeTotal * 100, 1),
            ])->values()->toArray();
    }

    $karakteristik = [
        ['label' => 'Jenis Kelamin', 'data' => $getDist('jenis_kelamin')],
        ['label' => 'Pendidikan',    'data' => $getDist('jenis_pendidikan')],
        ['label' => 'Pekerjaan',     'data' => $getDist('pekerjaan')],
        ['label' => 'Jenis Layanan', 'data' => $layananDist ?: [['label'=>'Tidak ada data','count'=>0,'pct'=>0]]],
    ];
@endphp

<table>
    <thead>
        <tr>
            <th style="width:22%;">Karakteristik</th>
            <th style="width:38%;">Indikator</th>
            <th style="width:20%;" class="text-center">Jumlah</th>
            <th style="width:20%;" class="text-center">Persentase</th>
        </tr>
    </thead>
    <tbody>
        @foreach($karakteristik as $grp)
            @foreach($grp['data'] as $idx => $item)
                <tr>
                    @if($idx === 0)
                        <td class="font-bold" rowspan="{{ count($grp['data']) }}">{{ $grp['label'] }}</td>
                    @endif
                    <td>{{ $item['label'] }}</td>
                    <td class="text-center">{{ $item['count'] }}</td>
                    <td class="text-center">{{ $item['pct'] }}%</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>


{{-- ═══════ BAB 3 — TABEL NILAI KRITERIA ═══════ --}}
<div class="section-header">3. Nilai Kriteria</div>

@php
    function getMutuSKM(float $norm): array {
        if ($norm >= 0.88) return ['A', 'Sangat Baik'];
        if ($norm >= 0.76) return ['B', 'Baik'];
        if ($norm >= 0.65) return ['C', 'Cukup'];
        return ['D', 'Kurang dan Perlu Perbaikan'];
    }
@endphp

<table>
    <thead>
        <tr>
            <th style="width:5%;" class="text-center">No</th>
            <th style="width:36%;">Kriteria</th>
            <th style="width:20%;" class="text-center">Nilai Akhir Kriteria</th>
            <th style="width:10%;" class="text-center">Mutu</th>
            <th style="width:29%;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sortedCriteria as $i => $r)
            @php [$mutu, $mutuLabel] = getMutuSKM((float)($r['normalized'] ?? 0)); @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $r['criteria'] }}</td>
                <td class="text-center font-bold">{{ number_format($r['normalized'] ?? 0, 4) }}</td>
                <td class="text-center font-bold">{{ $mutu }}</td>
                <td>{{ $mutu }} ({{ $mutuLabel }})</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="info-box">
    <strong>Keterangan Mutu:</strong>
    &nbsp;A = Sangat Baik (&ge;0,88)
    &nbsp;|&nbsp; B = Baik (0,76&ndash;0,87)
    &nbsp;|&nbsp; C = Cukup (0,65&ndash;0,75)
    &nbsp;|&nbsp; D = Kurang dan Perlu Perbaikan (&lt;0,65)
</div>


{{-- ═══════ BAB 4 — GRAFIK SKM HISTORIS ═══════ --}}
<div class="page-break"></div>
<div class="section-header">4. Grafik Nilai SKM Historis</div>

@php
    $histori = \App\Models\SurveyPeriod::orderBy('year','asc')->orderBy('id','asc')
        ->get()
        ->map(function($p){
            $vi = \App\Models\SAWCalculationResult::where('period_id',$p->id)->sum('weighted_score');
            return ['label' => $p->period_name . ' ' . $p->year, 'vi' => round((float)$vi, 4)];
        })
        ->filter(fn($p) => $p['vi'] > 0)
        ->values()
        ->take(8);

    $nH     = $histori->count();
    $maxVi  = $histori->max('vi') ?: 1;
    $colPct = $nH > 0 ? floor(100 / $nH) : 100;
    // tinggi kolom maks = 80px (proporsional)
    $maxBarH = 80;
@endphp

@if($nH > 0)
<table class="skm-cols">
    <tbody>
        {{-- baris nilai --}}
        <tr>
            @foreach($histori as $p)
                @php $barH = max(4, round(($p['vi'] / $maxVi) * $maxBarH)); @endphp
                <td style="width:{{ $colPct }}%; vertical-align:bottom; text-align:center; padding:0 3px;">
                    <span class="skm-val">{{ number_format($p['vi'], 3) }}</span>
                    <div style="width:60%; background:#2563EB; height:{{ $barH }}px; margin:0 auto;"></div>
                </td>
            @endforeach
        </tr>
        {{-- baris label --}}
        <tr style="border-top: 1px solid #ccc;">
            @foreach($histori as $p)
                @php $parts = explode(' ', $p['label'], 2); @endphp
                <td style="width:{{ $colPct }}%; text-align:center; padding:3px 2px 0 2px; font-size:7.5pt; color:#555;">
                    {{ $parts[0] ?? '' }}<br>{{ $parts[1] ?? '' }}
                </td>
            @endforeach
        </tr>
    </tbody>
</table>
@else
    <div class="info-box">Data historis belum tersedia.</div>
@endif


{{-- ═══════ BAB 5 — HASIL PENGELOLAHAN DATA SKM ═══════ --}}
<div class="section-header">5. Hasil Pengelolahan Data SKM</div>

<table>
    <thead>
        <tr>
            <th style="width:5%;"  class="text-center">No</th>
            <th style="width:30%;">Kriteria</th>
            <th style="width:16%;" class="text-center">Nilai Kriteria (r)</th>
            <th style="width:13%;" class="text-center">Bobot (w)</th>
            <th style="width:17%;" class="text-center">Nilai Terbobot (w&times;r)</th>
            <th style="width:19%;" class="text-center">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sortedCriteria as $i => $r)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $r['criteria'] }}</td>
            <td class="text-center">{{ number_format($r['normalized'] ?? 0, 4) }}</td>
            <td class="text-center">{{ number_format($r['weight_normalized'] ?? 0, 4) }}</td>
            <td class="text-center font-bold">{{ number_format($r['weighted_score'] ?? 0, 4) }}</td>
            <td class="text-center">{{ $r['interpretation'] ?? '-' }}</td>
        </tr>
        @endforeach
        @php
            $totalInt = $totalVi >= 0.9 ? 'Excellent'
                : ($totalVi >= 0.8 ? 'Sangat Baik'
                : ($totalVi >= 0.6 ? 'Baik'
                : ($totalVi >= 0.4 ? 'Cukup' : 'Perlu Perbaikan')));
        @endphp
        <tr class="row-total">
            <td colspan="4" class="text-right">Total Nilai SAW (&Sigma;V<sub>i</sub>)</td>
            <td class="text-center">{{ number_format($totalVi, 4) }}</td>
            <td class="text-center">{{ $totalInt }}</td>
        </tr>
    </tbody>
</table>

<div class="info-box">
    <strong>Kesimpulan:</strong>
    Berdasarkan hasil perhitungan menggunakan metode Simple Additive Weighting (SAW),
    total nilai preferensi SKM periode <strong>{{ $period_label }}</strong>
    adalah <strong>{{ number_format($totalVi, 4) }}</strong>
    dengan kategori <strong>{{ $totalInt }}</strong>,
    diikuti oleh <strong>{{ $total_responses }} responden</strong>.
</div>


{{-- ═══════ LAMPIRAN — DAFTAR PERTANYAAN ═══════ --}}
<div class="page-break"></div>
<div class="section-header">Lampiran: Daftar Pertanyaan Survey</div>

@php
    use App\Helpers\SurveyDefaults;

    $defSection   = SurveyDefaults::getDefaultSection();
    $defQuestions = collect(SurveyDefaults::getDefaultQuestions())
        ->filter(fn($q) => is_array($q) ? ($q['is_active'] ?? true) : ($q->is_active ?? true))
        ->values();
    $defSection->questions = $defQuestions;

    $allSections = collect([$defSection])->merge($sections);
    $no = 1;
@endphp

<table>
    <thead>
        <tr>
            <th style="width:8%;" class="text-center">No</th>
            <th>Pertanyaan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allSections as $sec)
            @php
                $secTitle     = is_object($sec) ? $sec->title : ($sec['title'] ?? '');
                $secQuestions = is_object($sec) ? ($sec->questions ?? collect()) : ($sec['questions'] ?? []);
            @endphp
            <tr class="row-section-header">
                <td colspan="2">{{ $secTitle }}</td>
            </tr>
            @foreach($secQuestions as $q)
                @php $qText = is_array($q) ? ($q['question_text'] ?? '') : ($q->question_text ?? ''); @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $qText }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<div class="footer-note">
    Dokumen ini digenerate otomatis oleh sistem pada {{ $generated_at }} &mdash;
    Laporan SKM {{ $period_label }}
</div>

</body>
</html>