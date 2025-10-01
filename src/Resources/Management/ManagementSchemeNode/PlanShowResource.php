<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementSchemeNode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Management\ManagementSchemeNodePlan
 */
final class PlanShowResource extends JsonResource
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
                'management_scheme_id',
                'management_scheme_branch_id',
                'target_scope',
                'content_type',
                'content_id',
                'content_title',
                'content_payload',
                'plan_style',
                'ep_reference_column',
                'ep_reference_payload',
                'ep_time_expression',
                'lp_start_day',
                'lp_end_day',
                'lp_reference_column',
                'lp_reference_payload',
                'lp_every_position',
                'lp_every_time',
                'lp_every_unit',
                'lp_every_daily',
                'created_at',
                'updated_at',
                'creator_id',
                'updater_id',
            ]),
            'creator_name' => $this->creator_name,
            'updater_name' => $this->updater_name,
        ];
    }
}
