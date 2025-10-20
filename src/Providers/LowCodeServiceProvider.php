<?php

namespace BrightLiu\LowCode\Providers;

use Illuminate\Support\ServiceProvider;
use BrightLiu\LowCode\Console\Commands\LowCodeInitCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeModelsCommand;
use BrightLiu\LowCode\Console\Commands\CopyCrowdKitServiceCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeListServiceCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeQueryResourceCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeQueryEngineCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeCacheObserverCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeListControllerCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeListV2ControllerCommand;
use BrightLiu\LowCode\Console\Commands\CopyBasicInfoResourceResourceCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodePersonalizeModuleControllerCommand;
use BrightLiu\LowCode\Console\Commands\CopyBmpCheetahMedicalCrowdkitApiServiceCommand;
use BrightLiu\LowCode\Console\Commands\CopyBmpCheetahMedicalPlatformApiServiceCommand;


class LowCodeServiceProvider extends ServiceProvider
{
    /**
     * 注册服务提供者
     */
    public function register()
    {
        // 合并配置文件
        $this->mergeConfigFrom(
            __DIR__.'/../../config/low-code.php', 'low-code'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../../config/medical-platform.php', 'medical-platform'
        );
    }

    /**
     * 启动服务提供者
     */
    public function boot()
    {
        $this->publishConfig();
        // 注册命令行
        if ($this->app->runningInConsole()) {
            $this->commands([
                CopyLowCodeModelsCommand::class,
                CopyLowCodeCacheObserverCommand::class,
                CopyLowCodeQueryEngineCommand::class,
                CopyLowCodeQueryResourceCommand::class,
                CopyLowCodeListControllerCommand::class,
                CopyLowCodeListServiceCommand::class,
                CopyLowCodePersonalizeModuleControllerCommand::class,
                CopyLowCodeListV2ControllerCommand::class,
                CopyBmpCheetahMedicalCrowdkitApiServiceCommand::class,
                CopyCrowdKitServiceCommand::class,
                LowCodeInitCommand::class,
                CopyBasicInfoResourceResourceCommand::class,
                CopyBmpCheetahMedicalPlatformApiServiceCommand::class
            ]);
        }
    }

    protected function publishConfig()
    {
        // 只在控制台环境下发布资源
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.
                '/../../config/low-code.php' => config_path('low-code.php'),
            ], 'low-code-config');

            $this->publishes([
                __DIR__.
                '/../../config/medical-platform.php' => config_path('medical-platform.php'),
            ], 'medical-platform');
        }
    }

}