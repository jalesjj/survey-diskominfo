<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveyPeriod;
use App\Models\SAWCalculationResult;

class DashboardController extends Controller
{
    /**
     * Halaman Dashboard Baru dengan 3 Card Statistik
     */
    public function index(Request $request)
    {
        // Cek autentikasi admin
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        // Ambil parameter period_id dari request
        $periodId = $request->get('period_id');
        
        // Ambil semua periode untuk dropdown
        $allPeriods = SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->get();
        
        // Tentukan periode yang dipilih
        if ($periodId) {
            $selectedPeriod = SurveyPeriod::find($periodId);
        } else {
            // Default: ambil periode yang aktif, atau periode terbaru
            $selectedPeriod = SurveyPeriod::where('is_active', true)->first() 
                           ?? SurveyPeriod::orderBy('year', 'desc')->orderBy('id', 'desc')->first();
        }

        // Inisialisasi data default
        $totalNilaiPreferensi = 0;
        $keteranganNilai = 'Tidak Ada Data';
        $jumlahKriteriaAktif = 0;
        $totalResponden = 0;
        $criteriaChartData = [];

        if ($selectedPeriod) {
            // Hitung total responden dari survey_responses
            $totalResponden = DB::table('survey_responses')
                ->where('period_id', $selectedPeriod->id)
                ->distinct('survey_id')
                ->count('survey_id');

            // Ambil data SAW dari database
            $sawResults = SAWCalculationResult::where('period_id', $selectedPeriod->id)->get();

            if ($sawResults->isNotEmpty()) {
                $totalNilaiPreferensi = $sawResults->sum('weighted_score');

                if ($totalNilaiPreferensi >= 0.9) {
                    $keteranganNilai = 'Excellent';
                } elseif ($totalNilaiPreferensi >= 0.8) {
                    $keteranganNilai = 'Sangat Baik';
                } elseif ($totalNilaiPreferensi >= 0.6) {
                    $keteranganNilai = 'Baik';
                } elseif ($totalNilaiPreferensi >= 0.4) {
                    $keteranganNilai = 'Cukup';
                } else {
                    $keteranganNilai = 'Perlu Perbaikan';
                }

                $jumlahKriteriaAktif = $sawResults->count();

                // ✅ DIURUTKAN: terbaik (tertinggi) di atas, terburuk (terendah) di bawah
                $criteriaChartData = $sawResults
                    ->sortByDesc('normalized_score') // urutkan berdasar normalized_score
                    ->map(function($result) {
                        $normalized = $result->normalized_score;

                        // Interpretasi berdasarkan normalized_score (0–1)
                        if ($normalized >= 0.9) {
                            $interpretation = 'Sangat Baik';
                        } elseif ($normalized >= 0.8) {
                            $interpretation = 'Baik';
                        } elseif ($normalized >= 0.6) {
                            $interpretation = 'Cukup';
                        } elseif ($normalized >= 0.4) {
                            $interpretation = 'Kurang';
                        } else {
                            $interpretation = 'Perlu Perbaikan';
                        }

                        return [
                            'criteria'         => $result->criteria_name,
                            'weighted_score'   => (float) $result->weighted_score,
                            'normalized_score' => (float) $result->normalized_score,
                            'interpretation'   => $interpretation,
                        ];
                    })->values()->toArray();

            } else {
                $sawQuestions = SurveyQuestion::where('enable_saw', true)
                    ->where('question_type', 'linear_scale')
                    ->whereNotNull('criteria_name')
                    ->get();

                if ($sawQuestions->isNotEmpty()) {
                    $jumlahKriteriaAktif = $sawQuestions->pluck('criteria_name')->unique()->count();
                }
            }
        } else {
            $totalResponden = DB::table('survey_responses')
                ->distinct('survey_id')
                ->count('survey_id');
        }

        // ============================================================
        // DATA BARIS KE-3: PERBANDINGAN ANTAR PERIODE
        // ============================================================
        $periodeComparison = SurveyPeriod::orderBy('year', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function($period) {
                $sawResults = SAWCalculationResult::where('period_id', $period->id)->get();
                $totalVi     = $sawResults->sum('weighted_score');
                $jumlahKriteria = $sawResults->count();
                $responden   = DB::table('survey_responses')
                    ->where('period_id', $period->id)
                    ->distinct('survey_id')
                    ->count('survey_id');

                // Predikat berdasarkan Total Vi
                if ($jumlahKriteria === 0) {
                    $predikat = 'Belum Ada Data';
                } elseif ($totalVi >= 0.9) {
                    $predikat = 'Excellent';
                } elseif ($totalVi >= 0.8) {
                    $predikat = 'Sangat Baik';
                } elseif ($totalVi >= 0.6) {
                    $predikat = 'Baik';
                } elseif ($totalVi >= 0.4) {
                    $predikat = 'Cukup';
                } else {
                    $predikat = 'Perlu Perbaikan';
                }

                return [
                    'id'             => $period->id,
                    'period_name'    => $period->period_name,
                    'year'           => $period->year,
                    'is_active'      => $period->is_active,
                    'jumlah_kriteria' => $jumlahKriteria,
                    'predikat'       => $predikat,
                    'total_vi'       => $totalVi,
                    'responden'      => $responden,
                ];
            });

        // Data untuk grafik roller-coaster (line chart) perbandingan periode
        $periodeChartData = $periodeComparison
            ->filter(fn($p) => $p['jumlah_kriteria'] > 0) // hanya yang ada data SAW
            ->values()
            ->toArray();

        return view('admin.dashboard.index', [
            'totalNilaiPreferensi' => $totalNilaiPreferensi,
            'keteranganNilai'      => $keteranganNilai,
            'jumlahKriteriaAktif'  => $jumlahKriteriaAktif,
            'totalResponden'       => $totalResponden,
            'selectedPeriod'       => $selectedPeriod,
            'allPeriods'           => $allPeriods,
            'criteriaChartData'    => $criteriaChartData,
            'periodeComparison'    => $periodeComparison,
            'periodeChartData'     => $periodeChartData,
        ]);
    }
}