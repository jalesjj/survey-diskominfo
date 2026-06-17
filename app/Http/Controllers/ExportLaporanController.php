<?php
// app/Http/Controllers/ExportLaporanController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveySection;
use App\Models\SurveyPeriod;
use App\Models\SAWCalculationResult;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportLaporanController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // HELPERS PRIVATE
    // ══════════════════════════════════════════════════════════════════════════

    private function checkAdminAuth()
    {
        if (!session('admin_id') && !session('admin_user') && !session('admin')) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }
        return null;
    }

    /**
     * Ambil SAW results untuk periode tertentu dari DB (sudah dihitung sebelumnya)
     */
    private function getSAWResults(?int $periodId): \Illuminate\Support\Collection
    {
        if ($periodId) {
            return SAWCalculationResult::where('period_id', $periodId)->get();
        }
        // Fallback: ambil dari semua periode aktif
        $activePeriod = SurveyPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            return SAWCalculationResult::where('period_id', $activePeriod->id)->get();
        }
        return collect();
    }

    /**
     * Mapping SAW results ke array standar
     */
    private function mapCriteriaResults(\Illuminate\Support\Collection $sawResults): \Illuminate\Support\Collection
    {
        return $sawResults->sortByDesc('normalized_score')->map(function ($r) {
            return [
                'criteria'          => $r->criteria_name,
                'normalized'        => (float) $r->normalized_score,
                'weight_normalized' => (float) $r->weight_normalized,
                'weighted_score'    => (float) $r->weighted_score,
                'interpretation'    => $r->interpretation,
                'criteria_type'     => $r->criteria_type,
            ];
        })->values();
    }

    /**
     * Ambil total responden untuk periode
     */
    private function getTotalResponden(?int $periodId): int
    {
        $q = DB::table('survey_responses')->distinct('survey_id');
        if ($periodId) $q->where('period_id', $periodId);
        return $q->count('survey_id');
    }

    /**
     * Ambil distribusi jawaban per question_id
     */
    private function getDistribusi(string $questionId, ?int $periodId, int $safeTotal): array
    {
        $q = DB::table('survey_responses')
            ->where('question_id', $questionId)
            ->whereNotNull('answer')
            ->where('answer', '!=', '');
        if ($periodId) $q->where('period_id', $periodId);
        return $q->select('answer', DB::raw('count(*) as cnt'))
            ->groupBy('answer')
            ->orderByDesc('cnt')
            ->get()
            ->map(fn($row) => [
                'label' => $row->answer,
                'count' => (int) $row->cnt,
                'pct'   => round($row->cnt / $safeTotal * 100, 1),
            ])->values()->toArray();
    }

    /**
     * Bangun data karakteristik responden
     */
    private function buildKarakteristik(?int $periodId, int $totalResponden): array
    {
        $safe = $totalResponden > 0 ? $totalResponden : 1;

        $layananQ = SurveyQuestion::whereIn('question_type', ['multiple_choice', 'dropdown', 'radio'])
            ->where(function ($q) {
                $q->where('question_text', 'LIKE', '%layanan%')
                  ->orWhere('question_text', 'LIKE', '%Layanan%');
            })->first();

        $layananDist = [];
        if ($layananQ) {
            $layananDist = $this->getDistribusi($layananQ->id, $periodId, $safe);
        }

        return [
            ['label' => 'Jenis Kelamin', 'data' => $this->getDistribusi('jenis_kelamin', $periodId, $safe)],
            ['label' => 'Pendidikan',    'data' => $this->getDistribusi('jenis_pendidikan', $periodId, $safe)],
            ['label' => 'Pekerjaan',     'data' => $this->getDistribusi('pekerjaan', $periodId, $safe)],
            // ['label' => 'Jenis Layanan', 'data' => $layananDist ?: [['label' => 'Tidak ada data', 'count' => 0, 'pct' => 0]]],
        ];
    }

    /**
     * Ambil sections untuk lampiran pertanyaan
     */
    private function getSections(): \Illuminate\Support\Collection
    {
        return SurveySection::where('is_active', true)
            ->with(['allQuestions' => fn($q) => $q->where('is_active', true)->orderBy('order_index')])
            ->orderBy('order_index')
            ->get()
            ->filter(fn($s) => $s->allQuestions->count() > 0)
            ->map(function ($s) {
                $s->questions = $s->allQuestions;
                return $s;
            })->values();
    }

    /**
     * Mutu SAW
     */
    private function getMutu(float $norm): array
    {
        if ($norm >= 0.88) return ['A', 'Sangat Baik'];
        if ($norm >= 0.76) return ['B', 'Baik'];
        if ($norm >= 0.65) return ['C', 'Cukup'];
        return ['D', 'Kurang dan Perlu Perbaikan'];
    }

    /**
     * Interpretasi total Vi
     */
    private function getInterpretasiTotal(float $vi): string
    {
        if ($vi >= 0.9) return 'Excellent';
        if ($vi >= 0.8) return 'Sangat Baik';
        if ($vi >= 0.6) return 'Baik';
        if ($vi >= 0.4) return 'Cukup';
        return 'Perlu Perbaikan';
    }

    /**
     * Resolve periode dari request
     */
    private function resolvePeriod(?string $periodId): ?SurveyPeriod
    {
        if ($periodId) return SurveyPeriod::find($periodId);
        return SurveyPeriod::where('is_active', true)->first()
            ?? SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->first();
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EXPORT PDF — LAPORAN KOMINFO
    // ══════════════════════════════════════════════════════════════════════════

    public function exportLaporan(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $selectedPeriod  = $this->resolvePeriod($request->get('period_id'));
        $periodId        = $selectedPeriod?->id;
        $sawResults      = $this->getSAWResults($periodId);
        $criteriaResults = $this->mapCriteriaResults($sawResults);
        $totalVi         = $criteriaResults->sum('weighted_score');
        $totalResponden  = $this->getTotalResponden($periodId);
        $sections        = $this->getSections();
        $period_label    = $selectedPeriod
            ? $selectedPeriod->period_name . ' ' . $selectedPeriod->year
            : 'Semua Periode';

        $data = [
            'period_label'    => $period_label,
            'tahun'           => $selectedPeriod?->year ?? date('Y'),
            'generated_at'    => now()->format('d F Y H:i:s'),
            'total_responses' => $totalResponden,
            'selected_period' => $selectedPeriod,
            'periodId'        => $periodId,
            'criteriaResults' => $criteriaResults,
            'totalVi'         => $totalVi,
            'sections'        => $sections,
        ];

        $pdf = Pdf::loadView('admin.hasil-survey.laporan-kominfo', $data);
        $pdf->setPaper('a4', 'portrait');

        $suffix   = $selectedPeriod
            ? '_' . $selectedPeriod->year . '_' . str_replace(' ', '-', $selectedPeriod->period_name)
            : '';
        $filename = 'Laporan_SKM_Kominfo' . $suffix . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EXPORT EXCEL — LAPORAN KOMINFO
    // Sheet 1: Karakteristik Responden
    // Sheet 2: Nilai Kriteria
    // Sheet 3: Hasil Pengelolahan Data SKM
    // Sheet 4: Lampiran Daftar Pertanyaan
    // ══════════════════════════════════════════════════════════════════════════

    public function exportLaporanExcel(Request $request)
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $selectedPeriod  = $this->resolvePeriod($request->get('period_id'));
        $periodId        = $selectedPeriod?->id;
        $sawResults      = $this->getSAWResults($periodId);
        $criteriaResults = $this->mapCriteriaResults($sawResults);
        $totalVi         = $criteriaResults->sum('weighted_score');
        $totalResponden  = $this->getTotalResponden($periodId);
        $sections        = $this->getSections();
        $karakteristik   = $this->buildKarakteristik($periodId, $totalResponden);
        $period_label    = $selectedPeriod
            ? $selectedPeriod->period_name . ' ' . $selectedPeriod->year
            : 'Semua Periode';
        $totalInt        = $this->getInterpretasiTotal($totalVi);

        // ── Style Helpers ─────────────────────────────────────────────────────
        $styleHeader = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial', 'size' => 11],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];

        $styleData = [
            'font'      => ['name' => 'Arial', 'size' => 10],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
        ];

        $styleTitle = [
            'font'      => ['bold' => true, 'name' => 'Arial', 'size' => 13, 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ];

        $styleSubHeader = [
            'font'      => ['bold' => true, 'name' => 'Arial', 'size' => 10],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EDF5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];

        $styleTotal = [
            'font'      => ['bold' => true, 'name' => 'Arial', 'size' => 10],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F4F8']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setTitle('Laporan SKM ' . $period_label)->setCreator('Sistem Survey');

        // ── SHEET 1: KARAKTERISTIK RESPONDEN ─────────────────────────────────
        $s1 = $spreadsheet->getActiveSheet()->setTitle('Karakteristik Responden');
        $s1->setCellValue('A1', 'KARAKTERISTIK RESPONDEN');
        $s1->setCellValue('A2', 'Periode: ' . $period_label . ' | Total Responden: ' . $totalResponden);
        $s1->getStyle('A1')->applyFromArray($styleTitle);
        $s1->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setName('Arial');
        $s1->mergeCells('A1:D1');
        $s1->mergeCells('A2:D2');

        $row = 4;
        foreach (['A' => 'Karakteristik', 'B' => 'Indikator', 'C' => 'Jumlah', 'D' => 'Persentase'] as $col => $label) {
            $s1->setCellValue($col . $row, $label);
        }
        $s1->getStyle('A' . $row . ':D' . $row)->applyFromArray($styleHeader);
        $s1->getRowDimension($row)->setRowHeight(20);
        $row++;

        foreach ($karakteristik as $grp) {
            $startRow = $row;
            foreach ($grp['data'] as $item) {
                $s1->setCellValue('B' . $row, $item['label']);
                $s1->setCellValue('C' . $row, $item['count']);
                $s1->setCellValue('D' . $row, $item['pct'] . '%');
                $s1->getStyle('B' . $row . ':D' . $row)->applyFromArray($styleData);
                $s1->getStyle('C' . $row . ':D' . $row)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $s1->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
            if ($row - $startRow > 1) {
                $s1->mergeCells('A' . $startRow . ':A' . ($row - 1));
            }
            $s1->setCellValue('A' . $startRow, $grp['label']);
            $s1->getStyle('A' . $startRow . ':A' . ($row - 1))->applyFromArray($styleData);
            $s1->getStyle('A' . $startRow . ':A' . ($row - 1))->getFont()->setBold(true);
            $s1->getStyle('A' . $startRow . ':A' . ($row - 1))->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        $s1->getColumnDimension('A')->setWidth(22);
        $s1->getColumnDimension('B')->setWidth(28);
        $s1->getColumnDimension('C')->setWidth(12);
        $s1->getColumnDimension('D')->setWidth(14);

        // ── SHEET 2: NILAI KRITERIA ───────────────────────────────────────────
        $s2 = $spreadsheet->createSheet()->setTitle('Nilai Kriteria');
        $s2->setCellValue('A1', 'NILAI KRITERIA');
        $s2->setCellValue('A2', 'Periode: ' . $period_label);
        $s2->getStyle('A1')->applyFromArray($styleTitle);
        $s2->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setName('Arial');
        $s2->mergeCells('A1:E1');
        $s2->mergeCells('A2:E2');

        $row = 4;
        foreach (['A' => 'No', 'B' => 'Kriteria', 'C' => 'Nilai Akhir Kriteria', 'D' => 'Mutu', 'E' => 'Keterangan'] as $col => $label) {
            $s2->setCellValue($col . $row, $label);
        }
        $s2->getStyle('A' . $row . ':E' . $row)->applyFromArray($styleHeader);
        $s2->getRowDimension($row)->setRowHeight(20);
        $row++;

        foreach ($criteriaResults as $i => $r) {
            [$mutu, $mutuLabel] = $this->getMutu((float)($r['normalized'] ?? 0));
            $s2->setCellValue('A' . $row, $i + 1);
            $s2->setCellValue('B' . $row, $r['criteria']);
            $s2->setCellValue('C' . $row, number_format($r['normalized'] ?? 0, 4));
            $s2->setCellValue('D' . $row, $mutu);
            $s2->setCellValue('E' . $row, $mutuLabel);
            $s2->getStyle('A' . $row . ':E' . $row)->applyFromArray($styleData);
            $s2->getStyle('A' . $row . ':D' . $row)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $s2->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        $row++;
        $s2->setCellValue('A' . $row, 'Keterangan:');
        $s2->setCellValue('B' . $row, 'A = Sangat Baik (≥0.88) | B = Baik (0.76–0.87) | C = Cukup (0.65–0.75) | D = Kurang dan Perlu Perbaikan (<0.65)');
        $s2->mergeCells('B' . $row . ':E' . $row);
        $s2->getStyle('A' . $row)->getFont()->setBold(true)->setName('Arial')->setSize(9);
        $s2->getStyle('B' . $row)->getFont()->setItalic(true)->setName('Arial')->setSize(9);

        $s2->getColumnDimension('A')->setWidth(6);
        $s2->getColumnDimension('B')->setWidth(35);
        $s2->getColumnDimension('C')->setWidth(22);
        $s2->getColumnDimension('D')->setWidth(8);
        $s2->getColumnDimension('E')->setWidth(35);

        // ── SHEET 3: HASIL PENGELOLAHAN DATA SKM ─────────────────────────────
        $s3 = $spreadsheet->createSheet()->setTitle('Hasil Pengelolahan SKM');
        $s3->setCellValue('A1', 'HASIL PENGELOLAHAN DATA SKM');
        $s3->setCellValue('A2', 'Periode: ' . $period_label . ' | Total Responden: ' . $totalResponden);
        $s3->getStyle('A1')->applyFromArray($styleTitle);
        $s3->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setName('Arial');
        $s3->mergeCells('A1:F1');
        $s3->mergeCells('A2:F2');

        $row = 4;
        foreach (['A' => 'No', 'B' => 'Kriteria', 'C' => 'Nilai Ternormalisasi', 'D' => 'Bobot', 'E' => 'Nilai Akhir Kriteria', 'F' => 'Keterangan'] as $col => $label) {
            $s3->setCellValue($col . $row, $label);
        }
        $s3->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleHeader);
        $s3->getRowDimension($row)->setRowHeight(20);
        $row++;

        foreach ($criteriaResults as $i => $r) {
            $s3->setCellValue('A' . $row, $i + 1);
            $s3->setCellValue('B' . $row, $r['criteria']);
            $s3->setCellValue('C' . $row, number_format($r['normalized'] ?? 0, 4));
            $s3->setCellValue('D' . $row, number_format($r['weight_normalized'] ?? 0, 4));
            $s3->setCellValue('E' . $row, number_format($r['weighted_score'] ?? 0, 4));
            $s3->setCellValue('F' . $row, $r['interpretation'] ?? '-');
            $s3->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleData);
            $s3->getStyle('A' . $row . ':E' . $row)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $s3->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        $s3->setCellValue('A' . $row, '');
        $s3->setCellValue('B' . $row, 'Total Nilai SAW');
        $s3->setCellValue('C' . $row, '');
        $s3->setCellValue('D' . $row, '');
        $s3->setCellValue('E' . $row, number_format($totalVi, 4));
        $s3->setCellValue('F' . $row, $totalInt);
        $s3->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleTotal);
        $s3->getStyle('B' . $row)->getFont()->setBold(true);
        $s3->getRowDimension($row)->setRowHeight(20);

        $s3->getColumnDimension('A')->setWidth(6);
        $s3->getColumnDimension('B')->setWidth(35);
        $s3->getColumnDimension('C')->setWidth(20);
        $s3->getColumnDimension('D')->setWidth(14);
        $s3->getColumnDimension('E')->setWidth(22);
        $s3->getColumnDimension('F')->setWidth(20);

        // ── SHEET 4: LAMPIRAN DAFTAR PERTANYAAN ──────────────────────────────
        $s4 = $spreadsheet->createSheet()->setTitle('Lampiran Pertanyaan');
        $s4->setCellValue('A1', 'LAMPIRAN: DAFTAR PERTANYAAN SURVEY');
        $s4->setCellValue('A2', 'Periode: ' . $period_label);
        $s4->getStyle('A1')->applyFromArray($styleTitle);
        $s4->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setName('Arial');
        $s4->mergeCells('A1:B1');
        $s4->mergeCells('A2:B2');

        $row = 4;
        $s4->setCellValue('A' . $row, 'No');
        $s4->setCellValue('B' . $row, 'Pertanyaan');
        $s4->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleHeader);
        $s4->getRowDimension($row)->setRowHeight(20);
        $row++;

        $no = 1;

        // Default questions (Data Diri)
        $s4->setCellValue('A' . $row, 'Data Diri');
        $s4->mergeCells('A' . $row . ':B' . $row);
        $s4->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleSubHeader);
        $s4->getRowDimension($row)->setRowHeight(18);
        $row++;

        $defaultQuestions = collect(config('survey_defaults.default_questions', []))
            ->filter(fn($q) => is_array($q) ? ($q['is_active'] ?? true) : ($q->is_active ?? true));

        foreach ($defaultQuestions as $q) {
            $qText = is_array($q) ? ($q['question_text'] ?? '') : ($q->question_text ?? '');
            $s4->setCellValue('A' . $row, $no++);
            $s4->setCellValue('B' . $row, $qText);
            $s4->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleData);
            $s4->getStyle('A' . $row)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $s4->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        foreach ($sections as $sec) {
            $s4->setCellValue('A' . $row, $sec->title);
            $s4->mergeCells('A' . $row . ':B' . $row);
            $s4->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleSubHeader);
            $s4->getRowDimension($row)->setRowHeight(18);
            $row++;

            foreach ($sec->questions as $q) {
                $s4->setCellValue('A' . $row, $no++);
                $s4->setCellValue('B' . $row, $q->question_text);
                $s4->getStyle('A' . $row . ':B' . $row)->applyFromArray($styleData);
                $s4->getStyle('A' . $row)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $s4->getRowDimension($row)->setRowHeight(18);
                $row++;
            }
        }

        $s4->getColumnDimension('A')->setWidth(8);
        $s4->getColumnDimension('B')->setWidth(60);

        // ── Output ────────────────────────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);

        $suffix   = $selectedPeriod
            ? '_' . $selectedPeriod->year . '_' . str_replace(' ', '-', $selectedPeriod->period_name)
            : '';
        $filename = 'Laporan_SKM_Kominfo' . $suffix . '_' . now()->format('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}