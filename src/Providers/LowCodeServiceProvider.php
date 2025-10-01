<?php
namespace BrightLiu\LowCode\Providers;

use Illuminate\Support\ServiceProvider;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeModelsCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeQueryResourceCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeQueryEngineCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeCacheObserverCommand;
use BrightLiu\LowCode\Console\Commands\CopyLowCodeListControllerCommand;


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
                CopyLowCodeListControllerCommand::class
            ]);
        }
    }
    protected function publishConfig()
    {
        // 只在控制台环境下发布资源
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/low-code.php' => config_path('low-code.php'),
            ], 'low-code-config');
        }
    }

}