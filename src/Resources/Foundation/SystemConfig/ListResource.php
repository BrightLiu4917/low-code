<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Foundation\SystemConfig;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Foundation\SystemConfig
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
                'name',
                'description',
                'data_key',
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
