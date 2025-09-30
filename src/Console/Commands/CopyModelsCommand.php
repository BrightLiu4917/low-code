<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyModelsCommand extends Command
{
    protected $signature = 'lowcode:copy-models 
                            {package? : The name of the Composer package}
                            {--a|all : Copy all models from all packages}';

    protected $description = 'Copy model files from Composer packages to app/Models/';

    public function handle()
    {
        if ($this->option('all')) {
            $this->copyAllPackageModels();
            return;
        }

        $package = $this->argument('package');
        if (!$package) {
            $this->error('Please specify a package name or use --all option');
            return;
        }

        $this->copyPackageModels($package);
    }

    protected function copyAllPackageModels()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $packages = array_merge(
            array_keys($composer['require'] ?? []),
            array_keys($composer['require-dev'] ?? [])
        );

        foreach ($packages as $package) {
            $this->copyPackageModels($package);
        }
    }

    protected function copyPackageModels(string $packageName)
    {
        try {
            $packagePath = $this->getPackagePath($packageName);
        } catch (\Exception $e) {
            $this->error("Package not found: {$packageName}");
            return;
        }

        $modelsPath = $packagePath . '/src/Models';
        $targetPath = app_path('Models');

        if (!File::exists($modelsPath)) {
            $this->info("Package {$packageName} has no models directory");
            return;
        }

        $files = File::allFiles($modelsPath);
        if (empty($files)) {
            $this->info("Package {$packageName} has no model files");
            return;
        }

        $copied = 0;
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $targetFile = $targetPath . '/' . $filename;

            // 跳过已经存在的文件
            if (File::exists($targetFile)) {
                $this->line("Skipped: {$filename} (already exists)");
                continue;
            }

            File::copy($file->getPathname(), $targetFile);
            $this->line("Copied: {$filename}");
            $copied++;
        }

        $this->info("Copied {$copied} model files from {$packageName} to app/Models/");
    }

    protected function getPackagePath(string $packageName): string
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $vendorDir = $composer['config']['vendor-dir'] ?? 'vendor';
        $packageDir = base_path($vendorDir . '/' . $packageName);

        if (!File::exists($packageDir)) {
            throw new \Exception("Package directory not found");
        }

        return $packageDir;
    }
}