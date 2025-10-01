<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Task\ManagementTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 任务-管理任务:详情
 *
 * @mixin \App\Models\Task\ManagementTask
 */
final class ShowResource extends JsonResource
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
                'id', 'task_title', 'task_type', 'task_receiver_type', 'complete_status',
                'created_at', 'complete_at', 'available_at',
                'resident_user_id',
            ]),
            'resident' => [
                'user_id' => $this->resident_user_id ?? '',
                'name' => $this->resident->ptt_nm ?? '',
            ],
        ];
    }
}
