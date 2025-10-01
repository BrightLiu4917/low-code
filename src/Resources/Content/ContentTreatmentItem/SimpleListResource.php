<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentTreatmentItem;

use App\Models\Content\ContentTreatmentItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContentTreatmentItem
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
                'is_must_do',
                'is_replace',
                'cate_name',
            ]),
        ];
    }
}
