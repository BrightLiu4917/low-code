# low-code

数据库相关：
参考 DDL.md 自己初始化数据库

1. 安装
```text
composer require bright-liu4917/low-code:dev-master --dev -vvv
```
2. 配置
```text
php artisan vendor:publish --provider="BrightLiu\LowCode\Providers\LowCodeServiceProvider" --tag="low-code-config"
```
3. 运行
```text
//创建缓存观察者
php artisan lowcode:install-cache-observer 

//创建列表控制器
php artisan lowcode:install-list-controller

//创建模型
php artisan lowcode:install-models --all

//创建查询引擎
php artisan lowcode:install-query-engine

//创建列表查询资源
php artisan lowcode:install-list-query-resource
```
