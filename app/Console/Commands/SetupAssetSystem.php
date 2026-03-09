<?php
// app/Console/Commands/SetupAssetSystem.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SetupAssetSystem extends Command
{
    protected $signature = 'assets:setup {--force : Force recreate directories and links}';
    protected $description = 'Setup asset system with proper directories and placeholder images';

    public function handle()
    {
        $this->info('🔧 Setting up Asset System...');

        // 1. Create storage directories
        $this->createStorageDirectories();

        // 2. Create storage link
        $this->createStorageLink();

        // 3. Create placeholder images
        $this->createPlaceholderImages();

        // 4. Set permissions
        $this->setPermissions();

        // 5. Test setup
        $this->testSetup();

        $this->info('✅ Asset system setup completed!');
    }

    private function createStorageDirectories()
    {
        $this->info('📁 Creating storage directories...');

        $directories = [
            storage_path('app/public'),
            storage_path('app/public/assets'),
            public_path('images'),
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0775, true);
                $this->line("Created: {$dir}");
            } else {
                $this->line("Exists: {$dir}");
            }
        }
    }

    private function createStorageLink()
    {
        $this->info('🔗 Creating storage link...');

        $publicStorageLink = public_path('storage');
        $storageAppPublic = storage_path('app/public');

        if (File::exists($publicStorageLink)) {
            if ($this->option('force')) {
                File::delete($publicStorageLink);
                $this->line("Removed existing storage link");
            } else {
                $this->line("Storage link already exists");
                return;
            }
        }

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows
                $output = shell_exec('mklink /D "' . $publicStorageLink . '" "' . $storageAppPublic . '" 2>&1');
                $this->line("Windows link created: {$output}");
            } else {
                // Unix/Linux
                symlink($storageAppPublic, $publicStorageLink);
                $this->line("Unix symlink created");
            }
            
            $this->info("✅ Storage link created successfully");
        } catch (\Exception $e) {
            $this->error("❌ Failed to create storage link: " . $e->getMessage());
            $this->warn("Please run manually: php artisan storage:link");
        }
    }

    private function createPlaceholderImages()
    {
        $this->info('🖼️ Creating placeholder images...');

        $placeholders = [
            'images/logo-placeholder.png' => $this->generatePlaceholderSVG('LOGO', '#e9ecef', '#6c757d'),
            'images/default-logo.png' => $this->generatePlaceholderSVG('DEFAULT LOGO', '#f8f9fa', '#adb5bd'),
            'storage/placeholder.png' => $this->generatePlaceholderSVG('NO IMAGE', '#fff3cd', '#856404'),
        ];

        foreach ($placeholders as $path => $svgContent) {
            $fullPath = public_path($path);
            
            // Create directory if not exists
            $directory = dirname($fullPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0775, true);
            }

            // Convert SVG to PNG using simple base64 method
            $pngData = $this->svgToPng($svgContent);
            File::put($fullPath, $pngData);
            
            $this->line("Created placeholder: {$path}");
        }
    }

    private function generatePlaceholderSVG($text, $bgColor = '#e9ecef', $textColor = '#6c757d')
    {
        return '
        <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <rect width="200" height="200" fill="' . $bgColor . '" stroke="' . $textColor . '" stroke-width="2" stroke-dasharray="5,5"/>
            <text x="100" y="90" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" font-weight="bold" fill="' . $textColor . '">' . $text . '</text>
            <text x="100" y="110" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="' . $textColor . '">200x200</text>
            <text x="100" y="130" text-anchor="middle" font-family="Arial, sans-serif" font-size="10" fill="' . $textColor . '">Placeholder Image</text>
        </svg>';
    }

    private function svgToPng($svgContent)
    {
        // Simple method: create a basic PNG placeholder
        // In real implementation, you might want to use ImageMagick or GD
        
        // Create a simple 1x1 transparent PNG data
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        return $pngData;
    }

    private function setPermissions()
    {
        $this->info('🔒 Setting permissions...');

        $paths = [
            storage_path('app/public'),
            storage_path('app/public/assets'),
            public_path('storage'),
            public_path('images'),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                chmod($path, 0775);
                $this->line("Set 775 permission: {$path}");
            }
        }
    }

    private function testSetup()
    {
        $this->info('🧪 Testing setup...');

        $tests = [
            'Storage link exists' => File::exists(public_path('storage')),
            'Storage link is valid' => is_link(public_path('storage')),
            'Assets directory exists' => File::exists(storage_path('app/public/assets')),
            'Assets directory writable' => is_writable(storage_path('app/public/assets')),
            'Placeholder images exist' => File::exists(public_path('images/logo-placeholder.png')),
        ];

        foreach ($tests as $test => $result) {
            $icon = $result ? '✅' : '❌';
            $this->line("{$icon} {$test}");
        }

        $allPassed = !in_array(false, $tests);
        
        if ($allPassed) {
            $this->info('🎉 All tests passed!');
        } else {
            $this->warn('⚠️ Some tests failed. Check the issues above.');
        }
    }
}