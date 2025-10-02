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
use App\Models\LowCode\LowCodeTemplateHasPart;
use App\Models\LowCode\LowCodePersonalizeModule;
use App\Services\LowCode\LowCodeQueryEngineService;
use App\Http\Resources\LowCode\LowCodeList\QuerySource;
use BrightLiu\LowCode\Requests\LowCode\LowCodeListRequest;
use BrightLiu\LowCode\Resources\LowCode\LowCodeList\ListSource;
use BrightLiu\LowCode\Enums\Model\LowCode\LowCodeList\ListTypeEnum;
use BrightLiu\LowCode\Resources\LowCode\LowCodeList\simpleListSource;

class CopyLowCodeListV2ControllerCommand extends Command
{
    protected $signature = 'lowcode:install-list-v2-controller 
                                            {--f : 覆盖现有的文件}';

    protected $description = '安装低代码-列表控制器';

    public function handle()
    {
        $className = 'LowCodeListV2Controller';
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

use BrightLiu\LowCode\Enums\Model\LowCode\AdminPreference\SceneEnum;//ok
use App\Services\LowCode\LowCodeQueryEngineService;//ok
use BrightLiu\LowCode\Enums\Model\LowCode\LowCodeList\ListTypeEnum;//ok
use App\Http\Resources\LowCode\LowCodeList\QuerySource;//ok
use BrightLiu\LowCode\Resources\LowCode\V2\LowCodeList\SimpleListSource;//ok
use App\Models\LowCode\AdminPreference;//ok
use App\Models\LowCode\LowCodePersonalizeModule;//ok
use App\Models\LowCode\LowCodeList;//ok
use App\Models\LowCode\LowCodePart;//ok
use App\Models\LowCode\LowCodeTemplateHasPart;//ok
use App\Services\LowCode\CrowdKitService;//om
use App\Services\LowCode\LowCodeListService;//ok
use App\Support\BmpCrowd\CrowdConnection;
//use AdminContext;
use Gupo\BetterLaravel\Http\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * 低代码-列表
 */
final class LowCodeListV2Controller extends BaseController
{
    /**
     * 简单列表
     */
    public function simpleList(Request $request): JsonResponse
    {
        $list = LowCodeList::query()
//                           ->byContextOrg()
//                           ->byContextDisease()
                           ->where('list_type', '<>', ListTypeEnum::GENERAL)
                           ->select([
                               'id', 'admin_name', 'code', 'parent_code', 'crowd_type_code', 'route_group',
                           ])
                           ->customPaginate(true);

        try {
            // 追加个性化菜单
            $personalizeModuels = LowCodePersonalizeModule::query()
//                                                   ->byContextDisease()
                                                   ->where('module_type', 'crowd_patients')
                                                   ->orderByDesc('weight')
                                                   ->get(['id', 'title', 'module_id', 'module_type', 'metadata', 'created_at'])
                                                   ->map(fn (LowCodePersonalizeModule $item) => new LowCodeList([
                                                       'id' => $item->id,
                                                       'admin_name' => $item->title,
                                                       'code' => $item->module_id,
                                                       'parent_code' => '',
                                                       'route_group' => [
                                                           $item->metadata['path'],
                                                       ],
                                                   ]));

            // 修改$list分页对象的数据
            $list->setCollection($list->getCollection()->merge($personalizeModuels));
        } catch (\Throwable $e) {
        }

        return $this->responseData($list, SimpleListSource::class);
    }

    /**
     * 查询数量
     */
    public function queryCount(Request $request): JsonResponse
    {
        $data = [];

        $codes = $request->input('codes', null);

        // TODO: 按人群患者查询时，需要携带条件

        foreach ($codes as $key => $code) {
            $data[$key]['crowd_type_total_count']
                = LowCodeQueryEngineService::instance()
                                           ->autoClient()
                                           ->whereListPresetCondition($code)
                                           ->setCache(10)
                                           ->getCountResult();

            $data[$key]['crowd_type_code'] = $code;
        }

        return $this->responseData($data);
    }

    /**
     * 预请求
     */
    public function pre(Request $request, LowCodeListService $srv): JsonResponse
    {
        $code = (string) $request->input('code', null);

        $data = $srv->pre($this->covertCrowdPatientCode($code));

        try {
            $preference = AdminPreference::query()
                                         ->where('scene', SceneEnum::LIST_COLUMNS)
                                         ->where('pkey', $code)
                                         ->value('pvalue');

            if (!empty($preference)) {
                $data['pre_config']['column'] = array_map(
                    fn ($item) => [
                        'title' => $item['name'],
                        'key' => $item['column'],
                    ],
                    $preference
                );
            }
        } catch (\Throwable) {
        }

        return $this->responseData($data);
    }

    /**
     * 查询数据
     */
    public function query(Request $request, LowCodeListService $srv): JsonResponse
    {
        $inputArgs = $request->input('input_args');

        $params = $request->except(
            ['input_args', 'undefined', 'per_page', 'page']
        );

        $codes = $this->covertCrowdPatientCode(array_column($inputArgs, 'code'));

        $inputArgs = array_map(
            function ($item) use ($codes) {
                // 为人群code时，携带人群code条件
                if ($item['code'] !== $codes[$item['code']]) {
                    $item['filters'][] = ['crowd_id', '=', $item['code']];
                }

                // 将人群code映射到"通用人群页"
                $item['code'] = $codes[$item['code']] ?? $item['code'];

                return $item;
            },
            $inputArgs
        );

        $data = $srv->query($inputArgs, $params);

        // TODO: 写法待完善
        try {
            // 追加人群分类信息
            $crowds = CrowdConnection::table('feature_user_detail')
                                     ->whereIn('user_id', $data->pluck('user_id')->toArray())
                                     ->get(['user_id', 'group_id', 'group_name'])
                                     ->mapToGroups(fn ($item) => [$item->user_id => $item])
                                     ->toArray();

            $data->each(function ($item) use ($crowds) {
                $item->_crowds = implode(',', array_column($crowds[$item->user_id ?? ''] ?? [], 'group_name'));
            });
        } catch (\Throwable) {
        }

        return $this->responseData($data, QuerySource::class);
    }

    /**
     * 可选列
     */
    public function optionalColumns(Request $request, CrowdKitService $srv): JsonResponse
    {
        $data = $srv->getOptionalColumns();

        // TODO：写法待完善
        // 预设人群分类模块
        $data->push(
            [
                'id' => 'preset',
                'name' => '人群信息',
                'columns' => [
                    [
                        'id' => 'preset_crowds',
                        'name' => '人群分类',
                        'type' => 'array',
                        'column' => '_crowds',
                    ],
                ],
            ],
        );

        return $this->responseData(['items' => $data]);
    }

    /**
     * 获取列偏好设置
     */
    public function getColumnPreference(Request $request): JsonResponse
    {
        $listCode = (string) $request->input('list_code', '');

        if (empty($listCode)) {
            return $this->responseError('参数错误');
        }

        $columns = AdminPreference::query()
                                  ->where('scene', SceneEnum::LIST_COLUMNS)
                                  ->where('pkey', $listCode)
                                  ->value('pvalue');

        // 缺省时，从low_code_part中解析获取
        if (empty($columns)) {
            $listCode = $this->covertCrowdPatientCode($listCode);

            $lowCodeList = LowCodeList::query()->where('code', $listCode)->first(['template_code_column']);
            if (empty($lowCodeList)) {
                return $this->responseData(['items' => []]);
            }

            $partCodes = LowCodeTemplateHasPart::query()
                                               ->where('template_code', $lowCodeList['template_code_column'])
                                               ->pluck('part_code');

            if ($partCodes->isEmpty()) {
                return $this->responseData(['items' => []]);
            }

            $columns = LowCodePart::query()
                                  ->whereIn('code', $partCodes->toArray())
                                  ->where('content_type', 1)
                                  ->pluck('content')
                                  ->pluck('key');
        } else {
            $columns = array_column($columns, 'column');
        }

        return $this->responseData(['items' => $columns]);
    }

    /**
     * 更新列偏好设置
     */
    public function updateColumnPreference(Request $request): JsonResponse
    {
        $listCode = (string) $request->input('list_code', '');

        $columns = (array) $request->input('columns', []);

        if (empty($listCode)) {
            return $this->responseError('参数错误');
        }

        $preference = AdminPreference::query()
                                     ->where('scene', SceneEnum::LIST_COLUMNS)
                                     ->where('pkey', $listCode)
                                     ->first();

        if (empty($preference)) {
            AdminPreference::query()->create([
                'scene' => SceneEnum::LIST_COLUMNS,
                'pkey' => $listCode,
                'pvalue' => $columns,
//                'admin_id' => AdminContext::instance()->getAdminId(),
            ]);
        } else {
            $preference->update(['pvalue' => $columns]);
        }

        return $this->responseSuccess();
    }

    /**
     * 转换人群患者编码
     */
    protected function covertCrowdPatientCode(string|array $codes): string|array
    {
        return Cache::remember('crowd_patient_code:' . md5(json_encode($codes)), 60 * 5, function () use ($codes) {
            $isOnce = !is_array($codes);

            $codes = (array) $codes;

            $existsCodes = LowCodeList::query()->whereIn('code', $codes)->pluck('code')->toArray();

            // TODO: 写法待完善
            $crowdPatientCode = LowCodeList::query()->byContextDisease()->where('admin_name', '人群患者列表')->value('code');

            return transform(
                array_combine($codes, array_map(fn ($code) => in_array($code, $existsCodes) ? $code : $crowdPatientCode, $codes)),
                fn ($value) => $isOnce ? end($value) ?? '' : $value
            );
        });
    }
}

EOT;

        // 写入文件
        File::put($targetPath, $content);

        $this->info("已安装成功: {$targetPath}");
    }
}