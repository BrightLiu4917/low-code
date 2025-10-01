<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Inner\ManagementTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Task\ManagementTask
 */
final class ContentDetailsResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->content->title ?? '',
            'content' => $this->content->content ?? '',
        ];
    }
}
