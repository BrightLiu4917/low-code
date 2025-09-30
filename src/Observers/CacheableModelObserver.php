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

