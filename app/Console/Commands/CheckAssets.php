<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Vite assets are built';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $manifestPath = public_path('build/manifest.json');
        $hotPath = public_path('hot');
        $buildPath = public_path('build');

        $this->info('🔍 Checking assets...');
        $this->newLine();

        // Check manifest
        if (File::exists($manifestPath)) {
            $this->info('✅ manifest.json found');
            
            $manifest = json_decode(File::get($manifestPath), true);
            if ($manifest) {
                $assetCount = count($manifest);
                $this->info("   📦 Found {$assetCount} asset(s) in manifest");
            }
        } else {
            $this->warn('❌ manifest.json not found');
            $this->line('   Run: php artisan assets:build');
        }

        // Check hot file (dev server)
        if (File::exists($hotPath)) {
            $hotUrl = File::get($hotPath);
            $this->info("✅ Vite dev server running at: {$hotUrl}");
        } else {
            $this->line('ℹ️  Vite dev server not running');
        }

        // Check build directory
        if (File::isDirectory($buildPath)) {
            $files = File::allFiles($buildPath);
            $fileCount = count($files);
            $this->info("📁 Build directory exists ({$fileCount} files)");
        } else {
            $this->warn('❌ Build directory not found');
        }

        $this->newLine();

        // Summary
        if (File::exists($manifestPath) || File::exists($hotPath)) {
            $this->info('✅ Assets are ready!');
            return Command::SUCCESS;
        }

        $this->error('❌ Assets need to be built!');
        $this->line('   Run: php artisan assets:build');
        return Command::FAILURE;
    }
}
