<?php

namespace App\Containers\ClientSection\Task\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class UnassignMemberRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ];
    }
}
