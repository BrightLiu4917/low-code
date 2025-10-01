<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Task\SmsTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 任务-短信任务:日志列表
 *
 * @mixin \App\Models\Notification\SmsLog
 */
final class LogListResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            ...$this->only([
                'id',
                'phone', 'user_id', 'user_name',
                'failure_cause', 'delivery_status',
                'title', 'message', 'delivery_at',
            ]),
        ];
    }
}
