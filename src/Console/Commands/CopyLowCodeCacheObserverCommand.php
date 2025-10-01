<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyLowCodeCacheObserverCommand extends Command
{
    protected $signature = 'lowcode:install-cache-observer 
                            {--f : 覆盖现有的文件}';

    protected $description = '安装缓存观察者';

    public function handle()
    {
        $dir = 'Observers';
        $className = 'CacheableModelObserver';
        $targetPath = app_path('Observers/' . $className . '.php');
        // 确保目录存在
        if (!File::exists(app_path($dir))) {
            File::makeDirectory(app_path($dir), 0755, true);
            $this->info("文件夹创建成功: app/{$dir}");
        }

        // 检查文件是否已存在
        if (File::exists($targetPath) && !$this->option('f')) {
            $this->error("{$dir} {$className} 已经存在，请勿重复安装。! 使用 --f 选项覆盖。");
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

        $this->info("已安装成功: {$className}");
    }
}