<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentTestItem;

use App\Models\Content\ContentTestItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContentTestItem
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
                'item_name',
                'patterns',
                'is_must_do',
                'created_at',
                'updated_at',
            ]),
            'creator_name' => $this->creator_name,
            'updater_name' => $this->updater_name,
        ];
    }
}
