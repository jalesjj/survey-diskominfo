@echo off
echo 🔧 Perbaikan Storage Link Windows
echo.

REM Set lokasi project
set PROJECT_DIR=C:\laragon\www\survei-diskominfo

echo 📁 Menghapus storage link lama...
if exist "%PROJECT_DIR%\public\storage" (
    rmdir /s /q "%PROJECT_DIR%\public\storage"
    echo ✅ Storage link lama dihapus
) else (
    echo ℹ️ Tidak ada storage link lama
)

echo.
echo 🔗 Membuat storage link baru dengan mklink...

REM Buat symbolic link dengan mklink
mklink /D "%PROJECT_DIR%\public\storage" "%PROJECT_DIR%\storage\app\public"

if %ERRORLEVEL% EQU 0 (
    echo ✅ Storage link berhasil dibuat!
) else (
    echo ❌ Gagal membuat storage link dengan mklink
    echo.
    echo 🔄 Mencoba dengan junction...
    mklink /J "%PROJECT_DIR%\public\storage" "%PROJECT_DIR%\storage\app\public"
    
    if %ERRORLEVEL% EQU 0 (
        echo ✅ Junction link berhasil dibuat!
    ) else (
        echo ❌ Gagal membuat junction link
        echo.
        echo 📋 Silakan jalankan manual sebagai Administrator:
        echo mklink /D "C:\laragon\www\survei-diskominfo\public\storage" "C:\laragon\www\survei-diskominfo\storage\app\public"
    )
)

echo.
echo 🧪 Testing hasil...
if exist "%PROJECT_DIR%\public\storage" (
    echo ✅ Storage link exists
    
    REM Test dengan membuat file test
    echo test > "%PROJECT_DIR%\storage\app\public\test.txt"
    if exist "%PROJECT_DIR%\public\storage\test.txt" (
        echo ✅ Storage link berfungsi dengan baik
        del "%PROJECT_DIR%\storage\app\public\test.txt"
    ) else (
        echo ❌ Storage link tidak berfungsi
    )
) else (
    echo ❌ Storage link tidak ada
)

echo.
echo 📂 Checking direktori assets...
if exist "%PROJECT_DIR%\storage\app\public\assets" (
    echo ✅ Assets directory exists
) else (
    echo 📁 Membuat assets directory...
    mkdir "%PROJECT_DIR%\storage\app\public\assets"
    echo ✅ Assets directory created
)

echo.
echo 🎉 Selesai! Sekarang coba upload logo baru di admin panel.
echo.
pause