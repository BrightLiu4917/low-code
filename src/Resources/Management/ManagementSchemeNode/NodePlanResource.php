<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementSchemeNode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Entities\Business\ManagementSchemeNode\NodePlanResultEntity
 */
final class NodePlanResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->node_plan->id,
            'node_count' => $this->node_count ?? 0,
        ];
    }
}
