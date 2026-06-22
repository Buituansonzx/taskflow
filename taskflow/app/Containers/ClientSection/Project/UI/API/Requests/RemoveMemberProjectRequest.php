<?php

namespace App\Containers\ClientSection\Project\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class RemoveMemberProjectRequest extends ParentRequest
{
    protected array $decode = [];

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại.',
        ];
    }
}
