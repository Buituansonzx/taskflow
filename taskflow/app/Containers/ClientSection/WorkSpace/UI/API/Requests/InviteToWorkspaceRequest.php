<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Requests;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

final class InviteToWorkspaceRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'members'          => ['required', 'array', 'min:1'],
            'members.*.email' => ['required', 'email'],
            'members.*.role'    => ['required', Rule::in([Role::ROLE_MEMBER, Role::ROLE_ADMIN])],
        ];
    }
}
