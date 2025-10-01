<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementSchemeNode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Management\ManagementSchemeNode
 *
 * @see \App\Services\Logic\ManagementScheme\NodeTimelineGeneratorService
 */
final class TimeListResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            // 时间线信息
            'line_title' => $this['line_title'] ?? '',
            'line_priority' => $this['line_priority'] ?? 0,
            'nodes' => collect($this['nodes'] ?? [])
                ->map(
                    fn ($node) => [
                        'plan_id' => $node['plan_id'] ?? 0,

                        // 分支信息
                        'branch_id' => $node['branch_id'] ?? 0,
                        'branch_name' => $node['branch_name'] ?? '',

                        // 节点信息
                        'node_id' => $node['node_id'] ?? 0,
                        'node_day' => $node['node_day'] ?? 0,
                        'node_daily' => $node['node_daily'] ?? '',
                        'node_title' => $node['node_title'] ?? '',
                        'node_summary' => $node['node_summary'] ?? '',
                        'node_priority' => $node['node_priority'] ?? 0,
                    ]
                )
                ->toArray(),
        ];
    }
}
