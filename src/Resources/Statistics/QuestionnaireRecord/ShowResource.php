<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Statistics\QuestionnaireRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Statistics\QuestionnaireRecord
 */
final class ShowResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            ...$this->only([
                'id',
                'content_questionnaire_id',
                'record_at',
                'user_name',
                'user_type',
                'record_count',
                'record_source_name',
                'result_analysis_data',
                'result_data',
                'result_score',
            ]),
            'user_type_definition' => $this->user_type_definition ?? '',

            'content_questionnaire' => [
                'id' => $this->contentQuestionnaire->id ?? 0,
                'title' => $this->contentQuestionnaire->title ?? '',
            ],
        ];
    }
}
