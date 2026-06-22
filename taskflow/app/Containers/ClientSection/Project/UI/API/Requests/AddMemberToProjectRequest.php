<?php

namespace App\Containers\ClientSection\Project\UI\API\Requests;

use App\Containers\AppSection\Authorization\Models\Role;
use App\Ship\Parents\Requests\Request as ParentRequest;
use Illuminate\Validation\Rule;

final class AddMemberToProjectRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'members'          => ['required', 'array', 'min:1'],
            'members.*.email' => ['required', 'email'],
            'members.*.role'    => ['required', Rule::in([Role::ROLE_PROJECT_MANAGER, Role::ROLE_DEVELOPER])],
        ];
    }

    public function messages(): array
    {
        return [
            'members.required' => 'Members là bắt buộc',
            'members.array' => 'Members phải là một mảng',
            'members.min' => 'Members phải có ít nhất 1 phần tử',
            'members.*.email.required' => 'Email là bắt buộc',
            'members.*.email.email' => 'Email không hợp lệ',
            'members.*.role.required' => 'Role là bắt buộc',
            'members.*.role.in' => 'Role không hợp lệ',
        ];
    }
}
