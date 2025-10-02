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

class CopyBmpCheetahMedicalCrowdkitApiServiceCommand extends Command
{
    protected $signature = 'lowcode:install-BmpCheetahMedicalCrowdkitApiService {--f : 覆盖现有的文件}';

    protected $description = '安装CopyBmpCheetahMedicalCrowdkitApiServiceService';

    public function handle()
    {
        $dir = 'Services/Api/Bmp/';
        $className = 'BmpCheetahMedicalCrowdkitApiService';
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

use Illuminate\Support\Facades\Http;
use BrightLiu\LowCode\Services\LowCodeBaseService;
use BrightLiu\LowCode\Traits\Context\WithAuthContext;
use BrightLiu\LowCode\Traits\Context\WithContext;
use Illuminate\Support\Arr;

/**
 * 业务平台-服务人群工具模块  自己去实现吧 跟低代码无关
 */
final class BmpCheetahMedicalCrowdkitApiService extends LowCodeBaseService
{
    use WithContext, WithAuthContext;

    /**
     * 声明 base_uri
     */
    protected function baseUriVia(): string
    {
        return config('business.api-service.bmp_cheetah_medical_crowdkit.uri', '');
    }

    /**
     * 获取专病的人员宽表
     */
    public function getPatientCrowdInfo(int $orgId = 0): ?array
    {
       return Http::asJson()
            ->retry(3)
            ->timeout(15)
            ->get($this->baseUriVia().'innerapi/get_patient_crowd_info',[
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
            ])
            ->throw()
            ->json();
    }

    /**
     * 获取专病的人员宽表
     */
    public function getPatientCrowdColGroup(): ?array
    {
        return Http::asJson()
                   ->retry(3)
                   ->timeout(15)
                   ->get($this->baseUriVia().'innerapi/get_patient_crowd_col_group',[
                       'org_code' => $this->getOrgCode(),
                       'sys_code' => $this->getSystemCode(),
                       'disease_code' => $this->getDiseaseCode(),
                   ])
                   ->throw()
                   ->json();
    }

    /**
     * 创建患者
     */
    public function createPatients(array $patients): void
    {
        if (empty($patients)) {
            return;
        }

         Http::asJson()
                   ->retry(3)
                   ->timeout(15)
                   ->post($this->baseUriVia().'innerapi/get_patient_crowd_col_group',[
                       'data_source' => 1,
                       'org_code' => $this->getOrgCode(),
                       'sys_code' => $this->getSystemCode(),
                       'disease_code' => $this->getDiseaseCode(),
                   ])
                   ->throw()
                   ->json();
    }

    /**
     * 更新患者信息
     *
     * @param string $userId 居民主索引
     * @param array $attributes 更新属性
     */
    public function updatePatientInfo(string $userId, array $attributes): void
    {
        if (empty($attributes)) {
            return;
        }

        Http::asJson()
            ->retry(3)
            ->timeout(15)
            ->post($this->baseUriVia().'innerapi/personal-archive/create',[
                'user_id' => $userId,
                'col_values' => array_values(Arr::map(
                    $attributes,
                    fn ($value, $key) => ['col_name' => $key, 'col_value' => $value]
                )),
                'data_source' => 1,
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
            ])
            ->throw()
            ->json();
    }

    /**
     * 获取居民可选指标项
     */
    public function getMetricOptional(): array
    {
        return Http::asJson()
                   ->retry(3)
                   ->timeout(15)
                   ->get($this->baseUriVia().'innerapi/personal-archive/field',[
                       'org_code' => $this->getOrgCode(),
                       'sys_code' => $this->getSystemCode(),
                       'disease_code' => $this->getDiseaseCode(),
                   ])
                   ->throw()
                   ->json();
    }
}
EOT;

        // 写入文件
        File::put($targetPath, $content);

        $this->info("安装成功: {$className}");
    }
}