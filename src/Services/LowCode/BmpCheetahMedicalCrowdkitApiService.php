<?php

declare(strict_types=1);

namespace App\Services\Api\Bmp;

use App\Services\Api\ApiService;
use BrightLiu\LowCode\Services\LowCodeBaseService;
use BrightLiu\LowCode\Traits\Context\WithAuthContext;
use BrightLiu\LowCode\Traits\Context\WithContext;
use Illuminate\Support\Arr;

/**
 * 业务平台-服务人群工具模块
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
    public function getPatientCrowdInfo(int $orgId): ?array
    {
        return $this->get(
            $this->baseUriVia().'innerapi/get_patient_crowd_info',
            [
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
            ]
        );
    }

    /**
     * 获取专病的人员宽表
     */
    public function getPatientCrowdColGroup(): ?array
    {
        return $this->get(
            'innerapi/get_patient_crowd_col_group',
            [
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
            ]
        );
    }

    /**
     * 创建患者
     */
    public function createPatients(array $patients): void
    {
        if (empty($patients)) {
            return;
        }

        $this->json(
            'innerapi/personal-crowd/create',
            [
                'personal_batch_list' => array_map(
                    fn ($attributes) => [
                        'col_values' => array_values(Arr::map(
                            $attributes,
                            fn ($value, $key) => ['col_name' => $key, 'col_value' => $value]
                        )),
                    ],
                    $patients
                ),
                'data_source' => 1,
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
            ]
        );
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

        $this->json(
            'innerapi/personal-archive/create',
            [
                'user_id' => $userId,
                'col_values' => array_values(Arr::map(
                    $attributes,
                    fn ($value, $key) => ['col_name' => $key, 'col_value' => $value]
                )),
                'data_source' => 1,
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
            ]
        );
    }

    /**
     * 获取居民可选指标项
     */
    public function getMetricOptional(): array
    {
        return $this->get(
            'innerapi/personal-archive/field',
            [
                'org_code' => $this->getOrgCode(),
                'sys_code' => $this->getSystemCode(),
                'disease_code' => $this->getDiseaseCode(),
            ]
        );
    }
}
