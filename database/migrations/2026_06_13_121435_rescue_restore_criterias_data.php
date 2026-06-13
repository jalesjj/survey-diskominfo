<?php
// database/migrations/2026_06_13_000003_rescue_restore_criterias_data.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Insert semua kriteria dari data lama ──────────────────────────
        $criterias = [
            ['criteria_name' => 'Persyaratan',                              'criteria_weight' => 3.000, 'criteria_type' => 'benefit'],
            ['criteria_name' => 'Sistem, Mekanisme, dan Prosedur',          'criteria_weight' => 4.000, 'criteria_type' => 'benefit'],
            ['criteria_name' => 'Waktu Pelayanan',                          'criteria_weight' => 6.000, 'criteria_type' => 'benefit'],
            ['criteria_name' => 'Biaya/Tarif',                              'criteria_weight' => 7.000, 'criteria_type' => 'cost'],
            ['criteria_name' => 'Produk Spesifikasi Jenis Pelayanan',       'criteria_weight' => 5.000, 'criteria_type' => 'benefit'],
            ['criteria_name' => 'Kompetensi Pelaksana',                     'criteria_weight' => 9.000, 'criteria_type' => 'benefit'],
            ['criteria_name' => 'Perilaku Pelaksana',                       'criteria_weight' => 7.000, 'criteria_type' => 'benefit'],
            ['criteria_name' => 'penanganan Pengaduan, Saran dan Masukan',  'criteria_weight' => 3.000, 'criteria_type' => 'benefit'],
            ['criteria_name' => 'Sarana dan Prasarana',                     'criteria_weight' => 2.000, 'criteria_type' => 'benefit'],
        ];

        $now = now();
        foreach ($criterias as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }

        DB::table('criterias')->insert($criterias);

        // ── 2. Ambil semua kriteria yang baru diinsert (keyed by name) ───────
        $inserted = DB::table('criterias')->get()->keyBy('criteria_name');

        // ── 3. Map pertanyaan ke criteria_id berdasarkan data lama ───────────
        $questionMap = [
            // question_id => criteria_name
            84  => 'Persyaratan',
            85  => 'Sistem, Mekanisme, dan Prosedur',
            86  => 'Waktu Pelayanan',
            87  => 'Waktu Pelayanan',
            88  => 'Biaya/Tarif',
            89  => 'Produk Spesifikasi Jenis Pelayanan',
            90  => 'Produk Spesifikasi Jenis Pelayanan',
            91  => 'Produk Spesifikasi Jenis Pelayanan',
            92  => 'Produk Spesifikasi Jenis Pelayanan',
            93  => 'Kompetensi Pelaksana',
            94  => 'Perilaku Pelaksana',
            95  => 'Perilaku Pelaksana',
            96  => 'penanganan Pengaduan, Saran dan Masukan',
            97  => 'penanganan Pengaduan, Saran dan Masukan',
            98  => 'penanganan Pengaduan, Saran dan Masukan',
            99  => 'penanganan Pengaduan, Saran dan Masukan',
            100 => 'Sarana dan Prasarana',
            101 => 'Sarana dan Prasarana',
            102 => 'Sarana dan Prasarana',
        ];

        // ── 4. Update criteria_id di setiap pertanyaan ───────────────────────
        foreach ($questionMap as $questionId => $criteriaName) {
            if (isset($inserted[$criteriaName])) {
                DB::table('survey_questions')
                    ->where('id', $questionId)
                    ->update(['criteria_id' => $inserted[$criteriaName]->id]);
            }
        }
    }

    public function down(): void
    {
        // Kembalikan criteria_id ke null untuk pertanyaan yang kita update
        $questionIds = [84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102];

        DB::table('survey_questions')
            ->whereIn('id', $questionIds)
            ->update(['criteria_id' => null]);

        // Hapus kriteria yang kita insert
        DB::table('criterias')->truncate();
    }
};