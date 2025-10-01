<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\BmoSource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class FlagOrgTeamDoctorResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => $this['name'],
            'code' => $this['staff_phone'] ?? '',
        ];
    }
}
