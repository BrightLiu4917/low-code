<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementSchemeBranch;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Management\ManagementSchemeBranch
 */
final class CreateResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
        ];
    }
}
