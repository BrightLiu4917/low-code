<?php

declare(strict_types = 1);

namespace BrightLiu\LowCode\Models\Traits;

use App\Models\Admin\Admin;
use App\Support\Context\AdminContext;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Support\Tools\Mask;

/**
 * @property string $creator_name 创建人名称
 * @property string $updater_name 更新人名称
 * @property string $mask_creator_name 创建人名称(脱敏)
 * @property string $mask_updater_name 更新人名称(脱敏)
 */
trait AdministratorRelation
{
    /**
     * @return void
     */
    protected static function bootAdministratorRelation()
    {
        if (!static::bootAdministratorRelationEnabled()) {
            return;
        }

        static::creating(function ($model) {
            if (empty($model->creator_id)) {
                $model->creator_id = AdminContext::instance()->getAdminId();
            }
        });

        static::updating(function ($model) {
            $model->updater_id = AdminContext::instance()->getAdminId();
        });
    }

    /**
     * @return bool
     */
    protected static function bootAdministratorRelationEnabled(): bool
    {
        return true;
    }

    /**
     * 创建人
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class)->withDefault([
            'id'       => 0,
            'union_id' => '',
            'realname' => '',
        ]);
    }

    /**
     * 更新人
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class)->withDefault([
            'id'       => 0,
            'union_id' => '',
            'realname' => '',
        ]);
    }

    /**
     * 访问器: 创建人名称
     *
     * @return Attribute
     */
    public function creatorName(): Attribute
    {
        return Attribute::get(fn () => $this->creator->realname ?? '');
    }

    /**
     * 访问器: 更新人名称
     *
     * @return Attribute
     */
    public function updaterName(): Attribute
    {
        return Attribute::get(fn () => $this->updater->realname ?? '');
    }

    /**
     * 访问器: 创建人名称(脱敏)
     *
     * @return Attribute
     */
    public function maskCreatorName(): Attribute
    {
        return Attribute::get(fn () => Mask::name($this->creator->realname ?? ''));
    }

    /**
     * 访问器: 更新人名称(脱敏)
     *
     * @return Attribute
     */
    public function maskUpdaterName(): Attribute
    {
        return Attribute::get(fn () => Mask::name($this->updater->realname ?? ''));
    }
}
