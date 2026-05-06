<?php
// config/survey_defaults.php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Survey Section & Questions
    |--------------------------------------------------------------------------
    |
    | Section dan pertanyaan yang permanen/hardcoded untuk Data Diri.
    | Data ini tidak tersimpan di database dan tidak bisa diedit/dihapus.
    |
    */

    'default_section' => [
        'id' => 'data_diri', // Menggunakan string identifier, bukan integer
        'title' => 'Data Diri',
        'description' => null,
        'order_index' => 0, // Selalu di urutan pertama
        'is_active' => true,
        'is_permanent' => true, // Flag untuk menandai section permanen
    ],

    'default_questions' => [
        [
            'id' => 'nama',
            'section_id' => 'data_diri',
            'question_text' => 'Nama',
            'question_description' => null,
            'question_type' => 'short_text',
            'options' => null,
            'settings' => [],
            'order_index' => 1,
            'is_required' => true,
            'is_active' => true,
            'is_permanent' => true,
        ],
        [
            'id' => 'email',
            'section_id' => 'data_diri',
            'question_text' => 'Email',
            'question_description' => null,
            'question_type' => 'short_text',
            'options' => null,
            'settings' => [],
            'order_index' => 2,
            'is_required' => true,
            'is_active' => true,
            'is_permanent' => true,
        ],
        [
            'id' => 'jenis_kelamin',
            'section_id' => 'data_diri',
            'question_text' => 'Jenis Kelamin',
            'question_description' => null,
            'question_type' => 'dropdown',
            'options' => ['Laki-laki', 'Perempuan'],
            'settings' => [],
            'order_index' => 3,
            'is_required' => true,
            'is_active' => true,
            'is_permanent' => true,
        ],
        [
            'id' => 'umur',
            'section_id' => 'data_diri',
            'question_text' => 'Umur',
            'question_description' => null,
            'question_type' => 'multiple_choice',
            'options' => ['18 - 25 Tahun', '26 - 35 Tahun', '36 - 45 Tahun', '> 45 Tahun'],
            'settings' => [],
            'order_index' => 4,
            'is_required' => true,
            'is_active' => true,
            'is_permanent' => true,
        ],
        [
            'id' => 'jenis_pendidikan',
            'section_id' => 'data_diri',
            'question_text' => 'Jenis Pendidikan',
            'question_description' => null,
            'question_type' => 'multiple_choice',
            'options' => ['SD', 'SMP', 'SMA', 'S1', 'S2', 'S3'],
            'settings' => [],
            'order_index' => 5,
            'is_required' => true,
            'is_active' => true,
            'is_permanent' => true,
        ],
        [
            'id' => 'pekerjaan',
            'section_id' => 'data_diri',
            'question_text' => 'Pekerjaan',
            'question_description' => null,
            'question_type' => 'multiple_choice',
            'options' => ['PNS', 'TNI', 'POLRI', 'SWASTA', 'WIRAUSAHA', 'Lainnya'],
            'settings' => [],
            'order_index' => 6,
            'is_required' => true,
            'is_active' => true,
            'is_permanent' => true,
        ],
    ],
];