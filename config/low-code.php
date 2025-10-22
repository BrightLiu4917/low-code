<?php

return [
    "cache-model" => [
        // 是否开启 id/code 缓存
        'enable-id-code-cache' => env('LOW_CODE_CACHE_MODEL_ENABLE_ID_CODE_CACHE',false),

        // 是否开启 SQL 查询缓存（含 with）
        'enable-query-cache' => env('LOW_CODE_CACHE_MODEL_ENABLE_QUERY_CACHE',false),

        // 模型保存/删除时是否清理该模型 tag 下所有缓存
        'flush-tag-on-update' => env('LOW_CODE_CACHE_MODEL_FLUSH_TAG_ON_UPDATE',false),

        // 缓存有效时间（秒）
        'ttl' => env('LOW_CODE_CACHE_MODEL_TTL',600),
    ],

    /**
     * Http模块
     */
    'http' => [
        'modules' => [
            'api' => [
                'prefix' => 'api',
                'middleware' => ['api', 'auth.disease'],
            ]
        ],
    ],

    'dependencies' => []
];