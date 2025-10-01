<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementSchemeBranch;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Management\ManagementSchemeBranch
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
                'id',
                'disease_code',
                'org_code',
                'code',
                'name',
                'model_name',
                'model_code',
                'creator_id',
                'updater_id',
                'created_at',
                'updated_at',
            ]),
            'creator_name' => $this->creator_name,
            'updater_name' => $this->updater_name,
        ];
    }
}
