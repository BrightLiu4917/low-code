<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementScheme;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Management\ManagementScheme
 */
final class PreCreateResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'code' => $this->code,
        ];
    }
}
