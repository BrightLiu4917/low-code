<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiseasePermissionResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'   => $this['id'] ?? '',
            'code' => $this['code'] ?? '',
            'name' => $this['name'] ?? '',
        ];
    }
}
