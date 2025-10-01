<?php

declare(strict_types = 1);

namespace BrightLiu\LowCode\Controllers\DatabaseSource;

use Illuminate\Http\JsonResponse;
use App\Models\LowCode\DatabaseSource;
use Gupo\BetterLaravel\Http\BaseController;
use BrightLiu\LowCode\Resources\DatabaseSource\ShowResource;
use BrightLiu\LowCode\Resources\DatabaseSource\ListResource;
use BrightLiu\LowCode\Services\LowCode\LowCodeDatabaseSourceService;
use BrightLiu\LowCode\Requests\Foundation\DatabaseSourceRequest\DatabaseSourceRequest;

final class DatabaseSourceController extends BaseController
{
    /**
     * @param DatabaseSourceService $service
     */
    public function __construct(private readonly LowCodeDatabaseSourceService $service)
    {

    }

    public function create(DatabaseSourceRequest $request): JsonResponse
    {
        $args = $request->input();
        $this->service->create($args);
        return $this->responseSuccess();
    }

    public function list(): JsonResponse
    {
        $list = DatabaseSource::query()
            ->byContextDisease()
            ->with(
                ['creator:id,realname', 'updater:id,realname',
                 'disease:code,name']
            )->select(
                ['id', 'disease_code', 'code', 'name', 'host', 'code',
                 'database', 'table', 'port', 'options', 'creator_id',
                 'created_at', 'updated_at', 'source_type', 'updater_id']
            )->customPaginate(true);
        return $this->responseData($list, ListResource::class);
    }

    public function show(DatabaseSourceRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if (empty($result = $this->service->show($id))) {
            return $this->responseError('数据不存在');
        }
        return $this->responseData(
            $result, ShowResource::class
        );
    }

    public function delete(DatabaseSourceRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        if ($this->service->delete($id)) {
            return $this->responseSuccess('');
        }
        return $this->responseError();
    }

    public function update(DatabaseSourceRequest $request): JsonResponse
    {
        $id = (int)$request->input('id', 0);
        $args = $request->input();
        $this->service->update($id, $args);
        return $this->responseSuccess();
    }
}
