# low-code

数据库相关：
参考 DDL.md 自己初始化数据库

### 安装
```text
composer require bright-liu4917/low-code:dev-master --dev -vvv
```
### 发布配置
```text
php artisan vendor:publish --provider="BrightLiu\LowCode\Providers\LowCodeServiceProvider" --tag="low-code-config"
```

### 初始化若干个文件
```text
//初始化  相当于以下命令 创建缓存观察者，创建列表控制器，创建查询引擎，创建列表查询资源（不包含模型）
--f:是否强制覆盖
php artisan lowcode:init --f 

//创建缓存观察者
php artisan lowcode:install-cache-observer  --f

//创建列表控制器
php artisan lowcode:install-list-controller --f

//创建模型
//创建查询引擎
php artisan lowcode:install-query-engine --f

//创建列表查询资源
php artisan lowcode:install-list-query-resource --f
```
### 创建模型
```text
php artisan lowcode:copy-models bright-liu4917/low-code --f

```
