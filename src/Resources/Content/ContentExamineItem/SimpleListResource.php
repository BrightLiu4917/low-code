<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentExamineItem;

use App\Models\Content\ContentExamineItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContentExamineItem
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
                'code',
                'item_name',
                'patterns',
                'item_children',
                'is_must_do',
            ]),
        ];
    }
}
