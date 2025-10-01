<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Content\ContentSms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Content\ContentSms
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
                'content_code',
                'send_mode',
                'send_config',
                'default_params',
                'debug_phone',
                'precheck_enabled',
                'creator_id',
                'updater_id',
                'created_at',
                'updated_at',
            ]),
            'enabled_definition'          => $this->enabled_definition,
            'precheck_enabled_definition' => $this->precheck_enabled_definition,
            'creator_name'                => $this->creator_name,
            'updater_name'                => $this->updater_name,
        ];
    }
}
