<?php

namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class LowCodeInitCommand extends Command
{
    protected $signature = 'lowcode:init 
                            {--f : 覆盖已存在的文件}';

    protected $description = '初始化低代码环境，安装所有必要组件';

    public function handle()
    {
        $force = $this->option('f') ? ['--f' => true] : [];

        // 基础命令列表
        $commands = [
            'lowcode:install-cache-observer' => $force,
            'lowcode:install-list-controller' => $force,
            'lowcode:install-list-service' => $force,
            'lowcode:install-query-engine' => $force,
            'lowcode:install-list-query-resource' => $force,
            'lowcode:install-personalize-module-controller'=> $force,
            'lowcode:install-list-v2-controller' => $force,
            'lowcode:install-crowd-kit-service' => $force,
            'lowcode:install-BmpCheetahMedicalCrowdkitApiService' => $force,
            'lowcode:install-basic-info-resource'=> $force,
        ];


        $this->info("开始初始化低代码环境...");

        $successCount = 0;
        $errorCount = 0;

        foreach ($commands as $command => $options) {
            try {
                $this->info("执行命令: {$command}");
                Artisan::call($command, $options);
                $output = Artisan::output();

                if (!empty($output)) {
                    $this->line($output);
                }

                $successCount++;
            } catch (\Exception $e) {
                $this->error("执行 {$command} 失败: " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->info("低代码环境初始化完成: {$successCount} 成功, {$errorCount} 失败");
    }
}