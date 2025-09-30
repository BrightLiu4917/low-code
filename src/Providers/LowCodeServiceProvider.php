<?php
namespace BrightLiu\LowCode\Providers;

use Illuminate\Support\ServiceProvider;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeModelsCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeQueryEngineCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeCacheObserverCommand;


class LowCodeServiceProvider extends ServiceProvider
{
    /**
     * 注册服务提供者
     */
    public function register()
    {

    }

    /**
     * 启动服务提供者
     */
    public function boot()
    {
        // 注册命令行
        if ($this->app->runningInConsole()) {
            $this->commands([
                CopyLowCodeModelsCommand::class,
                CopyLowCodeCacheObserverCommand::class,
                CopyLowCodeQueryEngineCommand::class
            ]);
        }
    }
}