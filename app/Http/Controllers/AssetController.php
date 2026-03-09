<?php
// app/Http/Controllers/AssetController.php
namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class AssetController extends Controller
{
    private function checkAdminAuth()
    {
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }
        return null;
    }

    private function checkAssetPermission()
    {
        $authCheck = $this->checkAdminAuth();
        if ($authCheck) return $authCheck;

        $admin = AdminUser::find(session('admin_id'));
        if (!$admin || (!$admin->isSuperAdmin() && $admin->role !== 'admin')) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
        }
        return null;
    }

    // Halaman manajemen assets
    public function index()
    {
        $authCheck = $this->checkAssetPermission();
        if ($authCheck) return $authCheck;

        try {
            $currentAdmin = AdminUser::find(session('admin_id'));
            $assets = Asset::orderBy('type')->orderBy('created_at', 'desc')->get();
            $availableTypes = Asset::getAvailableTypes();

            // Handle special actions
            if (request()->get('repair') == 'true') {
                return $this->repair();
            }

            if (request()->get('check_storage') == 'true') {
                return $this->checkStorage();
            }

            if (request()->get('cleanup') == 'true') {
                $removedCount = Asset::cleanupMissingFiles();
                if ($removedCount > 0) {
                    session()->flash('success', "✅ Berhasil menonaktifkan {$removedCount} asset dengan file yang hilang.");
                } else {
                    session()->flash('info', "ℹ️ Semua asset dalam kondisi baik.");
                }
            }

            return view('admin.assets.index', compact('assets', 'currentAdmin', 'availableTypes'));

        } catch (Exception $e) {
            Log::error('Asset index error', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')
                           ->with('error', 'Terjadi kesalahan saat memuat halaman assets.');
        }
    }

    // Form upload asset baru
    public function create()
    {
        $authCheck = $this->checkAssetPermission();
        if ($authCheck) return $authCheck;

        $availableTypes = Asset::getAvailableTypes();
        return view('admin.assets.create', compact('availableTypes'));
    }

    // Simpan asset baru - FIXED untuk single file upload
    public function store(Request $request)
    {
        $authCheck = $this->checkAssetPermission();
        if ($authCheck) return $authCheck;

        try {
            // Debug log untuk troubleshooting
            Log::info('Asset store request data', [
                'type' => $request->input('type'),
                'has_file' => $request->hasFile('file'),
                'has_files' => $request->hasFile('files'),
                'request_files' => $request->file(),
                'all_input' => $request->all()
            ]);

            // Validasi request untuk SINGLE file upload
            $request->validate([
                'type' => 'required|in:' . implode(',', array_keys(Asset::getAvailableTypes())),
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ], [
                'file.required' => 'File gambar harus dipilih.',
                'file.image' => 'File harus berupa gambar.',
                'file.mimes' => 'Format file harus JPEG, PNG, JPG, GIF, SVG, atau WebP.',
                'file.max' => 'Ukuran file maksimal 2MB.',
                'type.required' => 'Tipe asset harus dipilih.',
                'type.in' => 'Tipe asset tidak valid.'
            ]);

            // Pastikan file ada
            if (!$request->hasFile('file')) {
                throw new Exception('File tidak ditemukan dalam request.');
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Validasi file tambahan
            if (!$file->isValid()) {
                throw new Exception('File tidak valid atau rusak.');
            }

            // Buat nama file yang safe dan unique
            $safeName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $fileName = time() . '_' . $safeName . '.' . $extension;

            // Pastikan direktori ada
            $assetsDir = storage_path('app/public/assets');
            if (!File::exists($assetsDir)) {
                File::makeDirectory($assetsDir, 0775, true);
                Log::info('Created assets directory', ['path' => $assetsDir]);
            }

            // Upload file
            $filePath = $file->storeAs('assets', $fileName, 'public');

            if (!$filePath) {
                throw new Exception('Gagal menyimpan file ke storage.');
            }

            // Verify file tersimpan
            $fullStoragePath = storage_path('app/public/' . $filePath);
            if (!File::exists($fullStoragePath)) {
                throw new Exception('File tidak ditemukan setelah upload.');
            }

            // Simpan ke database
            $asset = Asset::create([
                'type' => $request->type,
                'name' => $fileName,
                'file_path' => $filePath, // Format: assets/filename.ext
                'original_name' => $originalName,
                'is_active' => true, // Langsung aktif
                'description' => $request->description ?? null,
            ]);

            // Log berhasil
            Log::info('Asset uploaded successfully', [
                'asset_id' => $asset->id,
                'file_path' => $filePath,
                'storage_path' => $fullStoragePath,
                'file_exists' => File::exists($fullStoragePath),
                'original_name' => $originalName,
                'uploaded_by' => session('admin_id')
            ]);

            return redirect()->route('admin.assets.index')
                            ->with('success', '✅ Asset "' . $asset->original_name . '" berhasil diupload! Logo sekarang tampil di halaman survei.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            Log::warning('Asset upload validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all(),
                'admin_id' => session('admin_id')
            ]);

            return redirect()->back()
                            ->withErrors($e->errors())
                            ->withInput()
                            ->with('error', '❌ Validasi gagal. Periksa form input Anda.');

        } catch (Exception $e) {
            Log::error('Asset upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_info' => $request->hasFile('file') ? [
                    'original_name' => $request->file('file')->getClientOriginalName(),
                    'size' => $request->file('file')->getSize(),
                    'mime_type' => $request->file('file')->getMimeType(),
                    'is_valid' => $request->file('file')->isValid(),
                ] : 'No file uploaded',
                'admin_id' => session('admin_id')
            ]);

            return redirect()->back()
                            ->withInput()
                            ->with('error', '❌ Gagal mengupload asset: ' . $e->getMessage());
        }
    }

    // Toggle status aktif asset
    public function toggle($id)
    {
        $authCheck = $this->checkAssetPermission();
        if ($authCheck) return $authCheck;

        try {
            $asset = Asset::findOrFail($id);
            
            // Cek file sebelum mengaktifkan
            if (!$asset->is_active && !$asset->fileExists()) {
                return redirect()->back()
                               ->with('error', '❌ File asset "' . $asset->original_name . '" tidak ditemukan. Silakan upload ulang.');
            }
            
            $asset->update(['is_active' => !$asset->is_active]);
            
            $status = $asset->is_active ? 'diaktifkan' : 'dinonaktifkan';
            $icon = $asset->is_active ? '✅' : '⏸️';
            
            Log::info('Asset status toggled', [
                'asset_id' => $asset->id,
                'new_status' => $asset->is_active,
                'admin_id' => session('admin_id')
            ]);
            
            return redirect()->route('admin.assets.index')
                            ->with('success', $icon . ' Asset "' . $asset->original_name . '" berhasil ' . $status . '.');
                            
        } catch (Exception $e) {
            Log::error('Asset toggle error', [
                'asset_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                            ->with('error', '❌ Gagal mengubah status asset: ' . $e->getMessage());
        }
    }

    // Hapus asset
    public function destroy($id)
    {
        $authCheck = $this->checkAssetPermission();
        if ($authCheck) return $authCheck;

        try {
            $asset = Asset::findOrFail($id);
            $assetName = $asset->original_name;
            
            // Asset akan otomatis hapus file melalui boot method
            $asset->delete();

            Log::info('Asset deleted successfully', [
                'asset_id' => $id,
                'asset_name' => $assetName,
                'admin_id' => session('admin_id')
            ]);

            return redirect()->route('admin.assets.index')
                            ->with('success', '🗑️ Asset "' . $assetName . '" berhasil dihapus beserta filenya.');
                            
        } catch (Exception $e) {
            Log::error('Asset delete error', [
                'asset_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                            ->with('error', '❌ Gagal menghapus asset: ' . $e->getMessage());
        }
    }

    // Repair assets
    public function repair()
    {
        $authCheck = $this->checkAssetPermission();
        if ($authCheck) return $authCheck;

        try {
            $assets = Asset::all();
            $repairedCount = 0;
            $disabledCount = 0;

            foreach ($assets as $asset) {
                if (!$asset->fileExists()) {
                    if ($asset->is_active) {
                        $asset->update(['is_active' => false]);
                        $disabledCount++;
                    }
                } else {
                    if (!$asset->is_active && $asset->type === 'logo') {
                        $asset->update(['is_active' => true]);
                        $repairedCount++;
                    }
                }
            }

            $message = "🔧 Perbaikan selesai. ";
            if ($repairedCount > 0) {
                $message .= "Diaktifkan kembali: {$repairedCount} asset. ";
            }
            if ($disabledCount > 0) {
                $message .= "Dinonaktifkan: {$disabledCount} asset yang filenya hilang.";
            }
            if ($repairedCount == 0 && $disabledCount == 0) {
                $message .= "Semua asset dalam kondisi baik.";
            }

            return redirect()->route('admin.assets.index')
                            ->with('success', $message);

        } catch (Exception $e) {
            Log::error('Asset repair error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                            ->with('error', '❌ Gagal melakukan perbaikan asset: ' . $e->getMessage());
        }
    }

    // Check storage
    public function checkStorage()
    {
        $authCheck = $this->checkAssetPermission();
        if ($authCheck) return $authCheck;

        try {
            $publicPath = public_path('storage');
            $storagePath = storage_path('app/public');
            
            $storageLinked = is_link($publicPath) && readlink($publicPath) === $storagePath;
            
            $message = $storageLinked 
                ? "✅ Storage link sudah tersedia dengan benar."
                : "❌ Storage link belum dibuat. Jalankan: php artisan storage:link";
            
            return redirect()->route('admin.assets.index')
                            ->with($storageLinked ? 'success' : 'error', $message);

        } catch (Exception $e) {
            return redirect()->back()
                            ->with('error', '❌ Gagal mengecek storage: ' . $e->getMessage());
        }
    }
}