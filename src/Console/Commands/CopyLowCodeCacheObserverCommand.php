<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyLowCodeCacheObserverCommand extends Command
{
    protected $signature = 'lowcode:install-observer 
                            {--f|force : Overwrite existing file}';

    protected $description = 'Install CacheableModelObserver to app/Observers';

    public function handle()
    {
        $className = 'CacheableModelObserver';
        $targetPath = app_path('Observers/' . $className . '.php');

        // 确保目录存在
        if (!File::exists(app_path('Observers'))) {
            File::makeDirectory(app_path('Observers'), 0755, true);
            $this->info('Created directory: app/Observers');
        }

        // 检查文件是否已存在
        if (File::exists($targetPath) && !$this->option('force')) {
            $this->error("Observer {$className} already exists! Use --force to overwrite.");
            return;
        }

        // 文件内容
        $content = <<<'EOT'
<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use BrightLiu\LowCode\Services\CacheModel\CacheModelService;

/**
 * CacheableModelObserver 类用于监听模型的变化，并在模型保存或删除时清除相关缓存。
 */
class CacheableModelObserver
{
    /**
     * 当模型被保存时触发此方法，清除该模型的缓存。
     *
     * @param Model $model 被保存的模型实例
     * @return void
     */
    public function saved(Model $model)
    {
        // 调用缓存服务清除该模型的缓存
        CacheModelService::clearModelCache($model);
    }

    /**
     * 当模型被删除时触发此方法，清除该模型的缓存。
     *
     * @param Model $model 被删除的模型实例
     * @return void
     */
    public function deleted(Model $model)
    {
        // 调用缓存服务清除该模型的缓存
        CacheModelService::clearModelCache($model);
    }
}
EOT;

        // 写入文件
        File::put($targetPath, $content);

        $this->info("Observer installed successfully: {$className}");
    }
}