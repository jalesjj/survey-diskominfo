<?php
// app/Models/Asset.php - Windows Compatible Version
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'file_path',
        'original_name',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope untuk logo aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk tipe tertentu
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper untuk mendapatkan URL file - Windows compatible
    public function getFileUrlAttribute()
    {
        try {
            // Jika file_path kosong, return fallback
            if (!$this->file_path || empty(trim($this->file_path))) {
                return $this->getFallbackUrl();
            }

            // Priority 1: Cek di public langsung (untuk Windows tanpa storage link)
            $publicPath = public_path($this->file_path);
            if (File::exists($publicPath)) {
                return asset($this->file_path);
            }

            // Priority 2: Cek di public/storage (jika storage link ada)
            $storagePublicPath = 'storage/' . $this->file_path;
            $fullStoragePublicPath = public_path($storagePublicPath);
            if (File::exists($fullStoragePublicPath)) {
                return asset($storagePublicPath);
            }

            // Priority 3: Cek tanpa prefix storage
            if (strpos($this->file_path, 'storage/') === 0) {
                $cleanPath = str_replace('storage/', '', $this->file_path);
                $publicCleanPath = public_path($cleanPath);
                if (File::exists($publicCleanPath)) {
                    return asset($cleanPath);
                }
            }

            // Priority 4: Cek berdasarkan nama file di berbagai lokasi
            if ($this->name) {
                $possiblePaths = [
                    'assets/' . $this->name,
                    'storage/assets/' . $this->name,
                    'images/' . $this->name,
                ];

                foreach ($possiblePaths as $path) {
                    if (File::exists(public_path($path))) {
                        return asset($path);
                    }
                }
            }

            // Priority 5: Cek di storage tanpa public link
            $storagePath = storage_path('app/public/' . str_replace('storage/', '', $this->file_path));
            if (File::exists($storagePath)) {
                // Copy file ke public jika tidak ada
                $publicTargetPath = public_path($this->file_path);
                $publicTargetDir = dirname($publicTargetPath);
                
                if (!File::exists($publicTargetDir)) {
                    File::makeDirectory($publicTargetDir, 0755, true);
                }
                
                try {
                    File::copy($storagePath, $publicTargetPath);
                    return asset($this->file_path);
                } catch (\Exception $e) {
                    \Log::warning('Failed to copy file from storage to public', [
                        'storage_path' => $storagePath,
                        'public_path' => $publicTargetPath,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Return fallback jika semua gagal
            return $this->getFallbackUrl();

        } catch (\Exception $e) {
            \Log::warning('Asset file URL error', [
                'asset_id' => $this->id,
                'file_path' => $this->file_path,
                'name' => $this->name,
                'error' => $e->getMessage()
            ]);
            
            return $this->getFallbackUrl();
        }
    }

    // Helper untuk mendapatkan fallback URL - improved
    private function getFallbackUrl()
    {
        // Coba berbagai fallback
        $fallbacks = [
            'images/logo-placeholder.png',
            'images/default-logo.png', 
            'assets/placeholder.png'
        ];

        foreach ($fallbacks as $fallback) {
            if (File::exists(public_path($fallback))) {
                return asset($fallback);
            }
        }

        // Data URL fallback sebagai solusi terakhir - SVG yang lebih baik
        return 'data:image/svg+xml;base64,' . base64_encode('
            <svg width="100" height="100" xmlns="http://www.w3.org/2000/svg" style="background:#f8f9fa;">
                <rect width="100" height="100" fill="#e9ecef" stroke="#dee2e6" stroke-width="2"/>
                <circle cx="50" cy="35" r="12" fill="#6c757d"/>
                <rect x="35" y="55" width="30" height="20" rx="3" fill="#6c757d"/>
                <text x="50" y="85" text-anchor="middle" font-family="Arial" font-size="8" fill="#495057">LOGO</text>
                <text x="50" y="95" text-anchor="middle" font-family="Arial" font-size="6" fill="#6c757d">NOT FOUND</text>
            </svg>
        ');
    }

    // Helper untuk cek apakah file exists - Windows compatible
    public function fileExists()
    {
        try {
            if (!$this->file_path) {
                return false;
            }

            // Cek berbagai kemungkinan lokasi file
            $paths = [
                public_path($this->file_path),
                public_path('storage/' . $this->file_path),
                public_path('assets/' . $this->name),
                storage_path('app/public/' . str_replace('storage/', '', $this->file_path)),
            ];

            foreach ($paths as $path) {
                if (File::exists($path)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Helper untuk mendapatkan ukuran file
    public function getFileSizeAttribute()
    {
        try {
            if (!$this->file_path) {
                return 'Unknown';
            }

            // Coba berbagai path untuk mendapatkan ukuran
            $paths = [
                public_path($this->file_path),
                public_path('storage/' . $this->file_path),
                storage_path('app/public/' . str_replace('storage/', '', $this->file_path))
            ];

            foreach ($paths as $path) {
                if (File::exists($path)) {
                    $bytes = File::size($path);
                    return $this->formatBytes($bytes);
                }
            }

            return 'File not found';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    // Helper untuk format bytes
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Static method untuk mendapatkan logo aktif berdasarkan tipe
    public static function getActiveLogo($type = 'logo')
    {
        return self::active()->ofType($type)->latest()->first();
    }

    // Tipe-tipe asset yang tersedia
    public static function getAvailableTypes()
    {
        return [
            'logo' => 'Logo',
            'banner' => 'Banner',
            'icon' => 'Icon',
        ];
    }

    // Static method untuk mendapatkan semua logo aktif dengan file yang valid
    public static function getAllActiveLogos()
    {
        $logos = self::active()
                     ->ofType('logo')
                     ->orderBy('created_at', 'asc')
                     ->get();

        // Return semua karena kita punya fallback yang baik
        return $logos;
    }

    // Method untuk debug file path - Windows specific
    public function debugFilePath()
    {
        $info = [
            'id' => $this->id,
            'file_path' => $this->file_path,
            'name' => $this->name,
            'original_name' => $this->original_name,
            'computed_url' => $this->file_url,
            'file_exists' => $this->fileExists(),
            'possible_paths' => []
        ];

        $possiblePaths = [
            'public_direct' => public_path($this->file_path),
            'public_storage' => public_path('storage/' . $this->file_path),
            'public_assets' => public_path('assets/' . $this->name),
            'storage_app' => storage_path('app/public/' . str_replace('storage/', '', $this->file_path)),
        ];

        foreach ($possiblePaths as $key => $path) {
            $info['possible_paths'][$key] = [
                'path' => $path,
                'exists' => File::exists($path),
                'url' => $key === 'storage_app' ? 'N/A' : asset(str_replace(public_path() . DIRECTORY_SEPARATOR, '', $path))
            ];
        }

        return $info;
    }

    // Method untuk cleanup file yang rusak
    public static function cleanupMissingFiles()
    {
        $assets = self::all();
        $removedCount = 0;

        foreach ($assets as $asset) {
            if (!$asset->fileExists()) {
                // Jangan hapus, tapi nonaktifkan saja
                $asset->update(['is_active' => false]);
                $removedCount++;
            }
        }

        return $removedCount;
    }

    // Method untuk sync files dari storage ke public (Windows helper)
    public function syncToPublic()
    {
        try {
            $storagePath = storage_path('app/public/' . str_replace('storage/', '', $this->file_path));
            $publicPath = public_path($this->file_path);

            if (File::exists($storagePath) && !File::exists($publicPath)) {
                $publicDir = dirname($publicPath);
                if (!File::exists($publicDir)) {
                    File::makeDirectory($publicDir, 0755, true);
                }

                File::copy($storagePath, $publicPath);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to sync file to public', [
                'asset_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // Static method untuk sync semua files ke public (Windows mass sync)
    public static function syncAllToPublic()
    {
        $assets = self::all();
        $syncedCount = 0;

        foreach ($assets as $asset) {
            if ($asset->syncToPublic()) {
                $syncedCount++;
            }
        }

        return $syncedCount;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Ketika asset dihapus, hapus file dari berbagai lokasi
        static::deleting(function ($asset) {
            try {
                $paths = [
                    public_path($asset->file_path),
                    public_path('storage/' . $asset->file_path),
                    storage_path('app/public/' . str_replace('storage/', '', $asset->file_path))
                ];

                foreach ($paths as $path) {
                    if (File::exists($path)) {
                        File::delete($path);
                        \Log::info('Deleted asset file', ['path' => $path]);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to delete asset file', [
                    'asset_id' => $asset->id,
                    'file_path' => $asset->file_path,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }
}