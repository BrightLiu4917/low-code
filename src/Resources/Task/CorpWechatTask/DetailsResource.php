<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Task\CorpWechatTask;

use App\Models\CorpWechat\CorpWechatMessageWorkflow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CorpWechat\CorpWechatMessageRecord
 */
class DetailsResource extends JsonResource
{
    /**
     * @param Request $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'task_title' => $this->attr_message_type_definition,
            'task_status' => $this->message_state,
            'task_status_definition' => $this->attr_message_state_definition,
            'task_source_name' => sprintf('%s%s', $this->org_name ?? '', $this->disease->name),

            // 患者信息
            'patient_name' => $this->resident->ptt_nm ?? '',
            'patient_gender' => $this->resident->gdr_nm ?? '',
            'patient_age' => $this->resident->age,
            'patient_crowd_type' => $this->managementTask->residentManagement->crowdType->name ?? '',
            'patient_phone' => $this->resident->slf_tel_no ?? '',
            'patient_managed_org_name' => $this->org_name ?? '',

            // 消息信息
            'message_type' => $this->message_type,
            'message_type_definition' => $this->attr_message_type_definition,
            'message_title' => $this->content['title'] ?? '',
            'message_content' => $this->content['content'] ?? '',
            'message_content_id' => $this->managementTask->content_id ?? 0,

            // TODO: message_type = 4 with drug_items
            'message_payload' => [
                'drug_items' => [],
            ],

            // 工作流信息
            'workflows' => collect($this->attr_biz_workflows)->map(fn ($item) => [
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'datetime' => $item['created_at'] ?? '',
            ]),
        ];
    }
}
