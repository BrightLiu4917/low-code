<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\CrowdType;

use Illuminate\Http\Request;
use App\Models\CrowdType\CrowdType;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CrowdType
 */
final class ListResource extends JsonResource
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
                'color',
                'import_resident_name',
                'route_group',
                'weight',
                'creator_id',
                'create_list',
                'updater_id',
                'created_at',
                'updated_at',
            ]),
            'create_list_definition'  => $this->create_list_definition ?? '',
            'creator_name'            => $this->creator_name ?? '',
            'updater_name'            => $this->updater_name ?? '',
            'disease_name'            => $this->disease_name ?? '',
            'management_scheme'       => [
                'id'   => $this->managementScheme->id ?? 0,
                'name' => $this->managementScheme->name ?? '',
            ]
        ];
    }
}
