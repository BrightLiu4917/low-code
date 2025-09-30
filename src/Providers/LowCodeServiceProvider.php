<?php
namespace BrightLiu\LowCode\Providers;

use Illuminate\Support\ServiceProvider;
use BrightLiu\LowCode\Console\Commands\GenerateCommand;
use BrightLiu\LowCode\Console\Commands\CopyModelsCommand;

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
                CopyModelsCommand::class,
            ]);
        }
    }
}