{{-- resources/views/admin/hasil-survey/export-pdf-saw-respondent.blade.php --}}

<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan SAW Per Responden</title>


<style>
    @page {
        size: A4 portrait;
        margin: 20mm 15mm;
    }

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }

    body{
        font-family:'DejaVu Sans', sans-serif;
        font-size:9pt;
        color:#333;
        line-height:1.4;
    }

    .header{
        text-align:center;
        margin-bottom:18px;
        padding-bottom:10px;
        border-bottom:2px solid #444;
    }

    .header h1{
        font-size:15pt;
        font-weight:bold;
        text-transform:uppercase;
        margin-bottom:4px;
    }

    .header p{
        font-size:8pt;
        color:#666;
    }

    .meta{
        margin-bottom:15px;
        padding:8px 10px;
        background:#f7f7f7;
        border:1px solid #ddd;
        font-size:8.5pt;
    }

    table{
        width:100%;
        border-collapse:collapse;
        margin-bottom:15px;
        font-size:8pt;
    }

    thead{
        background:#4a4a4a;
        color:#fff;
    }

    th{
        border:1px solid #666;
        padding:6px 4px;
        text-align:center;
        font-weight:bold;
    }

    td{
        border:1px solid #d5d5d5;
        padding:5px;
        vertical-align:middle;
    }

    tbody tr:nth-child(even){
        background:#fafafa;
    }

    .c{
        text-align:center;
    }

    .score{
        font-weight:bold;
    }

    .email{
        font-size:7.5pt;
    }

    .note{
        margin-top:10px;
        padding:8px;
        background:#f9f9f9;
        border-left:3px solid #999;
        font-size:8pt;
        color:#555;
    }

    .signature{
        width:250px;
        margin-left:auto;
        margin-top:45px;
        text-align:center;
        font-size:9pt;
    }

    .signature .date{
        margin-bottom:10px;
    }

    .signature .label{
        margin-bottom:60px;
    }

    .signature .name{
        border-top:1px solid #333;
        padding-top:5px;
        font-weight:bold;
    }

    .footer{
        margin-top:25px;
        text-align:center;
        font-size:7.5pt;
        color:#777;
    }
</style>


</head>
<body>

{{-- HEADER --}}
<div class="header">
    <h1>Laporan Hasil SAW Per Responden</h1>
    <p>Metode Simple Additive Weighting (SAW)</p>
    <p>Periode: {{ $periodLabel }}</p>
    <p>Dicetak: {{ $generatedAt }}</p>
</div>

{{-- META --}}
<div class="meta">
    Total Responden:
    <strong>{{ count($respondentRows) }} Orang</strong>

    &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;

    Jumlah Kriteria:
    <strong>{{ $totalCriteria }}</strong>
</div>

{{-- TABEL --}}
@if(count($respondentRows) > 0)

<table>
    <thead>
        <tr>
            <th width="4%">No</th>
            <th width="15%">Nama</th>
            <th width="18%">Email</th>
            <th width="10%">Jenis Kelamin</th>
            <th width="6%">Umur</th>
            <th width="12%">Pendidikan</th>
            <th width="14%">Pekerjaan</th>
            <th width="9%">Skor</th>
            <th width="12%">Keterangan</th>
        </tr>
    </thead>

    <tbody>
        @foreach($respondentRows as $i => $row)
        <tr>
            <td class="c">
                {{ $i + 1 }}
            </td>

            <td>
                {{ $row['nama'] }}
            </td>

            <td class="email">
                {{ $row['email'] }}
            </td>

            <td class="c">
                {{ $row['jenis_kelamin'] }}
            </td>

            <td class="c">
                {{ $row['umur'] }}
            </td>

            <td class="c">
                {{ $row['jenis_pendidikan'] }}
            </td>

            <td>
                {{ $row['pekerjaan'] }}
            </td>

            <td class="c score">
                {{ number_format($row['vi'], 3) }}
            </td>

            <td class="c">
                {{ $row['interpretation'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@else

<div style="text-align:center;padding:30px;color:#888;">
    Belum ada data responden dengan nilai SAW untuk periode ini.
</div>

@endif

{{-- KETERANGAN --}}
<div class="note">
    <strong>Kategori Nilai SAW:</strong><br>

    Sangat Baik ≥ 0.900 |
    Baik 0.800 - 0.899 |
    Cukup 0.600 - 0.799 |
    Kurang 0.400 - 0.599 |
    Sangat Kurang &lt; 0.400
</div>

{{-- TANDA TANGAN --}}
<div class="signature">
    <div class="label">
        Mengetahui,
    </div>

    <div class="name">
        ( ________________________ )
    </div>
</div>

{{-- FOOTER --}}
<div class="footer">
    Laporan Hasil Perhitungan Metode Simple Additive Weighting (SAW)
</div>


</body>
</html>
