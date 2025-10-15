# low-code
### 安装 Composer 包
```text 
composer require bright-liu4917/low-code:dev-master --dev -vvv
```
### 在 config/app.php配置文件中注册服务提供者：
```text
'providers' => [
    // 其他服务提供者...
    BrightLiu\LowCode\Providers\LowCodeServiceProvider::class,
],
```
### 发布配置文件
```text
php artisan vendor:publish --provider="BrightLiu\LowCode\Providers\LowCodeServiceProvider" --tag="low-code-config"
```

### 文件初始化
#### 快速初始化 (初始化所有核心文件（不包含模型)
```text
php artisan lowcode:init --f
```
#### 分步初始化
```text
# 1. 创建缓存观察者 --f：强制覆盖现有文件
php artisan lowcode:install-cache-observer --f

# 2. 创建列表控制器 --f：强制覆盖现有文件
php artisan lowcode:install-list-controller --f

# 3. 创建查询引擎 --f：强制覆盖现有文件
php artisan lowcode:install-query-engine --f

# 4. 创建列表查询资源 --f：强制覆盖现有文件
php artisan lowcode:install-list-query-resource --f
```
#### 模型文件创建
```text
# 从包中复制模型文件到项目 --f：强制覆盖现有文件
php artisan lowcode:copy-models bright-liu4917/low-code --f
```



### 使用方式
#### 路由
```text
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LowCode\LowCodeListController;
use BrightLiu\LowCode\Controllers\LowCode\LowCodePartController;
use BrightLiu\LowCode\Controllers\LowCode\LowCodeTemplateController;
use BrightLiu\LowCode\Controllers\Disease\DiseaseController;
use BrightLiu\LowCode\Controllers\DatabaseSource\DatabaseSourceController;
use App\Http\Controllers\LowCode\LowCodePersonalizeModuleController;
use App\Http\Controllers\LowCode\LowCodeListV2Controller;
use BrightLiu\LowCode\Controllers\LowCode\InitOrgDiseaseController;
Route::group([
        'middleware' => ['auth.disease'],//登陆中间件
], function () {

    Route::post('/org-disease', InitOrgDiseaseController::class);
    
    // 列表v1版本
    Route::prefix('v1/low-code/list')->group(function () {
        Route::get('list', [LowCodeListController::class, 'list']);
        Route::get('show', [LowCodeListController::class, 'show']);
        Route::post('simple-list', [LowCodeListController::class, 'simpleList']);
        Route::post('update', [LowCodeListController::class, 'update']);
        Route::post('delete', [LowCodeListController::class, 'delete']);
        Route::post('query', [LowCodeListController::class, 'query']);
        Route::post('pre', [LowCodeListController::class, 'pre']);
    });

    // 个性化模板
    Route::prefix('v2/foundation/personalize-module')->group(function () {
        Route::get('list', [LowCodePersonalizeModuleController::class, 'list']);
        Route::get('routes', [LowCodePersonalizeModuleController::class, 'routes']);
        Route::post('save', [LowCodePersonalizeModuleController::class, 'save']);
    });

    // 列表v2版本
    Route::prefix('v2/low-code/list')->group(function () {
        Route::get('simple-list', [LowCodeListV2Controller::class, 'simpleList']);
        Route::post('query', [LowCodeListV2Controller::class, 'query']);
        Route::post('pre', [LowCodeListV2Controller::class, 'pre']);
        Route::post('query-count', [LowCodeListV2Controller::class, 'queryCount']);
        Route::get('optional-columns', [LowCodeListV2Controller::class, 'optionalColumns']);
        Route::get('get-column-preference', [LowCodeListV2Controller::class, 'getColumnPreference']);
        Route::post('update-column-preference', [LowCodeListV2Controller::class, 'updateColumnPreference']);

    });

// 列表部件
    Route::prefix('v1/low-code/part')->group(function () {
        Route::get('list', [LowCodePartController::class, 'list']);
        Route::get('show', [LowCodePartController::class, 'show']);
        Route::post('update', [LowCodePartController::class, 'update']);
        Route::post('delete', [LowCodePartController::class, 'delete']);
        Route::get('get-table-fields', [LowCodePartController::class, 'getTableFields']);
    });

// 列表模板
    Route::prefix('v1/low-code/template')->group(function () {
        Route::get('list', [LowCodeTemplateController::class, 'list']);
        Route::get('show', [LowCodeTemplateController::class, 'show']);
        Route::post('update', [LowCodeTemplateController::class, 'update']);
        Route::post('delete', [LowCodeTemplateController::class, 'delete']);
    });
    Route::post('v1/low-code/template-bind-parts', [LowCodeTemplateController::class, 'bindPart']);
    
//病种
    Route::prefix('v1/disease')->group(function () {
        Route::get('list', [DiseaseController::class, 'list']);
        Route::get('show', [DiseaseController::class, 'show']);
        Route::post('update', [DiseaseController::class, 'update']);
        Route::post('delete', [DiseaseController::class, 'delete']);
    });
//数据源
    Route::prefix('v1/database-source')->group(function () {
        Route::get('list', [DatabaseSourceController::class, 'list']);
        Route::get('show', [DatabaseSourceController::class, 'show']);
        Route::post('update', [DatabaseSourceController::class, 'update']);
        Route::post('delete', [DatabaseSourceController::class, 'delete']);
    });
});
```

