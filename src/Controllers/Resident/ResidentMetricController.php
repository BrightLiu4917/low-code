<?php

declare(strict_types=1);

namespace BrightLiu\LowCode\Controllers\Resident;

use BrightLiu\LowCode\Models\Resident\ResidentMonitorMetric;
use BrightLiu\LowCode\Requests\Resident\ResidentMetric\MonitorListRequest;
use BrightLiu\LowCode\Requests\Resident\ResidentMetric\MonitorTrendItemsRequest;
use BrightLiu\LowCode\Requests\Resident\ResidentMetric\SaveMonitorRequest;
use BrightLiu\LowCode\Resources\Resident\ResidentMetric\MonitorListResource;
use BrightLiu\LowCode\Resources\Resident\ResidentMetric\MonitorTrendItemsResource;
use BrightLiu\LowCode\Resources\Resident\ResidentMetric\OptionalResource;
use BrightLiu\LowCode\Services\LowCode\BmpCheetahMedicalCrowdkitApiService;
use BrightLiu\LowCode\Services\Resident\ResidentMetricService;
use BrightLiu\LowCode\Support\CrowdConnection;
use BrightLiu\LowCode\Traits\Context\WithAuthContext;
use BrightLiu\LowCode\Traits\Context\WithDiseaseContext;
use BrightLiu\LowCode\Traits\Context\WithOrgContext;
use Gupo\BetterLaravel\Http\BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;

/**
 * 居民指标
 */
class ResidentMetricController extends BaseController
{
    use WithAuthContext;
    use WithDiseaseContext;
    use WithOrgContext;

    /**
     * 可选指标
     *
     * @throws BindingResolutionException
     */
    public function optional(): JsonResponse
    {
        $items = BmpCheetahMedicalCrowdkitApiService::make()->getMetricOptional();

        return $this->responseData([
            'items' => OptionalResource::collection($items),
        ]);
    }

    /**
     * 监测指标列表
     */
    public function monitorList(MonitorListRequest $request): JsonResponse
    {
        $userId = (string) $request->input('user_id', '');

        $data = ResidentMonitorMetric::query()
            ->byContextDisease()
            ->where('resident_user_id', $userId)
            ->orderBy('id')
            ->get();

        return $this->responseData([
            'items' => MonitorListResource::collection($data),
        ]);
    }

    /**
     * 监测指标趋势
     */
    public function monitorTrendItems(MonitorTrendItemsRequest $request): JsonResponse
    {
        // 居民主索引
        $userId = (string) $request->input('user_id', '');

        // 指标ID
        $metricId = (string) $request->input('metric_id', '');

        // 时间范围-开始
        $dateRangeMin = (string) $request->input('date_range.0', '');

        // 时间范围-截至
        $dateRangeMax = (string) $request->input('date_range.1', '');

        // TODO: 写法待完善
        $data = CrowdConnection::table('personal_archive')
            ->where('tenant_id', $this->getTenantId())
            ->where('col_name', $metricId)
            ->where('disease_code', $this->getDiseaseCode())
            ->where('sys_code', $this->getSystemCode())
            ->where('org_code', $this->getOrgCode())
            ->where('user_id', $userId)
            ->whereBetweenDate('fill_date', $dateRangeMin, $dateRangeMax, forceFullDay: true)
            ->get(['col_value', 'fill_date', 'data_source'])
            ->sortBy('fill_date')
            ->toArray();

        return $this->responseData([
            'items' => MonitorTrendItemsResource::collection($data),
        ]);
    }

    /**
     * 保存监测指标项
     */
    public function saveMonitor(SaveMonitorRequest $request, ResidentMetricService $srv): JsonResponse
    {
        $userId = (string) $request->input('user_id', '');

        $metricIds = (array) $request->input('metric_ids', []);

        // TODO: 判断居民、指标是否存在

        $srv->saveMonitor($userId, $metricIds);

        return $this->responseSuccess();
    }
}
