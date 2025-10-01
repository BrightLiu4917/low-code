<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Resident\DoctorAction;

use Illuminate\Http\Request;
use App\Models\Resident\ResidentDoctorRecord;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ResidentDoctorRecord
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
                'content',
                'admin_id',
                'user_id',
                'created_at',
                'updated_at',
            ]),
            'doctor_name' => $this->doctor->realname ?? '',
            'doctor_username' => $this->doctor->username ?? '',
        ];
    }
}
