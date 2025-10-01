<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentEducation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Content\ContentEducation
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
                'content',
                'enabled',
                'weight',
                'content_type',
                'view_count',
                'creator_id',
                'updater_id',
                'created_at',
                'updated_at',
                'background_image_id',
            ]),
            'enabled_definition'      => $this->enabled_definition,
            'content_type_definition' => $this->content_type_definition,
            'background_image_url'    => $this->backgroundImage->file_url ?? '',
            'creator_name'            => $this->creator_name,
            'updater_name'            => $this->updater_name,
        ];
    }
}
