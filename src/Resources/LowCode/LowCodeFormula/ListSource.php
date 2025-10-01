<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\LowCode\LowCodeFormula;

use Illuminate\Http\Request;
use App\Models\LowCode\LowCodeFormula;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LowCodeFormula
 */
final class ListSource extends JsonResource
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
                "code",
                "description",
                'creator_id',
                'updater_id',
                'created_at',
                'updated_at',
                'content_type',
            ]),
            'creator_name' => $this->creator_name ?? '',
            'updater_name' => $this->updater_name ?? '',
            'content_type_definition' => $this->content_type_definition ?? '',
        ];
    }
}
