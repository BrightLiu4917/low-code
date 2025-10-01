<?php

declare(strict_types = 1);

namespace BrightLiu\LowCode\Controllers\LowCode;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\LowCode\LowCodeTemplate;
use Gupo\BetterLaravel\Http\BaseController;
use BrightLiu\LowCode\Requests\LowCode\LowCodeTemplateRequest;
use BrightLiu\LowCode\Services\LowCode\LowCodeTemplateService;
use BrightLiu\LowCode\Resources\LowCode\LowCodeTemplate\ListSource;
use BrightLiu\LowCode\Resources\LowCode\LowCodeTemplate\ShowSource;

/**
 * 低代码-模板
 */
final class LowCodeTemplateController extends BaseController
{
    public function __construct(protected LowCodeTemplateService $service)
    {
    }

    /**
     * @param LowCodeTemplateRequest $request
     *
     * @return JsonResponse
     */
    public function create(LowCodeTemplateRequest $request): JsonResponse
    {
        $args = $request->except(['id','code']);
        if ($result = $this->service->create($args)) {
            return $this->responseSuccess(
                '',
                //主键不是id
                LowCodeTemplate::query()->where('code', $result['code'])->first(
                    ['id', 'code']
                )
            );
        }
        return $this->responseError();
    }

    /**
     * 模板绑定零件
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \App\Exceptions\ApiServiceException
     */
    public function bindPart(Request $request): JsonResponse
    {
        $partCodes = (array)$request->input('part_codes', []);
        $lockedPartCodes = (array)$request->input('locked_part_codes', []);
        $templateCode = (string)$request->input('template_code', '');
        if ($this->service->bindPart($partCodes, $templateCode,$lockedPartCodes)) {
            return $this->responseSuccess();
        }
        return $this->responseError();
    }

    public function list(Request $request): JsonResponse
    {
        $name = trim($request->input('name', ''));
        $contentType = (int)$request->input('content_type', 0);
        $templateType = (int)$request->input('template_type', 0);
        $orgCode = (string)$request->input('org_code', '');
        $list = LowCodeTemplate::query()->with(
            'creator:id,realname', 'updater:id,realname'
        )->when(
            $contentType !== 0, function($query) use ($contentType) {
            $query->where('content_type', $contentType);
        }
        )->when($templateType !== 0, function($query) use ($templateType) {
            $query->where('template_type', $templateType);
        })->when($name !== '', function($query) use ($name) {
            $query->where('name', 'like', "%{$name}%");
        })->when($orgCode !== '', function($query) use ($orgCode) {
            $query->where('org_code', $orgCode);
        })->customPaginate(true);
        return $this->responseData($list, ListSource::class);
    }

    public function update(LowCodeTemplateRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if ($this->service->update($request->post(), $id)) {
            return $this->responseSuccess();
        }
        return $this->responseError();
    }

    /**
     * @param LowCodeTemplateRequest $request
     *
     * @return JsonResponse
     * @throws \App\Exceptions\ApiServiceException
     */
    public function show(LowCodeTemplateRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if ($result = $this->service->show($id)) {
            return $this->responseData($result, ShowSource::class);
        }
        return $this->responseError();
    }

    /**
     * @param LowCodeTemplateRequest $request
     *
     * @return JsonResponse
     * @throws \App\Exceptions\ApiServiceException
     */
    public function delete(LowCodeTemplateRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if ($this->service->delete($id)) {
            return $this->responseSuccess();
        }
        return $this->responseError();
    }
}
