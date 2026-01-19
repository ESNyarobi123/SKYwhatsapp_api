<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class EnsureAssetsBuilt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check in development or if explicitly enabled
        if (config('app.env') === 'production' && ! config('app.auto_build_assets', false)) {
            return $next($request);
        }

        $manifestPath = public_path('build/manifest.json');
        $hotPath = public_path('hot');

        // If assets exist or hot file exists (dev server running), continue
        if (File::exists($manifestPath) || File::exists($hotPath)) {
            return $next($request);
        }

        // Try to build assets automatically (only once per request cycle)
        if (! $this->isBuilding()) {
            $this->buildAssets();
        }

        return $next($request);
    }

    /**
     * Check if assets are currently being built.
     */
    protected function isBuilding(): bool
    {
        $lockFile = storage_path('framework/assets-building.lock');
        
        if (File::exists($lockFile)) {
            $lockTime = File::lastModified($lockFile);
            // If lock is older than 5 minutes, assume build failed/stuck
            if (time() - $lockTime > 300) {
                File::delete($lockFile);
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Build assets automatically.
     */
    protected function buildAssets(): void
    {
        $lockFile = storage_path('framework/assets-building.lock');
        
        // Create lock file
        File::put($lockFile, time());

        try {
            // Run build command in background (non-blocking)
            $artisanPath = base_path('artisan');
            $command = "php {$artisanPath} assets:build";
            
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows - run in background
                $command = "start /B {$command}";
            } else {
                // Unix/Linux - run in background
                $command = "{$command} > /dev/null 2>&1 &";
            }

            exec($command);
        } catch (\Exception $e) {
            // Silently fail - don't break the request
            if (File::exists($lockFile)) {
                File::delete($lockFile);
            }
        }
    }
}
