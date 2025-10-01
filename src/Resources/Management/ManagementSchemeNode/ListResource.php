<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementSchemeNode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Management\ManagementSchemeNode
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
        return $this->only([
            'id',
            'disease_code', 'org_code',
            'management_scheme_id', 'management_scheme_branch_id', 'management_scheme_node_plan_id',
            'target_scope', 'content_type', 'content_model', 'content_id', 'content_title', 'content_payload',
            'reference_column', 'reference_payload', 'generate_trigger', 'generate_time_expression',
            'created_at', 'updated_at',
        ]);
    }
}
