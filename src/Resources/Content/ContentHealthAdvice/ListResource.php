<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentHealthAdvice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Content\ContentHealthAdvice
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
                'code',
                'title',
                'summary',
                'enabled',
                'creator_id',
                'updater_id',
                'created_at',
                'updated_at',
            ]),
            'enabled_definition' => $this->enabled_definition,
            'creator_name'       => $this->creator_name,
            'updater_name'       => $this->updater_name,
        ];
    }
}
