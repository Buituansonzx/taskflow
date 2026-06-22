<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class CreateTaskCommentRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:task_comments,id',
            'content' => 'required|string',
        ];
    }
}
