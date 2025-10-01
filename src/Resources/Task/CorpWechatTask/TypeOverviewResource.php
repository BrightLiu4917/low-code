<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Task\CorpWechatTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property-read string $label
 * @property-read string $code
 * @property-read int $count
 * @property-read Collection $items
 */
final class TypeOverviewResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'label' => $this['label'],
            'code' => $this['code'],
            'count' => $this['count'],
            'items' => $this['items']->map(fn ($item) => [
                'label' => $item['label'],
                'code' => $item['code'],
                'count' => $item['count'],
            ])->all(),
        ];
    }
}
