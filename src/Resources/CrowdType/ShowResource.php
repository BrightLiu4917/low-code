<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\CrowdType;

use Illuminate\Http\Request;
use App\Models\CrowdType\CrowdType;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CrowdType
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
                'code',
                'name',
                'description',
                'weight',
                'color',
                'management_scheme_id',
                'import_resident_name',
                'creator_id',
                'route_group',
                'create_list',
                'updater_id',
                'created_at',
                'updated_at',
            ]),
            'create_list_definition'  => $this->create_list_definition ?? '',
            'creator_name'            => $this->creator_name,
            'updater_name'            => $this->updater_name,
        ];
    }
}
