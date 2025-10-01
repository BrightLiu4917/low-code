<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementScheme;

use App\Enums\Model\Management\ManagementSchemeNode\ContentTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin \App\Entities\Model\Task\ManagementTaskEntity
 */
final class SimulationTaskResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $referenceAt = Carbon::parse($request->input('reference_at', today()));

        $daysDaily = ['days' => $taskDays, 'daily' => $taskDaily] = $this->parseAvailableAt($referenceAt);

        return [
            'node_id' => $this->node->id,

            'branch_id' => $this->node->managementSchemeBranch->id ?? 0,
            'branch_name' => $this->node->managementSchemeBranch->name ?? '',

            'content_id' => $this->node->content_id,
            'content_title' => $this->node->content_title,
            'content_type' => $this->node->content_type,
            'content_type_definition' => $this->node->content_type_definition,
            'content_summary' => $this->node->content_summary ?? '',

            'task_days' => $taskDays,
            'task_daily' => $taskDaily,
            'task_title' => $this->buildTaskTitle(),
            'task_summary' => $this->buildTaskSummary($daysDaily),
        ];
    }

    /**
     * 解析出执行时间
     *
     * @param Carbon $referenceAt
     *
     * @return array{days:int,daily:string}
     */
    protected function parseAvailableAt(Carbon $referenceAt): array
    {
        return [
            'days' => $this->available_at->diffInDays($referenceAt),
            'daily' => $this->available_at->format('H:i'),
        ];
    }

    /**
     * @return string
     */
    protected function buildTaskTitle(): string
    {
        return sprintf(
            '%s：%s',
            $this->node->content_type_definition,
            $this->task_title,
        );
    }

    /**
     * @param array{days:int,daily:string} $daysDaily
     *
     * @return string
     */
    protected function buildTaskSummary(array $daysDaily): string
    {
        return match ($this->node->content_type) {
            ContentTypeEnum::MANAGED,
            ContentTypeEnum::TREATMENT,
            ContentTypeEnum::OUTPATIENT => $this->node->content->content ?? '',
            ContentTypeEnum::DRUG,
            ContentTypeEnum::TEST,
            ContentTypeEnum::EXAMINE => $this->node->content_summary ?? '',
            default => sprintf(
                '%s%s自动发送至%s',
                $daysDaily['days'] > 0 ? ('第' . $daysDaily['days'] . '天') : '当天',
                $daysDaily['daily'],
                $this->node->target_scope_definition,
            )
        };
    }
}
