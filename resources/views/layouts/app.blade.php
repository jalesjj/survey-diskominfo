{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Survei Kepuasan Layanan Diskominfo Lamongan')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            color: #2c3e50;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            margin: 0;
            background: white;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
            color: white;
            padding: 30px 40px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(180deg); }
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }

        .logos {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding: 8px 0;
            position: relative;
        }

        .logo-item {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 0;
        }

        .logo-image {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s ease;
        }

        .logo-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.25);
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
            font-weight: 600;
            border-radius: 10px;
            text-align: center;
            line-height: 1.2;
        }

        .program-badges {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .badge:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .title-section {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .title-section h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .title-section h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
            opacity: 0.95;
        }

        .title-section h3 {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 5px;
            opacity: 0.9;
        }

        .title-section h4 {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.85;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Footer Styles - Clean Layout tanpa kotak */
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 40px 0 20px 0;
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #5a9b9e 0%, #4a8b8e 50%, #5a9b9e 100%);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
            position: relative;
            z-index: 2;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 40px;
        }

        /* Footer sections tanpa background/border */
        .footer-section {
            /* Removed all card styling */
        }

        .footer-section h3 {
            color: #5a9b9e;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid #5a9b9e;
            padding-bottom: 8px;
        }

        .footer-section p {
            line-height: 1.8;
            color: #bdc3c7;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section li {
            margin-bottom: 12px;
        }

        .footer-section a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 4px 0;
            display: inline-block;
            border-left: 3px solid transparent;
            padding-left: 8px;
            font-size: 14px;
        }

        .footer-section a:hover {
            color: #5a9b9e;
            border-left-color: #5a9b9e;
            transform: translateX(5px);
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            padding: 4px 0;
        }

        .contact-icon {
            width: 18px;
            height: 18px;
            color: #5a9b9e;
            font-size: 14px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contact-item p {
            margin: 0;
            line-height: 1.6;
        }

        .footer-bottom {
            border-top: 1px solid #34495e;
            padding-top: 20px;
            text-align: center;
            color: #95a5a6;
            font-size: 14px;
        }

        /* Empty state styling */
        .empty-state {
            color: #95a5a6;
            font-style: italic;
            font-size: 13px;
            line-height: 1.6;
        }

        .empty-state em {
            color: #7f8c8d;
            font-size: 12px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }

            .header-content {
                flex-direction: column;
                gap: 20px;
                margin-bottom: 20px;
            }

            .logos {
                justify-content: center;
                gap: 12px;
            }

            .logo-image {
                width: 60px;
                height: 60px;
            }

            .program-badges {
                flex-wrap: wrap;
                justify-content: center;
            }

            .title-section h1 {
                font-size: 24px;
            }

            .title-section h2 {
                font-size: 20px;
            }

            .title-section h3 {
                font-size: 16px;
            }

            .title-section h4 {
                font-size: 14px;
            }

            .main-content {
                padding: 30px 20px;
            }

            .footer-content {
                padding: 0 20px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .footer-section h3 {
                margin-bottom: 15px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                @php
                    // Get all active logos from database with error handling
                    try {
                        $activeLogos = \App\Models\Asset::getAllActiveLogos();
                        $logoCount = $activeLogos->count();
                    } catch (\Exception $e) {
                        // Fallback if database error
                        $activeLogos = collect();
                        $logoCount = 0;
                        \Log::warning('Failed to load logos in layout', ['error' => $e->getMessage()]);
                    }
                @endphp
                
                <div class="logos">
                    @if($logoCount > 0)
                        @foreach($activeLogos as $logo)
                        <div class="logo-item">
                            <div class="logo-image">
                                @if($logo->fileExists())
                                    <img src="{{ $logo->file_url }}" 
                                         alt="{{ $logo->original_name }}" 
                                         loading="lazy">
                                @else
                                    <div class="logo-placeholder">
                                        Logo tidak ditemukan
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        {{-- Fallback jika tidak ada logo yang aktif --}}
                        <div class="logo-item">
                            <div class="logo-image">
                                <div class="logo-placeholder">
                                    🏢<br>LOGO
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="title-section">
                <h1>Survei Kepuasan Layanan</h1>
                <h2>Diskominfo Kabupaten Lamongan</h2>
                <h3>Survey Kepuasan Masyarakat (SKM)</h3>
                <h4>Tahun {{ date('Y') }}</h4>
            </div>
        </div>

        <div class="main-content">
            @yield('content')
        </div>

        <div class="footer">
            <div class="footer-content">
                @php
                    try {
                        // Ambil footer links yang aktif
                        $layananLinks = \App\Models\FooterLink::active()->layanan()->ordered()->get();
                        $informasiLinks = \App\Models\FooterLink::active()->informasi()->ordered()->get();
                        // Ambil informasi kontak yang dinamis
                        $contactInfo = \App\Models\ContactInfo::getCurrent();
                    } catch (\Exception $e) {
                        $layananLinks = collect();
                        $informasiLinks = collect();
                        $contactInfo = null;
                    }
                @endphp

                <div class="footer-grid">
                    <!-- Kolom 1: Layanan -->
                    <div class="footer-section">
                        <h3>Layanan</h3>
                        @if($layananLinks->count() > 0)
                            <ul>
                                @foreach($layananLinks as $link)
                                    <li><a href="{{ $link->url }}" target="_blank">{{ $link->title }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <div class="empty-state">
                                <p>Belum ada layanan yang ditambahkan.</p>
                                <p><em>Silakan tambahkan melalui admin panel → Footer Links → Kategori: Layanan</em></p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Kolom 2: Informasi -->
                    <div class="footer-section">
                        <h3>Informasi</h3>
                        @if($informasiLinks->count() > 0)
                            <ul>
                                @foreach($informasiLinks as $link)
                                    <li><a href="{{ $link->url }}" target="_blank">{{ $link->title }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <div class="empty-state">
                                <p>Belum ada informasi yang ditambahkan.</p>
                                <p><em>Silakan tambahkan melalui admin panel → Footer Links → Kategori: Informasi</em></p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Kolom 3: Kontak Info -->
                    <div class="footer-section">
                        <h3>Kontak Kami</h3>
                        @if($contactInfo)
                            <div class="contact-item">
                                <p><strong>{{ $contactInfo->department_name }}</strong></p>
                            </div>
                            <div class="contact-item">
                                <p>{{ $contactInfo->regency_name }}</p>
                            </div>
                            <div class="contact-item">
                                <p>{{ $contactInfo->address }}</p>
                            </div>
                            <div class="contact-item">
                                <p>{{ $contactInfo->province }}</p>
                            </div>
                            <div class="contact-item">
                                <p>{{ $contactInfo->whatsapp }}</p>
                            </div>
                            <div class="contact-item">
                                <p>{{ $contactInfo->email }}</p>
                            </div>
                        @else
                            <div class="empty-state">
                                <p>Informasi kontak belum diatur.</p>
                                <p><em>Silakan atur melalui admin panel → Contact Info</em></p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} Diskominfo Kabupaten Lamongan. Semua hak dilindungi. | 
                       Sistem Survei Digital v2.0</p>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>