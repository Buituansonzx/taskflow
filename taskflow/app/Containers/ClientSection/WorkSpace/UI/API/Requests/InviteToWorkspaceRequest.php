<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class InviteToWorkspaceRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'role' => 'required|in:member,admin',
        ];
    }
}
