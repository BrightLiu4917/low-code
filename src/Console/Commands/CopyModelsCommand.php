<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class CopyModelsCommand extends Command
{
    protected $signature = 'lowcode:copy-models 
                            {package? : The name of the Composer package}
                            {--a|all : Copy all models from all packages}
                            {--f|force : Overwrite existing files}
                            {--m|merge : Merge changes instead of overwriting}';

    protected $description = 'Copy model files from Composer packages to app/Models/LowCode';

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
        $targetPath = app_path('Models/LowCode');

        // 确保目标目录存在
        if (!File::exists($targetPath)) {
            File::makeDirectory($targetPath, 0755, true);
            $this->info("Created directory: {$targetPath}");
        }

        if (!File::exists($modelsPath)) {
            $this->info("Package {$packageName} has no models directory");
            return;
        }

        // 获取所有文件（不包括目录）
        $files = $this->getModelFiles($modelsPath);

        if (empty($files)) {
            $this->info("Package {$packageName} has no model files");
            return;
        }

        $copied = 0;
        $overwritten = 0;
        $merged = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $targetFile = $targetPath . '/' . $filename;
            $sourceContent = File::get($file->getPathname());

            // 如果目标文件不存在，直接复制
            if (!File::exists($targetFile)) {
                File::put($targetFile, $sourceContent);
                $this->line("Copied: {$filename}");
                $copied++;
                continue;
            }

            // 处理已存在的文件
            $targetContent = File::get($targetFile);

            // 强制覆盖选项
            if ($this->option('force')) {
                File::put($targetFile, $sourceContent);
                $this->line("Overwritten: {$filename}");
                $overwritten++;
                continue;
            }

            // 合并选项
            if ($this->option('merge')) {
                $mergedContent = $this->mergeModelFiles($sourceContent, $targetContent);
                File::put($targetFile, $mergedContent);
                $this->line("Merged: {$filename}");
                $merged++;
            } else {
                $this->line("Skipped: {$filename} (already exists)");
            }
        }

        $this->info("Copied {$copied}, overwritten {$overwritten}, merged {$merged} model files from {$packageName}");
    }

    /**
     * 获取模型目录下的所有文件（不包括子目录）
     */
    protected function getModelFiles(string $path): array
    {
        $files = [];

        // 只获取顶级目录下的文件
        $allFiles = File::files($path);

        // 过滤掉目录，只保留文件
        foreach ($allFiles as $file) {
            if ($file->isFile()) {
                $files[] = $file;
            }
        }

        return $files;
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

    /**
     * 合并模型文件内容
     */
    protected function mergeModelFiles(string $source, string $target): string
    {
        // 简单合并策略：保留目标文件的类定义，合并属性
        $sourceLines = explode("\n", $source);
        $targetLines = explode("\n", $target);

        $merged = [];
        $inProperties = false;

        // 保留目标文件的命名空间和类定义
        foreach ($targetLines as $line) {
            if (strpos($line, 'namespace ') !== false ||
                strpos($line, 'class ') !== false) {
                $merged[] = $line;
            }
        }

        // 合并use语句
        $useStatements = [];
        foreach (array_merge($sourceLines, $targetLines) as $line) {
            if (strpos($line, 'use ') === 0 && !in_array($line, $useStatements)) {
                $useStatements[] = $line;
            }
        }
        $merged = array_merge($merged, $useStatements);
        $merged[] = ''; // 空行分隔

        // 添加源文件的属性
        foreach ($sourceLines as $line) {
            if (strpos($line, 'protected $fillable') !== false ||
                strpos($line, 'protected $casts') !== false ||
                strpos($line, 'protected $appends') !== false ||
                strpos($line, 'public function ') !== false) {
                $merged[] = $line;
            }
        }

        return implode("\n", $merged);
    }
}