#### 日志
```text
    在 config/logging.php配置文件中注册服务提供者：
    
    'low-code-list' => 
        [
            'driver' => 'daily',
            'path' => storage_path('logs/low-code-list/daily.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],
```

### 初始化数据库
```text
# 数据库表机构
```mysql
CREATE TABLE `low_code_lists`
(
    `id`                       bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
    `disease_code`             varchar(32)         NOT NULL DEFAULT '' COMMENT '病种编码',
    `code`                     varchar(64)         NOT NULL DEFAULT '' COMMENT '列表编码',
    `parent_code`              varchar(64)         NOT NULL DEFAULT '' COMMENT '上级列表编码',
    `org_code`                 varchar(64)         NOT NULL DEFAULT '' COMMENT '机构编码',
    `admin_name`               varchar(64)         NOT NULL DEFAULT '' COMMENT '专病后台列表名字',
    `family_doctor_name`       varchar(64)         NOT NULL DEFAULT '' COMMENT '家庭医生列表名字',
    `mobile_doctor_name`       varchar(64)         NOT NULL DEFAULT '' COMMENT '移动医生列表名字',
    `admin_weight`             int(10) unsigned    NOT NULL DEFAULT '0' COMMENT '专病后台权重(降序)',
    `family_doctor_weight`     int(10) unsigned    NOT NULL DEFAULT '0' COMMENT '家庭医生权重(降序)',
    `mobile_doctor_weight`     int(10) unsigned    NOT NULL DEFAULT '0' COMMENT '移动医生权重(降序)',
    `crowd_type_code`          varchar(64)         NOT NULL DEFAULT '' COMMENT '人群类型编码',
    `template_code_filter`     varchar(64)         NOT NULL DEFAULT '' COMMENT '模板-筛选',
    `template_code_column`     varchar(64)         NOT NULL DEFAULT '' COMMENT '模板-表头',
    `template_code_field`      varchar(64)         NOT NULL DEFAULT '' COMMENT '模板-查询字段',
    `template_code_button`     varchar(64)         NOT NULL DEFAULT '' COMMENT '模板-操作按钮',
    `template_code_top_button` varchar(64)         NOT NULL DEFAULT '' COMMENT '模板-顶部操作按钮',
    `route_group`              json                         DEFAULT NULL COMMENT '前端路由组',
    `append_field_json`        json                         DEFAULT NULL COMMENT '追加查询字段，模板少了',
    `append_column_json`       json                         DEFAULT NULL COMMENT '追加表头，模板少了',
    `append_filter_json`       json                         DEFAULT NULL COMMENT '追加筛选条件，模板少了',
    `append_button_json`       json                         DEFAULT NULL COMMENT '追加按钮，模板少了',
    `append_top_button_json`   json                         DEFAULT NULL COMMENT '追加顶部按钮，模板少了',
    `remove_field_json`        json                         DEFAULT NULL COMMENT '移除查询字段，模板多了',
    `remove_filter_json`       json                         DEFAULT NULL COMMENT '移除筛选条件，模板多了',
    `remove_column_json`       json                         DEFAULT NULL COMMENT '移除表头，模板多了',
    `remove_button_json`       json                         DEFAULT NULL COMMENT '移除筛选条件，模板多了',
    `remove_top_button_json`   json                         DEFAULT NULL COMMENT '移除表头，模板多了',
    `default_order_by_json`    json                         DEFAULT NULL COMMENT '默认排序字段',
    `preset_condition_json`    json                         DEFAULT NULL COMMENT '预设条件',
    `list_type`                tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '列表类型0默认，9通用（适配多个人群分类）不可删除',
    `creator_id`               bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
    `updater_id`               bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新人ID',
    `created_at`               datetime                     DEFAULT NULL COMMENT '创建时间',
    `updated_at`               datetime                     DEFAULT NULL COMMENT '更新时间',
    `deleted_at`               datetime                     DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_code` (`code`),
    KEY `idx_disease_code` (`disease_code`),
    KEY `idx_org_code` (`org_code`),
    KEY `idx_crowd_type_code` (`crowd_type_code`),
    KEY `idx_creator_id` (`creator_id`),
    KEY `idx_updater_id` (`updater_id`),
    KEY `idx_template_code_filter` (`template_code_filter`),
    KEY `idx_template_code_field` (`template_code_field`),
    KEY `idx_template_code_column` (`template_code_column`),
    KEY `template_code_button` (`template_code_button`),
    KEY `idx_template_code_top_button` (`template_code_top_button`),
    KEY `idx_disease_code_org_code` (`disease_code`(16), `org_code`(16)),
    KEY `idx_partent_code` (`parent_code`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='低代码-列表';

CREATE TABLE `low_code_parts`
(
    `id`           bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
    `name`         varchar(64)         NOT NULL DEFAULT '' COMMENT '名字',
    `code`         varchar(64)         NOT NULL DEFAULT '' COMMENT '唯一编码',
    `org_code`     varchar(64)         NOT NULL DEFAULT '' COMMENT '机构编码',
    `part_type`    int(10) unsigned    NOT NULL DEFAULT '0' COMMENT '零件类型 1系统内置,2客户自定义',
    `description`  varchar(200)        NOT NULL DEFAULT '' COMMENT '描述',
    `content_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型:1列头展示，2筛选项，3操作栏按钮，4顶部按钮，5查询字段集合',
    `content`      json                         DEFAULT NULL COMMENT '组件内容',
    `weight`       int(10) unsigned    NOT NULL DEFAULT '0' COMMENT '权重(降序)',
    `creator_id`   bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
    `updater_id`   bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新人ID',
    `created_at`   datetime                     DEFAULT NULL COMMENT '创建时间',
    `updated_at`   datetime                     DEFAULT NULL COMMENT '更新时间',
    `deleted_at`   datetime                     DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_code` (`code`),
    KEY `idx_content_type` (`content_type`),
    KEY `idx_org_code` (`org_code`),
    KEY `idx_creator_id` (`creator_id`),
    KEY `idx_updater_id` (`updater_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4 COMMENT ='低代码-组件（零件）';

/******************************************/
/*   DatabaseName = ih_medication_sanya   */
/*   TableName = low_code_templates   */
/******************************************/
CREATE TABLE `low_code_templates` (
                                      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
                                      `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
                                      `disease_code` varchar(64) NOT NULL DEFAULT '' COMMENT '疾病编码',
                                      `code` varchar(64) NOT NULL DEFAULT '' COMMENT '唯一编码',
                                      `org_code` varchar(64) NOT NULL DEFAULT '' COMMENT '机构编码',
                                      `template_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '模板类型:1通用的已纳管,2通用的待纳管,3通用的推荐纳管,4通用的出组',
                                      `content_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '内容类型:1列头展示，2筛选项，3操作栏按钮，4顶部按钮，5查询字段集合',
                                      `description` varchar(200) NOT NULL DEFAULT '' COMMENT '描述',
                                      `weight` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权重(降序)',
                                      `creator_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
                                      `updater_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新人ID',
                                      `created_at` datetime DEFAULT NULL COMMENT '创建时间',
                                      `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
                                      `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
                                      PRIMARY KEY (`id`),
                                      UNIQUE KEY `unique_code` (`code`),
                                      KEY `idx_content_type` (`content_type`),
                                      KEY `idx_disease_code` (`disease_code`),
                                      KEY `idx_org_code` (`org_code`),
                                      KEY `idx_disease_code_org_code` (`disease_code`(16),`org_code`(16)),
                                      KEY `idx_template_type` (`template_type`),
                                      KEY `idx_creator_id` (`creator_id`),
                                      KEY `idx_updater_id` (`updater_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='低代码-组件（模板）';

CREATE TABLE `low_code_template_has_parts` (
                                               `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
                                               `part_code` varchar(64) NOT NULL DEFAULT '' COMMENT '零件唯一编码',
                                               `template_code` varchar(64) NOT NULL DEFAULT '' COMMENT '模板唯一编码',
                                               `locked` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否锁定字段：1锁定,0未锁定',
                                               `weight` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权重(降序)',
                                               `created_at` datetime DEFAULT NULL COMMENT '创建时间',
                                               `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
                                               `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
                                               PRIMARY KEY (`id`),
                                               KEY `idx_part_code` (`part_code`),
                                               KEY `idx_template_code` (`template_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='低代码-模板和组件的关系表';


CREATE TABLE `database_sources` (
                                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
                                    `disease_code` varchar(32) NOT NULL DEFAULT '' COMMENT '病种编码',
                                    `code` varchar(32) NOT NULL DEFAULT '' COMMENT '编码',
                                    `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
                                    `host` varchar(64) NOT NULL DEFAULT '' COMMENT '主机地址',
                                    `database` varchar(64) NOT NULL DEFAULT '' COMMENT '数据库',
                                    `table` varchar(64) NOT NULL DEFAULT '' COMMENT '表',
                                    `port` varchar(64) NOT NULL DEFAULT '' COMMENT '端口',
                                    `username` varchar(255) NOT NULL DEFAULT '' COMMENT '账号',
                                    `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
                                    `options` json DEFAULT NULL COMMENT '扩展项',
                                    `source_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '数据源类型:1数据,2业务',
                                    `creator_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人ID',
                                    `updater_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新人ID',
                                    `created_at` datetime DEFAULT NULL COMMENT '创建时间',
                                    `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
                                    `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
                                    PRIMARY KEY (`id`),
                                    UNIQUE KEY `unique_code` (`code`),
                                    KEY `idx_creator_id` (`creator_id`),
                                    KEY `idx_updater_id` (`updater_id`),
                                    KEY `idx_disease_code` (`disease_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='数据库源表';

/******************************************/
/*   DatabaseName = ih_medication_sanya   */
/*   TableName = pa_admin_preferences   */
/******************************************/
CREATE TABLE `pa_admin_preferences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `disease_code` varchar(32) NOT NULL DEFAULT '' COMMENT '病种编码',
  `org_code` varchar(32) NOT NULL DEFAULT '' COMMENT '所属机构编码',
  `admin_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '管理员id',
  `scene` varchar(32) NOT NULL DEFAULT '' COMMENT '场景',
  `pkey` varchar(64) NOT NULL DEFAULT '' COMMENT '偏好键',
  `pvalue` json DEFAULT NULL COMMENT '偏好值',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_disease_code_org_code` (`disease_code`(8),`org_code`(16))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员-偏好设置'
;

CREATE TABLE `personalize_modules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `disease_code` varchar(32) NOT NULL DEFAULT '' COMMENT '病种编码',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `module_id` varchar(32) NOT NULL DEFAULT '' COMMENT '模块ID',
  `module_type` varchar(32) NOT NULL DEFAULT '' COMMENT '模块类型',
  `metadata` json DEFAULT NULL COMMENT '元数据',
  `weight` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权重(降序)',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_disease_code_org_code` (`disease_code`(8)),
  KEY `idx_module_id` (`module_id`(8))
) ENGINE=InnoDB  COMMENT='个性化模块表';
```

```text
AppServiceProvider.register() 追加以下代码

use BrightLiu\LowCode\Context\AuthContext;
use BrightLiu\LowCode\Context\DiseaseContext;
use BrightLiu\LowCode\Context\OrgContext;


    public function register()
    {
        $this->app->singleton('context:org', OrgContext::class);
        $this->app->singleton('context:auth', AuthContext::class);
        $this->app->singleton('context:disease', DiseaseContext::class);
        //其他代码
    }
     
     获取用户当前信息
     auth()->user();
     
     //初始化病种用户信息等 中间件 DiseaseAuthenticate
     App\Http.Kernel.$routeMiddleware = [
         'auth.disease' => DiseaseAuthenticate::class
     ]
     
     创建文件 并配置用户中心请求地址
     config('business.bmo-service.auth.base_uri',env('BMO_AUTH_BASE_URI')) 
     
     创建文件 并配置业务中台请求地址
     config('business.api-service.bmp_cheetah_medical_crowdkit.uri', env('BMP_CHEETAH_MEDICAL_CROWDKIT_URI'))
     
     
     
    Env 添加
    BMP_CHEETAH_MEDICAL_CROWDKIT_URI=
    BMO_AUTH_BASE_URI=

    DB_MEDICAL_PLATFORM_CONNECTION=mysql
    DB_MEDICAL_PLATFORM_HOST={}
    DB_MEDICAL_PLATFORM_PORT=3306
    DB_MEDICAL_PLATFORM_DATABASE={}
    DB_MEDICAL_PLATFORM_USERNAME={}
    DB_MEDICAL_PLATFORM_PASSWORD={}
    DB_MEDICAL_PLATFORM_CONNECTION_TIMEOUT=10
    DB_MEDICAL_PLATFORM_PREPARES=false
```