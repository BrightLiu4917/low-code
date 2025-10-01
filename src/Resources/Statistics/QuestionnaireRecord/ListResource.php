<?php

declare(strict_types=1);

namespace App\Http\V1\Resources\Statistics\QuestionnaireRecord;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * @mixin \App\Models\Statistics\QuestionnaireRecord
 */
final class ListResource extends JsonResource
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
            ]),
            'user_type_definition' => $this->user_type_definition ?? '',

            'content_questionnaire' => [
                'id'    => $this->contentQuestionnaire->id ?? 0,
                'title' => $this->contentQuestionnaire->title ?? '',
            ],
        ];
    }
}
