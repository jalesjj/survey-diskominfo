<div align="center">

# Survei Kepuasan Masyarakat (SKM)
### Dinas Komunikasi dan Informatika Kabupaten Lamongan

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Blade-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white" />
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Metode-Simple%20Additive%20Weighting-teal?style=flat-square" />
  <img src="https://img.shields.io/badge/Status-Tugas%20Akhir-blue?style=flat-square" />
  <img src="https://img.shields.io/badge/Versi-2.0-green?style=flat-square" />
</p>

> Sistem informasi survei kepuasan masyarakat berbasis web dengan pengolahan data menggunakan metode **Simple Additive Weighting (SAW)** untuk mendukung pengambilan keputusan peningkatan kualitas layanan Diskominfo Kabupaten Lamongan.

</div>

---

## Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Peran Pengguna](#-peran-pengguna)
- [Metode SAW](#-metode-simple-additive-weighting-saw)
- [Struktur Database](#-struktur-database)
- [Struktur Proyek](#-struktur-proyek)
- [Lisensi](#-lisensi)

---

## Tentang Proyek

Sistem ini dibangun sebagai solusi digitalisasi proses Survei Kepuasan Masyarakat (SKM) yang sebelumnya dilakukan secara manual. Dengan memanfaatkan metode **Simple Additive Weighting (SAW)**, sistem mampu menghasilkan analisis kuantitatif yang terstandar sesuai pedoman penyusunan SKM dari Kementerian Pendayagunaan Aparatur Negara.

### Permasalahan yang Diselesaikan

| Sebelum | Sesudah |
|---|---|
| Survei manual menggunakan kertas | Survei digital berbasis web, bisa diakses dari mana saja |
| Pengolahan data manual & rentan human error | Kalkulasi SAW otomatis & akurat |
| Laporan dibuat secara manual | Laporan PDF & Excel ter-generate otomatis |
| Tidak ada histori antar periode | Manajemen periode survei terstruktur dengan histori |
| Tidak ada bobot kriteria | Kriteria terbobot dengan metode SAW |

---

## Fitur Utama

### Antarmuka Publik (Responden)

- **Form survei multi-seksi** — Pertanyaan dikelompokkan dalam seksi, navigasi antar seksi dengan progres bar real-time
- **Beragam tipe pertanyaan** — Teks singkat, paragraf, pilihan ganda, checkbox, dropdown, upload file, dan **skala linier**
- **Validasi dinamis** — Validasi di sisi klien sebelum submit, mencegah data kosong pada field wajib
- **Tampilan responsif** — Dapat diakses dari perangkat mobile maupun desktop
- **Guard periode aktif** — Survei hanya dapat diisi saat periode aktif; jika tidak ada periode aktif, muncul halaman informatif

### 🛠️ Panel Admin

#### Manajemen Survei
- **Kelola seksi & pertanyaan** — CRUD lengkap untuk seksi dan pertanyaan dengan drag-and-drop reordering
- **Kunci pertanyaan** — Pertanyaan otomatis terkunci saat periode aktif untuk menjaga konsistensi data
- **Toggle aktif/nonaktif** — Aktifkan atau nonaktifkan pertanyaan dan seksi tanpa menghapusnya

#### Manajemen Periode
- **Manajemen periode survei** — Buat, edit, dan arsipkan periode survei berdasarkan tahun
- **Satu periode aktif** — Sistem memastikan hanya satu periode yang berjalan di waktu yang sama
- **Histori periode** — Hasil survei tersimpan per periode dan dapat dibandingkan

#### Manajemen Kriteria SAW
- **CRUD kriteria** — Tambah, edit, hapus kriteria dengan bobot dan tipe (benefit/cost)
- **Kunci saat periode aktif** — Kriteria tidak dapat diubah saat survei sedang berjalan untuk menjaga integritas hasil
- **Pemetaan kriteria ke pertanyaan** — Setiap pertanyaan skala linier dapat dipetakan ke satu kriteria

#### Analisis & Hasil
- **Dashboard hasil SAW** — Visualisasi nilai per kriteria, nilai terbobot, dan total nilai preferensi (Vi)
- **Perbandingan antar periode** — Grafik histori nilai SKM antar periode
- **Jawaban individual** — Detail jawaban per responden dengan skor SAW masing-masing
- **Distribusi jawaban** — Grafik batang dan donat untuk setiap pertanyaan
- **Analisis skala linier** — Keterangan distribusi skor per nilai untuk pertanyaan SKM

#### Ekspor Laporan
- **Export PDF** — Laporan SAW per responden berformat siap cetak dengan area tanda tangan
- **Export Excel (Raw Data)** — Data mentah seluruh jawaban responden per periode
- **Export Excel Laporan Kominfo** — Multi-sheet: karakteristik responden, nilai kriteria, hasil SAW, daftar pertanyaan
- **Export PDF Laporan Kominfo** — Laporan lengkap berformat resmi: cover, grafik, tabel karakteristik, nilai kriteria, histori, dan hasil SAW

#### Manajemen Sistem
- **Manajemen aset** — Upload dan kelola logo instansi yang ditampilkan di halaman survei
- **Informasi kontak** — Kelola informasi kontak Diskominfo yang tampil di footer
- **Footer links** — Kelola tautan yang tampil di footer halaman publik
- **Manajemen pengguna admin** — CRUD akun admin dengan pembatasan akses berbasis role

---

## 🛠️ Teknologi

### Backend
| Teknologi | Kegunaan |
|---|---|
| **Laravel (PHP 8.x)** | Framework utama MVC |
| **MySQL** | Database relasional |
| **PhpSpreadsheet** | Generate file Excel (.xlsx) |
| **DomPDF / Barryvdh** | Generate file PDF |

### Frontend
| Teknologi | Kegunaan |
|---|---|
| **Blade Templating** | Template engine Laravel |
| **Chart.js** | Visualisasi grafik distribusi & SAW |
| **Font Awesome 6** | Ikon antarmuka |
| **Vanilla CSS** | Styling kustom flat design |

### Pola Arsitektur
| Pola | Implementasi |
|---|---|
| **MVC** | Controller, Model, View terpisah |
| **Service Layer** | `SAWRespondentService` untuk logika kalkulasi SAW per responden |
| **Session-based Auth** | Autentikasi admin menggunakan Laravel Session |
| **Repository-like** | Query terpusat di Controller dengan Eloquent ORM |

---

## Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                    PENGGUNA PUBLIK                      │
│              (Masyarakat / Responden)                   │
└────────────────────────┬────────────────────────────────┘
                         │ Mengisi Survei
                         ▼
┌─────────────────────────────────────────────────────────┐
│              ANTARMUKA PUBLIK (Blade Views)             │
│   Form Survei → Validasi → Submit → Halaman Terima     │
└────────────────────────┬────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│                  SurveyController                       │
│   Validasi Periode Aktif → Simpan Jawaban → Kalkulasi  │
└────────────────────────┬────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│                    DATABASE (MySQL)                     │
│                                                         │
│  surveys ──────── survey_responses                      │
│       │                  │                              │
│  survey_periods    survey_questions ── criterias        │
│                          │                              │
│                    survey_sections                      │
│                          │                              │
│                 saw_calculation_results                 │
└────────────────────────┬────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────┐
│              PANEL ADMIN (Blade Views)                  │
│                                                         │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │  Dashboard  │  │ Manajemen    │  │ Hasil & Export│  │
│  │  SAW        │  │ Pertanyaan   │  │ PDF/Excel     │  │
│  └─────────────┘  └──────────────┘  └───────────────┘  │
│                                                         │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  │
│  │  Periode    │  │  Kriteria    │  │ Manajemen     │  │
│  │  Survei     │  │  SAW         │  │ Pengguna      │  │
│  └─────────────┘  └──────────────┘  └───────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Alur Kalkulasi SAW

```
Jawaban Responden (Skala 1–5)
         │
         ▼
   Rata-rata per Kriteria (X̄ij)
         │
         ▼
   Normalisasi SAW
   ┌─────────────────────────────────────────────┐
   │ Benefit: rij = X̄ij / scale_max             │
   │ Cost:    rij = scale_min / X̄ij             │
   └─────────────────────────────────────────────┘
         │
         ▼
   Normalisasi Bobot: wj = Wj / ΣWj
         │
         ▼
   Nilai Terbobot: Vij = wj × rij
         │
         ▼
   Total Nilai Preferensi: Vi = Σ Vij
         │
         ▼
   Interpretasi: Excellent / Sangat Baik / Baik / Cukup / Perlu Perbaikan
```

---

## Peran Pengguna

### Publik (Responden)
Masyarakat yang mengisi survei kepuasan layanan. Tidak memerlukan akun; survei diisi secara anonim. Data demografis (nama, umur, jenis kelamin, pendidikan, pekerjaan) dikumpulkan sebagai bagian dari profil responden.

### Admin
Pengelola sistem dengan akses penuh ke panel admin. Dapat:
- Mengelola pertanyaan, seksi, dan periode survei
- Melihat dan mengunduh hasil survei dan laporan
- Mengubah password akun sendiri

### Super Admin
Memiliki seluruh hak akses Admin, ditambah:
- Membuat dan menghapus akun admin lain
- Mengubah role pengguna
- Akses ke seluruh fitur manajemen sistem

---

## Metode Simple Additive Weighting (SAW)

Sistem menggunakan metode **SAW** sebagai Decision Support System (DSS) untuk mengolah data survei menjadi nilai kepuasan terstandar.

### Kenapa SAW?

SAW dipilih karena:
1. **Sederhana & transparan** — Proses kalkulasi mudah dipahami dan diaudit
2. **Mendukung multi-kriteria** — Setiap aspek layanan dapat diberi bobot sesuai prioritas
3. **Fleksibel** — Mendukung kriteria benefit (semakin tinggi semakin baik) dan cost (semakin rendah semakin baik)
4. **Sesuai standar** — Kompatibel dengan pedoman SKM Kemenpan RB

### Normalisasi Nilai

Sistem menggunakan **batas skala tetap** (bukan max/min dari data aktual):

$$r_{ij} = \frac{\bar{X}_{ij}}{scale\_max} \quad \text{(Benefit)}$$

$$r_{ij} = \frac{scale\_min}{\bar{X}_{ij}} \quad \text{(Cost)}$$

Di mana `scale_max = 5` dan `scale_min = 1` sesuai konfigurasi skala survei.

### Tabel Interpretasi

| Rentang Nilai (r) | Mutu | Keterangan |
|---|---|---|
| ≥ 0.88 | **A** | Sangat Baik |
| 0.76 – 0.87 | **B** | Baik |
| 0.65 – 0.75 | **C** | Cukup |
| < 0.65 | **D** | Kurang dan Perlu Perbaikan |

### Interpretasi Total Vi

| Rentang Total Vi | Keterangan |
|---|---|
| ≥ 0.90 | Excellent |
| 0.80 – 0.89 | Sangat Baik |
| 0.60 – 0.79 | Baik |
| 0.40 – 0.59 | Cukup |
| < 0.40 | Perlu Perbaikan |

---

## Struktur Database

```
┌──────────────────┐       ┌──────────────────────┐
│   admin_users    │       │    survey_periods     │
├──────────────────┤       ├──────────────────────┤
│ id               │       │ id                   │
│ username         │       │ period_name          │
│ name             │       │ year                 │
│ password         │       │ description          │
│ role             │       │ is_active            │
└──────────────────┘       └──────────┬───────────┘
                                      │
              ┌───────────────────────┼──────────────────────┐
              │                       │                      │
              ▼                       ▼                      ▼
┌─────────────────────┐  ┌──────────────────────┐  ┌────────────────────────┐
│      surveys        │  │   survey_responses   │  │  saw_calculation_results│
├─────────────────────┤  ├──────────────────────┤  ├────────────────────────┤
│ id                  │  │ id                   │  │ id                     │
│ nama, email         │  │ survey_id            │  │ period_id              │
│ jenis_kelamin       │  │ question_id          │  │ criteria_name          │
│ umur, pendidikan    │  │ answer               │  │ criteria_weight        │
│ pekerjaan           │  │ period_id            │  │ average_score          │
│ created_at          │  │ created_at           │  │ normalized_score       │
└──────────┬──────────┘  └──────────────────────┘  │ weight_normalized      │
           │                                        │ weighted_score         │
           │                                        │ interpretation         │
           ▼                                        └────────────────────────┘
┌──────────────────────┐
│   survey_sections    │       ┌──────────────────┐
├──────────────────────┤       │    criterias     │
│ id                   │       ├──────────────────┤
│ title                │       │ id               │
│ description          │       │ name             │
│ order_index          │       │ weight           │
│ is_active            │       │ type (benefit/   │
└──────────┬───────────┘       │  cost)           │
           │                   └────────┬─────────┘
           ▼                            │
┌──────────────────────────────────────┐│
│         survey_questions             ││
├──────────────────────────────────────┤│
│ id                                   ││
│ section_id                           ││
│ question_text, question_description  ││
│ question_type                        ││
│ options (JSON), settings (JSON)      ││
│ order_index                          ││
│ is_required, is_active               ││
│ enable_saw                           ││
│ criteria_id ─────────────────────────┘│
│ criteria_name, criteria_weight        │
│ criteria_type                         │
└───────────────────────────────────────┘
```

---

## Struktur Proyek

```
survei_diskominfo/
│
├── app/
│   ├── Helpers/
│   │   └── SurveyDefaults.php          # Konstanta pertanyaan default (Data Diri)
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AdminController.php     # Dashboard, export Excel raw data
│   │       ├── CriteriaController.php  # CRUD kriteria SAW
│   │       ├── DashboardController.php # Dashboard admin
│   │       ├── ExportLaporanController.php # Export PDF & Excel laporan Kominfo
│   │       ├── SurveyController.php    # Form survei publik
│   │       ├── SurveyPeriodController.php # Manajemen periode
│   │       ├── SurveyResultController.php # Hasil & kalkulasi SAW
│   │       └── UserController.php      # Manajemen akun admin
│   ├── Models/
│   │   ├── AdminUser.php
│   │   ├── Criteria.php
│   │   ├── SAWCalculationResult.php
│   │   ├── Survey.php
│   │   ├── SurveyPeriod.php
│   │   ├── SurveyQuestion.php
│   │   ├── SurveyResponse.php (implied)
│   │   └── SurveySection.php
│   └── Services/
│       └── SAWRespondentService.php    # Logika kalkulasi SAW per responden
│
├── database/
│   └── migrations/                     # 15+ migration file
│
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── criterias/              # CRUD kriteria
│       │   ├── hasil-survey/           # Dashboard SAW, ekspor PDF/Excel
│       │   ├── questions/              # CRUD seksi & pertanyaan
│       │   ├── users/                  # Manajemen akun
│       │   ├── jawaban.blade.php       # Halaman analisis jawaban
│       │   └── login.blade.php
│       ├── layouts/
│       │   ├── admin.blade.php         # Layout panel admin
│       │   └── app.blade.php           # Layout publik
│       └── survey/
│           └── index.blade.php         # Form survei publik
│
├── routes/
│   └── web.php                         # Seluruh routing aplikasi
│
└── config/
    └── survey_defaults.php             # Konfigurasi default survei
```

---

## Catatan Teknis

### Kalkulasi SAW: Per-Respondent vs Agregat

Sistem menerapkan SAW dalam dua tingkatan:

1. **Per responden** — `SAWRespondentService` menghitung skor SAW individual setiap responden. Digunakan untuk menampilkan peringkat responden dan export PDF per-responden.

2. **Agregat per kriteria** — `SurveyResultController` menghitung rata-rata seluruh responden per kriteria, lalu menerapkan SAW. Digunakan untuk laporan resmi SKM.

### Kunci Integritas Data

- **Pertanyaan terkunci** saat periode aktif → mencegah perubahan struktur survei di tengah pengumpulan data
- **Kriteria terkunci** saat periode aktif → mencegah perubahan bobot yang memengaruhi hasil kalkulasi
- **Session key `admin_id`** digunakan konsisten di seluruh controller untuk autentikasi admin

### Skala Normalisasi

Normalisasi menggunakan **batas skala tetap** (`scale_max=5`, `scale_min=1`), bukan nilai max/min dari data aktual. Pendekatan ini memastikan nilai ternormalisasi dapat dibandingkan secara adil antar periode meski distribusi jawaban berbeda.

---

## Lisensi

Proyek ini dikembangkan sebagai **Tugas Akhir / Skripsi** mahasiswa untuk keperluan akademik dan implementasi di **Dinas Komunikasi dan Informatika Kabupaten Lamongan**.

Penggunaan ulang kode dalam konteks non-akademik atau komersial tanpa izin tertulis dari pengembang **tidak diperkenankan**.

---

## capan Terima Kasih

- **Dinas Komunikasi dan Informatika Kabupaten Lamongan** — atas kepercayaan dan kesempatan pengembangan sistem ini
- **Laravel Community** — framework dan ekosistem yang luar biasa
- **PhpSpreadsheet & DomPDF** — library pengolahan dokumen yang handal
- **Chart.js** — visualisasi data yang fleksibel dan ringan

---

<div align="center">

**Dikembangkan dengan ☕ dan semangat melayani masyarakat Lamongan**

![Lamongan](https://img.shields.io/badge/Kabupaten-Lamongan-teal?style=flat-square)
![SKM](https://img.shields.io/badge/Program-SKM%20Digital-blue?style=flat-square)

</div>