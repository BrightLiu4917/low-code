<?php

declare(strict_types = 1);

namespace BrightLiu\LowCode\Controllers\Disease;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\LowCode\Disease;
use Gupo\BetterLaravel\Http\BaseController;
use BrightLiu\LowCode\Requests\Disease\DiseaseRequest;
use BrightLiu\LowCode\Resources\Disease\ShowResource;
use BrightLiu\LowCode\Resources\Disease\ListResource;
use BrightLiu\LowCode\Services\LowCode\LowCodeDiseaseService;

/**
 * 疾病
 */
final class DiseaseController extends BaseController
{
    /**
     * @param DiseaseService $diseaseService
     */
    public function __construct(private readonly LowCodeDiseaseService $diseaseService,
    ) {

    }

    /**
     * @param DiseaseRequest $request
     *
     * @return JsonResponse
     */
    public function create(DiseaseRequest $request): JsonResponse
    {
        $args = $request->except(['id']);
        if ($this->diseaseService->create($args)) {
            return $this->responseSuccess();
        }
        return $this->responseError();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $name = (string)$request->input('name','');
        $list = Disease::query()
            ->when($name !== '',function($query)use($name){
            $query->where('name','like',"%{$name}%");
        })
            ->with(['creator','updater'])
            ->orderByDesc('weight')
            ->orderByDesc('id')
            ->customPaginate(true);
        return $this->responseData($list,ListResource::class);

    }

    /**
     * @param DiseaseRequest $request
     *
     * @return JsonResponse
     * @throws \Gupo\BetterLaravel\Exceptions\ServiceException
     */
    public function show(DiseaseRequest $request): JsonResponse
    {
        $id = (int)$request->input('id',0);
        return $this->responseData($this->diseaseService->show($id),ShowResource::class);
    }

    /**
     * @param DiseaseRequest $request
     *
     * @return JsonResponse
     * @throws \Gupo\BetterLaravel\Exceptions\ServiceException
     */
    public function update(DiseaseRequest $request): JsonResponse
    {
        $id = (int)$request->input('id',0);
        if ($result = $this->diseaseService->update($request->post(),$id)){
            return $this->responseSuccess('',$result);
        }
        return $this->responseError();
    }

    /**
     * @param DiseaseRequest $request
     *
     * @return JsonResponse
     * @throws \Gupo\BetterLaravel\Exceptions\ServiceException
     */
    public function delete(DiseaseRequest $request): JsonResponse
    {
        $id = (int)$request->input('id',0);
        if ($this->diseaseService->delete($id)){
            return $this->responseSuccess();
        }
        return $this->responseError();
    }
}
