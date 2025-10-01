<?php

return [
    "cache-model" => [
        // 是否开启 id/code 缓存
        'enable-id-code-cache' => true,

        // 是否开启 SQL 查询缓存（含 with）
        'enable-query-cache' => false,

        // 模型保存/删除时是否清理该模型 tag 下所有缓存
        'flush-tag-on-update' => false,

        // 缓存有效时间（秒）
        'ttl' => 600,
    ],
];