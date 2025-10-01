<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Task\CorpWechatTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CorpWechat\CorpWechatMessageRecord
 *
 * @property-read int $count
 * @property-read int $total
 */
final class TypeStatisticsResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->attr_message_type_definition,
            'value' => intval($this->count ?? 0),
            'rate' => $this->total > 0 ? round($this->count / $this->total * 100, 2) : 0,
        ];
    }
}
