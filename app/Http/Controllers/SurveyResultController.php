<?php
// app/Http/Controllers/SurveyResultController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\SurveySection;
use Illuminate\Support\Collection;

class SurveyResultController extends Controller
{
    /**
     * Check admin authentication - FLEXIBLE
     */
    private function checkAdminAuth()
    {
        // Support multiple session keys
        if (!session('admin_id') && !session('admin_user') && !session('admin')) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai admin terlebih dahulu.');
        }
        return null;
    }

    /**
     * DASHBOARD LANGSUNG TABEL SAW
     * 
     * Halaman utama yang langsung menampilkan tabel:
     * | Kriteria | Skor (x) | Bobot Normalisasi (wᵢ) | Normalisasi (rᵢ) | Nilai Terbobot (wᵢ×rᵢ) | Keterangan |
     * 
     * Tanpa perlu pilih survey ID, langsung agregasi semua data
     */
    public function dashboard()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        // Get all SAW enabled questions
        $sawQuestions = SurveyQuestion::where('enable_saw', true)
                                    ->where('question_type', 'linear_scale')
                                    ->whereNotNull('criteria_name')
                                    ->with('responses')
                                    ->get();

        if ($sawQuestions->isEmpty()) {
            return view('admin.hasil-survey.dashboard', [
                'criteriaResults' => collect(),
                'hasSAW' => false,
                'totalVi' => 0,
                'totalResponses' => 0,
                'message' => 'Tidak ada pertanyaan dengan pengaturan SAW yang aktif. 
                             Silakan aktifkan fitur SAW pada pertanyaan dengan tipe skala linier.'
            ]);
        }

        // Calculate aggregate SAW results from all surveys
        $criteriaResults = $this->calculateAggregateSAWResults($sawQuestions);
        
        // TOTAL NILAI PREFERENSI (Vi)
        // Vi = Σ(wᵢ × rᵢ) untuk semua kriteria
        $totalVi = $criteriaResults->sum('weighted_score');

        // Count total responses
        $totalResponses = SurveyResponse::whereHas('question', function($query) {
            $query->where('enable_saw', true);
        })->distinct('survey_id')->count();

        return view('admin.hasil-survey.dashboard', compact('criteriaResults', 'totalVi', 'totalResponses') + ['hasSAW' => true]);
    }

    /**
     * ============================================================================
     * PERHITUNGAN AGREGAT SAW DARI SEMUA SURVEY
     * ============================================================================
     * 
     * Metode SAW (Simple Additive Weighting) untuk Decision Support System
     * 
     * RUMUS UTAMA:
     * Vi = Σ(wⱼ × rᵢⱼ)
     * 
     * Di mana:
     * - Vi  = Nilai preferensi akhir untuk alternatif ke-i
     * - wⱼ  = Bobot ternormalisasi untuk kriteria ke-j
     * - rᵢⱼ = Nilai ternormalisasi dari alternatif i pada kriteria j
     * 
     * ============================================================================
     */
    private function calculateAggregateSAWResults($sawQuestions)
    {
        $results = collect();
        
        // ========================================================================
        // TAHAP 0: PERSIAPAN DATA
        // ========================================================================
        // Grouping semua pertanyaan berdasarkan nama kriteria
        $questionsByCriteria = $sawQuestions->groupBy('criteria_name');
        $criteriaAggregates = collect();

        // ========================================================================
        // TAHAP 1: AGREGASI SKOR PER KRITERIA
        // ========================================================================
        // Mengumpulkan dan menghitung rata-rata skor dari semua responden
        // untuk setiap kriteria
        
        foreach ($questionsByCriteria as $criteriaName => $questions) {
            // Kumpulkan semua jawaban untuk kriteria ini dari semua survey
            $allScores = collect();
            
            foreach ($questions as $question) {
                // Ambil semua response untuk pertanyaan ini
                $questionResponses = $question->responses;
                
                foreach ($questionResponses as $response) {
                    // Tambahkan setiap jawaban ke collection
                    $allScores->push((float) $response->answer);
                }
            }

            if ($allScores->isNotEmpty()) {
                // Hitung rata-rata skor untuk kriteria ini
                // Ini akan menjadi nilai Xᵢⱼ (skor alternatif i pada kriteria j)
                $criteriaAverage = $allScores->avg();
                
                $firstQuestion = $questions->first();
                
                // Simpan data agregat per kriteria
                $criteriaAggregates->push([
                    'criteria_name' => $criteriaName ?: 'Tidak Dikategorikan',
                    'criteria_weight' => $firstQuestion->criteria_weight ?? 0,  // wⱼ (bobot kriteria)
                    'criteria_type' => $firstQuestion->criteria_type ?? 'benefit', // benefit atau cost
                    'average_score' => $criteriaAverage,  // Xᵢⱼ (skor rata-rata)
                    'total_responses' => $allScores->count(),
                    'questions_count' => $questions->count()
                ]);
            }
        }

        // ========================================================================
        // TAHAP 2: NORMALISASI BOBOT KRITERIA
        // ========================================================================
        // Formula: wⱼ_normalized = wⱼ / Σwⱼ
        //
        // Contoh:
        // Kriteria A: bobot = 30
        // Kriteria B: bobot = 40
        // Kriteria C: bobot = 30
        // Total bobot = 100
        //
        // wA_normalized = 30/100 = 0.300
        // wB_normalized = 40/100 = 0.400
        // wC_normalized = 30/100 = 0.300
        // ========================================================================
        
        $totalWeight = $criteriaAggregates->sum('criteria_weight');
        if ($totalWeight == 0) {
            return $results;
        }

        // ========================================================================
        // TAHAP 3: NORMALISASI MATRIKS KEPUTUSAN & PERHITUNGAN NILAI TERBOBOT
        // ========================================================================
        foreach ($criteriaAggregates as $criteria) {
            
            // --------------------------------------------------------------------
            // STEP 3.1: Normalisasi Bobot untuk Kriteria Ini
            // --------------------------------------------------------------------
            // wⱼ_normalized = wⱼ / Σwⱼ
            $weightNormalized = $criteria['criteria_weight'] / $totalWeight;
            
            // --------------------------------------------------------------------
            // STEP 3.2: Normalisasi Matriks Keputusan (rᵢⱼ)
            // --------------------------------------------------------------------
            // Tergantung tipe kriteria (benefit atau cost)
            
            if ($criteria['criteria_type'] === 'benefit') {
                // ----------------------------------------------------------------
                // UNTUK KRITERIA BENEFIT (semakin tinggi semakin baik)
                // ----------------------------------------------------------------
                // Formula: rᵢⱼ = Xᵢⱼ / max(Xᵢⱼ)
                //
                // Contoh:
                // Kriteria "Kognitif" (benefit), skor rata-rata = 82.10
                // Skor tertinggi dari semua kriteria = 90.00
                // rᵢⱼ = 82.10 / 90.00 = 0.912
                // ----------------------------------------------------------------
                
                $maxScore = $criteriaAggregates->max('average_score');
                $normalized = $maxScore > 0 ? ($criteria['average_score'] / $maxScore) : 0;
                
            } else {
                // ----------------------------------------------------------------
                // UNTUK KRITERIA COST (semakin rendah semakin baik)
                // ----------------------------------------------------------------
                // Formula: rᵢⱼ = min(Xᵢⱼ) / Xᵢⱼ
                //
                // Contoh:
                // Kriteria "Biaya" (cost), skor rata-rata = 50.00
                // Skor terendah dari semua kriteria = 30.00
                // rᵢⱼ = 30.00 / 50.00 = 0.600
                // ----------------------------------------------------------------
                
                $minScore = $criteriaAggregates->min('average_score');
                $normalized = $criteria['average_score'] > 0 ? ($minScore / $criteria['average_score']) : 0;
            }
            
            // Pastikan nilai normalisasi berada di range [0, 1]
            $normalized = max(0, min(1, $normalized));
            
            // --------------------------------------------------------------------
            // STEP 3.3: Perhitungan Nilai Terbobot
            // --------------------------------------------------------------------
            // Formula: Vᵢⱼ = wⱼ × rᵢⱼ
            //
            // Contoh:
            // wⱼ_normalized = 0.400 (bobot ternormalisasi)
            // rᵢⱼ = 0.912 (nilai ternormalisasi)
            // Vᵢⱼ = 0.400 × 0.912 = 0.3648
            //
            // Nilai ini adalah KONTRIBUSI kriteria ini terhadap nilai akhir
            // --------------------------------------------------------------------
            
            $weightedScore = $weightNormalized * $normalized;
            
            // --------------------------------------------------------------------
            // STEP 3.4: Interpretasi Kualitatif
            // --------------------------------------------------------------------
            // Memberikan label deskriptif berdasarkan nilai normalisasi
            
            $interpretation = $this->getSAWInterpretation($normalized);
            
            // --------------------------------------------------------------------
            // STEP 3.5: Simpan Hasil untuk Ditampilkan
            // --------------------------------------------------------------------
            // Build result untuk tabel output
            
            $results->push([
                'criteria' => $criteria['criteria_name'],
                'score' => round($criteria['average_score'], 2),        // Skor rata-rata (Xᵢⱼ)
                'weight_normalized' => round($weightNormalized, 3),     // Bobot Normalisasi (wⱼ)
                'normalized' => round($normalized, 3),                  // Normalisasi (rᵢⱼ)
                'weighted_score' => round($weightedScore, 4),           // Nilai Terbobot (wⱼ × rᵢⱼ)
                'interpretation' => $interpretation,                    // Keterangan kualitatif
                'total_responses' => $criteria['total_responses'],
                'questions_count' => $criteria['questions_count'],
                'criteria_type' => $criteria['criteria_type']
            ]);
        }

        // ========================================================================
        // TAHAP 4: TOTAL NILAI PREFERENSI
        // ========================================================================
        // Total Vi akan dihitung di controller utama dengan:
        // Vi = Σ(weighted_score) untuk semua kriteria
        //
        // Contoh:
        // Kriteria A: weighted_score = 0.2610
        // Kriteria B: weighted_score = 0.3648
        // Kriteria C: weighted_score = 0.2502
        // Total Vi = 0.2610 + 0.3648 + 0.2502 = 0.8760
        //
        // Nilai Vi berkisar antara 0 sampai 1
        // Semakin tinggi nilai Vi, semakin baik kondisi keseluruhan
        // ========================================================================

        return $results;
    }

    /**
     * ============================================================================
     * INTERPRETASI KUALITATIF UNTUK NILAI NORMALISASI SAW
     * ============================================================================
     * 
     * Memberikan label deskriptif berdasarkan nilai normalisasi (rᵢⱼ)
     * Range nilai normalisasi: 0 sampai 1
     * 
     * Skala Interpretasi:
     * - 0.90 - 1.00 : Sangat Baik
     * - 0.80 - 0.89 : Baik
     * - 0.60 - 0.79 : Cukup
     * - 0.40 - 0.59 : Kurang
     * - 0.00 - 0.39 : Sangat Kurang
     * 
     * ============================================================================
     */
    private function getSAWInterpretation($normalizedScore)
    {
        if ($normalizedScore >= 0.9) return 'Sangat Baik';
        if ($normalizedScore >= 0.8) return 'Baik';
        if ($normalizedScore >= 0.6) return 'Cukup';
        if ($normalizedScore >= 0.4) return 'Kurang';
        return 'Sangat Kurang';
    }

    /**
     * ============================================================================
     * CONTOH OUTPUT YANG DIHASILKAN
     * ============================================================================
     * 
     * Tabel Hasil Perhitungan SAW:
     * 
     * +---------------+----------+-----------------------+------------------+-------------------------+-------------+
     * | Kriteria      | Skor (x) | Bobot Normalisasi (wᵢ)| Normalisasi (rᵢ) | Nilai Terbobot (wᵢ×rᵢ) | Keterangan  |
     * +---------------+----------+-----------------------+------------------+-------------------------+-------------+
     * | Afektif       | 78.50    | 0.300                 | 0.870            | 0.2610                  | Baik        |
     * | Kognitif      | 82.10    | 0.400                 | 0.912            | 0.3648                  | Sangat Baik |
     * | Psikomotorik  | 75.30    | 0.300                 | 0.836            | 0.2508                  | Baik        |
     * +---------------+----------+-----------------------+------------------+-------------------------+-------------+
     * | TOTAL NILAI PREFERENSI (Vi)                                         | 0.8766                  |             |
     * +---------------------------------------------------------------------+-------------------------+-------------+
     * 
     * Penjelasan Kolom:
     * 
     * 1. Kriteria           : Nama kriteria penilaian
     * 2. Skor (x)           : Rata-rata skor dari semua responden untuk kriteria ini (Xᵢⱼ)
     * 3. Bobot Normalisasi  : Bobot kriteria yang sudah dinormalisasi (wⱼ = bobot / total_bobot)
     * 4. Normalisasi        : Nilai yang sudah dinormalisasi dengan rumus SAW (rᵢⱼ)
     *                         - Benefit: rᵢⱼ = Xᵢⱼ / max(Xᵢⱼ)
     *                         - Cost: rᵢⱼ = min(Xᵢⱼ) / Xᵢⱼ
     * 5. Nilai Terbobot     : Kontribusi kriteria ke nilai akhir (wⱼ × rᵢⱼ)
     * 6. Keterangan         : Interpretasi kualitatif dari nilai normalisasi
     * 
     * TOTAL Vi = Σ(Nilai Terbobot) = 0.2610 + 0.3648 + 0.2508 = 0.8766
     * 
     * Interpretasi:
     * - Vi = 0.8766 menunjukkan kondisi "Sangat Baik" (range 0.80 - 0.89)
     * - Kriteria "Kognitif" memberikan kontribusi terbesar (0.3648)
     * - Semua kriteria berada dalam kategori "Baik" atau "Sangat Baik"
     * 
     * ============================================================================
     */
}