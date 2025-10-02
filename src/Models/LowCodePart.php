<?php

declare(strict_types=1);

namespace App\Models\Foundation;

use App\Models\Traits\Concerns\DiseaseRelation;
use Gupo\BetterLaravel\Database\BaseModel;

/**
 * 个性化模块
 */
final class PersonalizeModule extends BaseModel
{
    use DiseaseRelation;

    public const UPDATED_AT = null;

    protected $casts = [
        'metadata' => 'json',
    ];
}
