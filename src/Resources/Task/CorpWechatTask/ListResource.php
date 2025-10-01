<?php

declare(strict_types = 1);

namespace App\Http\V1\Resources\Task\CorpWechatTask;

use App\Enums\Model\CorpWechatMessageRecord\MessageStateEnum;
use App\Enums\Model\CorpWechatMessageRecord\MessageTypeEnum;
use App\Enums\Model\Task\ManagementTask\CompleteStatusEnum;
use App\Enums\Model\Task\ManagementTask\ExecuteStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CorpWechat\CorpWechatMessageRecord
 */
class ListResource extends JsonResource
{
    /**
     * @param Request $request
     */
    public function toArray($request): array
    {
        //        dd($this->managementTask);
        return [
            'id' => $this->id,

            'user_id'             => $this->user_id,

            // 患者信息
            'patient_name'        => $this->resident->ptt_nm ?? '',
            'patient_crowd_type'  => $this->managementTask->residentManagement->crowdType->name
                ?? '',

            // 纳管医生信息
            'managed_doctor_name' => $this->incprt_into_mng_dct_nm ?? '',
            'managed_doctor_code' => $this->incprt_into_mng_dct_cd ?? '',
            'message_type'        => $this->message_type ?? '',
            'message_state'       => $this->message_state ?? '',

            // 任务信息

            'task'                   => $this->managementTask ?? null,
            'task_id'                => $this->managementTask->id ?? 0,
            'task_title'             => $this->attr_message_type_definition,
            'task_status'            => $this->managementTask->execute_status,
            'task_status_definition' => ExecuteStatusEnum::make()
                ->translate($this->managementTask->execute_status),

            // TODO: 执行角色状态
            'executors'              => [
                'spec_doctor' => $this->fetchSpecDoctorProcess(),
                'patient'     => $this->fetchPatientProcess(),
            ],

            'created_at'    => $this->created_at,
            'failure_cause' => $this->attr_failure_cause ?? '',
        ];
    }

    /**
     * 获取医生的处置状态
     */
    protected function fetchSpecDoctorProcess(): string
    {
        return $this->attr_message_state_definition ?? '';
    }

    /**
     * 获取患者的处置状态
     */
    protected function fetchPatientProcess(): string
    {
        // TODO: 写法待完善
        if (($this->message_state ?? null) === MessageStateEnum::DOCTOR_SEND_RESIDENT_SUCCESS) {
            $process = '';

            switch (intval($this->message_type)) {
                case MessageTypeEnum::EDUCATION:
                    $process = match (true) {
                        CompleteStatusEnum::COMPLETED ==
                        $this->managementTask->complete_status => '已查看',
                        //                        CompleteStatusEnum::PENDING == $this->managementTask->complete_status => '未查看',
                        default                                => '未查看',
                    };
                    break;
                case MessageTypeEnum::QUESTIONNAIRE:
                    $process = match (true) {
                        CompleteStatusEnum::COMPLETED ==
                        $this->managementTask->complete_status => '已完成',
                        //                        CompleteStatusEnum::PENDING == $this->managementTask->complete_status => '未完成',
                        default                                => '未完成',
                    };
                    break;
            }

            return $process;
        }

        return '';
    }
}
