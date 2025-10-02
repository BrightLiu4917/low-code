# low-code

数据库相关：
参考 DDL.md 自己初始化数据库

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
Route::group([
    //    'middleware' => ['auth.api'],
], function () {

    //列表
    Route::prefix('v1/low-code/list')->group(function () {
        Route::get('list', [LowCodeListController::class, 'list']);
        Route::get('show', [LowCodeListController::class, 'show']);
        Route::post('simple-list', [LowCodeListController::class, 'simpleList']);
        Route::post('update', [LowCodeListController::class, 'update']);
        Route::post('delete', [LowCodeListController::class, 'delete']);
        Route::post('query', [LowCodeListController::class, 'query']);
        Route::post('pre', [LowCodeListController::class, 'pre']);
    });

    //零件
    Route::prefix('v1/low-code/part')->group(function () {
        Route::get('list', [LowCodePartController::class, 'list']);
        Route::get('show', [LowCodePartController::class, 'show']);
        Route::post('update', [LowCodePartController::class, 'update']);
        Route::post('delete', [LowCodePartController::class, 'delete']);
        Route::get('get-table-fields', [LowCodePartController::class, 'getTableFields']);
    });

    //模板
    Route::prefix('v1/low-code/template')->group(function () {
        Route::get('list', [LowCodeTemplateController::class, 'list']);
        Route::get('show', [LowCodeTemplateController::class, 'show']);
        Route::post('update', [LowCodeTemplateController::class, 'update']);
        Route::post('delete', [LowCodeTemplateController::class, 'delete']);
    });
    Route::post('v1/low-code/template-bind-parts', [LowCodeTemplateController::class, 'bindPart']);

    //疾病
    Route::prefix('v1/disease')->group(function () {
        Route::get('list', [DiseaseController::class, 'list']);
        Route::get('show', [DiseaseController::class, 'show']);
        Route::post('update', [DiseaseController::class, 'update']);
        Route::post('delete', [DiseaseController::class, 'delete']);
    });
    
    //数据库资源
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