<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentQuestionnaire;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Content\ContentQuestionnaire
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
                'disease_code',
                'org_code',
                'code',
                'title',
                'summary',
                'enabled',
                'content_type',
                'weight',
                'creator_id',
                'updater_id',
                'created_at',
                'updated_at',
            ]),
            'enabled_definition'      => $this->enabled_definition,
            'content_type_definition' => $this->content_type_definition,
            'creator_name'            => $this->creator_name,
            'updater_name'            => $this->updater_name,

            'is_protected_content' => $this->is_protected_content,
        ];
    }
}
