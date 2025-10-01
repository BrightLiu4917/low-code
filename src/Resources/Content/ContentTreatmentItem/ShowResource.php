<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentTreatmentItem;

use App\Models\Content\ContentExamineItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContentExamineItem
 */
final class ShowResource extends JsonResource
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
                'created_at',
                'updated_at',
            ]),
            'treatment_replace_lists' => $this->treatmentReplaceLists,
            'creator_name'            => $this->creator_name,
            'updater_name'            => $this->updater_name,
        ];
    }
}
