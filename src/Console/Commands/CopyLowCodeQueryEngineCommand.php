<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Gupo\BetterLaravel\Traits\InstanceMake;

class CopyLowCodeQueryEngineCommand extends Command
{
    protected $signature = 'lowcode:install-query-engine
                                        {--f : 覆盖现有的文件}';

    protected $description = '安装查询引擎服务';

    public function handle()
    {
        $dir = 'Services/LowCode/';
        $className = 'LowCodeQueryEngineService';
        $targetPath = app_path($dir . $className . '.php');

        // 确保目录存在
        if (!File::exists(app_path($dir))) {
            File::makeDirectory(app_path($dir), 0755, true);
            $this->info('创建文件夹: app/Services/LowCode');
        }

        // 检查文件是否已存在
        if (File::exists($targetPath) && !$this->option('f')) {
            $this->error("{$dir} {$className} 已经存在，请勿重复安装。! 使用 --f 选项覆盖。");
            return;
        }

        // 文件内容
        $content = <<<'EOT'
<?php


declare(strict_types = 1);

namespace App\Services\LowCode;


use Gupo\BetterLaravel\Traits\InstanceMake;
use BrightLiu\LowCode\Core\Abstracts\QueryEngineAbstract;


/**
* @Class
 * @Description: 动态查询
 * @created: 2025-09-30 17:23:48
 * @modifier: 2025-09-30 17:23:48
 */
class LowCodeQueryEngineService extends QueryEngineAbstract
{
    use InstanceMake;
}

EOT;

        // 写入文件
        File::put($targetPath, $content);

        $this->info("安装成功: {$className}");
    }
}