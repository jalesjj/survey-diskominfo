{{-- resources/views/admin/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Survei Kepuasan Diskominfo Lamongan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=1">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #5a9b9e 0%, #4a8b8e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c3e50;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #7f8c8d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-input:focus {
            outline: none;
            border-color: #5a9b9e;
            box-shadow: 0 0 0 3px rgba(90, 155, 158, 0.1);
        }

        .btn-login {
            width: 100%;
            background-color: #5a9b9e;
            color: white;
            border: none;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #4a8b8e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(90, 155, 158, 0.3);
        }

        .btn-login:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #5a9b9e;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 20px;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">Login Administrator</h1>
            <p class="login-subtitle">Survei Kepuasan Diskominfo Lamongan</p>
        </div>

        @if ($errors->has('login'))
            <div class="error-message">
                {{ $errors->first('login') }}
            </div>
        @endif

        @if (session('message'))
            <div class="success-message">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.authenticate') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-input" placeholder="Masukkan username" required value="{{ old('username') }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                Masuk
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('survey.index') }}">← Kembali ke Survei</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const loginBtn = document.getElementById('loginBtn');

            form.addEventListener('submit', function() {
                loginBtn.disabled = true;
                loginBtn.textContent = 'Sedang masuk...';
            });
        });
    </script>
</body>
</html>