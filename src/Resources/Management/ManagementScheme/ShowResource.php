<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Management\ManagementScheme;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin \App\Models\Management\ManagementScheme
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
                'name',
                'description',
                'precondition',
                'valid_days',
                'runtime_options',
                'enabled',
                'creator_id',
                'updater_id',
                'created_at',
                'updated_at',
            ]),
            'start_at' => Carbon::parse($this->start_at)->format('Y-m-d'),
            'enabled_definition' => $this->enabled_definition,
            'creator_name' => $this->creator_name,
            'updater_name' => $this->updater_name,
        ];
    }
}
