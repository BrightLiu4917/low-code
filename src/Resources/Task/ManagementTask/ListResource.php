<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Task\ManagementTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 任务-管理任务:列表
 *
 * @mixin \App\Models\Task\ManagementTask
 */
final class ListResource extends JsonResource
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
                'id', 'task_title', 'task_type', 'task_receiver_type',
                'execute_status', 'execute_at',
                'complete_status', 'complete_at',
                'created_at', 'available_at',
                'resident_user_id',
            ]),

            'task_type_definition' => $this->task_type_definition ?? '',
            'task_receiver_type_definition' => $this->task_receiver_type_definition ?? '',

            'resident' => [
                'user_id' => $this->resident_user_id ?? '',
                'name' => $this->resident->ptt_nm ?? '',
            ],
        ];
    }
}
