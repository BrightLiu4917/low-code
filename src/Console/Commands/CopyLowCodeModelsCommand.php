<?php

namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyLowCodeModelsCommand extends Command
{
    protected $signature = 'lowcode:copy-models 
                            {package : Composer包名 (如: vendor/package)}
                            {--f : 覆盖已存在的文件}';

    protected $description = '复制Composer包中的模型文件到app/Models/LowCode目录';

    public function handle()
    {
        $package = $this->argument('package');
        $force = $this->option('f');

        // 确定包路径
        $packagePath = base_path("vendor/{$package}");

        if (!File::exists($packagePath)) {
            $this->error("包未找到: {$package}");
            return;
        }

        // 源模型目录
        $sourceModelsPath = "{$packagePath}/src/Models";

        if (!File::exists($sourceModelsPath)) {
            $this->error("包中没有模型目录: {$sourceModelsPath}");
            return;
        }

        // 目标目录
        $targetPath = app_path('Models/LowCode');

        // 确保目标目录存在
        if (!File::exists($targetPath)) {
            File::makeDirectory($targetPath, 0755, true);
            $this->info("已创建目录: {$targetPath}");
        }

        // 获取所有模型文件
        $files = File::files($sourceModelsPath);

        if (empty($files)) {
            $this->info("包中没有模型文件");
            return;
        }

        $copied = 0;
        $overwritten = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $targetFile = "{$targetPath}/{$filename}";

            // 读取源文件内容
            $content = File::get($file->getPathname());

            // 只替换命名空间
            $content = preg_replace(
                '/namespace\s+[^;]+;/',
                'namespace App\\Models\\LowCode;',
                $content,
                1
            );

            // 检查文件是否已存在
            if (File::exists($targetFile) && !$force) {
                $this->line("已跳过: {$filename} (已存在)");
                $skipped++;
                continue;
            }

            // 写入文件
            File::put($targetFile, $content);

            if (File::exists($targetFile)) {
                if ($force) {
                    $this->line("已覆盖: {$filename}");
                    $overwritten++;
                } else {
                    $this->line("已复制: {$filename}");
                    $copied++;
                }
            }
        }

        $this->info("模型复制完成: 复制 {$copied}, 覆盖 {$overwritten}, 跳过 {$skipped}");
    }
}