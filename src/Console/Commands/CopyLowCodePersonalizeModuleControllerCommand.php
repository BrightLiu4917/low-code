<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use App\Models\LowCode\LowCodeList;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Gupo\BetterLaravel\Http\BaseController;
use App\Services\LowCode\LowCodeListService;
use App\Http\V2\Resources\Foundation\ListResource;
use App\Services\LowCode\LowCodeQueryEngineService;
use App\Http\V2\Resources\Foundation\RoutesResource;
use App\Http\Resources\LowCode\LowCodeList\QuerySource;
use App\Services\Business\Foundation\PersonalizeModuleService;


class CopyLowCodePersonalizeModuleControllerCommand extends Command
{
    protected $signature = 'lowcode:install-personalize-module-controller 
                                            {--f : 覆盖现有的文件}';

    protected $description = '安装低代码-列表控制器';

    public function handle()
    {
        $className = 'LowCodePersonalizeModuleController';
        $dir = "Http/Controllers/LowCode";
        $targetDir = app_path($dir);
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $className . '.php';

        // 确保目录存在
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
            $this->info("创建文件夹成功: {$targetDir}");
        }

        // 检查文件是否已存在
        if (File::exists($targetPath) && !$this->option('f')) {
            $this->error("{$dir} {$className} 已经存在，请勿重复安装。! 使用 --f 选项覆盖。");
            return;
        }

        // 文件内容
        $content = <<<'EOT'
<?php

declare(strict_types=1);

namespace App\Http\Controllers\LowCode;

use BrightLiu\LowCode\Requests\Foundation\PersonalizeMenu\SaveRequest;
use BrightLiu\LowCode\Resources\PersonalizeMenu\ListResource;
use BrightLiu\LowCode\Resources\PersonalizeMenu\RoutesResource;
use App\Models\LowCode\LowCodePersonalizeModule;
use BrightLiu\LowCode\Services\LowCode\LowCodePersonalizeModuleService;
use Gupo\BetterLaravel\Http\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 个性化模块
 */
final class PersonalizeModuleController extends BaseController
{
    /**
     * 列表
     */
    public function list(Request $request): JsonResponse
    {
        $moduleType = (string) $request->input('module_type', 'crowd_patients');

        $data = LowCodePersonalizeModule::query()
//            ->byContextDisease() 
            ->where('module_type', $moduleType)
            ->orderByDesc('weight')
            ->get(['id', 'title', 'module_id', 'module_type', 'metadata', 'created_at']);

        return $this->responseData([
            'list' => ListResource::collection($data),
        ]);
    }

    /**
     * 路由
     */
    public function routes(Request $request): JsonResponse
    {
        $moduleType = (string) $request->input('module_type', 'crowd_patients');

        $data = LowCodePersonalizeModule::query()
            ->byContextDisease()
            ->where('module_type', $moduleType)
            ->orderByDesc('weight')
            ->get(['id', 'title', 'module_id', 'module_type', 'metadata', 'created_at']);

        return $this->responseData($data, RoutesResource::class);
    }

    /**
     * 保存
     */
    public function save(SaveRequest $request, LowCodePersonalizeModuleService $srv): JsonResponse
    {
        $items = (array) $request->input('items', []);

        $srv->save($items, defaultModuleType: 'crowd_patients');

        return $this->responseSuccess();
    }
}


EOT;

        // 写入文件
        File::put($targetPath, $content);

        $this->info("已安装成功: {$targetPath}");
    }
}