<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BuildAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:build {--force : Force rebuild even if assets exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Vite assets automatically if they don\'t exist';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $buildPath = public_path('build');
        $manifestPath = $buildPath . '/manifest.json';

        // Check if assets already exist
        if (File::exists($manifestPath) && ! $this->option('force')) {
            $this->info('✅ Assets already built. Use --force to rebuild.');
            return Command::SUCCESS;
        }

        $this->info('🔨 Building assets...');

        // Check if node_modules exists
        if (! File::exists(base_path('node_modules'))) {
            $this->warn('⚠️  node_modules not found. Installing dependencies...');
            $this->callNpmInstall();
        }

        // Build assets
        $this->info('📦 Running npm run build...');
        $exitCode = $this->callNpmBuild();

        if ($exitCode === Command::SUCCESS) {
            $this->info('✅ Assets built successfully!');
            return Command::SUCCESS;
        }

        $this->error('❌ Failed to build assets.');
        return Command::FAILURE;
    }

    /**
     * Call npm install command.
     */
    protected function callNpmInstall(): int
    {
        $command = PHP_OS_FAMILY === 'Windows' ? 'npm.cmd install' : 'npm install';
        $this->info("Running: {$command}");
        
        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            base_path()
        );

        if (! is_resource($process)) {
            $this->error('Failed to start npm install');
            return Command::FAILURE;
        }

        // Read output
        while ($line = fgets($pipes[1])) {
            $this->line(trim($line));
        }

        $exitCode = proc_close($process);
        return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Call npm build command.
     */
    protected function callNpmBuild(): int
    {
        $command = PHP_OS_FAMILY === 'Windows' ? 'npm.cmd run build' : 'npm run build';
        $this->info("Running: {$command}");
        
        $process = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            base_path()
        );

        if (! is_resource($process)) {
            $this->error('Failed to start npm build');
            return Command::FAILURE;
        }

        // Read output
        while ($line = fgets($pipes[1])) {
            $this->line(trim($line));
        }

        $exitCode = proc_close($process);
        return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
