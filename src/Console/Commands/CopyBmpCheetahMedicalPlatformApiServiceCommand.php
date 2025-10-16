<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use App\Services\Api\ApiService;
use App\Traits\Context\WithContext;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Traits\Context\WithAuthContext;
use Gupo\BetterLaravel\Exceptions\ServiceException;
use App\Services\LowCode\LowCodeQueryEngineService;

class CopyBmpCheetahMedicalPlatformApiServiceCommand extends Command
{
    protected $signature = 'lowcode:install-BmpCheetahMedicalPlatformApiService {--f : 覆盖现有的文件}';

    protected $description = '安装CopyBmpCheetahMedicalPlatformApiService';

    public function handle()
    {
        $dir = 'Services/Api/Bmp/';
        $className = 'BmpCheetahMedicalPlatformApiService';
        $targetPath = app_path($dir . $className . '.php');

        // 确保目录存在
        if (!File::exists(app_path($dir))) {
            File::makeDirectory(app_path($dir), 0755, true);
            $this->info('创建文件夹成功: '.$dir);
        }

        // 检查文件是否已存在
        if (File::exists($targetPath) && !$this->option('f')) {
            $this->error("{$dir} {$className} 已经存在，请勿重复安装。! 使用 --f 选项覆盖。");
            return;
        }

        // 文件内容
        $content = <<<'EOT'
<?php

declare(strict_types=1);

namespace App\Services\Api\Bmp;

use App\Services\Api\ApiService;
use BrightLiu\LowCode\Traits\Context\WithAuthContext;
use BrightLiu\LowCode\Traits\Context\WithContext;

/**
 * 业务平台-服务平台模块
 */
final class BmpCheetahMedicalPlatformApiService extends ApiService
{
    use WithContext, WithAuthContext;

    /**
     * 声明 base_uri
     */
    protected function baseUriVia(): string
    {
        return config('business.api-service.bmp_cheetah_medical_platform.uri', '');
    }

    /**
     * 获取人群分类
     */
    public function getCrowds(): array
    {
        return $this->json('innerapi/user/group/list', [
            'org_code' => $this->getOrgCode(),
            'sys_code' => $this->getSystemCode(),
            'disease_code' => $this->getDiseaseCode(),
            'tenant_id' => $this->getTenantId(),
        ]);
    }

    /**
     * 获取患者统计数据
     *
     * @param array<int> $statisticsTypes 统计项目类型 0-患者任务状态统计，1-服务方任务状态统计，2-近N天新增患者统计，3-今日打卡人数统计
     */
    public function getPatientStatisticsData(array $statisticsTypes = [0, 3]): array
    {
        return $this->json(
            'innerapi/stat/queryStatData',
            [
                'stat_types' => $statisticsTypes,
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
                'tenant_id' => $this->getTenantId(),
            ],
            options: [
                'timeout' => 3,
            ]
        );
    }

    /**
     * 获取患者预警统计数据
     */
    public function getPatientWarningStatisticsData(): int
    {
        return (int) $this->json(
            'innerapi/stat/queryWarnCount',
            [
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
                'tenant_id' => $this->getTenantId(),
            ],
            options: [
                'timeout' => 3,
            ]
        );
    }
}

EOT;

        // 写入文件
        File::put($targetPath, $content);

        $this->info("安装成功: {$className}");
    }
}