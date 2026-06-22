<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class RemoveTagForTaskRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'tags' => 'required|array',
            'tags.*' => 'required|exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tags.required' => 'Tags bắt buộc',
            'tags.array' => 'Tags phải là mảng',
            'tags.*.required' => 'Tags bắt buộc',
            'tags.*.exists' => 'Tags không tồn tại',
        ];
    }
}
