<?php

namespace App\Containers\ClientSection\WorkSpace\UI\API\Requests;

use App\Ship\Parents\Requests\Request as ParentRequest;

final class TransferOwnershipRequest extends ParentRequest
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
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'email.exist' => 'Email không tồn tại',
        ];
    }
}
