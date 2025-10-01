<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentDrug;

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
                'cate_name',
                'health_advice_content_id',
                'health_insurance_code',
                'vendor',
                'drug_name',
                'drug_dosage_form',
                'dose',
                'is_replace',
                'is_must_do',
                'drug_format',
                'drug_quantity_unit',
                'drug_ch_name',
                'use_dose_number',
                'drug_use_unit',
            ]),
        ];
    }
}
