<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Task\CorpWechatTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CorpWechat\CorpWechatMessageRecord
 *
 * @property-read string $label
 * @property-read int $count
 * @property-read string $code
 */
final class StatusOverviewResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'label' => $this['label'] ?? '',
            'count' => (int) ($this['count'] ?? 0),
            'code' => (string) ($this['code'] ?? ''),
        ];
    }
}
