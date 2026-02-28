{{-- resources/views/layouts/admin.blade.php - MENU LANGSUNG KE TABEL SAW --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - Survei Kepuasan Diskominfo Lamongan')</title>
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
            color: #2c3e50;
            line-height: 1.6;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 999;
            background: #2c3e50;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: #34495e;
            transform: scale(1.05);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .sidebar-close-btn {
            display: none;
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .sidebar-close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #ecf0f1;
        }

        .sidebar-subtitle {
            font-size: 13px;
            opacity: 0.8;
            color: #bdc3c7;
        }

        .sidebar-menu {
            padding: 25px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 16px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            margin-bottom: 2px;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #5a9b9e;
            color: white;
            text-decoration: none;
            transform: translateX(5px);
        }

        .menu-item.active {
            background: rgba(90, 155, 158, 0.2);
            border-left-color: #5a9b9e;
            color: #5a9b9e;
        }

        .menu-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            margin-right: 15px;
            font-size: 16px;
        }

        .menu-text {
            font-weight: 500;
            font-size: 15px;
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            background: rgba(231, 76, 60, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid rgba(231, 76, 60, 0.3);
        }

        .logout-btn:hover {
            background: rgba(231, 76, 60, 0.4);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .content-header {
            background: white;
            padding: 25px 35px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            border-bottom: 3px solid #5a9b9e;
        }

        .content-header-left {
            flex: 1;
        }

        .breadcrumb {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .breadcrumb a {
            color: #5a9b9e;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb-separator {
            margin: 0 10px;
            color: #bdc3c7;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 6px;
        }

        .page-subtitle {
            font-size: 15px;
            color: #7f8c8d;
            line-height: 1.5;
        }

        .content-body {
            padding: 35px;
        }

        /* Success/Error Messages */
        .success-message, .error-message {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
            border: none;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .success-message {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .error-message {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .error-list {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .error-list li {
            margin-bottom: 6px;
        }

        .error-list li:before {
            content: "• ";
            color: #dc3545;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .mobile-menu-toggle {
                display: block;
            }

            .mobile-overlay {
                display: block;
            }

            .sidebar-close-btn {
                display: block;
            }

            .sidebar-header {
                padding-right: 70px;
            }

            .main-content {
                margin-left: 0;
            }

            .content-header {
                padding: 20px 25px;
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .content-body {
                padding: 25px 20px;
            }

            .page-title {
                font-size: 24px;
            }

            .page-subtitle {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .content-header {
                padding: 20px;
            }

            .content-body {
                padding: 20px 15px;
            }

            .page-title {
                font-size: 22px;
            }

            .sidebar {
                width: 260px;
            }

            .menu-item {
                padding: 14px 20px;
            }

            .menu-text {
                font-size: 14px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars" id="hamburgerIcon"></i>
        </button>

        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobileMenu()"></div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <button class="sidebar-close-btn" onclick="closeMobileMenu()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="sidebar-title">Admin Panel</div>
                <div class="sidebar-subtitle">Survei Kepuasan Diskominfo Lamongan</div>
            </div>

            <div class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" class="menu-item @yield('active-dashboard')">
                    <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.questions.index') }}" class="menu-item @yield('active-questions')">
                    <span class="menu-icon"><i class="fas fa-clipboard-list"></i></span>
                    <span class="menu-text">Pertanyaan</span>
                </a>
                
                <a href="{{ route('admin.hasil-survey') }}" class="menu-item @yield('active-hasil-survey')">
                    <span class="menu-icon"><i class="fas fa-chart-bar"></i></span>
                    <span class="menu-text">Hasil Survey</span>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="menu-item @yield('active-users')">
                    <span class="menu-icon"><i class="fas fa-users"></i></span>
                    <span class="menu-text">Manajemen User</span>
                </a>
                
                <a href="{{ route('admin.assets.index') }}" class="menu-item @yield('active-assets')">
                    <span class="menu-icon"><i class="fas fa-images"></i></span>
                    <span class="menu-text">Upload Assets</span>
                </a>
                
                <a href="{{ route('admin.footer-links.index') }}" class="menu-item @yield('active-footer-links')">
                    <span class="menu-icon"><i class="fas fa-link"></i></span>
                    <span class="menu-text">Footer Links</span>
                </a>
                
                <a href="{{ route('admin.contact-info.edit') }}" class="menu-item @yield('active-contact')">
                    <span class="menu-icon"><i class="fas fa-address-book"></i></span>
                    <span class="menu-text">Contact Info</span>
                </a>
            </div>

            <div class="logout-section">
                <a href="{{ route('admin.logout') }}" class="logout-btn">
                    <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <div class="content-header-left">
                    @yield('breadcrumb')
                    <h1 class="page-title">@yield('page-title')</h1>
                    <p class="page-subtitle">@yield('page-subtitle')</p>
                </div>
            </div>

            <div class="content-body">
                @if(session('success'))
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            const isOpen = sidebar.classList.contains('open');
            
            if (isOpen) {
                closeMobileMenu();
            } else {
                sidebar.classList.add('open');
                overlay.classList.add('active');
            }
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }

        // Close menu when clicking on menu items (mobile)
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        setTimeout(closeMobileMenu, 200);
                    }
                });
            });

            // Auto hide success/error messages
            const successMessage = document.querySelector('.success-message');
            const errorMessage = document.querySelector('.error-message');
            
            if (successMessage) {
                setTimeout(() => {
                    successMessage.style.opacity = '0';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 300);
                }, 5000);
            }
            
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.opacity = '0';
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 300);
                }, 7000);
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeMobileMenu();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>