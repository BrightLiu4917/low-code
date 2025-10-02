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

```