<?php

namespace BrightLiu\LowCode;

use BrightLiu\LowCode\Models\Traits\OrgRelation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use BrightLiu\LowCode\Models\Traits\UniqueCodeRelation;
use BrightLiu\LowCode\Models\Traits\AdministratorRelation;
use BrightLiu\LowCode\Models\Traits\DiseaseRelationQueries;
use BrightLiu\LowCode\Enums\Model\LowCodePart\ContentTypeEnum;
use BrightLiu\LowCode\Enums\Model\LowCodeTemplate\TemplateTypeEnum;

/**
 * @mixin IdeHelperLowCodeTemplate
 */
class LowCodeTemplate extends LowCodeBaseModel
{

    /**
     * @var string
     */
    protected string  $primaryKey = 'code';

    /**
     * @var bool
     */
    public bool $incrementing = false;

    use
        OrgRelation,
        SoftDeletes,
        UniqueCodeRelation,
        DiseaseRelationQueries,
        AdministratorRelation;

    protected array $casts = [
        'id' => 'integer', // bigint(20) unsigned NOT NULL AUTO_INCREMENT
        'name' => 'string', // varchar(64) NOT NULL DEFAULT ''
        'disease_code' => 'string', // varchar(64) NOT NULL DEFAULT ''
        'code' => 'string', // varchar(64) NOT NULL DEFAULT ''
        'org_code' => 'string', // varchar(64) NOT NULL DEFAULT ''
        'template_type' => 'integer', // tinyint(3) unsigned NOT NULL DEFAULT '0'
        'content_type' => 'integer', // tinyint(3) unsigned NOT NULL DEFAULT '0'
        'description' => 'string', // varchar(200) NOT NULL DEFAULT ''
        'weight' => 'integer', // int(10) unsigned NOT NULL DEFAULT '0'
        'creator_id' => 'integer', // bigint(20) unsigned NOT NULL DEFAULT '0'
        'updater_id' => 'integer', // bigint(20) unsigned NOT NULL DEFAULT '0'
        'created_at' => 'datetime', // datetime DEFAULT NULL
        'updated_at' => 'datetime', // datetime DEFAULT NULL
        'deleted_at' => 'datetime', // datetime DEFAULT NULL
    ];

    protected array $fillable = [
          "id",//bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          "name",//varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
          "disease_code",//varchar(64) NOT NULL DEFAULT '' COMMENT '疾病编码',
          "code",//varchar(64) NOT NULL DEFAULT '' COMMENT '唯一编码',
          "org_code",//varchar(64) NOT NULL DEFAULT '' COMMENT '机构编码',
          "template_type",//tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '模板类型:1通用的已纳管,2通用的待纳管,3通用的推荐纳管,4通用的出组',
          "content_type",//tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容类型:1列头展示，2筛选项，3操作栏按钮，4顶部按钮，5查询字段集合',
          "description",//varchar(200) NOT NULL DEFAULT '' COMMENT '描述',
          "weight",//int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权重(降序)',
          "creator_id",//bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
          "updater_id",//bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新人ID',
          "created_at",//datetime DEFAULT NULL COMMENT '创建时间',
          "updated_at",//datetime DEFAULT NULL COMMENT '更新时间',
          "deleted_at",//datetime DEFAULT NULL COMMENT '删除时间',
    ];





    public function templateTypeDefinition(): Attribute
    {
        return TemplateTypeEnum::makeAttribute($this);
    }

    /**
     * @return Attribute
     */
    public function contentTypeDefinition(): Attribute
    {
        return ContentTypeEnum::makeAttribute($this);
    }

    /**
     * 模板绑定零件
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bindPartList():BelongsToMany
    {
        return $this->belongsToMany(LowCodePart::class, 'low_code_template_has_parts', 'template_code', 'part_code')
            ->orderBy('low_code_template_has_parts.weight','asc')
            ->withPivot(['locked','weight']);
    }
}
