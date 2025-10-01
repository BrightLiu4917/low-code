<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Task\CorpWechatTask;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CorpWechat\CorpWechatMessageRecord
 *
 * @property int $total 系统推送成功任务数
 * @property int $sended_count 医生已发送任务数
 * @property float $executed_rate 任务执行率
 */
class SendedCountResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'doctor_name' => $this->incprt_into_mng_dct_nm,
            'doctor_code' => $this->incprt_into_mng_dct_cd,
            'total' => (int) $this->total,
            'sended_count' => (int) $this->sended_count,
            'executed_rate' => (float) $this->executed_rate,
        ];
    }
}
