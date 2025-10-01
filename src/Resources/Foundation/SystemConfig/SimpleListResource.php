<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Foundation\SystemConfig;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Foundation\SystemConfig
 */
final class SimpleListResource extends JsonResource
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
                'name',
                'data_key',
                'created_at',
            ]),
        ];
    }
}
