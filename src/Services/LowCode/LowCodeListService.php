<?php

declare(strict_types = 1);

namespace BrightLiu\LowCode\Services\LowCode;

use App\Models\LowCode\LowCodeList;
use BrightLiu\LowCode\Traits\CastDefaultFixHelper;
use BrightLiu\LowCode\Services\LowCodeBaseService;
use Gupo\BetterLaravel\Exceptions\ServiceException;
use BrightLiu\LowCode\Enums\Model\LowCodeList\ListTypeEnum;
use BrightLiu\LowCode\Core\TemplatePartCacheManager;
use App\Services\Common\QueryEngine\QueryEngineService;
use BrightLiu\LowCode\Exceptions\QueryEngineException;
use App\Services\LowCode\LowCodeQueryEngineService;

/**
 * 低代码-列表
 */
class LowCodeListService extends LowCodeBaseService
{
    use CastDefaultFixHelper;

    /**
     * @param array $data
     *
     * @return LowCodeList|null
     */
    public function create(array $data = []): LowCodeList|null
    {
        $filterArgs = $this->fixInputDataByCasts($data, LowCodeList::class);
        return LowCodeList::query()->create($filterArgs);
    }

    public function show(int $id = 0): LowCodeList|null
    {
        if ($id <= 0) {
            return null; // 防止无效id继续查询和缓存
        }
        return LowCodeList::query()->where('id', $id)->with(
            ['filter:name,code,content_type', 'button:name,code,content_type',
             'topButton:name,code,content_type',
             'column:name,code,content_type', 'field:name,code,content_type',
             'updater:id,realname', 'creator:id,realname',]
        )->first();
    }

    public function update(array $data, int $id = 0)
    {
        if (empty($result = LowCodeList::query()->where('id', $id)->first(['id', 'code']))) {
            throw new ServiceException("数据{$id}不存在");
        }
        $filterArgs = $this->fixInputDataByCasts(
            $data, LowCodeList::class
        );
        if ($result->update($filterArgs)) {
            TemplatePartCacheManager::clearListCache($result->code);
            return true;
        }
        return false;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws ServiceException
     */
    public function delete(int $id = 0): bool
    {
        if (!$result = LowCodeList::query()->where('id', $id)->first(
            ['id', 'list_type']
        )
        ) {
            throw new ServiceException("ID:{$id}不存在");
        }

        if ($result->list_type == ListTypeEnum::GENERAL) {
            throw new ServiceException(
                "通用列表不支持删除,删了就无法自动生成列表"
            );
        }
        return $result->delete();
    }

    /**
     * @param string $code
     *
     * @return LowCodeList|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function pre(string $code = '')
    {
        return TemplatePartCacheManager::getListWithParts($code);
    }

    /**
     *
     * @param array $inputArgs
     * @param array $params
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|void
     * @throws ServiceException
     */
    public function query(array $inputArgs = [])
    {
        try {
            $list = LowCodeList::query()->whereIn(
                'code', collect($inputArgs)->pluck('code')->toArray()
            )->get(['id', 'crowd_type_code', 'default_order_by_json', 'code'])
                ->keyBy('code')->toArray();

            $query = LowCodeQueryEngineService::instance()->autoClient();
            foreach ($inputArgs as $value) {
                $crowdTypeCode = $list[$value['code']]['crowd_type_code'] ?? '';
                $filter = $value['filters'] ?? [];
                //合并条件
                $mergeFilter = array_merge(
                    [['ptt_crwd_clsf_cd', '=', $crowdTypeCode]], $filter
                );
                //筛选项
                if (!empty($mergeFilter)) {
                    $query->whereMixed($mergeFilter);
                }

                $inputOrderBy = $value['order_by'] ?? [];

                $defaultOrderBy = $list[$value['code']]['default_order_by_json']
                    ?? [];

                // 排序
                $query->multiOrderBy(
                    array_merge($inputOrderBy, $defaultOrderBy)
                );
                return $query->setCache(60)->getPaginateResult();
            }
        } catch (QueryEngineException $e) {
        }
    }
}
