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
use App\Services\LowCode\LowCodeQueryEngineService;
use App\Http\Resources\LowCode\LowCodeList\QuerySource;
use BrightLiu\LowCode\Requests\LowCode\LowCodeListRequest;
use BrightLiu\LowCode\Resources\LowCode\LowCodeList\ListSource;
use BrightLiu\LowCode\Enums\Model\LowCode\LowCodeList\ListTypeEnum;
use BrightLiu\LowCode\Resources\LowCode\LowCodeList\simpleListSource;

class CopyLowCodeListControllerCommand extends Command
{
    protected $signature = 'lowcode:install-list-controller 
                                            {--f : 覆盖现有的文件}';

    protected $description = '安装低代码-列表控制器';

    public function handle()
    {
        $className = 'LowCodeListController';
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

declare(strict_types = 1);

namespace App\Http\Controllers\LowCode;

use Illuminate\Http\Request;
use App\Models\LowCode\LowCodeList;
use Illuminate\Support\Facades\Cache;
use App\Support\BmpCrowd\CrowdConnection;
use App\Services\LowCode\LowCodeQueryEngineService;
use App\Http\Resources\LowCode\LowCodeList\QuerySource;
use BrightLiu\LowCode\Requests\LowCode\LowCodeListRequest;
use Gupo\BetterLaravel\Http\BaseController;
use Illuminate\Http\JsonResponse;
use App\Services\LowCode\LowCodeListService;
use BrightLiu\LowCode\Enums\Model\LowCode\LowCodeList\ListTypeEnum;
use BrightLiu\LowCode\Resources\LowCode\LowCodeList\ListSource;
use BrightLiu\LowCode\Resources\LowCode\LowCodeList\simpleListSource;

/**
 * @Class
 * @Description:低代码-列表
 * @created    : 2025-10-01 10:55:45
 * @modifier   : 2025-10-01 10:55:45
 */
final class LowCodeListController extends BaseController
{
    /**
     * @param \BrightLiu\LowCode\Services\LowCode\LowCodeListService $service
     */
    public function __construct(protected LowCodeListService $service)
    {

    }

    /**
     * @param \BrightLiu\LowCode\Requests\LowCode\LowCodeListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(LowCodeListRequest $request): JsonResponse
    {
        try {
            $args = $request->except(['id', 'code']);
            if ($this->service->create($args)) {
                return $this->responseSuccess();
            }
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
        return $this->responseError();
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $name         = trim($request->input('name', ''));
        $templateCode = trim($request->input('template_code', ''));
        //权限判断
        $query = LowCodeList::query()
            //                            ->byContextDisease()->byContextDisease()
                            ->orderByRaw("CASE WHEN admin_name = '全部人群' THEN 0 ELSE 1 END asc")
                            ->where('list_type', '<>', ListTypeEnum::GENERAL);
        if ($name !== '') {
            $query->where(function($q) use ($name) {
                $q->orWhere('admin_name', 'like', "%{$name}%")
                  ->orWhere('family_doctor_name', 'like', "%{$name}%")
                  ->orWhere('mobile_doctor_name', 'like', "%{$name}%");
            });
        }

        if ($templateCode !== '') {
            $query->where(function($q) use ($templateCode) {
                $q->orWhere('template_code_filter', 'like', "%{$templateCode}%")
                  ->orWhere('template_code_column', 'like',
                      "%{$templateCode}%")
                  ->orWhere('template_code_top_button', 'like',
                      "%{$templateCode}%")
                  ->orWhere('template_code_field', 'like',
                      "%{$templateCode}%")
                  ->orWhere('template_code_button', 'like',
                      "%{$templateCode}%");
            });
        }
        $list = $query
            //->byContextOrg()->byContextDisease()
            //                                      ->with([
            //                'updater:id,realname', 'creator:id,realname',
            //                'crowdType:code,name,color,weight',
            //            ])
            ->select([
                'id', 'admin_name', 'code', 'parent_code', 'crowd_type_code',
                'route_group', 'admin_weight', 'creator_id', 'updater_id',
            ])->orderByDesc('created_at')->orderByDesc('id')
            ->customPaginate(true);

        return $this->responseData($list, ListSource::class);
    }

    /**
     * @param \BrightLiu\LowCode\Requests\LowCode\LowCodeListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LowCodeListRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if ($this->service->update($request->post(), $id)) {
            return $this->responseSuccess();
        }
        return $this->responseError();
    }

    /**
     * @param \BrightLiu\LowCode\Requests\LowCode\LowCodeListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(LowCodeListRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if ($result = $this->service->show($id)) {
            return $this->responseSuccess('', $result);
        }
        return $this->responseError();

    }

    /**
     * @param \BrightLiu\LowCode\Requests\LowCode\LowCodeListRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(LowCodeListRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if ($this->service->delete($id)) {
            return $this->responseSuccess();
        }
        return $this->responseError();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function simpleList(Request $request): JsonResponse
    {
        $list = LowCodeList::query()->byContextOrg()->byContextDisease()
                           ->orderByRaw("CASE WHEN admin_name = '全部人群' THEN 0 ELSE 1 END asc")
                           ->where('list_type', '<>', ListTypeEnum::GENERAL)
                           ->select([
                               'id', 'admin_name', 'code', 'parent_code',
                               'crowd_type_code',
                               'route_group',
                           ])
            //                                                             ->with(['crowdType:name,code,color,weight'])
                           ->customPaginate(true);
        return $this->responseData($list, simpleListSource::class);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function pre(Request $request)
    {
        $code = (string)$request->input('code', '');
        return $this->responseSuccess('', $this->service->pre($code));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function query(Request $request): JsonResponse
    {
        $inputArgs = $request->input('input_args');
        $codes     = $this->covertCrowdPatientCode(array_column($inputArgs,
            'code'));
        $inputArgs = array_map(
            function($item) use ($codes) {
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
        $data      = $this->service->query($inputArgs);
        try {
            // 追加人群分类信息
            $userIds = $data->pluck('user_id')->toArray();

            //查询人群分类表里人群
            $crowds  = LowCodeQueryEngineService::instance()
                                                ->useTable('feature_user_detail')
                                                ->whereMixed([
                                                    [
                                                        'user_id', 'in',
                                                        $userIds,
                                                    ],
                                                ])
                                                ->select([
                                                    'user_id', 'group_id',
                                                    'group_name',
                                                ])
                                                ->getAllResult();
            $grouped = [];
            foreach ($crowds as $item) {
                $userId = $item->user_id;
                if (!isset($grouped[$userId])) {
                    $grouped[$userId] = [];
                }
                $grouped[$userId][] = [
                    'group_id'   => $item->group_id,
                    'group_name' => $item->group_name,
                ];
            }

            //$grouped 将患者的人群分类收集到一起

            $data = $data->each(function($item) use ($grouped) {
                $res = (array)$grouped[$item->user_id ?? ''];
                return $item->_crowds = implode(',',
                    array_column($res ?? [], 'group_name'));
            });
        } catch (\Throwable $exception) {
        }
        return $this->responseData($data, QuerySource::class);
    }

    protected function covertCrowdPatientCode(string|array $codes): string|array
    {
        return Cache::remember('crowd_patient_code:'.md5(json_encode($codes)),
            60 * 5, function() use ($codes) {
                $isOnce = !is_array($codes);

                $codes = (array)$codes;

                $existsCodes = LowCodeList::query()->whereIn('code', $codes)
                                          ->pluck('code')->toArray();

                // TODO: 写法待完善
                $crowdPatientCode = LowCodeList::query()
                    //                                           ->byContextDisease()
                                               ->where('admin_name',
                        '人群患者列表')->value('code');

                return transform(
                    array_combine($codes,
                        array_map(fn ($code) => in_array($code, $existsCodes) ?
                            $code : $crowdPatientCode, $codes)),
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